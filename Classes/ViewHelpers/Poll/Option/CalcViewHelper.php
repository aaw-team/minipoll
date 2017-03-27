<?php
namespace AawTeam\Minipoll\ViewHelpers\Poll\Option;

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

use AawTeam\Minipoll\Domain\Model\PollOption;
use AawTeam\Minipoll\ViewModel\PollOptionViewModel;

/**
 * CalcViewHelper
 */
class CalcViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * Disable output escaping
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @param object $pollOption
     * @param string $as
     * @param int $roundPercent
     * @return string
     */
    public function render($pollOption, $as = 'optionCalc', $roundPercent = null)
    {
        if ($pollOption instanceof PollOptionViewModel) {
            $pollOption = $pollOption->getPollOption();
        } elseif (!($pollOption instanceof PollOption)) {
            throw new \InvalidArgumentException('$pollOption must be ' . PollOptionViewModel::class . ' or ' . PollOption::class);
        }
        // Calculate total answer count
        $totalAnswers = 0;
        foreach ($pollOption->getPoll()->getOptions() as $options) {
            $totalAnswers += $options->getAnswers()->count();
        }
        // Get answer count of the current option
        $answers = $pollOption->getAnswers()->count();
        $percent = 0;
        if ($totalAnswers > 0 && $answers > 0) {
            $percent = 100 / $totalAnswers * $answers;
            if ($roundPercent !== null && $roundPercent >= 0) {
                $percent = \round($percent, $roundPercent);
            }
        }
        $calc = [
            'answers' => $answers,
            'totalAnswers' => $totalAnswers,
            'percent' => $percent
        ];

        // Assign calculation info to variable 'optionCalc'
        $previousAsValue = null;
        if ($this->templateVariableContainer->exists($as)) {
            $previousAsValue = $this->templateVariableContainer->get($as);
            $this->templateVariableContainer->remove($as);
        }
        $this->templateVariableContainer->add($as, $calc);
        $return = $this->renderChildren();
        $this->templateVariableContainer->remove($as);

        if ($previousAsValue !== null) {
            $this->templateVariableContainer->add($as, $previousAsValue);
        }

        return $return;
    }
}
