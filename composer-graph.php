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

use JBZoo\ComposerGraph\CommandBuild;
use Symfony\Component\Console\Application;

define('PATH_ROOT', __DIR__);

$vendorPaths = [
    __DIR__ . '/../../autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/vendor/autoload.php'
];

foreach ($vendorPaths as $file) {
    if (file_exists($file)) {
        define('JBZOO_COMPOSER_GRAPH', $file);
        break;
    }
}

require JBZOO_COMPOSER_GRAPH;

$application = new Application('JBZoo/Composer-Graph', '@git-version@');
$application->add(new CommandBuild());
$application->setDefaultCommand('build');
$application->run();
