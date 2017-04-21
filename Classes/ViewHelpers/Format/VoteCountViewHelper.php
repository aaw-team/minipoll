<?php
namespace AawTeam\Minipoll\ViewHelpers\Format;

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
use AawTeam\Minipoll\Domain\Model\PollOption;
use AawTeam\Minipoll\ViewModel\PollOptionViewModel;
use AawTeam\Minipoll\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * VoteCountViewHelper
 */
class VoteCountViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * {@inheritDoc}
     * @see \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper::initializeArguments()
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('poll', Poll::class, 'The poll to work with');
        $this->registerArgument('pollOption', 'object', 'The poll option (view model) to work with');
        $this->registerArgument('value', 'float', 'The value to work with');
        $this->registerArgument('useLabel', 'boolean', 'Whether to display a label', false, true);
        $this->registerArgument('percent', 'boolean', 'Calculate percent value (pollOptions only)');
        $this->registerArgument('round', 'integer', 'Round precision (percentage values only)');
    }

    /**
     * @throws \InvalidArgumentException
     * @return string
     */
    public function render()
    {
        $value = 0.0;
        $label = null;
        if ($this->hasArgument('pollOption')) {
            $pollOption = $this->arguments['pollOption'];
            if ($pollOption instanceof PollOptionViewModel) {
                $pollOption = $pollOption->getPollOption();
            } elseif (!($pollOption instanceof PollOption)) {
                throw new \InvalidArgumentException('$pollOption must be ' . PollOptionViewModel::class . ' or ' . PollOption::class, 1492701623);
            }
            $value = (float) $pollOption->getAnswers()->count();

            if ($this->arguments['percent']) {
                $label = 'text.votepercent';
                if ($value > 0) {
                    $totalAnswers = 0;
                    foreach ($pollOption->getPoll()->getOptions() as $option) {
                        $totalAnswers += $option->getAnswers()->count();
                    }
                    if ($totalAnswers > 0) {
                        $value = 100 / $totalAnswers * $value;
                        $value = $this->round($value);
                    }
                }
            }
        } elseif ($this->hasArgument('poll')) {
            $value = 0.0;
            foreach ($this->arguments['poll']->getOptions() as $option) {
                $value += $option->getAnswers()->count();
            }
        } else {
            if ($this->arguments['value'] > 0) {
                $value = (float) $this->arguments['value'];
            } else {
                $value = (float) $this->renderChildren();
            }
            if ($this->arguments['percent']) {
                $label = 'text.votepercent';
                $value = $this->round($value);
            }
        }

        if ($this->arguments['useLabel']) {
            if ($label === null) {
                $label = $value == 1
                    ? 'text.votecount_s'
                    : 'text.votecount_p';
            }
            $value = LocalizationUtility::translate($label, [$value]);
        }

        return $value;
    }

    /**
     * @param float $value
     * @return float
     */
    protected function round($value)
    {
        if (MathUtility::canBeInterpretedAsInteger($this->arguments['round']) && $this->arguments['round'] >= 0) {
            $value = \round($value, $this->arguments['round']);
        }
        return $value;
    }
}
