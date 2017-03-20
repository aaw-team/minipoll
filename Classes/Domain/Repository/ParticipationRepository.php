<?php
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
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;

/**
 * ParticipationRepository
 */
class ParticipationRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    public function initializeObject()
    {
        $querySettings = $this->objectManager->get(QuerySettingsInterface::class);
        $querySettings->setRespectStoragePage(false);
        $this->defaultQuerySettings = $querySettings;
    }

    public function countByPollAndIpAddress(Poll $poll, $ipAddress)
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

    public function countByPollAndFrontendUser(Poll $poll, $frontendUser)
    {
        if (!MathUtility::canBeInterpretedAsInteger($frontendUser)) {
            throw new \InvalidArgumentException('$frontendUser must be integer');
        }

        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('poll', $poll->getUid()),
                $query->equals('frontend_user', (int) $frontendUser)
            )
        );
        return $query->execute()->count();
    }
}
