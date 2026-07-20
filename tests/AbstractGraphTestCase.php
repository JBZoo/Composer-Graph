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

namespace JBZoo\PHPUnit;

use JBZoo\Cli\CliApplication;
use JBZoo\ComposerGraph\Commands\Build;
use JBZoo\Utils\Cli;
use JBZoo\Utils\Str;
use JBZoo\Utils\Sys;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

abstract class AbstractGraphTestCase extends PHPUnit
{
    public function task(array $params = []): string
    {
        $params['--no-ansi'] = null;

        $application = new CliApplication();
        // Symfony Console 8.0 removed Application::add(); addCommands() exists on 7.3+/8.x.
        $application->addCommands([new Build()]);
        $application->setDefaultCommand('build');
        $command = $application->find('build');

        $buffer = new BufferedOutput();
        $args   = new StringInput(Cli::build('', $params));
        $code   = $command->run($args, $buffer);

        if ($code > 0) {
            throw new \RuntimeException($buffer->fetch());
        }

        return $buffer->fetch();
    }

    public function taskReal(array $params = []): string
    {
        $rootDir             = PROJECT_ROOT;
        $params['--no-ansi'] = null;

        return Cli::exec(
            \implode(' ', [
                // -d error_reporting mutes E_DEPRECATED in the child: the --prefer-lowest CI leg pulls
                // old transitive dev-tool deps (amphp/*, sabre/event via php-coveralls) whose files-
                // autoload emits "implicitly nullable" deprecations on PHP 8.5+ that Cli::exec captures
                // into the report/help output. Production (the phar or a no-dev install) never loads them.
                Sys::getBinary() . ' -d error_reporting=' . (\E_ALL & ~\E_DEPRECATED),
                "{$rootDir}/composer-graph.php",
            ]),
            $params,
            $rootDir,
            false,
        );
    }

    protected function buildGraph(array $params = [], string $fixture = ''): string
    {
        $fixture = $fixture ?: Str::getClassName(static::class);

        $testName = getTestName();
        $output   = PROJECT_ROOT . "/build/{$testName}.html";
        $root     = PROJECT_ROOT . "/tests/fixtures/{$fixture}";

        $params = \array_merge([
            'root'           => $root,
            'output'         => $output,
            'abc-order'      => null,
            'ansi'           => null,
            'no-interaction' => null,
        ], $params);

        isFile("{$root}/composer.json", 'JSON file not found.');
        isFile("{$root}/composer.lock", 'LOCK file not found.');

        $cliOutput = $this->task(\array_merge($params, ['format' => 'html']));
        isFile($output, "HTML file not found. Output: {$cliOutput}");

        return \trim($this->task(\array_merge($params, ['format' => 'mermaid'])));
    }
}
