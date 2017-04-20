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
        'title' => 'Answer', // No localization: table is not shown to user
        'crdate' => 'crdate',
        'hideTable' => true,
        'searchFields' => 'participation,poll_option,value'
    ],
    'interface' => [
        'showRecordFieldList' => 'participation,poll_option,value'
    ],
    'columns' => [
        'participation' => [
            'label' => 'Participation', // No localization: field is not shown to user
            'config' => [
                'readOnly' => true,
                'type' => 'select',
                'foreign_table' => 'tx_minipoll_participation',
                'minitems' => 1,
                'maxitems' => 1
            ]
        ],
        'poll_option' => [
            'label' => 'Poll option', // No localization: field is not shown to user
            'config' => [
                'readOnly' => true,
                'type' => 'select',
                'foreign_table' => 'tx_minipoll_poll_option',
                'minitems' => 1,
                'maxitems' => 1
            ]
        ],
        'value' => [
            'label' => 'Value', // No localization: field is not shown to user
            'config' => [
                'readOnly' => true,
                'type' => 'text',
                'cols' => '80',
                'rows' => '15'
            ]
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => 'participation,poll_option,value',
        ]
    ],
    'palettes' => []
];
