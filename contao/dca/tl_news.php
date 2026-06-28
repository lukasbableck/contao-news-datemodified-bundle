<?php

use Contao\CoreBundle\DataContainer\PaletteManipulator;

$GLOBALS['TL_DCA']['tl_news']['fields']['dateModified'] = [
    'inputType' => 'text',
    'eval' => [
        'tl_class' => 'w50 clr wizard',
        'rgxp' => 'datim',
        'datepicker' => true,
    ],
    'sql' => ['type' => 'string', 'length' => 10, 'default' => ''],
];

PaletteManipulator::create()
    ->addField('dateModified', 'time')
    ->applyToPalette('default', 'tl_news')
;
