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

namespace JBZoo\ComposerGraph\Commands;

use JBZoo\Cli\Cli;
use JBZoo\Cli\CliCommand;
use JBZoo\Cli\Codes;
use JBZoo\ComposerGraph\Collection;
use JBZoo\ComposerGraph\ComposerGraph;
use JBZoo\Data\JSON;
use JBZoo\MermaidPHP\Graph;
use Symfony\Component\Console\Input\InputOption;

use function JBZoo\Data\json;

/**
 * Class Build
 * @package JBZoo\ComposerGraph\Commands
 */
class Build extends CliCommand
{
    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $required = InputOption::VALUE_REQUIRED;
        $none = InputOption::VALUE_NONE;

        if (!\defined('\IS_PHPUNIT_TEST')) {
            \define('\IS_PHPUNIT_TEST', false);
        }

        $this
            ->setName('build')
            ->addOption('root', 'r', $required, 'The path has to contain ' .
                '"composer.json" and "composer.lock" files', './')
            ->addOption('output', 'o', $required, 'Path to html output.', './build/composer-graph.html')
            ->addOption('format', 'f', $required, 'Output format. Available options: <info>' . \implode(',', [
                    ComposerGraph::FORMAT_HTML,
                    ComposerGraph::FORMAT_MERMAID,
                ]) . '</info>', ComposerGraph::FORMAT_HTML)
            ->addOption('direction', 'D', $required, 'Direction of graph. Available options: <info>' . \implode(',', [
                    Graph::LEFT_RIGHT,
                    Graph::TOP_BOTTOM,
                    Graph::BOTTOM_TOP,
                    Graph::RIGHT_LEFT,
                ]) . '</info>', Graph::LEFT_RIGHT)
            ->addOption('show-php', 'p', $none, 'Show PHP-node')
            ->addOption('show-ext', 'e', $none, 'Show all ext-* nodes (PHP modules)')
            ->addOption('show-dev', 'd', $none, 'Show all dev dependencies')
            ->addOption('show-suggests', 's', $none, 'Show not installed suggests packages')
            ->addOption('show-link-versions', 'l', $none, 'Show version requirements in links')
            ->addOption('show-package-versions', 'P', $none, 'Show version of packages')
            ->addOption('abc-order', 'O', $none, 'Strict ABC ordering nodes in graph. ' .
                "It's fine tuning, sometimes it useful.");

        parent::configure();
    }

    /**
     * @inheritDoc
     */
    protected function executeAction(): int
    {
        $format = $this->getOptString('format');

        [$composerJson, $composerLock] = $this->getJsonData();
        $vendorDir = $this->findVendorDir($composerJson);

        $composerGraph = new ComposerGraph(
            new Collection($composerJson, $composerLock, $vendorDir),
            [
                'direction'    => $this->getOptString('direction') ?: Graph::LEFT_RIGHT,
                'php'          => $this->getOptBool('show-php'),
                'ext'          => $this->getOptBool('show-ext'),
                'dev'          => $this->getOptBool('show-dev'),
                'suggest'      => $this->getOptBool('show-suggests'),
                'link-version' => $this->getOptBool('show-link-versions'),
                'lib-version'  => $this->getOptBool('show-package-versions'),
                'format'       => $format,
                'output-path'  => $this->getOptString('output'),
                'abc-order'    => $this->getOptBool('abc-order'),
            ]
        );

        $result = $composerGraph->build();
        if (ComposerGraph::FORMAT_HTML === $format) {
            $this->_("Report is ready: <info>{$result}</info>");
        } else {
            $this->_($result);
        }

        return Codes::OK;
    }

    /**
     * @return string
     */
    private function getRootPath(): string
    {
        $origRootPath = $this->getOptString('root');
        $realRootPath = \realpath($origRootPath);

        // Validate root path
        if (!$realRootPath || !\is_dir($realRootPath)) {
            throw new Exception("Root path is not directory or not found: {$origRootPath}");
        }

        return $realRootPath;
    }

    /**
     * @return JSON[]
     */
    private function getJsonData(): array
    {
        $realRootPath = $this->getRootPath();

        // Validate "composer.json" path and file
        $composerJsonPath = "{$realRootPath}/composer.json";
        if (!\file_exists($composerJsonPath)) {
            throw new Exception("The file \"{$composerJsonPath}\" not found");
        }

        $composerJson = json($composerJsonPath);
        if (\count($composerJson) <= 1) {
            throw new Exception("The file \"{$composerJsonPath}\" is empty");
        }

        $this->_("Composer JSON file found: <info>{$composerJsonPath}</info>", Cli::DEBUG);

        // Validate "composer.lock" path and file
        $composerLockPath = "{$realRootPath}/composer.lock";
        if (!\file_exists($composerLockPath)) {
            throw new Exception("The file \"{$composerLockPath}\" not found");
        }

        $composerLock = json($composerLockPath);
        if (\count($composerLock) <= 1) {
            throw new Exception("The file \"{$composerLockPath}\" is empty");
        }

        $this->_("Composer LOCK file found: <info>{$composerLockPath}</info>", Cli::DEBUG);

        return [$composerJson, $composerLock];
    }

    /**
     * @param JSON $composerJson
     * @return string|null
     */
    private function findVendorDir(JSON $composerJson): ?string
    {
        $realRootPath = $this->getRootPath();

        $vendorDir = $composerJson->find('config.vendor-dir') ?? 'vendor';

        $realVendorDir = \realpath("{$realRootPath}/{$vendorDir}");
        if ($realVendorDir && \is_dir($realVendorDir)) {
            return $realVendorDir;
        }

        return null;
    }
}
