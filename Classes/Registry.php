<?php
namespace AawTeam\Minipoll;

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

/**
 * Registry
 */
final class Registry implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * @var array
     */
    private static $votedPolls = [];

    /**
     * @var array
     */
    private static $displayedPolls = [];

    /**
     * @param int $pollUid
     * @throws \InvalidArgumentException
     * @return void
     */
    public static function addVotedPoll($pollUid)
    {
        if (!\is_int($pollUid) || $pollUid < 1) {
            throw new \InvalidArgumentException('$pollUid must be int greater than zero');
        }
        self::$votedPolls[$pollUid] = $pollUid;
    }

    /**
     * @param int $pollUid
     * @throws \InvalidArgumentException
     * @return boolean
     */
    public static function isVotedPoll($pollUid)
    {
        if (!\is_int($pollUid) || $pollUid < 1) {
            throw new \InvalidArgumentException('$pollUid must be int greater than zero');
        }
        return \array_key_exists($pollUid, self::$votedPolls);
    }

    /**
     * @param int $pollUid
     * @throws \InvalidArgumentException
     * @return void
     */
    public static function addDisplayedPoll($pollUid)
    {
        if (!\is_int($pollUid) || $pollUid < 1) {
            throw new \InvalidArgumentException('$pollUid must be int greater than zero');
        }
        self::$displayedPolls[$pollUid] = $pollUid;
    }

    /**
     * @param int $pollUid
     * @throws \InvalidArgumentException
     * @return boolean
     */
    public static function isDisplayedPoll($pollUid)
    {
        if (!\is_int($pollUid) || $pollUid < 1) {
            throw new \InvalidArgumentException('$pollUid must be int greater than zero');
        }
        return \array_key_exists($pollUid, self::$displayedPolls);
    }
}
