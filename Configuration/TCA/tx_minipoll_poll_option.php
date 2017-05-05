<?php
/*
 * Copyright 2017 Agentur am Wasser | Maeder & Partner AG
 *
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

$labels = [
    'sheet.general' => 'LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general',
    'sheet.access' => 'LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access',
];
if (\version_compare(TYPO3_version, '8', '<')) {
    $labels['sheet.general'] = 'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.sheet.general';
    $labels['sheet.access'] = 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access';
}

return [
    'ctrl' => [
        'label' => 'title',
        'tstamp' => 'tstamp',
        'hideTable' => true,
        'title' => 'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll_option.title',
        'typeicon_classes' => [
            'default' => 'minipoll-poll-option'
        ],
        'delete' => 'deleted',
        'crdate' => 'crdate',
        'hideAtCopy' => false,
        'prependAtCopy' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.prependAtCopy',
        'cruser_id' => 'cruser_id',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'dividers2tabs' => '1',
        'searchFields' => 'title,poll'
    ],
    'interface' => [
        'showRecordFieldList' => 'title,poll,hidden'
    ],
    'columns' => [
        'hidden' => $GLOBALS['TCA']['tt_content']['columns']['hidden'],
        'title' => [
            'label' => 'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll_option.field.title',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
                'eval' => 'trim,required'
            ]
        ],
        'poll' => [
            'label' => 'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll_option.field.poll',
            'config' => [
                'readOnly' => true,
                'type' => 'select',
                'foreign_table' => 'tx_minipoll_poll',
                'minitems' => 1,
                'maxitems' => 1,
                'renderType' => 'selectSingle'
            ]
        ],
        'answers' => [
            'label' => 'Answers', // No localization: field is not shown to user
            'config' => [
                'readOnly' => true,
                'type' => 'inline',
                'foreign_table' => 'tx_minipoll_answer',
                'foreign_field' => 'poll_option',
            ]
        ]
    ],
    'types' => [
        '1' => [
            'showitem' => '
                --div--;' . $labels['sheet.general'] . ',
                    title,poll,
                --div--;' . $labels['sheet.access'] . ',
                    --palette--;;hidden,
            '
        ]
    ],
    'palettes' => [
        'hidden' => [
            'showitem' => '
                hidden;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:field.default.hidden
            '
        ],
        'relatedOptions' => [
            'showitem' => 'hidden',
            'isHiddenPalette' => true
        ]
    ]
];
