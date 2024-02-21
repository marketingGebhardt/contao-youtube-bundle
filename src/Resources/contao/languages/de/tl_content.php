<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein-Youtube-Bundle.
 *
 * (c) Werbeagentur Dreibein GmbH
 *
 * @license LGPL-3.0-or-later
 */

$table = 'tl_content';

// Überschreiben der Übersetzung für die Checkbox "splash image"
$GLOBALS['TL_LANG'][$table]['splashImage'] = ['Eigenes Vorschaubild verwenden', 'Es kann ein eigenes Vorschaubild ausgewählt werden. Ansonsten wird das Preview-Bild von Youtube geladen.'];

// translations for new fields and legends
$GLOBALS['TL_LANG'][$table]['youtube_schemaorg_legend'] = 'Schema.org Einstellungen';
$GLOBALS['TL_LANG'][$table]['youtube_name'] = ['Name des Videos', 'Geben Sie den Namen des Videos an. Dieser wird für die Schema.org-Tags verwendet.'];
$GLOBALS['TL_LANG'][$table]['youtube_description'] = ['Beschreibung des Videos', 'Geben Sie eine Beschreibung des Videos an. Diese wird für die Schema.org-Tags verwendet.'];
$GLOBALS['TL_LANG'][$table]['youtube_upload_date'] = ['Upload-Datum des Videos', 'Datum, wann das Video hochgeladen wurde. Dieser Wert wird in den Schema.org-Tags verwendet.'];
