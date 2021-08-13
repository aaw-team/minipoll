<?php
namespace AawTeam\Minipoll\ViewHelpers\Poll;

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
use AawTeam\Minipoll\ResultRenderer\Factory as ResultRendererFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ResultsViewHelper
 */
class ResultsViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * {@inheritDoc}
     * @see \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper::initializeArguments()
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('poll', Poll::class, 'The poll object', true);
        $this->registerArgument('as', 'string', 'The variable name that holds the results from the resultRenderer', false, 'results');
        $this->registerArgument('rendererNameAs', 'string', 'The variable name that holds the name of the resultRenderer', false, 'resultRendererName');
    }

    /**
     * @return string
     */
    public function render()
    {
        /** @var Poll $poll */
        $poll = $this->arguments['poll'];

        $settings = $this->templateVariableContainer->get('settings');
        if (!\is_array($settings)
            || !\array_key_exists('resultRenderer', $settings)
            || !\is_array($settings['resultRenderer'])
        ) {
            return 'Error: no resultRenderer configuration found';
        }

        $resultRendererConfiguration = $settings['resultRenderer'];

        $usedRenderers = [];
        $content = '';

        foreach (GeneralUtility::trimExplode(',', $resultRendererConfiguration['show'], true) as $rendererAlias) {
            if (\in_array($rendererAlias, $usedRenderers)) {
                continue;
            }
            try {
                $resultRenderer = ResultRendererFactory::getRenderer($rendererAlias);
            } catch (\AawTeam\Minipoll\Exception\NoResultRendererFoundException $e) {
                // Silently fail
                continue;
            }

            // Load global configuration
            $configuration = \is_array($resultRendererConfiguration['global'])
                                 ? $resultRendererConfiguration['global']
                                 : [];
            // Merge current renderer configuration onto global configuration
            if (\array_key_exists($rendererAlias, $resultRendererConfiguration) && \is_array($resultRendererConfiguration[$rendererAlias])) {
                $configuration = \array_merge($configuration, $resultRendererConfiguration[$rendererAlias]);
            }

            // Setup renderer
            $resultRenderer->setup($poll, $configuration);

            // The resultRendererName is the last part of the class name (everything behind the last '\')
            $resultRendererName = \substr(\get_class($resultRenderer), \strrpos(\get_class($resultRenderer), '\\') + 1);
            $this->templateVariableContainer->add($this->arguments['rendererNameAs'], $resultRendererName);
            $this->templateVariableContainer->add($this->arguments['as'], $resultRenderer->getRenderedResults());

            // Render children
            $content .= $this->renderChildren();

            // Clean up
            $this->templateVariableContainer->remove($this->arguments['rendererNameAs']);
            $this->templateVariableContainer->remove($this->arguments['as']);
            $usedRenderers[] = $rendererAlias;
        }

        return $content;
    }
}
