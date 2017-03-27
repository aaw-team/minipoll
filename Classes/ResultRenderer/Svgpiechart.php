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
use TYPO3\CMS\Core\Utility\MathUtility;

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
            'width' => (int) $this->configuration['width'],
            'height' => (int) $this->configuration['height'],
            'slices' => $this->getSlices()
        ];
    }

    /**
     * Gets all slices
     *
     * @throws \Exception
     * @return array
     */
    protected function getSlices()
    {
        if (!MathUtility::canBeInterpretedAsInteger($this->configuration['width']) || (int) $this->configuration['width'] <= 0) {
            throw new \Exception('width must be a positive int');
        }
        if (!MathUtility::canBeInterpretedAsInteger($this->configuration['height']) || (int) $this->configuration['height'] <= 0) {
            throw new \Exception('height must be a positive int');
        }
        if (!MathUtility::canBeInterpretedAsFloat($this->configuration['radius']) || $this->configuration['radius'] <= 0) {
            throw new \Exception('radius must be a positive int or float');
        }
        if (!MathUtility::canBeInterpretedAsFloat($this->configuration['textRadius']) || $this->configuration['textRadius'] <= 0) {
            throw new \Exception('textRadius must be a positive int or float');
        }

        $colors = ['#2236e9', '#f40c0c', '#35821f', '#a30cf4', '#daf40c', '#1ecf1e', '#11d6d6', '#fe9800'];

        $radius = $this->configuration['radius']; // Radius of the circle
        $textRadius = $this->configuration['textRadius']; // Radius of the circle at which the text will be aligned
        $centerX = $this->configuration['width'] / 2; // Center of the circle
        $centerY = $this->configuration['height'] / 2;

        $startX = $centerX; // The starting point of the circle
        $startY = $centerY - $radius;

        // Control vars
        $previousRadianSum = 0.0; // This is 0 because we start at the top of the pie
        $previousEndX = $startX;
        $previousEndY = $startY;

        // Order options
        $orderedOptions = $this->poll->getOptions()->toArray();
        if ($this->configuration['orderBy'] == 'answers') {
            $reverseOrder = $this->configuration['reverseOrder'] == 1 ? -1 : 1;
            \uasort($orderedOptions, function(PollOption $p1, PollOption $p2) use ($reverseOrder) {
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

        // Calculate total answer count
        $totalAnswers = 0;
        foreach ($orderedOptions as $options) {
            $totalAnswers += $options->getAnswers()->count();
        }

        // Loop through each poll
        $iterator = 0;
        $return = [];
        foreach ($orderedOptions as $key => $pollOption) {
            // Get answer count of the current option
            $answers = $pollOption->getAnswers()->count();

            // Get percentage of answers of the current option
            $percent = $this->getPercentage($totalAnswers, $answers);

            if($totalAnswers == $answers) {
                // in case a answer has 100% then draw a circle
                $slice = [
                    'type' => 'circle',
                    'radius' => $radius,
                    'centerX' => $centerX,
                    'centerY' => $centerY
                ];
            } elseif ($answers === 0) {
                continue;
            } else {
                $slice = $this->getSlice(
                    $percent,
                    $radius,
                    $textRadius,
                    $startX,
                    $startY,
                    $centerX,
                    $centerY,
                    $previousEndX,
                    $previousEndY,
                    $previousRadianSum
                );
                $slice['type'] = 'path';
            }

            // Build the return array
            $return[] = \array_merge($slice, [
                'fill' => $colors[$iterator],
                'id' => $key,
                'votes' => $answers,
                'percent' => $percent
            ]);

            $iterator++;
        }

        return $return;
    }

    /**
     * Calculates all the needed stuff for the path
     *
     * @return array
     */
    function getSlice(
        $percent,
        $radius,
        $textRadius,
        $startX,
        $startY,
        $centerX,
        $centerY,
        &$previousEndX,
        &$previousEndY,
        &$previousRadianSum
    ) {
        // Calculate radians with percentage
        $radian = $this->percentToRadian($percent);

        // Calculate the sum of all radian
        $radianSum = $radian + $previousRadianSum;

        // calculate the end point of the current slice
        $endX = $startX + \sin($radianSum) * $radius;
        $endY = $startY + (\cos($radianSum) * -1 + 1) * $radius;

        // Now create the path string
        // We need something like this:
        // M 50 10 A 40 40 0 0 1 84 69 L 50 50 Z

        // Move to the end position of the previous pie slice
        $path = "M " . $previousEndX . " " . $previousEndY;

        // Make a arc to the new end position
        $largeArcFlag = 0;
        if($percent > 50) {
            $largeArcFlag = 1;
        }
        $path .= "A " . $radius . " " . $radius . " 0 " . $largeArcFlag . " 1 " . $endX . " " . $endY;

        // Draw a line back to the center
        $path .= "L " . $centerX . " " . $centerY;

        // End the string with a Z to self close the path
        $path .= "Z";

        // Calculate the center point of the current slice
        $cX = $startX + \sin($radian/2 + $previousRadianSum) * $textRadius;
        $cY = ($startY + $radius - $textRadius) + (\cos($radian/2 + $previousRadianSum) * -1 + 1) * $textRadius;

        // Update the previous end for the next slice
        $previousEndX = $endX;
        $previousEndY = $endY;

        // Calculate the sum of previous radian for the next slide
        $previousRadianSum = $radianSum;

        return [
            'd' => $path,
            'centerX' => $cX,
            'centerY' => $cY
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
    function percentToRadian($percent)
    {
        return 0.02 * $percent * \pi();
    }

    /**
     * Gets the percentage of answers compared to total amount of answers
     *
     * @return float
     */
    function getPercentage($totalAnswers, $answers)
    {
        $percent = 0.0;
        if ($totalAnswers > 0 && $answers > 0) {
            $percent = 100 / $totalAnswers * $answers;
        }

        return $percent;
    }
}
