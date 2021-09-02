<?php
declare(strict_types=1);
namespace AawTeam\Minipoll\ViewHelpers\Poll;

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

use AawTeam\Minipoll\CaptchaProvider\Factory as CaptchaProviderFactory;
use AawTeam\Minipoll\Utility\PollUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * CaptchaProviderAliasViewHelper
 */
class CaptchaProviderAliasViewHelper extends AbstractViewHelper
{
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
     * @return string
     */
    public function render(): string
    {
        // Load the captchaProvider
        $captchaProviderAlias = $this->pollUtility->getCaptchaProviderAliasFromSettings($this->templateVariableContainer->get('settings'));
        if (!empty($captchaProviderAlias)) {
            try {
                // Load the CaptchaProvider to ensure it exists and is valid
                $captchaProvider = CaptchaProviderFactory::getCaptchaProvider($captchaProviderAlias);
                return $captchaProviderAlias;
            } catch (\AawTeam\Minipoll\Exception\NoCaptchaProviderFoundException $e) {
                // Silently fail here
            }
        }
        return '';
    }
}
