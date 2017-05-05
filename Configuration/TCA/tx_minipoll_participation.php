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

return [
    'ctrl' => [
        'title' => 'Participation', // No localization: table is not shown to user
        'crdate' => 'crdate',
        'hideTable' => true,
        'searchFields' => 'poll,ip,frontend_user,answers'
    ],
    'interface' => [
        'showRecordFieldList' => 'poll,ip,frontend_user,answers'
    ],
    'columns' => [
        'poll' => [
            'label' => 'Poll', // No localization: field is not shown to user
            'config' => [
                'readOnly' => true,
                'type' => 'select',
                'foreign_table' => 'tx_minipoll_poll',
                'minitems' => 1,
                'maxitems' => 1,
                'renderType' => 'selectSingle'
            ]
        ],
        'ip' => [
            'label' => 'IP Address', // No localization: field is not shown to user
            'config' => [
                'readOnly' => true,
                'type' => 'input',
                'size' => 50
            ]
        ],
        'frontend_user' => [
            'label' => 'Frontend User', // No localization: field is not shown to user
            'config' => [
                'readOnly' => true,
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'minitems' => 1,
                'maxitems' => 1
            ]
        ],
        'answers' => [
            'label' => 'Answers', // No localization: field is not shown to user
            'config' => [
                'readOnly' => true,
                'type' => 'inline',
                'foreign_table' => 'tx_minipoll_answer',
                'foreign_field' => 'participation',
            ]
        ]
    ],
    'types' => [
        '1' => [
            'showitem' => '
                poll,ip,frontend_user,answers',
        ]
    ],
    'palettes' => []
];
