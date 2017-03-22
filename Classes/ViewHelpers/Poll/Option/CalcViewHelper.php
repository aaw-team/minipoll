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

/**
 * CalcViewHelper
 */
class CalcViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * @param \AawTeam\Minipoll\Domain\Model\PollOption $pollOption
     * @param string $as
     * @param int $roundPercent
     * @return string
     */
    public function render(PollOption $pollOption, $as = 'optionCalc', $roundPercent = null)
    {
        $answers = \count($pollOption->getAnswers());
        $participations = \count($pollOption->getPoll()->getParticipations());
        $percent = 0;
        if ($participations > 0 && $answers > 0) {
            $percent = 100 / $participations * $answers;
            if ($roundPercent !== null && $roundPercent >= 0) {
                $percent = \round($percent, $roundPercent);
            }
        }
        $calc = [
            'answers' => $answers,
            'participations' => $participations,
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
