<?php
declare(strict_types=1);
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

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
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
        //'captcha' => ExtensionCaptcha::class,
        'sr_freecap' => ExtensionSrfreecap::class
    ];

    /**
     * @var array
     */
    protected static $registeredProviders;

    /**
     * Returns the captchaProvider instance described by $alias. If $alias is
     * null, the default captchaProvider alias will be tried (defined in
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
    public static function getCaptchaProvider(string $alias = null): CaptchaProviderInterface
    {
        if ($alias !== null && empty($alias)) {
            throw new \InvalidArgumentException('$alias must be not empty string or null', 1490963464);
        }

        $captchaProviders = static::getRegisteredCaptchaProviders();

        // Handle calls with $alias specified
        if ($alias !== null) {
            if (array_key_exists($alias, $captchaProviders)) {
                return static::loadCaptchaProviderInternal($captchaProviders[$alias]);
            }
            throw new \AawTeam\Minipoll\Exception\NoCaptchaProviderFoundException('No CaptchaProvider found for alias "' . htmlspecialchars($alias) . '"');
        }

        // Try to load default alias
        if (($defaultAlias = static::getDefaultProviderAlias()) && array_key_exists($defaultAlias, $captchaProviders)) {
            try {
                return static::loadCaptchaProviderInternal($captchaProviders[$defaultAlias]);
            } catch (\AawTeam\Minipoll\Exception\CaptchaProviderException $e) {
                // Remove unavailable alias
                unset($captchaProviders[$defaultAlias]);
            }
        }

        // Loop all registered captchaProviders (LIFO) to find an active one
        foreach (array_reverse($captchaProviders) as $className) {
            try {
                return static::loadCaptchaProviderInternal($className);
            } catch (\AawTeam\Minipoll\Exception\CaptchaProviderException $e) {
                // Silently fail
            }
        }

        // No available captchaProvider found
        throw new \AawTeam\Minipoll\Exception\NoCaptchaProviderFoundException();
    }

    /**
     * @param string $className
     * @throws \AawTeam\Minipoll\Exception\InvalidCaptchaProviderRegisteredException
     * @throws \AawTeam\Minipoll\Exception\CaptchaProviderException
     * @return CaptchaProviderInterface
     */
    protected static function loadCaptchaProviderInternal(string $className): CaptchaProviderInterface
    {
        /** @var CaptchaProviderInterface $instance */
        $instance = GeneralUtility::makeInstance($className);
        if (!($instance instanceof CaptchaProviderInterface)) {
            throw new \AawTeam\Minipoll\Exception\InvalidCaptchaProviderRegisteredException('Registered CaptchaProvider " ' . \htmlspecialchars($className) . '" must implement ' . CaptchaProviderInterface::class, 1490950349);
        } elseif (!$instance->isAvailable()) {
            throw new \AawTeam\Minipoll\Exception\CaptchaProviderException('CaptchaProvider " ' . \htmlspecialchars($className) . '" is not available', 1490962835);
        }
        return $instance;
    }

    /**
     * @return bool
     */
    public static function hasAvailableCaptchaProvider(): bool
    {
        foreach (static::getRegisteredCaptchaProviders() as $className) {
            try {
                static::loadCaptchaProviderInternal($className);
                return true;
            } catch (\AawTeam\Minipoll\Exception\CaptchaProviderException $e) {
                // Silently fail
            }
        }
        return false;
    }

    /**
     * @throws \AawTeam\Minipoll\Exception\InvalidCaptchaProviderRegisteredException
     * @return array
     */
    public static function getRegisteredCaptchaProviders(): array
    {
        if (static::$registeredProviders === null) {
            $captchaProviders = static::$builtInProviders;
            if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['minipoll']['captchaProviders'])) {
                foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['minipoll']['captchaProviders'] as $alias => $provider) {
                    $captchaProviders[$alias] = $provider;
                }
            }
            foreach ($captchaProviders as $alias => $captchaProvider) {
                if (!class_exists($captchaProvider)) {
                    throw new \AawTeam\Minipoll\Exception\InvalidCaptchaProviderRegisteredException('Registered CaptchaProvider " ' . htmlspecialchars($captchaProvider) . '" does not exist', 1490950342);
                } elseif (!in_array(CaptchaProviderInterface::class, class_implements($captchaProvider))) {
                    throw new \AawTeam\Minipoll\Exception\InvalidCaptchaProviderRegisteredException('Registered CaptchaProvider " ' . htmlspecialchars($captchaProvider) . '" must implement ' . CaptchaProviderInterface::class, 1490950349);
                }
            }
            static::$registeredProviders = $captchaProviders;
        }
        return static::$registeredProviders;
    }

    /**
     * @return string|null
     */
    protected static function getDefaultProviderAlias(): ?string
    {
        $defaultCaptchaProvider = trim((string)GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('minipoll', 'defaultCaptchaProvider'));
        if ($defaultCaptchaProvider === ''){
            return null;
        }
        return $defaultCaptchaProvider;
    }
}
