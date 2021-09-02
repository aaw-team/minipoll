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

use AawTeam\Minipoll\CaptchaProvider\CaptchaProviderInterface;
use AawTeam\Minipoll\CaptchaProvider\Factory as CaptchaProviderFactory;
use AawTeam\Minipoll\Domain\Model\Poll;
use AawTeam\Minipoll\ResultRenderer\ResultRendererInterface;
use AawTeam\Minipoll\Utility\PollUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * RegisterResourcesViewHelper
 */
class RegisterResourcesViewHelper extends AbstractViewHelper
{
    use ResultRendererConfigurationTrait;

    /**
     * @var PollUtility
     */
    protected $pollUtility;

    /**
     * @param PollUtility $pollUtility
     */
    public function injectPollUtility(PollUtility $pollUtility)
    {
        $this->pollUtility = $pollUtility;
    }

    /**
     * {@inheritDoc}
     * @see \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper::initializeArguments()
     */
    public function initializeArguments()
    {
        $this->registerArgument('poll', Poll::class, 'The poll object', true);
        $this->registerArgument('type', 'string', 'either "resultrenderer", "captchaprovider" or "all"', false, 'all');
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function render(): void
    {
        $type = $this->arguments['type'];
        $poll = $this->arguments['poll'];
        if (in_array($type, ['all', 'resultrenderer'], true)) {
            foreach ($this->loadResultRenderers($poll) as $resultRenderer) {
                if ($collection = $resultRenderer->getAdditionalResources()) {
                    $collection->registerInPageRenderer();
                }
            }
        }
        if (in_array($type, ['all', 'captchaprovider'], true)) {
            if ($captchaProvider = $this->loadCaptchaProvider($poll)) {
                if ($collection = $captchaProvider->getAdditionalResources()) {
                    $collection->registerInPageRenderer();
                }
            }
        }
    }

    /**
     * @param Poll $poll
     * @return ResultRendererInterface[]
     */
    protected function loadResultRenderers(Poll $poll): array
    {
        $resultRendererConfiguration = $this->getResultRendererConfigurationFromSettings($this->templateVariableContainer);
        return $this->getAllResultRenderers($poll, $resultRendererConfiguration);
    }

    /**
     * @param Poll $poll
     * @return CaptchaProviderInterface|null
     */
    protected function loadCaptchaProvider(Poll $poll): ?CaptchaProviderInterface
    {
        if ($poll->getUseCaptcha()) {
            // Load the captchaProvider
            $captchaProviderAlias = $this->pollUtility->getCaptchaProviderAliasFromSettings($this->templateVariableContainer->get('settings'));
            if (!empty($captchaProviderAlias)) {
                try {
                    return CaptchaProviderFactory::getCaptchaProvider($captchaProviderAlias);
                } catch (\AawTeam\Minipoll\Exception\NoCaptchaProviderFoundException $e) {
                    // Silently fail here
                }
            }
        }
        return null;
    }
}
