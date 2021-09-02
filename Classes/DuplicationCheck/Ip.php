<?php
declare(strict_types=1);
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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Ip duplication check
 */
class Ip implements DuplicationCheckInterface
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
    public function canVote(Poll $poll): bool
    {
        return $this->participationRepository->countByPollAndIpAddress($poll, GeneralUtility::getIndpEnv('REMOTE_ADDR')) == 0;
    }

    /**
     * @param Poll $poll
     * @param Participation $participation
     * @return bool
     */
    public function disableVote(Poll $poll, Participation $participation): bool
    {
        return true;
    }

    /**
     * @param Poll $poll
     * @return bool
     */
    public function canDisplayResults(Poll $poll): bool
    {
        return !$this->canVote($poll);
    }
}
