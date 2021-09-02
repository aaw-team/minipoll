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

use AawTeam\Minipoll\Domain\Model\Poll;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * DuplicationCheck factory
 */
class Factory
{
    /**
     * @var array
     */
    protected $aliasMap = [
        Poll::DUPLICATION_CHECK_IP => Ip::class,
        Poll::DUPLICATION_CHECK_COOKIE => Cookie::class,
        Poll::DUPLICATION_CHECK_FEUSER => FrontendUser::class,
        Poll::DUPLICATION_CHECK_NONE => Dummy::class
    ];

    /**
     * @param Poll $poll
     * @return DuplicationCheckInterface
     */
    public function getDuplicationCheck(Poll $poll)
    {
        $duplicationCheckName = \trim($poll->getDuplicationCheck());
        if (empty($duplicationCheckName)) {
            $className = Dummy::class;
        } elseif (\array_key_exists($duplicationCheckName, $this->aliasMap)) {
            $className = $this->aliasMap[$duplicationCheckName];
        } else {
            $className = $duplicationCheckName;
        }

        $instance = GeneralUtility::makeInstance($className);
        if (!($instance instanceof DuplicationCheckInterface)) {
            throw new \RuntimeException('Class "' . \htmlspecialchars($className) . '" must implement ' . DuplicationCheckInterface::class);
        }
        return $instance;
    }
}
