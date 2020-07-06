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
use JBZoo\Utils\Sys;

/**
 * Class CliRunner
 * @package JBZoo\PHPUnit
 */
class CliRunner
{
    /**
     * @param string $taskName
     * @param array  $params
     * @return string
     */
    public static function task($taskName = '', array $params = [])
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
