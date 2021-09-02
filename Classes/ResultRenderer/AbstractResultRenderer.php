<?php
declare(strict_types=1);
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
use AawTeam\Minipoll\PageRendering\ResourceCollection;
use AawTeam\Minipoll\ViewModel\PollOptionViewModel;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * AbstractResultRenderer
 */
abstract class AbstractResultRenderer implements ResultRendererInterface
{
    /**
     * @var Poll
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
     * @see ResultRendererInterface::setup()
     */
    public function setup(Poll $poll, array $configuration): void
    {
        $this->poll = $poll;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritDoc}
     * @see ResultRendererInterface::getAdditionalResources()
     */
    public function getAdditionalResources(): ?ResourceCollection
    {
        return null;
    }

    /**
     * @return array
     */
    protected function getPollOptionsAsViewModels(): array
    {
        $orderedOptions = $this->poll->getOptions()->toArray();
        if ($this->configuration['orderBy'] == 'answers') {
            uasort($orderedOptions, function(PollOption $p1, PollOption $p2): int {
                $c1 = $p1->getAnswers()->count();
                $c2 = $p2->getAnswers()->count();
                if ($c1 == $c2) {
                    return 0;
                }
                return ($c2 - $c1);
            });
        } elseif ($this->configuration['orderBy'] == 'random') {
            uasort($orderedOptions, function(PollOption $p1, PollOption $p2): int {
                return random_int(-1,1);
            });
        }

        if ($this->configuration['reverseOrder'] == 1) {
            $orderedOptions = array_reverse($orderedOptions);
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
    protected function getConfigurationOptionPerItem(string $option, int $itemCount): array
    {
        if ($itemCount < 1 ) {
            return [];
        }
        if (!isset($this->configuration[$option])) {
            return array_fill(0, $itemCount, '');
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
    protected function getConfigurationOptionPerItemFromList(string $list, int $itemCount): array
    {
        if ($itemCount < 1 ) {
            return [];
        }
        $optionsList = GeneralUtility::trimExplode(',', $list, true);
        $optionsCount = count($optionsList);
        if ($optionsCount < 1) {
            return array_fill(0, $itemCount, '');
        } elseif ($optionsCount == 1) {
            return array_fill(0, $itemCount, $optionsList[0]);
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
    protected function getConfigurationOptionPerItemFromOptionSplit(string $list, int $itemCount): array
    {
        $splitConf = $this->getTyposcriptService()->explodeConfigurationForOptionSplit(['list' => $list], $itemCount);

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
    protected function getTyposcriptConfiguration(): array
    {
        if ($this->typoscriptConfiguration === null) {
            $this->typoscriptConfiguration = $this->getTyposcriptService()->convertPlainArrayToTypoScriptArray($this->configuration);
        }
        return $this->typoscriptConfiguration;
    }

    /**
     * @return TypoScriptService
     */
    protected function getTyposcriptService()
    {
        return GeneralUtility::makeInstance(TypoScriptService::class);
    }
}
