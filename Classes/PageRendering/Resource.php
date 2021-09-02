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

/**
 * Resource
 */
class Resource
{
    public const TYPE_CSS = 1;
    public const TYPE_CSS_LIB = 2;
    public const TYPE_JS_LIB = 3;
    public const TYPE_JS_FILE = 4;
    public const TYPE_JS_INLINE = 5;
    public const TYPE_JS_FOOTER_LIB = 6;
    public const TYPE_JS_FOOTER_FILE = 7;
    public const TYPE_JS_FOOTER_INLINE = 8;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var string
     */
    protected $source;

    /**
     * @param int $type
     * @param string $source
     */
    public function __construct(int $type, string $source)
    {
        $this->type = $type;
        $this->source = $source;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    public static function createJsFooterFile(string $source): self
    {
        return new self(self::TYPE_JS_FOOTER_FILE, $source);
    }

    public static function createJsFooterLibrary(string $source): self
    {
        return new self(self::TYPE_JS_FOOTER_LIB, $source);
    }

    public static function createJsFooterInline(string $source): self
    {
        return new self(self::TYPE_JS_FOOTER_INLINE, $source);
    }
}
