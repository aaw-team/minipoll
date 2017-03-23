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
 * Svgpiechart ResultRenderer
 */
class Svgpiechart extends AbstractResultRenderer
{
    /**
     * @return array
     */
    public function getRenderedResults()
    {
        return [
            'paths' => $this->getPaths(),
            'svgcontent' => 'YAHOOO'
        ];
    }

    /**
     * @return array
     */
    protected function getPaths()
    {
        return [
            [
                'd' => 'M0,0 L100,100z',
                'fill' => '#ff0000',
                'stroke' => '#ff0000',
                'class' => 'row1',
            ],
            [
                'd' => 'M10,10 L50,50z',
                'fill' => '#00ff00',
                'stroke' => '#00ff00',
                'class' => 'row2',
            ],
            [
                'd' => 'M20,20 L10,50z',
                'fill' => '#0000ff',
                'stroke' => '#0000ff',
                'class' => 'row3',
            ],
        ];
    }
}
