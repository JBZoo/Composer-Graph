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

use JBZoo\MermaidPHP\Graph;
use JBZoo\Utils\Sys;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function JBZoo\Data\json;

/**
 * Class Build
 * @package JBZoo\ComposerGraph\Commands
 */
class CommandBuild extends Command
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
            ->addOption('format', null, $required, 'Output format. Available options: <info>' . implode(',', [
                    ComposerGraph::FORMAT_HTML,
                    ComposerGraph::FORMAT_MERMAID,
                ]) . '</info>', ComposerGraph::FORMAT_HTML)
            ->addOption('direction', null, $required, 'Direction of graph. Available options: <info>' . implode(',', [
                    Graph::LEFT_RIGHT,
                    Graph::TOP_BOTTOM,
                    Graph::BOTTOM_TOP,
                    Graph::RIGHT_LEFT,
                ]) . '</info>', Graph::LEFT_RIGHT)
            ->addOption('show-php', null, $none, 'Show PHP-node')
            ->addOption('show-ext', null, $none, 'Show all ext-* nodes')
            ->addOption('show-dev', null, $none, 'Show all dev dependencies')
            ->addOption('show-suggests', null, $none, 'Show not installed suggests packages')
            ->addOption('show-link-versions', null, $none, 'Show version requirements in links')
            ->addOption('show-lib-versions', null, $none, 'Show version of packages');
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

        $result = (new ComposerGraph($collection, [
            'direction'    => $direction,
            'php'          => $input->getOption('show-php'),
            'ext'          => $input->getOption('show-ext'),
            'dev'          => $input->getOption('show-dev'),
            'suggest'      => $input->getOption('show-suggests'),
            'link-version' => $input->getOption('show-link-versions'),
            'lib-version'  => $input->getOption('show-lib-versions'),
            'format'       => $input->getOption('format'),
            'output-path'  => $input->getOption('output'),
        ]))->build();

        $output->writeln($result);

        $totalTime = number_format(microtime(true) - $startTimer, 2);
        $maxMemory = Sys::getMemory();

        if ($output->isDebug()) {
            $output->writeln("Time: <info>{$totalTime} sec</info>; Peak Memory: <info>{$maxMemory}</info>;\n");
        }

        return 0;
    }
}
