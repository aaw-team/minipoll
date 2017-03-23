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

use AawTeam\Minipoll\Domain\Model\PollOption;

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
        // order options
        $orderedOptions = $this->poll->getOptions()->toArray();
        if ($this->configuration['orderBy'] == 'answers') {
            $reverseOrder = $this->configuration['reverseOrder'] == 1 ? -1 : 1;
            uasort($orderedOptions, function(PollOption $p1, PollOption $p2) use ($reverseOrder) {
                $c1 = $p1->getAnswers()->count();
                $c2 = $p2->getAnswers()->count();
                if ($c1 == $c2) {
                    return 0;
                }
                return ($c2 - $c1) * $reverseOrder;
            });
        } elseif ($this->configuration['reverseOrder'] == 1) {
            $orderedOptions = \array_reverse($orderedOptions);
        }

        $colors = ['#2236e9', '#f40c0c', '#35821f', '#a30cf4', '#daf40c', '#1ecf1e', '#11d6d6', '#fe9800'];
        $return = [];
        $previousRadian = 0; // this is 0 because we start at the top of the pie
        $radius = 190; // radius of the circle
        $center = new Position(200,200); // center of the circle
        $seed = new Position(200,10); // seed as postion
        $lastPosition = new Position(200,10); // same as seed!

        // loop through each poll
        $iterator = 0;
        foreach ($orderedOptions as $key => $pollOption) {
            $radian = $this->percentToRadian($this->getPercentage($pollOption));

            // get the d tag
            $slice = $this->getSlice($radian, $previousRadian, $lastPosition, $center, $radius, $seed);

            // it is current radian + the radian of all previous slices
            $previousRadian = $radian + $previousRadian;

            $return[] = [
                'd' => $slice['d'],
                'fill' => $colors[$iterator],
                'class' => 'slice'.$key,
                'votes' => $pollOption->getAnswers()->count(),
                'text' => [
                    'text' => $this->getPercentage($pollOption),
                    'x' => $slice['center']['x'],
                    'y' => $slice['center']['y']
                ]
            ];

            $iterator++;
        }

        return $return;
    }

    /**
     * Calculates all the needed stuff for the path
     *
     * @return array
     */
    function getSlice($currentRadian, $previousRadian, &$lastPosition, $center, $radius, $seed) {
        // it is current radian + the radian of all previous slices
        $radian = $currentRadian + $previousRadian;

        // now we get to the point
        // calculate the end point of the current slice
        $x2 = $seed->getX() + sin($radian) * $radius;
        $y2 = $seed->getY() + (cos($radian) * -1 + 1) * $radius;
        $endPos = new Position($x2, $y2);

        // calculate the center point of the current slice
        $textRadius = $radius*0.75;
        $cX = $seed->getX() + sin($currentRadian/2 + $previousRadian) * $textRadius;
        $cY = ($seed->getY() + $radius - $textRadius) + (cos($currentRadian/2 + $previousRadian) * -1 + 1) * $textRadius;

        // we need something like this:
        // M 50 10 A 40 40 0 0 1 84 69 L 50 50 Z

        // move to the end position of the last pie slice
        $moveto = new Moveto($lastPosition);
        // make a arc to the new end position
        $arcto = new Arcto($radius, $endPos);
        // draw a line back to the center
        $lineto = new Lineto($center);
        // and end the string with a Z to self close the path
        $end = "Z";

        //return 'M 10 10 L 90 10';
        $path = $moveto->toString() . " " . $arcto->toString() . " " . $lineto->toString() . " " . $end;

        // update the lastPosition for the next slice
        $lastPosition->setPosition($x2, $y2);

        return [
            'd' => $path,
            'center' => [
                'x' => $cX,
                'y' => $cY
            ]
        ];
    }

    /**
     * Helper function to calculate percent to radians
     *
     * formula instead of: degToRad(percentToDeg(percent))
     * 360 / 100 * percent * Math.PI / 180 =
     * 3.6 * percent * Math.PI / 180 =
     * 0.02 * percent * Math.PI
     *
     * @return double
     */
    function percentToRadian($percent) {
        return 0.02 * $percent * pi();
    }

    /**
     * @return int
     */
    function getPercentage($pollOption) {
        // Calculate total answer count
        $totalAnswers = 0;
        foreach ($pollOption->getPoll()->getOptions() as $options) {
            $totalAnswers += $options->getAnswers()->count();
        }
        // Get answer count of the current option
        $answers = $pollOption->getAnswers()->count();
        $percent = 0;
        if ($totalAnswers > 0 && $answers > 0) {
            $percent = 100 / $totalAnswers * $answers;
            if ($roundPercent !== null && $roundPercent >= 0) {
                $percent = \round($percent, $roundPercent);
            }
        }

        return $percent;
    }
}

/**
 * vec2 Position
 *
 * @return void
 */
class Position {
    private $x;
    private $y;

    /**
     * @return void
     */
    public function __construct($x, $y) {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * @return string
     */
    public function getX() {
        return $this->x;
    }

    /**
     * @return string
     */
    public function getY() {
        return $this->y;
    }

    /**
     * @return void
     */
    public function setPosition($x, $y) {
        $this->x = $x;
        $this->y = $y;
    }
}

/**
 * Arcto svg representation
 */
class Arcto {
    private $rx;
    private $ry;
    private $xAxisRotation;
    private $largeArcFlag;
    private $sweepFlag;
    private $x;
    private $y;

    /**
     * @return void
     */
    public function __construct($radius, $endPosition) {
        $this->rx = $radius; // set to radius
        $this->ry = $radius; // set to radius
        $this->xAxisRotation = 0; // no rotation needed
        $this->largeArcFlag = 0; // no largeArcFlag needed
        $this->sweepFlag = 1; // we sweep because we go clock wise
        $this->x = $endPosition->getX(); // end position
        $this->y = $endPosition->getY(); // end position
    }

    /**
     * @return string
     */
    public function toString() {
        // A rx ry x-axis-rotation large-arc-flag sweep-flag x y
        return "A " . $this->rx . " " . $this->ry . " " . $this->xAxisRotation . " " . $this->largeArcFlag . " " . $this->sweepFlag . " " . $this->x . " " . $this->y;
    }
}

/**
 * Lineto svg representation
 */
class Lineto {
    private $position; // class Position

    /**
     * @return void
     */
    public function __construct($postion) {
        $this->position = $postion;
    }

    /**
     * @return string
     */
    public function toString() {
        return "L " . $this->position->getX() . " " . $this->position->getY();
    }
}

/**
 * Moveto svg representation
 */
class Moveto {
    private $position; // class Position

    /**
     * @return void
     */
    public function __construct($postion) {
        $this->position = $postion;
    }

    /**
     * @return string
     */
    public function toString() {
        return "M " . $this->position->getX() . " " . $this->position->getY();
    }
}
