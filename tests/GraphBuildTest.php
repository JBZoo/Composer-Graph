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

/**
 * Class GraphBuildTest
 *
 * @package JBZoo\PHPUnit
 */
class GraphBuildTest extends PHPUnit
{
    public function testDefault()
    {
        $result = CliRunner::task('build');
        isContain('Report is ready: ./build/jbzoo-composer-graph.html', $result);
    }

    public function testSelfMinimal()
    {
        $output = PROJECT_ROOT . '/build/' . __FUNCTION__ . '.html';

        $result = CliRunner::task('build -vvv --no-php --no-ext --no-dev', [
            'output'       => $output,
            'link-version' => 'false',
            'lib-version'  => 'false',
        ]);

        isContain('Report is ready', $result);
    }

    public function testEmpty()
    {
        $output = PROJECT_ROOT . '/build/' . __FUNCTION__ . '.html';
        $fixturesPath = PROJECT_ROOT . '/tests/fixtures/' . __FUNCTION__;

        $result = CliRunner::task('build', [
            'composer-json' => "{$fixturesPath}/composer.json",
            'composer-lock' => "{$fixturesPath}/composer.lock",
            'output'        => $output
        ]);

        isContain('Report is ready', $result);

        isContain(implode("\n", [
            'graph LR;',
            '    subgraph "Your package"',
            '        a21583cedf3174136523a39c2b1c715b("test/main");',
            '    end',
            'linkStyle default interpolate basis;',
        ]), file_get_contents($output));
    }

    public function testSimpleDeps()
    {
        $output = PROJECT_ROOT . '/build/' . __FUNCTION__ . '.html';
        $fixturesPath = PROJECT_ROOT . '/tests/fixtures/' . __FUNCTION__;

        $result = CliRunner::task('build', [
            'composer-json' => "{$fixturesPath}/composer.json",
            'composer-lock' => "{$fixturesPath}/composer.lock",
            'output'        => $output
        ]);

        isContain('Report is ready', $result);

        isContain(implode("\n", [
            'graph LR;',
            '    subgraph "Your package"',
            '        a21583cedf3174136523a39c2b1c715b("test/main");',
            '    end',
            '    subgraph "Required"',
            '        e74cdd8ebfa8918921215b9c2bb2044d("jbzoo/assets@3.0.x-dev");',
            '        a21583cedf3174136523a39c2b1c715b == "4.0.x-dev" ==> e74cdd8ebfa8918921215b9c2bb2044d;',
            '    end',
            'linkStyle default interpolate basis;',
        ]), file_get_contents($output));
    }

    public function testNestedReq()
    {
        $output = PROJECT_ROOT . '/build/' . __FUNCTION__ . '.html';
        $fixturesPath = PROJECT_ROOT . '/tests/fixtures/' . __FUNCTION__;

        $result = CliRunner::task('build', [
            'composer-json' => "{$fixturesPath}/composer.json",
            'composer-lock' => "{$fixturesPath}/composer.lock",
            'output'        => $output
        ]);

        isContain('Report is ready', $result);

        isContain(implode("\n", [
            'graph LR;',
            '    subgraph "Your package"',
            '        a21583cedf3174136523a39c2b1c715b("test/main");',
            '    end',
            '    subgraph "Required"',
            '        33e43173c7d1f8ec43e3537370f3eb50("jbzoo/data@4.0.x-dev");',
            '        e74cdd8ebfa8918921215b9c2bb2044d("jbzoo/assets@3.0.x-dev");',
            '        a21583cedf3174136523a39c2b1c715b == "4.0.x-dev" ==> e74cdd8ebfa8918921215b9c2bb2044d;',
            '        e74cdd8ebfa8918921215b9c2bb2044d-. "4.0.x-dev" .-> 33e43173c7d1f8ec43e3537370f3eb50;',
            '    end',
            '    subgraph "PHP Platform"',
            '        4d48b207cdf39d7efea6cb77e287a47d("ext-json");',
            '        e1bfd762321e409cee4ac0b6e841963c("php");',
            '        33e43173c7d1f8ec43e3537370f3eb50-. "^7.2" .-> e1bfd762321e409cee4ac0b6e841963c;',
            '        33e43173c7d1f8ec43e3537370f3eb50-.->4d48b207cdf39d7efea6cb77e287a47d;',
            '        e74cdd8ebfa8918921215b9c2bb2044d-. ">=7.2" .-> e1bfd762321e409cee4ac0b6e841963c;',
            '    end',
            'linkStyle default interpolate basis;',
        ]), file_get_contents($output));
    }

    public function testNestedReqAndDev()
    {
        $output = PROJECT_ROOT . '/build/' . __FUNCTION__ . '.html';
        $fixturesPath = PROJECT_ROOT . '/tests/fixtures/' . __FUNCTION__;

        $result = CliRunner::task('build', [
            'composer-json' => "{$fixturesPath}/composer.json",
            'composer-lock' => "{$fixturesPath}/composer.lock",
            'output'        => $output
        ]);

        isContain('Report is ready', $result);

        isContain(implode("\n", [
            'graph LR;',
            '    subgraph "Your package"',
            '        a21583cedf3174136523a39c2b1c715b("test/main");',
            '    end',
            '    subgraph "Required"',
            '        33e43173c7d1f8ec43e3537370f3eb50("jbzoo/data@4.0.x-dev");',
            '        e74cdd8ebfa8918921215b9c2bb2044d("jbzoo/assets@3.0.x-dev");',
            '        a21583cedf3174136523a39c2b1c715b == "4.0.x-dev" ==> 33e43173c7d1f8ec43e3537370f3eb50;',
            '        a21583cedf3174136523a39c2b1c715b == "4.0.x-dev" ==> e74cdd8ebfa8918921215b9c2bb2044d;',
            '        e74cdd8ebfa8918921215b9c2bb2044d-. "4.0.x-dev" .-> 33e43173c7d1f8ec43e3537370f3eb50;',
            '    end',
            '    subgraph "PHP Platform"',
            '        4d48b207cdf39d7efea6cb77e287a47d("ext-json");',
            '        e1bfd762321e409cee4ac0b6e841963c("php");',
            '        33e43173c7d1f8ec43e3537370f3eb50-. "^7.2" .-> e1bfd762321e409cee4ac0b6e841963c;',
            '        33e43173c7d1f8ec43e3537370f3eb50-.->4d48b207cdf39d7efea6cb77e287a47d;',
            '        e74cdd8ebfa8918921215b9c2bb2044d-. ">=7.2" .-> e1bfd762321e409cee4ac0b6e841963c;',
            '    end',
            'linkStyle default interpolate basis;',
        ]), file_get_contents($output));
    }

    public function testComplex()
    {
        $output = PROJECT_ROOT . '/build/' . __FUNCTION__ . '.html';
        $fixturesPath = PROJECT_ROOT . '/tests/fixtures/' . __FUNCTION__;

        $result = CliRunner::task('build', [
            'composer-json' => "{$fixturesPath}/composer.json",
            'composer-lock' => "{$fixturesPath}/composer.lock",
            'output'        => $output
        ]);

        isContain('Report is ready', $result);

        isContain(implode("\n", [
            'graph LR;',
            '    subgraph "Your package"',
            '        a21583cedf3174136523a39c2b1c715b("test/main");',
            '    end',
            '    subgraph "Required"',
            '        33e43173c7d1f8ec43e3537370f3eb50("jbzoo/data@4.0.x-dev");',
            '        e74cdd8ebfa8918921215b9c2bb2044d("jbzoo/assets@3.0.x-dev");',
            '        a21583cedf3174136523a39c2b1c715b == "4.0.x-dev" ==> 33e43173c7d1f8ec43e3537370f3eb50;',
            '        a21583cedf3174136523a39c2b1c715b == "4.0.x-dev" ==> e74cdd8ebfa8918921215b9c2bb2044d;',
            '        e74cdd8ebfa8918921215b9c2bb2044d-. "4.0.x-dev" .-> 33e43173c7d1f8ec43e3537370f3eb50;',
            '    end',
            '    subgraph "Required Dev"',
            '        b639e1b42e0f7a49ac0e6eec145977b3("jbzoo/event@3.0.x-dev");',
            '        a21583cedf3174136523a39c2b1c715b-. "3.0.x-dev" .-> b639e1b42e0f7a49ac0e6eec145977b3;',
            '    end',
            '    subgraph "PHP Platform"',
            '        4d48b207cdf39d7efea6cb77e287a47d("ext-json");',
            '        e1bfd762321e409cee4ac0b6e841963c("php");',
            '        33e43173c7d1f8ec43e3537370f3eb50-. "^7.2" .-> e1bfd762321e409cee4ac0b6e841963c;',
            '        33e43173c7d1f8ec43e3537370f3eb50-.->4d48b207cdf39d7efea6cb77e287a47d;',
            '        b639e1b42e0f7a49ac0e6eec145977b3-. ">=7.2" .-> e1bfd762321e409cee4ac0b6e841963c;',
            '        e74cdd8ebfa8918921215b9c2bb2044d-. ">=7.2" .-> e1bfd762321e409cee4ac0b6e841963c;',
            '    end',
            'linkStyle default interpolate basis;',
        ]), file_get_contents($output));
    }

    public function testComplexMinimalOutput()
    {
        $output = PROJECT_ROOT . '/build/' . __FUNCTION__ . '.html';
        $fixturesPath = PROJECT_ROOT . '/tests/fixtures/' . __FUNCTION__;

        $result = CliRunner::task('build -vvv --no-php --no-ext --no-dev', [
            'composer-json' => "{$fixturesPath}/composer.json",
            'composer-lock' => "{$fixturesPath}/composer.lock",
            'output'        => $output,
            'link-version'  => 'false',
            'lib-version'   => 'false',
        ]);

        isContain('Report is ready', $result);

        isContain(implode("\n", [
            'graph LR;',
            '    subgraph "Your package"',
            '        a21583cedf3174136523a39c2b1c715b("test/main");',
            '    end',
            '    subgraph "Required"',
            '        33e43173c7d1f8ec43e3537370f3eb50("jbzoo/data");',
            '        e74cdd8ebfa8918921215b9c2bb2044d("jbzoo/assets");',
            '        a21583cedf3174136523a39c2b1c715b ==> e74cdd8ebfa8918921215b9c2bb2044d;',
            '        e74cdd8ebfa8918921215b9c2bb2044d-.->33e43173c7d1f8ec43e3537370f3eb50;',
            '    end',
            'linkStyle default interpolate basis;',
        ]), file_get_contents($output));
    }

    public function testRealProject()
    {
        $fixturesPath = PROJECT_ROOT . '/tests/fixtures/' . __FUNCTION__;

        $output = PROJECT_ROOT . '/build/' . __FUNCTION__ . '_full.html';
        $result = CliRunner::task('build', [
            'composer-json' => "{$fixturesPath}/composer.json",
            'composer-lock' => "{$fixturesPath}/composer.lock",
            'output'        => $output,
        ]);
        isContain('jbzoo/toolbox - Graph of Dependencies', file_get_contents($output));
        isContain('Report is ready', $result);

        $output = PROJECT_ROOT . '/build/' . __FUNCTION__ . '_min-dev.html';
        $result = CliRunner::task('build -vvv --no-php --no-ext', [
            'composer-json' => "{$fixturesPath}/composer.json",
            'composer-lock' => "{$fixturesPath}/composer.lock",
            'output'        => $output,
            'link-version'  => 'false',
            'lib-version'   => 'false',
        ]);
        isContain('jbzoo/toolbox - Graph of Dependencies', file_get_contents($output));
        isContain('Report is ready', $result);

        $output = PROJECT_ROOT . '/build/' . __FUNCTION__ . '_min_no-dev.html';
        $result = CliRunner::task('build -vvv --no-php --no-ext --no-dev', [
            'composer-json' => "{$fixturesPath}/composer.json",
            'composer-lock' => "{$fixturesPath}/composer.lock",
            'output'        => $output,
            'link-version'  => 'false',
            'lib-version'   => 'false',
        ]);
        isContain('jbzoo/toolbox - Graph of Dependencies', file_get_contents($output));
        isContain('Report is ready', $result);
    }
}
