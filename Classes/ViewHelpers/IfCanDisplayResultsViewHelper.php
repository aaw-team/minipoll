<?php
namespace AawTeam\Minipoll\ViewHelpers;

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

use AawTeam\Minipoll\Domain\Model\Poll;
use AawTeam\Minipoll\Utility\PollUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * IfCanDisplayResultsViewHelper
 */
class IfCanDisplayResultsViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper
{
    /**
     * Initializes the "then" and "else" arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('poll', Poll::class, 'The poll to evaluate', true);
    }

    /**
     * @param array|null $arguments
     * @throws \InvalidArgumentException
     * @return boolean
     */
    protected static function evaluateCondition($arguments = null)
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        /** @var PollUtility $pollUtility */
        $pollUtility = $objectManager->get(PollUtility::class);

        return $pollUtility->canDisplayResultsInPoll($arguments['poll']);
    }
}
