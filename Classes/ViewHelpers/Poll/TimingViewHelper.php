<?php
namespace AawTeam\Minipoll\ViewHelpers\Poll;

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

/**
 * TimingViewHelper
 */
class TimingViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * @param \AawTeam\Minipoll\Domain\Model\Poll $poll
     * @return string
     */
    public function render(Poll $poll)
    {
        switch ($poll->getStatus()) {
            case Poll::STATUS_CLOSED :
                $return = 'Closed';
                break;
            case Poll::STATUS_OPEN :
                $return = 'Running';
                break;
            case Poll::STATUS_BYDATE :
                $return = $this->renderTimingByDate($poll);
                break;
        }
        return $return;
    }

    /**
     * @param \AawTeam\Minipoll\Domain\Model\Poll $poll
     * @return string
     */
    protected function renderTimingByDate(Poll $poll)
    {
        // Will start in ...
        // Will end in ...
        // Ended since ...
        $return = '';
        $start = $poll->getOpenDatetime();
        if ($start && $start->getTimestamp() > $GLOBALS['EXEC_TIME']) {
            $return = 'Will start on ' . $start->format('c');
        } else {
            if($end = $poll->getCloseDatetime()) {
                if ($end->getTimestamp() > $GLOBALS['EXEC_TIME']) {
                    if ($start) {
                        $return = 'Running since ' . $start->format('c') . ' and will end on ';
                    } else {
                        $return = 'Will end on ';
                    }
                    $return .= $end->format('c');
                } else {
                    $return = 'Ended on ' . $end->format('c');
                }
            }
        }
        return $return;
    }
}
