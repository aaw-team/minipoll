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
$renderTypeDatetime = 'inputDateTime';
if (\version_compare(TYPO3_version, '8', '<')) {
    $labels['sheet.general'] = 'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.sheet.general';
    $labels['sheet.access'] = 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access';
    $renderTypeDatetime = '';
}

return [
    'ctrl' => [
        'label' => 'title',
        'tstamp' => 'tstamp',
        'sortby' => 'sorting',
        'title' => 'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.title',
        'typeicon_classes' => [
            'default' => 'minipoll-poll'
        ],
        'delete' => 'deleted',
        'crdate' => 'crdate',
        'hideAtCopy' => true,
        'prependAtCopy' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.prependAtCopy',
        'cruser_id' => 'cruser_id',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
            'fe_group' => 'fe_group'
        ],
        'dividers2tabs' => '1',
        // Remove 'requestUpdate' when dropping support for TYPO3 7.6
        'requestUpdate' => 'status',
        'searchFields' => 'title,description'
    ],
    'interface' => [
        'showRecordFieldList' => 'title,description,hidden,starttime,endtime,fe_group,use_captcha,duplication_check,status,allow_multiple,display_results,open_datetime,close_datetime'
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
        'use_captcha' => [
            'exclude' => true,
            'label' => 'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.use_captcha',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.use_captcha.enable'
                    ]
                ]
            ]
        ],
        'duplication_check' => [
            'exclude' => true,
            'label' => 'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.duplication_check',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.duplication_check.ip',
                        \AawTeam\Minipoll\Domain\Model\Poll::DUPLICATION_CHECK_IP
                    ],
                    [
                        'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.duplication_check.cookie',
                        \AawTeam\Minipoll\Domain\Model\Poll::DUPLICATION_CHECK_COOKIE
                    ],
                    [
                        'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.duplication_check.feuser',
                        \AawTeam\Minipoll\Domain\Model\Poll::DUPLICATION_CHECK_FEUSER
                    ],
                    [
                        'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.duplication_check.none',
                        \AawTeam\Minipoll\Domain\Model\Poll::DUPLICATION_CHECK_NONE
                    ]
                ],
                'default' => \AawTeam\Minipoll\Domain\Model\Poll::DUPLICATION_CHECK_IP
            ]
        ],
        'status' => [
            'exclude' => true,
            'label' => 'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.status',
            'onChange' => 'reload',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.status.closed',
                        \AawTeam\Minipoll\Domain\Model\Poll::STATUS_CLOSED
                    ],
                    [
                        'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.status.open',
                        \AawTeam\Minipoll\Domain\Model\Poll::STATUS_OPEN
                    ],
                    [
                        'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.status.bydate',
                        \AawTeam\Minipoll\Domain\Model\Poll::STATUS_BYDATE
                    ]
                ],
                'default' => \AawTeam\Minipoll\Domain\Model\Poll::STATUS_CLOSED
            ]
        ],
        'open_datetime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.open_datetime',
            'displayCond' => 'FIELD:status:=:' . \AawTeam\Minipoll\Domain\Model\Poll::STATUS_BYDATE,
            'config' => [
                'type' => 'input',
                'renderType' => $renderTypeDatetime,
                'eval' => 'datetime',
                'size' => '13',
                'default' => 0
            ]
        ],
        'close_datetime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.close_datetime',
            'displayCond' => 'FIELD:status:=:' . \AawTeam\Minipoll\Domain\Model\Poll::STATUS_BYDATE,
            'config' => [
                'type' => 'input',
                'renderType' => $renderTypeDatetime,
                'eval' => 'datetime',
                'size' => '13',
                'default' => 0
            ]
        ],
        'allow_multiple' => [
            'exclude' => true,
            'label' => 'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.allow_multiple',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.allow_multiple.enable'
                    ]
                ]
            ]
        ],
        'display_results' => [
            'exclude' => true,
            'label' => 'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.display_results',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.display_results.always',
                        \AawTeam\Minipoll\Domain\Model\Poll::DISPLAY_RESULTS_ALWAYS
                    ],
                    [
                        'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.display_results.onvote',
                        \AawTeam\Minipoll\Domain\Model\Poll::DISPLAY_RESULTS_ONVOTE
                    ],
                    [
                        'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.display_results.never',
                        \AawTeam\Minipoll\Domain\Model\Poll::DISPLAY_RESULTS_NEVER
                    ]
                ],
                'default' => \AawTeam\Minipoll\Domain\Model\Poll::DISPLAY_RESULTS_ALWAYS
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
                    'enabledControls' => [
                        'info' => true,
                        'new' => true,
                        'dragdrop' => true,
                        'sort' => true,
                        'hide' => true,
                        'delete' => true,
                        'localize' => false,
                    ]
                ]
            ]
        ],
        'participations' => [
            'label' => 'Participations',
            'config' => [
                'readOnly' => true,
                'type' => 'inline',
                'foreign_table' => 'tx_minipoll_participation',
                'foreign_field' => 'poll',
            ]
        ]
    ],
    'types' => [
        '1' => [
            'showitem' => '
                --div--;' . $labels['sheet.general'] . ',
                    title,
                    description,
                --div--;LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.sheet.options,
                    options;,
                --div--;LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.sheet.settings,
                    --palette--;LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.palette.status;status,
                    --palette--;LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.palette.display;display,
                    --palette--;LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.palette.settings;settings,
                --div--;' . $labels['sheet.access'] . ',
                    --palette--;;hidden,
                    --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access,
            ',
            'columnsOverrides' => [
                'description' => call_user_func(function(){
                    if (\version_compare(TYPO3_version, '8', '<')) {
                        return ['defaultExtras' => 'richtext:rte_transform[mode=ts_css]'];
                    }
                    return ['config' => [
                        'enableRichtext' => true,
                    ]];
                }),
            ]
        ]
    ],
    'palettes' => [
        'hidden' => [
            'showitem' => '
                hidden;LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:tca.poll.field.hidden
            '
        ],
        'access' => [
            'showitem' => '
                starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel,
                endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel,
                --linebreak--,
                fe_group;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:fe_group_formlabel
            '
        ],
        'settings' => [
            'showitem' => '
                duplication_check,use_captcha
            '
        ],
        'status' => [
            'showitem' => '
                status,--linebreak--,open_datetime,close_datetime
            '
        ],
        'display' => [
            'showitem' => '
                display_results,allow_multiple
            '
        ]
    ]
];
