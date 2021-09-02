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
use AawTeam\Minipoll\PageRendering\ResourceCollection;

/**
 * ResultRendererInterface
 */
interface ResultRendererInterface
{
    /**
     * @param Poll $poll
     * @param array $configuration
     * @return void
     */
    public function setup(Poll $poll, array $configuration): void;

    /**
     * @return array
     */
    public function getRenderedResults(): array;

    /**
     * @return string
     */
    public function getViewPartialName(): string;

    /**
     * @return ResourceCollection|null
     */
    public function getAdditionalResources(): ?ResourceCollection;
}
