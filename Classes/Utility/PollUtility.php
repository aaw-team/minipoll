<?php
declare(strict_types=1);
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
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * PollUtility
 */
class PollUtility
{
    /**
     * @var DuplicationCheckFactory
     */
    protected $duplicationCheckFactory;

    /**
     * @param DuplicationCheckFactory $duplicationCheckFactory
     */
    public function injectDuplicationCheckFactory(DuplicationCheckFactory $duplicationCheckFactory)
    {
        $this->duplicationCheckFactory = $duplicationCheckFactory;
    }

    /**
     * @param Poll $poll
     * @return bool
     */
    public function canDisplayResultsInPoll(Poll $poll): bool
    {
        if ($poll->getDisplayResults() === Poll::DISPLAY_RESULTS_ALWAYS) {
            return true;
        } elseif ($poll->getDisplayResults() === Poll::DISPLAY_RESULTS_NEVER) {
            return false;
        }

        // Try to find out whether one has voted already
        return $this->getDuplicationCheck($poll)->isVoted($poll);
    }

    /**
     * @param Poll $poll
     * @return bool
     */
    public function canVoteInPoll(Poll $poll): bool
    {
        // Early return when poll is closed
        if ($poll->getIsClosed()) {
            return false;
        }

        return !$this->getDuplicationCheck($poll)->isVoted($poll);
    }

    /**
     * @param Poll $poll
     * @param Participation $participation
     */
    public function disableVoteInPoll(Poll $poll, Participation $participation): void
    {
        $this->getDuplicationCheck($poll)->registerVote($poll, $participation);
    }

    /**
     * @param Poll $poll
     * @return \AawTeam\Minipoll\DuplicationCheck\DuplicationCheckInterface
     */
    protected function getDuplicationCheck(Poll $poll)
    {
        return $this->duplicationCheckFactory->getDuplicationCheck($poll);
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
        if (!array_key_exists('captcha', $settings)) {
            return false;
        }

        $alias = trim($settings['captcha']);
        if (empty($alias)) {
            return false;
        } elseif ($alias == 1) {
            return null;
        }
        return $alias;
    }

    /**
     * @param Poll $poll
     */
    public function addPollToPageCache(Poll $poll): void
    {
        if ($typoScriptFrontendController = $this->getTypoScriptFrontendController()) {
            $typoScriptFrontendController->addCacheTags([$this->poll2PageCacheTag($poll)]);
        }
    }

    /**
     * @param Poll $poll
     */
    public function clearPageCacheByPoll(Poll $poll): void
    {
        GeneralUtility::makeInstance(CacheManager::class)->getCache('pages')->flushByTag(
            $this->poll2PageCacheTag($poll)
        );
    }

    /**
     * @param Poll $poll
     * @return array
     */
    public function pollToArray(Poll $poll): array
    {
        $pollArray = [
            'uid' => $poll->getUid(),
            'title' => $poll->getTitle(),
            'description' => $poll->getDescription(),
            'participations' => [],
        ];
        foreach ($poll->getParticipations() as $participation) {
            $pollArray['participations'][$participation->getUid()] = [
                'uid' => $participation->getUid(),
                'answers' => [],
            ];

            foreach ($participation->getAnswers() as $answer) {
                $pollArray['participations'][$participation->getUid()]['answers'][$answer->getUid()] = [
                    'uid' => $answer->getUid(),
                    'pollOption' => [
                        'uid' => $answer->getPollOption()->getUid(),
                        'title' => $answer->getPollOption()->getTitle(),
                    ],
                    'value' => $answer->getValue(),
                ];
            }
        }

        return $pollArray;
    }

    /**
     * @param Poll $poll
     * @return string
     */
    protected function poll2PageCacheTag(Poll $poll): string
    {
        return 'tx_minipoll_' . $poll->getUid();
    }

    /**
     * @return TypoScriptFrontendController|null
     */
    protected function getTypoScriptFrontendController(): ?TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}
