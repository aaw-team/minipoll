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
use AawTeam\Minipoll\Exception\InvalidHmacException;
use AawTeam\Minipoll\Utility\SecurityUtility;
use ParagonIE\ConstantTime\Base64;

/**
 * Cookie duplication check
 */
class Cookie implements DuplicationCheckInterface
{
    /**
     * @param Poll $poll
     * @return bool
     */
    public function canVote(Poll $poll)
    {
        $value = $this->getCookieValue();
        return !\in_array($poll->getUid(), $value);
    }

    /**
     * @param Poll $poll
     * @param Participation $participation
     * @return bool
     */
    public function disableVote(Poll $poll, Participation $participation)
    {
        $value = $this->getCookieValue();
        $value[] = $poll->getUid();
        $this->setCookieValue($value);
        return true;
    }

    /**
     * @param Poll $poll
     * @return bool
     */
    public function canDisplayResults(Poll $poll)
    {
        return !$this->canVote($poll);
    }

    /**
     * Stores the array in $value in an authenticated (HMAC) cookie
     *
     * @see Cookie::getCookieValue()
     * @param array $value
     */
    protected function setCookieValue(array $value)
    {
        // Filter array
        // key: int >= 0
        // value: int > 0
        $value = \array_filter($value, function($v, $k) {
            return \is_int($k) && $k >= 0 && \is_int($v) && $v > 0;
        }, ARRAY_FILTER_USE_BOTH);

        if (empty($value)) {
            return;
        }
        // Base64 encode the json as php would urldecode the object in $_COOKIE
        $cookieData = Base64::encode(\json_encode($value));
        $cookieValue = SecurityUtility::appendHmacToString($cookieData);
        \setcookie('tx_minipoll', $cookieValue, $GLOBALS['EXEC_TIME'] + 3600 * 24 * 356);
        // Set the same value to the global
        $_COOKIE['tx_minipoll'] = $cookieValue;
    }

    /**
     * Retrieves an array from an authenticated cookie.
     *
     * @see Cookie::setCookieValue()
     * @return array
     */
    protected function getCookieValue()
    {
        if (!\array_key_exists('tx_minipoll', $_COOKIE)) {
            return [];
        }
        $rawCookieValue = (string) $_COOKIE['tx_minipoll'];

        $cookieValue = null;
        try {
            $cookieValue = SecurityUtility::validateAndStripHmac($rawCookieValue);
        } catch (InvalidHmacException $e) {
            return [];
        }
        $value = @\json_decode(Base64::decode($cookieValue), true);
        return (\is_array($value))
            ? $value
            : [];
    }
}
