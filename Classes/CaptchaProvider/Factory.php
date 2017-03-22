<?php
namespace AawTeam\Minipoll\CaptchaProvider;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * CaptchaProvider Factory
 */
class Factory
{
    /**
     * @var array
     */
    protected static $builtInProviders = [
        'captcha' => \AawTeam\Minipoll\CaptchaProvider\ExtensionCaptcha::class,
        'sr_freecap' => \AawTeam\Minipoll\CaptchaProvider\ExtensionSrfreecap::class
    ];

    /**
     * Returns the captchaProvider instance described by $alias. If $alias is
     * null, the default captchaProvider alias will be used (defined in
     * extConf).
     * When no matching alias has been found, the registered captchaProviders
     * will be searched in reverse order for a working instance.
     * In case no captchaProvider is available, a
     * \AawTeam\Minipoll\Exception\NoCaptchaProviderFoundException is thrown.
     *
     * @throws \AawTeam\Minipoll\Exception\NoCaptchaProviderFoundException
     * @param string $alias
     * @return CaptchaProviderInterface
     */
    public static function getCaptchaProvider($alias = null)
    {
        $captchaProviders = static::getRegisteredCaptchaProviders();
        if ($alias === null) {
            $alias = static::getDefaultProviderAlias();
        }
        if (\array_key_exists($alias, $captchaProviders)) {
            return GeneralUtility::makeInstance($captchaProviders[$alias]);
        }
        foreach (\array_reverse($captchaProviders) as $alias => $className) {
            /** @var CaptchaProviderInterface $instance */
            $instance = GeneralUtility::makeInstance($className);
            if ($instance->isAvailable()) {
                return $instance;
            }
        }
        throw new \AawTeam\Minipoll\Exception\NoCaptchaProviderFoundException();
    }

    /**
     * @return array
     */
    public static function getRegisteredCaptchaProviders()
    {
        $captchaProviders = static::$builtInProviders;
        if (\is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['minipoll']['captchaProviders'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['minipoll']['captchaProviders'] as $alias => $provider) {
                $captchaProviders[$alias] = $provider;
            }
        }
        return $captchaProviders;
    }

    /**
     * @return string
     */
    protected static function getDefaultProviderAlias()
    {
        if (\version_compare(PHP_VERSION, '7', '<')) {
            $extConf = @\unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['minipoll']);
        } else {
            $extConf = @\unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['minipoll'], ['allowed_classes' => false]);
        }
        if (\is_array($extConf) && $extConf['defaultCaptchaProvider']) {
            return $extConf['defaultCaptchaProvider'];
        }
        return '';
    }
}
