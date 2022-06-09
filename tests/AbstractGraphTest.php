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

namespace JBZoo\PHPUnit;

use JBZoo\Cli\CliApplication;
use JBZoo\ComposerGraph\CommandBuild;
use JBZoo\ComposerGraph\Commands\Build;
use JBZoo\Utils\Cli;
use JBZoo\Utils\Str;
use JBZoo\Utils\Sys;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class AbstractGraphTest
 *
 * @package JBZoo\PHPUnit
 */
abstract class AbstractGraphTest extends PHPUnit
{
    /**
     * @param array  $params
     * @param string $fixture
     * @return string
     */
    protected function buildGraph(array $params = [], string $fixture = ''): string
    {
        $fixture = $fixture ?: Str::getClassName(static::class);

        $testName = getTestName();
        $output = PROJECT_ROOT . "/build/{$testName}.html";
        $root = PROJECT_ROOT . "/tests/fixtures/{$fixture}";

        $params = array_merge([
            'root'           => $root,
            'output'         => $output,
            'abc-order'      => null,
            'ansi'           => null,
            'no-interaction' => null,
        ], $params);

        isFile("{$root}/composer.json", "JSON file not found.");
        isFile("{$root}/composer.lock", "LOCK file not found.");

        $cliOutput = $this->task(array_merge($params, ['format' => 'html']));
        isFile($output, "HTML file not found. Output: {$cliOutput}");

        return trim($this->task(array_merge($params, ['format' => 'mermaid'])));
    }

    /**
     * @param array $params
     * @return string
     * @throws \Exception
     */
    public function task(array $params = []): string
    {
        $params['--no-ansi'] = null;

        $application = new CliApplication();
        $application->add(new Build());
        $application->setDefaultCommand('build');
        $command = $application->find('build');

        $buffer = new BufferedOutput();
        $args = new StringInput(Cli::build('', $params));
        $code = $command->run($args, $buffer);

        if ($code > 0) {
            throw new \RuntimeException($buffer->fetch());
        }

        return $buffer->fetch();
    }

    /**
     * @param array $params
     * @return string
     */
    public function taskReal(array $params = []): string
    {
        $rootDir = PROJECT_ROOT;
        $params['--no-ansi'] = null;

        return Cli::exec(
            implode(' ', [
                Sys::getBinary(),
                "{$rootDir}/composer-graph.php",
            ]),
            $params,
            $rootDir,
            false
        );
    }
}
