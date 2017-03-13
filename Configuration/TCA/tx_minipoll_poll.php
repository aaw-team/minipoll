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
$renderTypeCloseDatetime = 'inputDateTime';
if (\version_compare(TYPO3_version, '8', '<')) {
    $labels['sheet.general'] = 'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.sheet.general';
    $labels['sheet.access'] = 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access';
    $renderTypeCloseDatetime = '';
}

return [
    'ctrl' => [
        'label' => 'title',
        'tstamp' => 'tstamp',
        'sortby' => 'sorting',
        'title' => 'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.title',
        'delete' => 'deleted',
        'crdate' => 'crdate',
        'hideAtCopy' => true,
        'prependAtCopy' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.prependAtCopy',
        'descriptionColumn' => 'description',
        'cruser_id' => 'cruser_id',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
            'fe_group' => 'fe_group'
        ],
        'dividers2tabs' => '1',
        'searchFields' => 'title,description'
    ],
    'interface' => [
        'showRecordFieldList' => 'title,description,hidden,starttime,endtime,fe_group'
    ],
    'columns' => [
        'hidden' => $GLOBALS['TCA']['tt_content']['columns']['hidden'],
        'starttime' => $GLOBALS['TCA']['tt_content']['columns']['starttime'],
        'endtime' => $GLOBALS['TCA']['tt_content']['columns']['endtime'],
        'fe_group' => $GLOBALS['TCA']['tt_content']['columns']['fe_group'],
        'title' => [
            'label' => 'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.title',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
                'eval' => 'trim,required'
            ]
        ],
        'description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.description',
            'config' => [
                'type' => 'text',
                'cols' => '80',
                'rows' => '15',
                'softref' => 'typolink_tag,images,email[subst],url'
            ]
        ],
        'useCaptcha' => [
            'exclude' => true,
            'label' => 'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.useCaptcha',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.useCaptcha.enable'
                    ]
                ]
            ]
        ],
        'duplicationCheck' => [
            'exclude' => true,
            'label' => 'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.duplicationCheck',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.duplicationCheck.ip',
                        'ip'
                    ],
                    [
                        'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.duplicationCheck.cookie',
                        'cookie'
                    ],
                    [
                        'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.duplicationCheck.none',
                        'none'
                    ]
                ],
                'default' => 'ip'
            ]
        ],
        'closeDatetime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.closeDatetime',
            'config' => [
                'type' => 'input',
                'renderType' => $renderTypeCloseDatetime,
                'eval' => 'datetime',
                'size' => '13',
                'default' => 0
            ]
        ],
        'options' => [
            'exclude' => false,
            'label' => 'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.options',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_minipoll_poll_option',
                'foreign_field' => 'poll',
                'foreign_sortby' => 'sorting',
                'foreign_types' => [
                    '1' => [
                        'showitem' => 'title,--palette--;;relatedOptions'
                    ]
                ],
                'appearance' => [
                    'newRecordLinkAddTitle' => true,
                    'useSortable' => true,
                    'showPossibleLocalizationRecords' => false,
                    'showRemovedLocalizationRecords' => false,
                    'showSynchronizationLink' => false,
                    'showAllLocalizationLink' => false,
                    'enabledControls' => [
                        'info' => true,
                        'new' => true,
                        'dragdrop' => true,
                        'sort' => true,
                        'hide' => true,
                        'delete' => true,
                        'localize' => true,
                    ]
                ],
                'behaviour' => [
                    'localizationMode' => 'select',
                    'localizeChildrenAtParentLocalization' => true,
                ]
            ]
        ]
    ],
    'types' => [
        '1' => [
            'showitem' => '
                --div--;' . $labels['sheet.general'] . ',
                    --palette--;LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.palette.header;header,
                    description,
                --div--;LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.sheet.settings,
                    --palette--;LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.palette.settings;settings,
                    options;,
                --div--;' . $labels['sheet.access'] . ',
                    --palette--;;hidden,
                    --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access,
            ',
            'columnsOverrides' => [
                'description' => [
                    'defaultExtras' => 'richtext:rte_transform[mode=ts_css]',
                    'config' => [
                        'enableRichtext' => true,
                        'richtextConfiguration' => 'default'
                    ]
                ]
            ]
        ]
    ],
    'palettes' => [
        'header' => [
            'showitem' => '
                title,closeDatetime
            '
        ],
        'hidden' => [
            'showitem' => '
                hidden;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:field.default.hidden
            '
        ],
        'access' => [
            'showitem' => '
                starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel,
                endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel,
                --linebreak--,
                fe_group;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:fe_group_formlabel,
                --linebreak--,editlock
            '
        ],
        'settings' => [
            'showitem' => '
                duplicationCheck,useCaptcha,
            '
        ]
    ]
];
