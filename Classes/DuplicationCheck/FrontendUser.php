<?php
namespace AawTeam\Minipoll\DuplicationCheck;

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
use AawTeam\Minipoll\Domain\Repository\ParticipationRepository;

/**
 * FrontendUser duplication check
 */
class FrontendUser implements DuplicationCheckInterface
{
    /**
     * @var ParticipationRepository
     */
    protected $participationRepository;

    /**
     * @param ParticipationRepository $participationRepository
     */
    public function injectParticipationRepository(ParticipationRepository $participationRepository)
    {
        $this->participationRepository = $participationRepository;
    }

    /**
     * @param Poll $poll
     * @return bool
     */
    public function canVote(Poll $poll)
    {
        if (!$this->isFrontednUserLoggedIn()) {
            return false;
        }
        return $this->participationRepository->countByPollAndFrontendUser($poll, $this->getFrontendUser()->user['uid']) == 0;
    }

    /**
     * @param Poll $poll
     * @param Participation $participation
     * @return bool
     */
    public function disableVote(Poll $poll, Participation $participation)
    {
        return true;
    }

    /**
     * @param Poll $poll
     * @return bool
     */
    public function canDisplayResults(Poll $poll)
    {
        if (!$this->isFrontednUserLoggedIn()) {
            return false;
        }
        return !$this->canVote($poll);
    }

    /**
     * @return boolean
     */
    protected function isFrontednUserLoggedIn()
    {
        return \is_array($this->getFrontendUser()->user && $this->getFrontendUser()->user['uid']);
    }

    /**
     * @return \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication
     */
    protected function getFrontendUser()
    {
        return $this->getTyposcriptFrontendController()->fe_user;
    }

    /**
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected function getTyposcriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}
