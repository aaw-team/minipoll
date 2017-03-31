<?php
namespace AawTeam\Minipoll\Backend\Form;

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

/**
 * DisplayConditionsEvaluator
 */
class DisplayConditionsEvaluator
{
    /**
     * @param array $params
     * @param object $pObj
     * @return boolean
     */
    public function showCaptchaField(array $params, $pObj)
    {
        return CaptchaProviderFactory::hasAvailableCaptchaProvider();
    }
}
