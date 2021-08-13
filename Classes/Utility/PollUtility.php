<?php
namespace AawTeam\Minipoll\Utility;

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

use AawTeam\Minipoll\Domain\Model\Participation;
use AawTeam\Minipoll\Domain\Model\Poll;
use AawTeam\Minipoll\DuplicationCheck\Factory as DuplicationCheckFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;


/**
 * PollUtility
 */
class PollUtility
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function injectObjectManager(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param Poll $poll
     * @return boolean
     */
    public function canDisplayResultsInPoll(Poll $poll)
    {
        if ($poll->getDisplayResults() === Poll::DISPLAY_RESULTS_ALWAYS) {
            return true;
        } elseif ($poll->getDisplayResults() === Poll::DISPLAY_RESULTS_NEVER) {
            return false;
        }

        // Try to find out whether one has voted already
        $duplicationCheck = $this->getDuplicationCheck($poll);
        return $duplicationCheck->canDisplayResults($poll);
    }

    /**
     * @param Poll $poll
     * @return boolean
     */
    public function canVoteInPoll(Poll $poll)
    {
        // Early return when poll is closed
        if ($poll->getIsClosed()) {
            return false;
        }

        $duplicationCheck = $this->getDuplicationCheck($poll);
        return $duplicationCheck->canVote($poll);
    }

    /**
     * @param Poll $poll
     * @return boolean
     */
    public function disableVoteInPoll(Poll $poll, Participation $participation)
    {
        $duplicationCheck = $this->getDuplicationCheck($poll);
        return $duplicationCheck->disableVote($poll, $participation);
    }

    /**
     * @param Poll $poll
     * @return \AawTeam\Minipoll\DuplicationCheck\DuplicationCheckInterface
     */
    protected function getDuplicationCheck(Poll $poll)
    {
        /** @var DuplicationCheckFactory $duplicationCheckFactory */
        $duplicationCheckFactory = $this->objectManager->get(DuplicationCheckFactory::class);
        return $duplicationCheckFactory->getDuplicationCheck($poll);
    }

    /**
     * Returns the caotchaProviderAlias registered as 'captcha' in $settings.
     *
     * If the value is empty, false is returned, meaning, the captcha mechanism
     * is disabled.
     *
     * If the value is '1', null is returned, meaning, the default
     * captchaProvider should be used.
     *
     * The output of this method can be used to check, whether a captcha should
     * be used and (if so [eg. returnvalue is not false]) pass it to
     * AawTeam\Minipoll\CaptchaProvider\Factory::getCaptchaProvider().
     *
     * @param array $settings
     * @return string|null|false
     */
    public function getCaptchaProviderAliasFromSettings(array $settings)
    {
        if (!\array_key_exists('captcha', $settings)) {
            return false;
        }

        $alias = \trim($settings['captcha']);
        if (empty($alias)) {
            return false;
        } elseif ($alias == 1) {
            return null;
        }
        return $alias;
    }
}
