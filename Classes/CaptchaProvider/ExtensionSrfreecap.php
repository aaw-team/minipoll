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

use AawTeam\Minipoll\Domain\Model\Poll;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use AawTeam\Minipoll\PageRendering\Resource;
use AawTeam\Minipoll\PageRendering\ResourceCollection;

/**
 * ExtensionSrfreecap
 *
 * Captcha provider for the TYPO3 Extension 'sr_freecap'
 * @see https://typo3.org/extensions/repository/view/sr_freecap
 */
class ExtensionSrfreecap implements CaptchaProviderInterface
{
    /**
     * {@inheritDoc}
     * @see CaptchaProviderInterface::getName()
     */
    public function getName(): string
    {
        return 'TYPO3 Extension "sr_freecap"';
    }

    /**
     * {@inheritDoc}
     * @see CaptchaProviderInterface::isAvailable()
     */
    public function isAvailable(): bool
    {
        return \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('sr_freecap');
    }

    /**
     * {@inheritDoc}
     * @see CaptchaProviderInterface::hasMultipleInstancesSupport()
     */
    public function hasMultipleInstancesSupport(): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     * @see CaptchaProviderInterface::validate()
     */
    public function validate(string $fieldValue, Poll $poll): bool
    {
        /** @var \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator $validator */
        $validator = GeneralUtility::makeInstance(\SJBR\SrFreecap\Validation\Validator\CaptchaValidator::class);
        $result = $validator->validate($fieldValue);
        return !$result->hasErrors();
    }

    /**
     * {@inheritDoc}
     * @see CaptchaProviderInterface::createCaptchaArray()
     */
    public function createCaptchaArray(Poll $poll): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     * @see CaptchaProviderInterface::getAdditionalResources()
     */
    public function getAdditionalResources(): ?ResourceCollection
    {
        return GeneralUtility::makeInstance(ResourceCollection::class)->withResource(
            Resource::createJsFooterFile('EXT:sr_freecap/Resources/Public/JavaScript/freeCap.js')
        )->withResource(
            Resource::createJsFooterInline('
document.addEventListener("DOMContentLoaded", function() {
    let elements = document.querySelectorAll("div.tx_minipoll-poll[data-minipoll-ajax]");
    for (let i=0; i<elements.length; i++) {
        elements[i].addEventListener("minipoll_post", e => {
            let captchaElements = e.target.querySelectorAll("[id^=tx_srfreecap_captcha_image_]");
            if (captchaElements.length === 1) {
                console.debug("Re-loading sr_freecap captcha");
                SrFreecap.newImage(captchaElements[0].id.split("tx_srfreecap_captcha_image_")[1]);
            }
        });
    }
});')
        );
    }
}
