<?php
declare(strict_types=1);
namespace AawTeam\Minipoll\DuplicationCheck;

/*
 * Copyright 2021 Agentur am Wasser | Maeder & Partner AG
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
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * FrontendSession
 */
class FrontendSession implements DuplicationCheckInterface
{
    /**
     * {@inheritDoc}
     * @see DuplicationCheckInterface::isAvailable()
     */
    public function isAvailable(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     * @see DuplicationCheckInterface::isVoted()
     */
    public function isVoted(Poll $poll): bool
    {
        $sessionDataString = $this->getFrontendUserAuthentication()->getSessionData('minipoll');
        if ($sessionDataString === null) {
            return false;
        }
        $sessionDataArray = json_decode($sessionDataString, true);
        if (!is_array($sessionDataArray)) {
            return false;
        }
        if (!is_array($sessionDataArray['votedPolls'])) {
            return false;
        }
        return in_array($poll->getUid(), $sessionDataArray['votedPolls']);
    }

    /**
     * {@inheritDoc}
     * @see DuplicationCheckInterface::registerVote()
     */
    public function registerVote(Poll $poll, Participation $participation): void
    {
        $sessionDataString = $this->getFrontendUserAuthentication()->getSessionData('minipoll');
        if (!is_string($sessionDataString)) {
            $sessionDataArray = [
                'votedPolls' => [],
            ];
        } else {
            $sessionDataArray = json_decode($sessionDataString, true);
            if (!is_array($sessionDataArray)) {
                $sessionDataArray = [
                    'votedPolls' => [],
                ];
            }
            if (!is_array($sessionDataArray['votedPolls'])) {
                $sessionDataArray['votedPolls'] = [];
            }
        }
        $sessionDataArray['votedPolls'][$poll->getUid()] = $poll->getUid();
        $sessionDataString = json_encode($sessionDataArray);
        $this->getFrontendUserAuthentication()->setAndSaveSessionData('minipoll', $sessionDataString);
    }

    /**
     * @return FrontendUserAuthentication
     */
    protected function getFrontendUserAuthentication(): FrontendUserAuthentication
    {
        return $this->getTypoScriptFrontendController()->fe_user;
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}
