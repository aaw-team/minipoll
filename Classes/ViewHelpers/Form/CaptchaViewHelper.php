<?php
namespace AawTeam\Minipoll\ViewHelpers\Form;

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

use AawTeam\Minipoll\CaptchaProvider\Factory as CaptchaProviderFactory;
use AawTeam\Minipoll\Domain\Model\Poll;

/**
 * CaptchaViewHelper
 */
class CaptchaViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * @var \AawTeam\Minipoll\Utility\PollUtility
     * @inject
     */
    protected $pollUtility;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('poll', Poll::class, 'The poll to create the captcha for', true);
    }

    /**
     * @return string
     */
    public function render()
    {
        $poll = $this->arguments['poll'];

        if (!$poll->getUseCaptcha()) {
            return '';
        }

        // Load the captchaProvider
        $captchaProviderAlias = $this->pollUtility->getCaptchaProviderAliasFromSettings($this->templateVariableContainer->get('settings'));
        if ($captchaProviderAlias === false) {
            return '';
        }
        try {
            $captchaProvider = CaptchaProviderFactory::getCaptchaProvider($captchaProviderAlias);
        } catch (\AawTeam\Minipoll\Exception\NoCaptchaProviderFoundException $e) {
            // Silently fail here
            return '';
        }

        // Get the info from the captchaProvider
        $captchaArray = $captchaProvider->createCaptchaArray($poll);

        // Compose the partial name
        $partialName = 'Form/Captcha/' . substr(get_class($captchaProvider), strrpos(get_class($captchaProvider), '\\') + 1);
        $captchaArray['partialName'] = $partialName;

        // Assign captcha info to variable 'captcha'
        $this->templateVariableContainer->add('captcha', $captchaArray);
        $return = $this->renderChildren();
        $this->templateVariableContainer->remove('captcha');

        return $return;
    }
}
