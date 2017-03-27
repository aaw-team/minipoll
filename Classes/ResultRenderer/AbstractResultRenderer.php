<?php
namespace AawTeam\Minipoll\ResultRenderer;

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

/**
 * AbstractResultRenderer
 */
abstract class AbstractResultRenderer implements ResultRendererInterface
{
    /**
     * @var \AawTeam\Minipoll\Domain\Model\Poll
     */
    protected $poll;

    /**
     * @var array
     */
    protected $configuration;

    /**
     * {@inheritDoc}
     * @see \AawTeam\Minipoll\ResultRenderer\ResultRendererInterface::setup()
     */
    public function setup(Poll $poll, array $configuration)
    {
        $this->poll = $poll;
        $this->configuration = $configuration;
    }

    /**
     * @return array
     */
    protected function getPollOptionsAsViewModels()
    {
        $orderedOptions = $this->poll->getOptions()->toArray();
        if ($this->configuration['orderBy'] == 'answers') {
            uasort($orderedOptions, function(PollOption $p1, PollOption $p2) {
                $c1 = $p1->getAnswers()->count();
                $c2 = $p2->getAnswers()->count();
                if ($c1 == $c2) {
                    return 0;
                }
                return ($c2 - $c1);
            });
        } elseif ($this->configuration['orderBy'] == 'random') {
            uasort($orderedOptions, function(PollOption $p1, PollOption $p2) {
                return \mt_rand(-1,1);
            });
        }

        if ($this->configuration['reverseOrder'] == 1) {
            $orderedOptions = \array_reverse($orderedOptions);
        }

        $viewModels = [];
        foreach ($orderedOptions as $pollOption) {
            $viewModels[] = PollOptionViewModel::createFromPollOption($pollOption);
        }
        return $viewModels;
    }
}
