<?php
declare(strict_types=1);
namespace AawTeam\Minipoll\Domain\Repository;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;

/**
 * ParticipationRepository
 */
class ParticipationRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    public function initializeObject()
    {
        $querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $this->defaultQuerySettings = $querySettings;
    }

    public function countByPollAndIpAddress(Poll $poll, string $ipAddress): int
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('poll', $poll->getUid()),
                $query->equals('ip', $ipAddress)
            )
        );
        return $query->execute()->count();
    }

    public function countByPollAndFrontendUser(Poll $poll, int $frontendUser): int
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('poll', $poll->getUid()),
                $query->equals('frontend_user', $frontendUser)
            )
        );
        return $query->execute()->count();
    }
}
