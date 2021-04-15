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

use JBZoo\Data\JSON;

use function JBZoo\Data\json;

/**
 * Class Collection
 * @package JBZoo\ComposerGraph
 */
class Collection
{
    /**
     * @var JSON
     */
    private $composerFile;

    /**
     * @var JSON
     */
    private $lockFile;

    /**
     * @var Package[]
     */
    private $collection = [];

    /**
     * @var string|null
     */
    private $vendorDir;

    /**
     * Collection constructor.
     * @param JSON        $composerFile
     * @param JSON        $lockFile
     * @param string|null $vendorDir
     */
    public function __construct(JSON $composerFile, JSON $lockFile, ?string $vendorDir = null)
    {
        $this->composerFile = $composerFile;
        $this->lockFile = $lockFile;
        $this->vendorDir = $vendorDir;

        $this->buildCollection();
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function buildCollection(): void
    {
        $istTest = defined('IS_PHPUNIT_TEST') && IS_PHPUNIT_TEST;

        $this->add('php', [
            'version' => $istTest ? null : PHP_VERSION,
            'tags'    => [Package::PHP, Package::HAS_META]
        ]);

        $this->add((string)$this->composerFile->get('name'), [
            'version'     => $istTest ? null : Helper::getGitVersion(),
            'require'     => $this->composerFile->get('require'),
            'require-dev' => $this->composerFile->get('require-dev'),
            'suggest'     => $this->composerFile->get('suggest'),
            'tags'        => [Package::MAIN, Package::HAS_META]
        ]);

        $mainRequire = array_keys((array)$this->composerFile->get('require'));
        foreach ($mainRequire as $package) {
            $this->add((string)$package, ['tags' => [Package::DIRECT]]);
        }

        $mainRequireDev = array_keys((array)$this->composerFile->get('require-dev'));
        foreach ($mainRequireDev as $packageDev) {
            $this->add((string)$packageDev, ['tags' => [Package::DIRECT]]);
        }

        $mainSuggest = array_keys((array)$this->composerFile->get('suggest'));
        foreach ($mainSuggest as $suggest) {
            $this->add((string)$suggest, ['tags' => [Package::DIRECT, Package::SUGGEST]]);
        }

        // Lock file
        $scopes = [
            Package::REQUIRED     => (array)$this->lockFile->get('packages'),
            Package::REQUIRED_DEV => (array)$this->lockFile->get('packages-dev'),
        ];

        foreach ($scopes as $scopeType => $scope) {
            foreach ($scope as $package) {
                $package = json($package);

                $require = (array)$package->get('require');
                $suggest = (array)$package->get('suggest');

                $version = $package->get('version');
                $version = $package->find("extra.branch-alias.{$version}") ?: $version;

                $this->add((string)$package->get('name'), [
                    'version' => $version,
                    'require' => $require,
                    'suggest' => $suggest,
                    'tags'    => [$scopeType, Package::HAS_META]
                ]);

                foreach (array_keys($require) as $innerRequired) {
                    $this->add((string)$innerRequired, ['tags' => [$scopeType]]);
                }

                foreach (array_keys($suggest) as $innerSuggested) {
                    $this->add((string)$innerSuggested, ['tags' => [$scopeType, Package::SUGGEST]]);
                }
            }
        }
    }

    /**
     * @param string $packageName
     * @param array  $packageMeta
     * @return Package
     */
    private function add(string $packageName, array $packageMeta): Package
    {
        $current = json($packageMeta);
        $packageAlias = Package::alias($packageName);

        /** @var Package $package */
        $package = $this->collection[$packageAlias] ?? new Package($packageName, $this->vendorDir);

        $package
            ->setVersion((string)$current->get('version'))
            ->addRequire((array)$current->get('require'))
            ->addRequireDev((array)$current->get('require-dev'))
            ->addSuggest((array)$current->get('suggest'))
            ->addTags((array)$current->get('tags'));

        $this->collection[$packageAlias] = $package;

        return $package;
    }

    /**
     * @return Package
     */
    public function getMain(): Package
    {
        foreach ($this->collection as $package) {
            if ($package->isMain()) {
                return $package;
            }
        }

        throw new Exception('Main package not found');
    }

    /**
     * @param string $packageName
     * @return Package
     */
    public function getByName(string $packageName): Package
    {
        $packageAlias = Package::alias($packageName);
        if (array_key_exists($packageAlias, $this->collection)) {
            return $this->collection[$packageAlias];
        }

        throw new Exception("Package \"{$packageName} ({$packageAlias})\" not found in collection");
    }
}
