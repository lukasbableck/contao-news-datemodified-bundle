<?php
namespace Lukasbableck\ContaoNewsDatemodifiedBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\CoreBundle\Routing\ResponseContext\JsonLd\JsonLdManager;
use Contao\CoreBundle\Routing\ResponseContext\ResponseContextAccessor;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\ModuleModel;
use Contao\ModuleNews;
use Contao\NewsModel;
use Spatie\SchemaOrg\NewsArticle;
use Symfony\Component\HttpFoundation\RequestStack;

class DatemodifiedSchemaListener {
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly ResponseContextAccessor $responseContextAccessor,
        private readonly ScopeMatcher $scopeMatcher,
    ) {
    }

    #[AsHook('getFrontendModule', priority: 61)]
    public function onGetFrontendModule(ModuleModel $model, string $buffer, object $module): string {
        if (!$request = $this->requestStack->getCurrentRequest()) {
            return $buffer;
        }

        if (!$this->scopeMatcher->isFrontendRequest($request)) {
            return $buffer;
        }

        if (!$module instanceof ModuleNews) {
            return $buffer;
        }

        $jsonldManager = $this->responseContextAccessor->getResponseContext()->get(JsonLdManager::class);
        $graph = $jsonldManager->getGraphForSchema(JsonLdManager::SCHEMA_ORG);
        if (!($graph->getNodes()[NewsArticle::class] ?? false)) {
            return $buffer;
        }

        $newsArticles = $graph->getNodes()[NewsArticle::class];
        foreach ($newsArticles as &$newsArticle) {
            $identifier = $newsArticle->getProperties()['identifier'] ?? null;
            if (!$identifier) {
                continue;
            }

            $newsModel = NewsModel::findByPk(array_last(explode('/', $identifier)));
            if (!$newsModel) {
                continue;
            }

            $dateModified = $newsModel->dateModified ?: 0;
            if (0 === $dateModified) {
                continue;
            }
            $newsArticle->dateModified(new \DateTime(strtotime($dateModified)));
        }

        return $buffer;
    }
}
