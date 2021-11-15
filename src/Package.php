<?php

/**
 * JBZoo Toolbox - Composer-Graph
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Composer-Graph
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/Composer-Graph
 */

declare(strict_types=1);

namespace JBZoo\ComposerGraph;

/**
 * Class Package
 * @package JBZoo\ComposerGraph
 */
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

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $version = '*';

    /**
     * @var array
     */
    private $required = [];

    /**
     * @var array
     */
    private $requiredDev = [];

    /**
     * @var array
     */
    private $suggests = [];

    /**
     * @var array
     */
    private $tags = [];

    /**
     * Package constructor.
     * @param string      $name
     * @param string|null $vendorDir
     */
    public function __construct(string $name, ?string $vendorDir = null)
    {
        $this->name = \strtolower($name);

        if (
            \strpos($this->name, '/') === false &&
            (
                \preg_match("#^ext-[a-z0-9]*#", $this->name) ||
                \preg_match("#^lib-[a-z0-9]*#", $this->name)
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
     * @param string $version
     * @return $this
     */
    public function setVersion(string $version): Package
    {
        if ($version) {
            $this->version = \strtolower($version);
        }

        return $this;
    }

    /**
     * @param array $required
     * @return $this
     */
    public function addRequire(array $required): Package
    {
        $this->required = \array_merge($this->required, $required);
        return $this;
    }

    /**
     * @param array $requiredDev
     * @return $this
     */
    public function addRequireDev(array $requiredDev): Package
    {
        $this->requiredDev = \array_merge($this->requiredDev, $requiredDev);
        return $this;
    }

    /**
     * @param array $suggest
     * @return $this
     */
    public function addSuggest(array $suggest): Package
    {
        $this->suggests = \array_merge($this->suggests, $suggest);
        return $this;
    }

    /**
     * @param array $tags
     * @return $this
     */
    public function addTags(array $tags): Package
    {
        $this->tags = \array_unique(\array_merge($this->tags, $tags));
        return $this;
    }

    /**
     * @param string $tag
     * @return bool
     */
    public function isTag(string $tag): bool
    {
        return \in_array($tag, $this->tags, true);
    }

    /**
     * @return bool
     */
    public function isDirectPackage(): bool
    {
        return $this->isTag(self::DIRECT) && $this->isTag(self::REQUIRED);
    }

    /**
     * @return bool
     */
    public function isDirectPackageDev(): bool
    {
        return $this->isTag(self::DIRECT) && $this->isTag(self::REQUIRED_DEV);
    }

    /**
     * @return bool
     */
    public function isPlatform(): bool
    {
        return !$this->isMain() && ($this->isTag(self::PHP) || $this->isTag(self::EXT));
    }

    /**
     * @return bool
     */
    public function isPhp(): bool
    {
        return $this->isTag(self::PHP);
    }

    /**
     * @return bool
     */
    public function isPhpExt(): bool
    {
        return $this->isTag(self::EXT);
    }

    /**
     * @return bool
     */
    public function isMain(): bool
    {
        return $this->isTag(self::MAIN);
    }

    /**
     * @param bool $addVersion
     * @return string
     */
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

    /**
     * @return array
     */
    public function getRequired(): array
    {
        return $this->required;
    }

    /**
     * @return array
     */
    public function getRequiredDev(): array
    {
        return $this->requiredDev;
    }

    /**
     * @return array
     */
    public function getSuggested(): array
    {
        return $this->suggests;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return self::alias($this->getName(false));
    }

    /**
     * @param string $string
     * @return string
     */
    public static function alias(string $string): string
    {
        $string = \strip_tags($string);
        return \str_replace(['/', '-', 'graph', '(', ')', ' ', '*'], ['__', '_', 'g_raph', '', '', '', ''], $string);
    }
}
