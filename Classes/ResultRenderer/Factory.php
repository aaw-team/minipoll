<?php
namespace AawTeam\Minipoll\ResultRenderer;

use TYPO3\CMS\Core\Utility\GeneralUtility;

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
 * ResultRenderer Factory
 */
class Factory
{
    /**
     * @var array
     */
    protected static $builtInRenderers = [
        'css' => \AawTeam\Minipoll\ResultRenderer\Css::class,
        'svgpiechart' => \AawTeam\Minipoll\ResultRenderer\Svgpiechart::class
    ];

    /**
     * @param string $alias
     * @throws \InvalidArgumentException
     * @throws \AawTeam\Minipoll\Exception\NoResultRendererFoundException
     * @throws \RuntimeException
     * @return \AawTeam\Minipoll\ResultRenderer\ResultRendererInterface
     */
    public static function getRenderer($alias)
    {
        if (!\is_string($alias) || empty($alias)) {
            throw new \InvalidArgumentException('$alias must be not empty string');
        }
        $registeredRenderers = static::getRegisteredRenderers();
        if (!\array_key_exists($alias, $registeredRenderers)) {
            if (!\in_array($alias, $registeredRenderers)) {
                throw new \AawTeam\Minipoll\Exception\NoResultRendererFoundException();
            }
            $alias = \array_search($alias, $registeredRenderers);
        }

        $resultRenderer = GeneralUtility::makeInstance($registeredRenderers[$alias]);
        if (!($resultRenderer instanceof ResultRendererInterface)) {
            throw new \RuntimeException('resultRenderer "' . get_class($registeredRenderers[$alias]) . '" must be ' . ResultRendererInterface::class);
        }
        return $resultRenderer;
    }

    /**
     * @return array
     */
    public static function getRegisteredRenderers()
    {
        $renderers = static::$builtInRenderers;
        if (\is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['minipoll']['resultRenderers'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['minipoll']['resultRenderers'] as $alias => $renderer) {
                $renderers[$alias] = $renderer;
            }
        }
        return $renderers;
    }
}
