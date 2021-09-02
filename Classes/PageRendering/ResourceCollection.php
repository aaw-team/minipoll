<?php
declare(strict_types=1);
namespace AawTeam\Minipoll\PageRendering;

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

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ResourceCollection
 */
class ResourceCollection
{
    /**
     * @var array
     */
    protected $resources = [];

    /**
     * @param Resource[] $resources
     * @throws \InvalidArgumentException
     */
    public function __construct(array $resources = [])
    {
        foreach ($resources as $resource) {
            if (!($resource instanceof Resource)) {
                throw new \InvalidArgumentException('$resources must contain ' . Resource::class . ' objects');
            }
        }
        $this->resources = $resources;
    }

    /**
     * @param Resource $resource
     * @return self
     */
    public function withResource(Resource $resource): self
    {
        $instance = new self($this->resources);
        $instance->resources[] = $resource;
        return $instance;
    }

    /**
     * @return Resource[]
     */
    public function getResources(): array
    {
        return $this->resources;
    }

    /**
     * @throws \RuntimeException
     */
    public function registerInPageRenderer(): void
    {
        /** @var PageRenderer $pageRenderer */
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);

        foreach ($this->resources as $resource) {
            /** @var Resource $resource */
            switch ($resource->getType()) {
                case Resource::TYPE_CSS :
                    $pageRenderer->addCssInlineBlock(md5($resource->getSource()), $resource->getSource());
                    break;
                case Resource::TYPE_CSS_LIB :
                    $pageRenderer->addCssLibrary($resource->getSource());
                    break;
                case Resource::TYPE_JS_LIB :
                    $pageRenderer->addJsLibrary(md5($resource->getSource()), $resource->getSource());
                    break;
                case Resource::TYPE_JS_FILE :
                    $pageRenderer->addJsFile($resource->getSource());
                    break;
                case Resource::TYPE_JS_INLINE :
                    $pageRenderer->addJsInlineCode(md5($resource->getSource()), $resource->getSource());
                    break;
                case Resource::TYPE_JS_FOOTER_LIB :
                    $pageRenderer->addJsFooterLibrary(md5($resource->getSource()), $resource->getSource());
                    break;
                case Resource::TYPE_JS_FOOTER_FILE :
                    $pageRenderer->addJsFooterFile($resource->getSource());
                    break;
                case Resource::TYPE_JS_FOOTER_INLINE :
                    $pageRenderer->addJsFooterInlineCode(md5($resource->getSource()), $resource->getSource());
                    break;
                default :
                    throw new \RuntimeException('Invalid Resource type: ' . htmlspecialchars($resource->getType()));
            }
        }
    }
}
