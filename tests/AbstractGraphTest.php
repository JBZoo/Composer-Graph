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

namespace JBZoo\PHPUnit;

use JBZoo\Utils\Cli;
use JBZoo\Utils\Str;
use JBZoo\Utils\Sys;

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

        $params = array_merge([
            'composer-json' => PROJECT_ROOT . "/tests/fixtures/{$fixture}/composer.json",
            'composer-lock' => PROJECT_ROOT . "/tests/fixtures/{$fixture}/composer.lock",
            'output'        => $output,
            'ansi'          => null,
        ], $params);

        isFile($params['composer-json'], "JSON file not found: {$params['composer-json']}");
        isFile($params['composer-lock'], "LOCK file not found: {$params['composer-lock']}");

        $cliOutput = $this->task('', array_merge($params, ['format' => 'html']));
        isFile($output, "HTML file not found. Output: {$cliOutput}");

        $result = trim($this->task('', array_merge($params, ['format' => 'mermaid'])));

        //$lines = explode("\n", $result);
        //foreach ($lines as $line) {
        //    Cli::out("'{$line}',");
        //}

        return $result;
    }

    /**
     * @param string $taskName
     * @param array  $params
     * @return string
     */
    public function task($taskName = '', array $params = [])
    {
        $rootDir = PROJECT_ROOT;

        return Cli::exec(
            implode(' ', [
                Sys::getBinary(),
                "{$rootDir}/tests/cli-wrapper.php",
                $taskName,
                '--no-interaction'
            ]),
            $params,
            $rootDir,
            0
        );
    }
}
