<?php
declare(strict_types=1);
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
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * IfCanVoteViewHelper
 */
class IfCanVoteViewHelper extends AbstractConditionViewHelper
{
    /**
     * Initializes the "then" and "else" arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('poll', Poll::class, 'The poll to evaluate', true);
    }

    /**
     * {@inheritDoc}
     * @see AbstractConditionViewHelper::verdict()
     */
    public static function verdict(array $arguments, RenderingContextInterface $renderingContext)
    {
        /** @var PollUtility $pollUtility */
        $pollUtility = GeneralUtility::makeInstance(PollUtility::class);
        return $pollUtility->canVoteInPoll($arguments['poll']);
    }
}
