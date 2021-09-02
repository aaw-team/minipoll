<?php
declare(strict_types=1);
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
use AawTeam\Minipoll\ViewHelpers\ResultRendererConfigurationTrait;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ResultsViewHelper
 */
class ResultsViewHelper extends AbstractViewHelper
{
    use ResultRendererConfigurationTrait;

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
    }

    /**
     * @return string
     */
    public function render()
    {
        /** @var Poll $poll */
        $poll = $this->arguments['poll'];

        $content = '';

        $resultRendererConfiguration = $this->getResultRendererConfigurationFromSettings($this->templateVariableContainer);
        foreach ($this->getAllResultRenderers($poll, $resultRendererConfiguration) as $resultRenderer) {

            $this->templateVariableContainer->add($this->arguments['as'], $resultRenderer->getRenderedResults());
            $this->templateVariableContainer->add('resultRendererPartialName', $resultRenderer->getViewPartialName());

            // Render children
            $content .= $this->renderChildren();

            // Clean up
            $this->templateVariableContainer->remove($this->arguments['as']);
            $this->templateVariableContainer->remove('resultRendererPartialName');

            // Register resources in PageRenderer
            // @todo: this is currently no needed. Should we have this here anyway to be able to run without ajax?
            //if ($resultRenderer->getAdditionalResources()) {
            //    $resultRenderer->getAdditionalResources()->registerInPageRenderer();
            //}
        }
        return $content;
    }
}
