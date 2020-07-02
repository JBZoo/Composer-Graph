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

namespace JBZoo\ComposerGraph;

/**
 * Class Package
 * @package JBZoo\ComposerGraph
 */
class Package
{
    public const TAG_MAIN        = 'main';
    public const TAG_DIRECT      = 'direct';
    public const TAG_PHP         = 'php';
    public const TAG_PHP_EXT     = 'ext';
    public const TAG_REQUIRE     = 'require';
    public const TAG_REQUIRE_DEV = 'require-dev';

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
    private $tags = [];

    /**
     * Package constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;

        if (preg_match("#ext-[a-z0-9]*#", $this->name) || preg_match("#lib-[a-z0-9]*#", $this->name)) {
            $this->addTags([self::TAG_PHP_EXT]);
        }
    }

    /**
     * @param string $version
     * @return $this
     */
    public function setVersion(string $version)
    {
        if ($version) {
            $this->version = $version;
        }

        return $this;
    }

    /**
     * @param array $required
     * @return $this
     */
    public function addRequire(array $required)
    {
        $this->required = array_merge($this->required, $required);
        return $this;
    }

    /**
     * @param array $requiredDev
     * @return $this
     */
    public function addRequireDev(array $requiredDev)
    {
        $this->requiredDev = array_merge($this->requiredDev, $requiredDev);
        return $this;
    }

    /**
     * @param array $tags
     * @return $this
     */
    public function addTags(array $tags)
    {
        $this->tags = array_unique(array_merge($this->tags, $tags));
        return $this;
    }

    /**
     * @param string $tag
     * @return bool
     */
    public function isTag(string $tag): bool
    {
        return in_array($tag, $this->tags, true);
    }

    /**
     * @return bool
     */
    public function isDirectPackage(): bool
    {
        return $this->isTag(self::TAG_DIRECT) && $this->isTag(self::TAG_REQUIRE);
    }

    /**
     * @return bool
     */
    public function isDirectPackageDev(): bool
    {
        return $this->isTag(self::TAG_DIRECT) && $this->isTag(self::TAG_REQUIRE_DEV);
    }

    /**
     * @return bool
     */
    public function isPlatform(): bool
    {
        return !$this->isMain() && ($this->isTag(self::TAG_PHP) || $this->isTag(self::TAG_PHP_EXT));
    }

    /**
     * @return bool
     */
    public function isPhp(): bool
    {
        return $this->isTag(self::TAG_PHP);
    }

    /**
     * @return bool
     */
    public function isPhpExt(): bool
    {
        return $this->isTag(self::TAG_PHP_EXT);
    }

    /**
     * @return bool
     */
    public function isMain(): bool
    {
        return $this->isTag(self::TAG_MAIN);
    }

    /**
     * @param bool $addVersion
     * @return string
     */
    public function getName($addVersion = true): string
    {
        if (!$addVersion) {
            return $this->name;
        }

        return $this->version && $this->version !== '*' ? "{$this->name}@{$this->version}" : $this->name;
    }

    /**
     * @return array
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * @return array
     */
    public function getRequiredDev()
    {
        return $this->requiredDev;
    }
}
