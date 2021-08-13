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

// Register list plugin
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'AawTeam.Minipoll',
    'Poll',
    'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:plugin.title',
    'EXT:minipoll/Resources/Public/Icons/plugin-minipoll-poll.svg'
);
// Register detail plugin
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'AawTeam.Minipoll',
    'PollDetail',
    'LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:plugin_detail.title',
    'EXT:minipoll/Resources/Public/Icons/plugin-minipoll-poll.svg'
);

// Disable unused fields when the list plugin is shown
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['minipoll_poll'] = 'select_key,pages,recursive';

// Add flexform for the detail plugin
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('minipoll_polldetail', 'FILE:EXT:minipoll/Configuration/Flexform/PluginPoll.xml');
// Show tt_content.pi_flexform when the detail plugin is shown
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['minipoll_polldetail'] = 'pi_flexform';
// Disable unused fields when the detail plugin is shown
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['minipoll_polldetail'] = 'select_key,pages,recursive';
