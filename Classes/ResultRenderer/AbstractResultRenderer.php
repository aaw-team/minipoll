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

use AawTeam\Minipoll\Domain\Model\Poll;
use AawTeam\Minipoll\Domain\Model\PollOption;
use AawTeam\Minipoll\ViewModel\PollOptionViewModel;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * AbstractResultRenderer
 */
abstract class AbstractResultRenderer implements ResultRendererInterface
{
    /**
     * @var \AawTeam\Minipoll\Domain\Model\Poll
     */
    protected $poll;

    /**
     * @var array
     */
    protected $configuration;

    /**
     * @var array
     */
    protected $typoscriptConfiguration;

    /**
     * {@inheritDoc}
     * @see \AawTeam\Minipoll\ResultRenderer\ResultRendererInterface::setup()
     */
    public function setup(Poll $poll, array $configuration)
    {
        $this->poll = $poll;
        $this->configuration = $configuration;
    }

    /**
     * @return array
     */
    protected function getPollOptionsAsViewModels()
    {
        $orderedOptions = $this->poll->getOptions()->toArray();
        if ($this->configuration['orderBy'] == 'answers') {
            uasort($orderedOptions, function(PollOption $p1, PollOption $p2) {
                $c1 = $p1->getAnswers()->count();
                $c2 = $p2->getAnswers()->count();
                if ($c1 == $c2) {
                    return 0;
                }
                return ($c2 - $c1);
            });
        } elseif ($this->configuration['orderBy'] == 'random') {
            uasort($orderedOptions, function(PollOption $p1, PollOption $p2) {
                return \mt_rand(-1,1);
            });
        }

        if ($this->configuration['reverseOrder'] == 1) {
            $orderedOptions = \array_reverse($orderedOptions);
        }

        $viewModels = [];
        foreach ($orderedOptions as $pollOption) {
            $viewModels[] = PollOptionViewModel::createFromPollOption($pollOption);
        }
        return $viewModels;
    }

    /**
     * @param string $option
     * @param int $itemCount
     * @throws \InvalidArgumentException
     * @return array
     */
    protected function getConfigurationOptionPerItem($option, $itemCount)
    {
        if (!\is_string($option)) {
            throw new \InvalidArgumentException('$option must be string');
        } elseif (!\is_int($itemCount)) {
            throw new \InvalidArgumentException('$itemCount must be int');
        }
        if ($itemCount < 1 ) {
            return [];
        }
        if (!isset($this->configuration[$option])) {
            return \array_fill(0, $itemCount, '');
        }

        // Get typoscript configuration
        $configuration = $this->getTyposcriptConfiguration();

        $optionList = $configuration[$option];
        $useOptionSplit = isset($configuration[$option . '.']['useOptionSplit']) && (bool) $configuration[$option . '.']['useOptionSplit'];

        return $useOptionSplit
            ? $this->getConfigurationOptionPerItemFromOptionSplit($optionList, $itemCount)
            : $this->getConfigurationOptionPerItemFromList($optionList, $itemCount);
    }

    /**
     * @param string $list
     * @param int $itemCount
     * @return array
     */
    protected function getConfigurationOptionPerItemFromList($list, $itemCount)
    {
        if (!\is_string($list)) {
            throw new \InvalidArgumentException('$list must be string');
        } elseif (!\is_int($itemCount)) {
            throw new \InvalidArgumentException('$itemCount must be int');
        }
        if ($itemCount < 1 ) {
            return [];
        }
        $optionsList = GeneralUtility::trimExplode(',', $list, true);
        $optionsCount = \count($optionsList);
        if ($optionsCount < 1) {
            return \array_fill(0, $itemCount, '');
        } elseif ($optionsCount == 1) {
            return \array_fill(0, $itemCount, $optionsList[0]);
        }
        $return = [];
        for ($i = 0; $i < $itemCount; ++$i) {
            $return[$i] = $optionsList[$i % $optionsCount];
        }
        return $return;
    }

    /**
     * @param string $list
     * @param int $itemCount
     * @return array
     */
    protected function getConfigurationOptionPerItemFromOptionSplit($list, $itemCount)
    {
        if (!\is_string($list)) {
            throw new \InvalidArgumentException('$list must be string');
        } elseif (!\is_int($itemCount)) {
            throw new \InvalidArgumentException('$itemCount must be int');
        }

        $splitConf = $this->getTyposcriptFrontendController()->tmpl->splitConfArray(['list' => $list], $itemCount);

        $return = [];
        foreach ($splitConf as $num => $value) {
            $return[$num] = $value['list'];
        }

        return $return;
    }

    /**
     * Returns $this->configuration as typoscript-style array
     *
     * @return array
     */
    protected function getTyposcriptConfiguration()
    {
        if ($this->typoscriptConfiguration === null) {
            $this->typoscriptConfiguration = $this->getTyposcriptService()->convertPlainArrayToTypoScriptArray($this->configuration);
        }
        return $this->typoscriptConfiguration;
    }

    /**
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected function getTyposcriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }

    /**
     * @return \TYPO3\CMS\Extbase\Service\TypoScriptService
     */
    protected function getTyposcriptService()
    {
        return GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Service\TypoScriptService::class);
    }
}
