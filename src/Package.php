<?php

/**
 * JBZoo Toolbox - Composer-Graph.
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @see        https://github.com/JBZoo/Composer-Graph
 */

declare(strict_types=1);

namespace JBZoo\ComposerGraph;

class Package
{
    public const MAIN         = 'main';
    public const DIRECT       = 'direct';
    public const PHP          = 'php';
    public const EXT          = 'ext';
    public const REQUIRED     = 'require';
    public const REQUIRED_DEV = 'require-dev';
    public const SUGGEST      = 'suggest';
    public const HAS_META     = 'has-meta';
    public const INSTALLED    = 'installed';

    private string $name;

    private string $version = '*';

    private array $required = [];

    private array $requiredDev = [];

    private array $suggests = [];

    private array $tags = [];

    public function __construct(string $name, ?string $vendorDir = null)
    {
        $this->name = \strtolower($name);

        if (
            !\str_contains($this->name, '/')
            && (
                \preg_match('#^ext-[a-z\\d]*#', $this->name)
                || \preg_match('#^lib-[a-z\\d]*#', $this->name)
            )
        ) {
            $this->addTags([self::EXT, self::HAS_META]);

            if (\extension_loaded($this->name) || \extension_loaded(\str_replace(['ext-', 'lib-'], '', $this->name))) {
                $this->addTags([self::INSTALLED]);
            }
        }

        if ($vendorDir && (\is_dir("{$vendorDir}/{$this->name}") || \is_dir("{$vendorDir}/{$name}"))) {
            $this->addTags([self::INSTALLED]);
        }
    }

    /**
     * @return $this
     */
    public function setVersion(string $version): self
    {
        if ($version) {
            $this->version = \strtolower($version);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function addRequire(array $required): self
    {
        $this->required = \array_merge($this->required, $required);

        return $this;
    }

    /**
     * @return $this
     */
    public function addRequireDev(array $requiredDev): self
    {
        $this->requiredDev = \array_merge($this->requiredDev, $requiredDev);

        return $this;
    }

    /**
     * @return $this
     */
    public function addSuggest(array $suggest): self
    {
        $this->suggests = \array_merge($this->suggests, $suggest);

        return $this;
    }

    /**
     * @return $this
     */
    public function addTags(array $tags): self
    {
        $this->tags = \array_unique(\array_merge($this->tags, $tags));

        return $this;
    }

    public function isTag(string $tag): bool
    {
        return \in_array($tag, $this->tags, true);
    }

    public function isDirectPackage(): bool
    {
        return $this->isTag(self::DIRECT) && $this->isTag(self::REQUIRED);
    }

    public function isDirectPackageDev(): bool
    {
        return $this->isTag(self::DIRECT) && $this->isTag(self::REQUIRED_DEV);
    }

    public function isPlatform(): bool
    {
        return !$this->isMain() && ($this->isTag(self::PHP) || $this->isTag(self::EXT));
    }

    public function isPhp(): bool
    {
        return $this->isTag(self::PHP);
    }

    public function isPhpExt(): bool
    {
        return $this->isTag(self::EXT);
    }

    public function isMain(): bool
    {
        return $this->isTag(self::MAIN);
    }

    public function getName(bool $addVersion = true): string
    {
        $name = \strtolower(\trim($this->name));

        if ($name === 'php') {
            $name = 'PHP';
        }

        $prefixNoMeta = '';
        if (!$this->isTag(self::HAS_META)) {
            $prefixNoMeta = '* ';
        }

        if (!$addVersion) {
            $result = $name;
        } else {
            $result = $this->version && $this->version !== '*' ? "{$name}@{$this->version}" : $name;
        }

        return $prefixNoMeta . $result;
    }

    public function getRequired(): array
    {
        return $this->required;
    }

    public function getRequiredDev(): array
    {
        return $this->requiredDev;
    }

    public function getSuggested(): array
    {
        return $this->suggests;
    }

    public function getId(): string
    {
        return self::alias($this->getName(false));
    }

    public static function alias(string $string): string
    {
        $string = \strip_tags($string);

        return \str_replace(['/', '-', 'graph', '(', ')', ' ', '*'], ['__', '_', 'g_raph', '', '', '', ''], $string);
    }
}
