<?php
declare(strict_types=1);
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
     * @throws \InvalidArgumentException
     * @return string
     */
    public static function appendHmacToString(string $input): string
    {
        if (Binary::safeStrlen($input) < 1) {
            throw new \InvalidArgumentException('$input must not be empty');
        }
        return $input . self::hmacWithTYPO3EncryptionKey($input);
    }

    /**
     * @param string $input
     * @throws \InvalidArgumentException
     * @throws InvalidHmacException
     * @return string
     */
    public static function validateAndStripHmac(string $input): string
    {
        if (Binary::safeStrlen($input) < (1 + self::HMAC_LENGTH)) {
            throw new \InvalidArgumentException('$input must not be empty');
        }
        $userData = Binary::safeSubstr($input, 0, Binary::safeStrlen($input) - self::HMAC_LENGTH);
        $hmac = Binary::safeSubstr($input, self::HMAC_LENGTH * -1);
        if (!hash_equals(self::hmacWithTYPO3EncryptionKey($userData), $hmac)) {
            throw new InvalidHmacException('Invalid HMAC', 1489758944);
        }
        return $userData;
    }

    /**
     * @param string $input
     * @return string
     */
    public static function hmacWithTYPO3EncryptionKey(string $input): string
    {
        return self::hmac($input, $GLOBALS['SYS']['encryptionKey']);
    }

    /**
     * @param string $input
     * @param string $key
     * @return string
     */
    public static function hmac(string $input, string $key): string
    {
        if (Binary::safeStrlen($input) < 1) {
            throw new \InvalidArgumentException('$input must not be empty');
        }
        return hash_hmac(self::HMAC_ALGO, $input, $key);
    }

    /**
     * @param int $length
     * @return string
     */
    public static function generateRandomBytes(int $length): string
    {
        return GeneralUtility::makeInstance(Random::class)->generateRandomBytes($length);
    }
}
