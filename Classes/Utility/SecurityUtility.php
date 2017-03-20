<?php
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

use AawTeam\Minipoll\Exception\InvalidHmacException;
use ParagonIE\ConstantTime\Binary;
use TYPO3\CMS\Core\Crypto\Random;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * SecurityUtility
 */
final class SecurityUtility
{
    const HMAC_ALGO = 'sha256';
    const HMAC_LENGTH = 64;

    /**
     * @param string $input
     * @param bool $rawOutput
     * @throws \InvalidArgumentException
     * @return string
     */
    public static function appendHmacToString($input)
    {
        if (!\is_string($input) || Binary::safeStrlen($input) < 1) {
            throw new \InvalidArgumentException('$input must be not empty string');
        }
        return $input . self::hmacWithTYPO3EncryptionKey($input);
    }

    /**
     * @param string $input
     * @throws \InvalidArgumentException
     * @throws InvalidHmacException
     * @return string
     */
    public static function validateAndStripHmac($input)
    {
        if (!\is_string($input) || Binary::safeStrlen($input) < (1 + self::HMAC_LENGTH)) {
            throw new \InvalidArgumentException('$input must be not empty string');
        }
        $userData = Binary::safeSubstr($input, 0, Binary::safeStrlen($input) - self::HMAC_LENGTH);
        $hmac = Binary::safeSubstr($input, self::HMAC_LENGTH * -1);
        if (!self::hashEquals(self::hmacWithTYPO3EncryptionKey($userData), $hmac)) {
            throw new InvalidHmacException('Invalid HMAC', 1489758944);
        }
        return $userData;
    }

    /**
     * @param string $input
     * @throws \InvalidArgumentException
     * @return string
     */
    public static function hmacWithTYPO3EncryptionKey($input)
    {
        return self::hmac($input, $GLOBALS['SYS']['encryptionKey']);
    }

    /**
     * @param string $input
     * @param string $key
     * @return string
     */
    public static function hmac($input, $key)
    {
        if (!\is_string($input) || Binary::safeStrlen($input) < 1) {
            throw new \InvalidArgumentException('$input must be not empty string');
        }
        return \hash_hmac(self::HMAC_ALGO, $input, $key, false);
    }

    /**
     * Timing attack safe string comparison
     *
     * @param string $knownString
     * @param string $userString
     * @throws \InvalidArgumentException
     * @return boolean
     */
    public static function hashEquals($knownString, $userString)
    {
        if (\function_exists('hash_equals')) {
            return \hash_equals($knownString, $userString);
        }

        if (!\is_string($knownString)) {
            throw new \InvalidArgumentException('$knownString must be string');
        } elseif (!\is_string($userString)) {
            throw new \InvalidArgumentException('$userString must be string');
        } elseif (Binary::safeStrlen($knownString) != Binary::safeStrlen($userString)) {
            return false;
        }

        $randomBlind = self::generateRandomBytes(16);
        return self::hmac($knownString, $randomBlind) === self::hmac($userString, $randomBlind);
    }

    /**
     * @param int $length
     * @throws \InvalidArgumentException
     * @return string
     */
    public static function generateRandomBytes($length)
    {
        if (!\is_int($length) || $length < 1) {
            throw new \InvalidArgumentException('$input must be integer greater than zero');
        }
        if (\version_compare(TYPO3_version, '8', '<')) {
            if (\version_compare(PHP_VERSION, '7', '<')) {
                return GeneralUtility::generateRandomBytes($length);
            }
            return \random_bytes($length);
        }
        return GeneralUtility::makeInstance(Random::class)->generateRandomBytes($length);
    }
}
