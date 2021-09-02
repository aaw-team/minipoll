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

/**
 * LocalizationUtility
 */
class LocalizationUtility
{
    /**
     * @param string $key
     * @param array $arguments
     * @return string
     */
    public static function translate(string $key, array $arguments = null): string
    {
        $translated = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key, 'minipoll', $arguments);
        if ($translated === null) {
            $translated = $key;
        }
        return $translated;
    }
}
