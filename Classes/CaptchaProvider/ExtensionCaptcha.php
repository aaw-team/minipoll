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

use AawTeam\Minipoll\Domain\Model\Poll;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * ExtensionCaptcha
 *
 * Captcha provider for the TYPO3 Extension 'captcha'
 * @see https://typo3.org/extensions/repository/view/captcha
 */
class ExtensionCaptcha implements CaptchaProviderInterface
{
    /**
     * {@inheritDoc}
     * @see \AawTeam\Minipoll\CaptchaProvider\CaptchaProviderInterface::getName()
     */
    public function getName()
    {
        return 'TYPO3 Extension "captcha"';
    }

    /**
     * EXT:captcha is available when installed in version 2.0.2 or later.
     *
     * {@inheritDoc}
     * @see \AawTeam\Minipoll\CaptchaProvider\CaptchaProviderInterface::isAvailable()
     */
    public function isAvailable()
    {
        return ExtensionManagementUtility::isLoaded('captcha')
            && \version_compare(ExtensionManagementUtility::getExtensionVersion('captcha'), '2.0.2', '>=');
    }

    /**
     * {@inheritDoc}
     * @see \AawTeam\Minipoll\CaptchaProvider\CaptchaProviderInterface::hasMultipleInstancesSupport()
     */
    public function hasMultipleInstancesSupport()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     * @see \AawTeam\Minipoll\CaptchaProvider\CaptchaProviderInterface::validate()
     */
    public function validate($fieldValue, Poll $poll)
    {
        return \ThinkopenAt\Captcha\Utility::checkCaptcha($fieldValue, $poll->getUid());
    }

    /**
     * {@inheritDoc}
     * @see \AawTeam\Minipoll\CaptchaProvider\CaptchaProviderInterface::createCaptchaArray()
     */
    public function createCaptchaArray(Poll $poll)
    {
        return [
            'image' => \ThinkopenAt\Captcha\Utility::makeCaptcha($poll->getUid()),
        ];
    }
}
