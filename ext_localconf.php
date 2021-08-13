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

defined ('TYPO3_MODE') or die ('Access denied.');

// Configure plugin
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin('Minipoll', 'Poll', [
    \AawTeam\Minipoll\Controller\PollController::class => 'list,detail,vote,showResult'
], [
    \AawTeam\Minipoll\Controller\PollController::class => 'vote'
]);
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin('Minipoll', 'PollDetail', [
    \AawTeam\Minipoll\Controller\PollController::class => 'detail,vote,showResult'
], [
    \AawTeam\Minipoll\Controller\PollController::class => 'vote'
]);

// Add the plugin to new content element wizard
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
mod.wizards.newContentElement.wizardItems.plugins {
    elements {
        minipoll_form {
            iconIdentifier = content-plugin-minipoll-poll
            title = LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:plugin.title
            description = LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:plugin.description
            tt_content_defValues {
                CType = list
                list_type = minipoll_poll
            }
        }
        minipoll_polldetail {
            iconIdentifier = content-plugin-minipoll-poll
            title = LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:plugin_detail.title
            description = LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:plugin_detail.description
            tt_content_defValues {
                CType = list
                list_type = minipoll_polldetail
            }
        }
    }
}');

// Register icons
/** @var \TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */
$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
$iconRegistry->registerIcon('content-plugin-minipoll-poll', \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class, [
    'source' => 'EXT:minipoll/Resources/Public/Icons/plugin-minipoll-poll.svg'
]);
$iconRegistry->registerIcon('minipoll-poll', \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class, [
    'source' => 'EXT:minipoll/Resources/Public/Icons/poll.svg'
]);
$iconRegistry->registerIcon('minipoll-poll-option', \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class, [
    'source' => 'EXT:minipoll/Resources/Public/Icons/poll-option.svg'
]);

// Register upgrade wizard
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update'][\AawTeam\Minipoll\Update\SplitMinipollPluginsBySwitchableControllerActions::class] = \AawTeam\Minipoll\Update\SplitMinipollPluginsBySwitchableControllerActions::class;

// Include autoloader fot the third-party code
if (!\class_exists('ParagonIE\\ConstantTime\\Encoding')) {
    require_once 'phar://' . \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('EXT:minipoll/Resources/Private/PHP/constant_time_encoding.phar/vendor/autoload.php');
}
