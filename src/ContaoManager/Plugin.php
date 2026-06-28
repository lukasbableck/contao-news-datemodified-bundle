<?php
namespace Lukasbableck\ContaoNewsDatemodifiedBundle\ContaoManager;

use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\NewsBundle\ContaoNewsBundle;
use Lukasbableck\ContaoNewsDatemodifiedBundle\ContaoNewsDatemodifiedBundle;

class Plugin implements BundlePluginInterface {
    public function getBundles(ParserInterface $parser): array {
        return [BundleConfig::create(ContaoNewsDatemodifiedBundle::class)->setLoadAfter([ContaoNewsBundle::class])];
    }
}
