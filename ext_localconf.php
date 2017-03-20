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
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin('AawTeam.Minipoll', 'Poll', [
    'Poll' => 'index,list,detail,vote,showResult'
], [
    'Poll' => 'vote'
]);

// Add default typoscript setup
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('
/**
 * Default plugin configuration
 */
plugin.tx_minipoll {
    view {
        layoutRootPaths.0 = EXT:minipoll/Resources/Private/Layouts
        partialRootPaths.0 = EXT:minipoll/Resources/Private/Partials
        templateRootPaths.0 = EXT:minipoll/Resources/Private/Templates
    }
}
plugin.tx_minipoll.persistence.classes {
    ' . \AawTeam\Minipoll\Domain\Model\Poll::class . ' {
        mapping.tableName = tx_minipoll_poll
    }
    ' . \AawTeam\Minipoll\Domain\Model\PollOption::class . ' {
        mapping.tableName = tx_minipoll_poll_option
    }
    ' . \AawTeam\Minipoll\Domain\Model\Participation::class . ' {
        mapping.tableName = tx_minipoll_participation
    }
    ' . \AawTeam\Minipoll\Domain\Model\Answer::class . ' {
        mapping.tableName = tx_minipoll_answer
    }
}');

// Add the plugin to new content element wizard
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
mod.wizards.newContentElement.wizardItems.plugins {
    elements {
        minipoll_form {
            # @TODO: Create/add plugin icon
            iconIdentifier = content-plugin
            title = LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:plugin.title
            description = LLL:EXT:minipoll/Resources/Private/Language/backend.xlf:plugin.description
            tt_content_defValues {
                CType = list
                list_type = minipoll_poll
            }
        }
    }
}');
