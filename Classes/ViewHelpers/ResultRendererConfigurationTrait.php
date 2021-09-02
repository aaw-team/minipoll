<?php
declare(strict_types=1);
namespace AawTeam\Minipoll\ViewHelpers;

/*
 * Copyright 2021 Agentur am Wasser | Maeder & Partner AG
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
use AawTeam\Minipoll\ResultRenderer\Factory as ResultRendererFactory;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Variables\VariableProviderInterface;

/**
 * ResultRendererConfigurationTrait
 */
trait ResultRendererConfigurationTrait
{
    /**
     * @param VariableProviderInterface $variableProvider
     * @return array
     */
    protected function getResultRendererConfigurationFromSettings(VariableProviderInterface $variableProvider): array
    {
        $resultRendererConfiguration = [];
        $settings = $variableProvider->exists('settings') ? $variableProvider->get('settings') : null;
        if (is_array($settings)
            && array_key_exists('resultRenderer', $settings)
            && is_array($settings['resultRenderer'])
        ) {
            $resultRendererConfiguration = $settings['resultRenderer'];
        }
        return $resultRendererConfiguration;
    }

    /**
     * @param Poll $poll
     * @param array $resultRendererConfiguration
     * @return array
     */
    protected function getAllResultRenderers(Poll $poll, array $resultRendererConfiguration): array
    {
        $resultRenderers = [];
        foreach (GeneralUtility::trimExplode(',', $resultRendererConfiguration['show'], true) as $rendererAlias) {
            if (array_key_exists($rendererAlias, $resultRenderers)) {
                continue;
            }
            try {
                $resultRenderer = ResultRendererFactory::getRenderer($rendererAlias);
            } catch (\AawTeam\Minipoll\Exception\NoResultRendererFoundException $e) {
                // Silently fail
                continue;
            }

            // Load global configuration
            $configuration = is_array($resultRendererConfiguration['global'])
                ? $resultRendererConfiguration['global']
                : [];

            // Merge current renderer configuration into global configuration
            if (is_array($resultRendererConfiguration[$rendererAlias])) {
                ArrayUtility::mergeRecursiveWithOverrule($configuration, $resultRendererConfiguration[$rendererAlias]);
            }

            // Setup renderer
            $resultRenderer->setup($poll, $configuration);

            $resultRenderers[$rendererAlias] = $resultRenderer;
        }
        return $resultRenderers;
    }
}
