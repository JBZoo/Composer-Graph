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

namespace JBZoo\ComposerGraph\Commands;

use JBZoo\ComposerGraph\Collection;
use JBZoo\ComposerGraph\ComposerGraph;
use JBZoo\MermaidPHP\Graph;
use JBZoo\Utils\FS;
use JBZoo\Utils\Sys;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function JBZoo\Data\json;
use function JBZoo\Utils\bool;

/**
 * Class Build
 * @package JBZoo\ComposerGraph\Commands
 */
class Build extends Command
{
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
            ->addOption('composer-json', null, $required, 'Path to composer.json file', './composer.json')
            ->addOption('composer-lock', null, $required, 'Path to composer.lock file', './composer.lock')
            ->addOption('output', null, $required, 'Path to html output.', './build/jbzoo-composer-graph.html')
            ->addOption('no-php', null, $none, 'Exclude PHP')
            ->addOption('no-ext', null, $none, 'Exclude all ext-* nodes')
            ->addOption('no-dev', null, $none, 'Exclude dev requirements')
            ->addOption('no-suggest', null, $none, 'Exclude suggested requirements')
            ->addOption('link-version', null, $required, 'Show version requirements in link', 'true')
            ->addOption('lib-version', null, $required, 'Show version of package', 'true')
            ->addOption('direction', null, $required, 'Direction of graph. Available <info>' . implode(',', [
                    Graph::LEFT_RIGHT,
                    Graph::TOP_BOTTOM,
                    Graph::BOTTOM_TOP,
                    Graph::RIGHT_LEFT,
                ]) . '</info>', Graph::LEFT_RIGHT);
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTimer = microtime(true);

        $composerJson = json($input->getOption('composer-json'));
        $composerLock = json($input->getOption('composer-lock'));
        $direction = $input->getOption('direction') ?: Graph::LEFT_RIGHT;

        $collection = new Collection($composerJson, $composerLock);

        $htmlOutputPath = (new ComposerGraph($collection, [
            'direction'    => $direction,
            'php'          => !$input->getOption('no-php'),
            'ext'          => !$input->getOption('no-ext'),
            'dev'          => !$input->getOption('no-dev'),
            'suggest'      => !$input->getOption('no-suggest'),
            'link-version' => bool($input->getOption('link-version')),
            'lib-version'  => bool($input->getOption('lib-version')),
            'output-path'  => $input->getOption('output'),
        ]))->build();

        $htmlOutputPath = './' . FS::getRelative($htmlOutputPath);
        $output->writeln("Report is ready: <comment>{$htmlOutputPath}</comment>");

        $totalTime = number_format(microtime(true) - $startTimer, 2);
        $maxMemory = Sys::getMemory();

        $output->writeln("Total Time: <info>{$totalTime} sec</info>; " .
            "Peak Memory: <info>{$maxMemory}</info>;\n");

        return 0;
    }
}