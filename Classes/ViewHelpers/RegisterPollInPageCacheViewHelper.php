<?php
declare(strict_types=1);
namespace AawTeam\Minipoll\ViewHelpers;

/*
 * Copyright 2021 Agentur am Wasser | Maeder & Partner AG
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
use AawTeam\Minipoll\Utility\PollUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * RegisterPollInPageCacheViewHelper
 */
class RegisterPollInPageCacheViewHelper extends AbstractViewHelper
{
    /**
     * @var PollUtility
     */
    protected $pollUtility;

    /**
     * @param PollUtility $pollUtility
     */
    public function injectPollUtility(PollUtility $pollUtility)
    {
        $this->pollUtility = $pollUtility;
    }

    /**
     * {@inheritDoc}
     * @see \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper::initializeArguments()
     */
    public function initializeArguments()
    {
        $this->registerArgument('poll', Poll::class, 'The poll to evaluate', true);
    }

    /**
     *
     */
    public function render()
    {
        $this->pollUtility->addPollToPageCache($this->arguments['poll']);
    }
}
