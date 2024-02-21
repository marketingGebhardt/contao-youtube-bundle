<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein-Youtube-Bundle.
 *
 * (c) Werbeagentur Dreibein GmbH
 *
 * @license LGPL-3.0-or-later
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

$table = 'tl_content';

PaletteManipulator::create()
    ->addLegend('youtube_schemaorg_legend', 'template_legend', PaletteManipulator::POSITION_BEFORE)
    ->addField(['youtube_name', 'youtube_description', 'youtube_upload_date'], 'youtube_schemaorg_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('youtube', $table)
;

// schema.org name of the youtube element
$GLOBALS['TL_DCA'][$table]['fields']['youtube_name'] = [
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 255, 'notnull' => true, 'default' => ''],
];

// schema.org description for the youtube element
$GLOBALS['TL_DCA'][$table]['fields']['youtube_description'] = [
    'exclude' => true,
    'inputType' => 'textarea',
    'eval' => ['tl_class' => 'clr'],
    'sql' => ['type' => 'text', 'notnull' => false],
];

// schema.org release_date for the youtube element
$GLOBALS['TL_DCA'][$table]['fields']['youtube_upload_date'] = [
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['rgxp' => 'date', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
    'sql' => ['type' => 'string', 'length' => 10, 'notnull' => true, 'default' => ''],
];
