<?php
namespace AawTeam\Minipoll\ResultRenderer;

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

/**
 * ResultRenderer Css
 */
class Css extends AbstractResultRenderer
{
    /**
     * @return array
     */
    public function getRenderedResults()
    {
        $options = $this->getPollOptionsAsViewModels();
        $optionsCount = \count($options);
        $colors = $this->getConfigurationOptionPerItem('colors', $optionsCount);
        $cssClasses = $this->getConfigurationOptionPerItem('cssClasses', $optionsCount);

        foreach ($options as $num => $option) {
            $option->setOption('color', $colors[$num]);
            $option->setOption('class', $cssClasses[$num]);
        }
        return [
            'options' => $options
        ];
    }
}
