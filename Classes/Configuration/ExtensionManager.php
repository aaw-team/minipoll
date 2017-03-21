<?php
namespace AawTeam\Minipoll\Configuration;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ExtensionManager configuration
 */
class ExtensionManager
{
    /**
     * @param array $params
     * @param object $pObj
     * @return string
     */
    public function getCaptchaProviderField(array $params, $pObj)
    {
        $return = '<select name="' . $params['fieldName'] . '"><option value=""></option>';
        $captchaProviders = CaptchaProviderFactory::getRegisteredCaptchaProviders();
        foreach ($captchaProviders as $alias => $className) {
            /** @var \AawTeam\Minipoll\CaptchaProvider\CaptchaProviderInterface $instance */
            $instance = GeneralUtility::makeInstance($className);
            if ($instance->isAvailable()) {
                $selected = $params['fieldValue'] == $alias ? ' selected="selected"' : '';
                $return .= sprintf('<option value="%s"%s>%s</option>',
                                \htmlspecialchars($alias),
                                $selected,
                                \htmlspecialchars($this->translate($instance->getName()))
                           );
            }
        }
        return $return . '</select>';
    }

    /**
     * @param string $key
     * @return string
     */
    protected function translate($key)
    {
        $translated = $this->getLanguageService()->sL($key);
        if (!$translated) {
            $translated = $key;
        }
        return $translated;
    }

    /**
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}
