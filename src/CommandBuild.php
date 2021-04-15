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
use JBZoo\MermaidPHP\Graph;
use JBZoo\Utils\Sys;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function JBZoo\Data\json;

/**
 * Class CommandBuild
 * @package JBZoo\ComposerGraph
 */
class CommandBuild extends Command
{
    /**
     * @var InputInterface
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private $input;

    /**
     * @var OutputInterface
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private $output;

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $required = InputOption::VALUE_REQUIRED;
        $none = InputOption::VALUE_NONE;

        if (!defined('IS_PHPUNIT_TEST')) {
            define('IS_PHPUNIT_TEST', false);
        }

        $this
            ->setName('build')
            ->addOption('root', 'r', $required, 'The path has to contain ' .
                '"composer.json" and "composer.lock" files', './')
            ->addOption('output', 'o', $required, 'Path to html output.', './build/composer-graph.html')
            ->addOption('format', 'f', $required, 'Output format. Available options: <info>' . implode(',', [
                    ComposerGraph::FORMAT_HTML,
                    ComposerGraph::FORMAT_MERMAID,
                ]) . '</info>', ComposerGraph::FORMAT_HTML)
            ->addOption('direction', 'D', $required, 'Direction of graph. Available options: <info>' . implode(',', [
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
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTimer = microtime(true);

        $this->input = $input;
        $this->output = $output;

        $format = $input->getOption('format');

        [$composerJson, $composerLock] = $this->getJsonData();
        $vendorDir = $this->findVendorDir($composerJson);

        $composerGraph = new ComposerGraph(
            new Collection($composerJson, $composerLock, $vendorDir),
            [
                'direction'    => $input->getOption('direction') ?: Graph::LEFT_RIGHT,
                'php'          => $input->getOption('show-php'),
                'ext'          => $input->getOption('show-ext'),
                'dev'          => $input->getOption('show-dev'),
                'suggest'      => $input->getOption('show-suggests'),
                'link-version' => $input->getOption('show-link-versions'),
                'lib-version'  => $input->getOption('show-package-versions'),
                'format'       => $format,
                'output-path'  => $input->getOption('output'),
                'abc-order'    => $input->getOption('abc-order'),
            ]
        );

        $result = $composerGraph->build();
        if (ComposerGraph::FORMAT_HTML === $format) {
            $output->writeln("Report is ready: <info>{$result}</info>");
        } else {
            $output->writeln($result);
        }

        $totalTime = number_format(microtime(true) - $startTimer, 2);
        $maxMemory = Sys::getMemory();

        if ($output->isDebug()) {
            $output->writeln("Time: <info>{$totalTime} sec</info>; Peak Memory: <info>{$maxMemory}</info>;\n");
        }

        return 0;
    }

    /**
     * @return string
     */
    private function getRootPath(): string
    {
        /** @var string $origRootPath */
        $origRootPath = $this->input->getOption('root');

        /** @phan-suppress-next-line PhanPartialTypeMismatchArgumentInternal */
        $realRootPath = realpath($origRootPath);

        // Validate root path
        if (!$realRootPath || !is_dir($realRootPath)) {
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
        if (!file_exists($composerJsonPath)) {
            throw new Exception("The file \"{$composerJsonPath}\" not found");
        }

        $composerJson = json($composerJsonPath);
        if (count($composerJson) <= 1) {
            throw new Exception("The file \"{$composerJsonPath}\" is empty");
        }

        if ($this->output->isDebug()) {
            $this->output->writeln("Composer JSON file found: <info>{$composerJsonPath}</info>");
        }


        // Validate "composer.lock" path and file
        $composerLockPath = "{$realRootPath}/composer.lock";
        if (!file_exists($composerLockPath)) {
            throw new Exception("The file \"{$composerLockPath}\" not found");
        }

        $composerLock = json($composerLockPath);
        if (count($composerLock) <= 1) {
            throw new Exception("The file \"{$composerLockPath}\" is empty");
        }

        if ($this->output->isDebug()) {
            $this->output->writeln("Composer JSON file found: <info>{$composerLockPath}</info>");
        }


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

        $realVendorDir = realpath("{$realRootPath}/{$vendorDir}");
        if ($realVendorDir && is_dir($realVendorDir)) {
            return $realVendorDir;
        }

        return null;
    }
}
