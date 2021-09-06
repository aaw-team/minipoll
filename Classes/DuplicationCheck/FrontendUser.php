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
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;

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
     * @var FrontendUserRepository
     */
    protected $frontendUserRepository;

    /**
     * @param ParticipationRepository $participationRepository
     */
    public function injectParticipationRepository(ParticipationRepository $participationRepository)
    {
        $this->participationRepository = $participationRepository;
    }

    /**
     * @param FrontendUserRepository $frontendUserRepository
     */
    public function injectFrontendUserRepository(FrontendUserRepository $frontendUserRepository)
    {
        $this->frontendUserRepository = $frontendUserRepository;
    }

    /**
     * {@inheritDoc}
     * @see DuplicationCheckInterface::isAvailable()
     */
    public function isAvailable(): bool
    {
        return $this->isFrontednUserLoggedIn();
    }

    /**
     * {@inheritDoc}
     * @see DuplicationCheckInterface::isVoted()
     */
    public function isVoted(Poll $poll): bool
    {
        if (!$this->isFrontednUserLoggedIn()) {
            throw new \LogicException('Cannot check poll votes, no frontend user is logged in');
        }

        $query = $this->participationRepository->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('poll', $poll->getUid()),
                $query->equals('frontend_user', $this->getFrontendUserUid())
            )
        );
        return $query->execute()->count() > 0;
    }

    /**
     * {@inheritDoc}
     * @see DuplicationCheckInterface::registerVote()
     */
    public function registerVote(Poll $poll, Participation $participation): void
    {
        if (!$this->isFrontednUserLoggedIn()) {
            throw new \LogicException('Cannot register vote, no frontend user is logged in');
        }
        if (!$participation->getFrontendUser() || $participation->getFrontendUser()->getUid() !== $this->getFrontendUserUid()) {
            $frontendUser = $this->frontendUserRepository->findByIdentifier($this->getFrontendUserUid());
            $participation->setFrontendUser($frontendUser);
        }
    }

    /**
     * @return bool
     */
    protected function isFrontednUserLoggedIn(): bool
    {
        return GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('frontend.user', 'isLoggedIn');
    }

    /**
     * @return int|null
     */
    protected function getFrontendUserUid(): ?int
    {
        if (!$this->isFrontednUserLoggedIn()) {
            return null;
        }
        return (int)GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('frontend.user', 'id');
    }
}
