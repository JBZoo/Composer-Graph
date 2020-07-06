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
    public function testHelpInReadme()
    {
        $result = CliRunner::task('--help');
        $readme = file_get_contents(PROJECT_ROOT . '/README.md');

        isContain($result, $readme);
    }

    public function testSelfMinimal()
    {
        $output = PROJECT_ROOT . '/build/' . __FUNCTION__ . '.html';

        $result = CliRunner::task('-vvv --no-php --no-ext --no-dev', [
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

        $result = CliRunner::task('', [
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
        ]), file_get_contents($output));
    }

    public function testSimpleDeps()
    {
        $output = PROJECT_ROOT . '/build/' . __FUNCTION__ . '.html';
        $fixturesPath = PROJECT_ROOT . '/tests/fixtures/' . __FUNCTION__;

        $result = CliRunner::task('', [
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
        ]), file_get_contents($output));
    }

    public function testNestedReq()
    {
        $output = PROJECT_ROOT . '/build/' . __FUNCTION__ . '.html';
        $fixturesPath = PROJECT_ROOT . '/tests/fixtures/' . __FUNCTION__;

        $result = CliRunner::task('', [
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
            '        e74cdd8ebfa8918921215b9c2bb2044d-->|"4.0.x-dev"|33e43173c7d1f8ec43e3537370f3eb50;',
            '    end',
            '    subgraph "PHP Platform"',
            '        4d48b207cdf39d7efea6cb77e287a47d("ext-json");',
            '        e1bfd762321e409cee4ac0b6e841963c("php");',
            '        33e43173c7d1f8ec43e3537370f3eb50-->4d48b207cdf39d7efea6cb77e287a47d;',
            '        33e43173c7d1f8ec43e3537370f3eb50-->|"^7.2"|e1bfd762321e409cee4ac0b6e841963c;',
            '        e74cdd8ebfa8918921215b9c2bb2044d-->|">=7.2"|e1bfd762321e409cee4ac0b6e841963c;',
            '    end',
        ]), file_get_contents($output));
    }

    public function testNestedReqAndDev()
    {
        $output = PROJECT_ROOT . '/build/' . __FUNCTION__ . '.html';
        $fixturesPath = PROJECT_ROOT . '/tests/fixtures/' . __FUNCTION__;

        $result = CliRunner::task('', [
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
            '        e74cdd8ebfa8918921215b9c2bb2044d-->|"4.0.x-dev"|33e43173c7d1f8ec43e3537370f3eb50;',
            '    end',
            '    subgraph "PHP Platform"',
            '        4d48b207cdf39d7efea6cb77e287a47d("ext-json");',
            '        e1bfd762321e409cee4ac0b6e841963c("php");',
            '        33e43173c7d1f8ec43e3537370f3eb50-->4d48b207cdf39d7efea6cb77e287a47d;',
            '        33e43173c7d1f8ec43e3537370f3eb50-->|"^7.2"|e1bfd762321e409cee4ac0b6e841963c;',
            '        e74cdd8ebfa8918921215b9c2bb2044d-->|">=7.2"|e1bfd762321e409cee4ac0b6e841963c;',
            '    end',
        ]), file_get_contents($output));
    }

    public function testComplex()
    {
        $output = PROJECT_ROOT . '/build/' . __FUNCTION__ . '.html';
        $fixturesPath = PROJECT_ROOT . '/tests/fixtures/' . __FUNCTION__;

        $result = CliRunner::task('', [
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
            '        e74cdd8ebfa8918921215b9c2bb2044d-->|"4.0.x-dev"|33e43173c7d1f8ec43e3537370f3eb50;',
            '    end',
            '    subgraph "Required Dev"',
            '        b639e1b42e0f7a49ac0e6eec145977b3("jbzoo/event@3.0.x-dev");',
            '        a21583cedf3174136523a39c2b1c715b == "3.0.x-dev" ==> b639e1b42e0f7a49ac0e6eec145977b3;',
            '    end',
            '    subgraph "PHP Platform"',
            '        4d48b207cdf39d7efea6cb77e287a47d("ext-json");',
            '        e1bfd762321e409cee4ac0b6e841963c("php");',
            '        33e43173c7d1f8ec43e3537370f3eb50-->4d48b207cdf39d7efea6cb77e287a47d;',
            '        33e43173c7d1f8ec43e3537370f3eb50-->|"^7.2"|e1bfd762321e409cee4ac0b6e841963c;',
            '        b639e1b42e0f7a49ac0e6eec145977b3-->|">=7.2"|e1bfd762321e409cee4ac0b6e841963c;',
            '        e74cdd8ebfa8918921215b9c2bb2044d-->|">=7.2"|e1bfd762321e409cee4ac0b6e841963c;',
            '    end',
        ]), file_get_contents($output));
    }

    public function testComplexMinimalOutput()
    {
        $output = PROJECT_ROOT . '/build/' . __FUNCTION__ . '.html';
        $fixturesPath = PROJECT_ROOT . '/tests/fixtures/' . __FUNCTION__;

        $result = CliRunner::task('-vvv --no-php --no-ext --no-dev', [
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
            '        e74cdd8ebfa8918921215b9c2bb2044d-->33e43173c7d1f8ec43e3537370f3eb50;',
            '    end',
        ]), file_get_contents($output));
    }

    public function testJBZooToolbox()
    {
        $output = PROJECT_ROOT . '/build/' . __FUNCTION__ . '.html';
        $fixturesPath = PROJECT_ROOT . '/tests/fixtures/' . __FUNCTION__;

        $result = CliRunner::task('', [
            'composer-json' => "{$fixturesPath}/composer.json",
            'composer-lock' => "{$fixturesPath}/composer.lock",
            'output'        => $output,
            'no-php'        => null,
            'no-ext'        => null,
            'no-dev'        => null,
            '-vvv'          => null,
        ]);

        isContain('Report is ready', $result);

        isContain(implode("\n", [
            'graph LR;',
            '    subgraph "Your package"',
            '        f06c5a752b1bc11a09b156111180758f("jbzoo/toolbox");',
            '    end',
            '    subgraph "Required"',
            '        33e43173c7d1f8ec43e3537370f3eb50("jbzoo/data@4.0.x-dev");',
            '        40a19d56bb68e2ffd5789d83252980c7("jbzoo/image@5.0.x-dev");',
            '        6bc45a25e39d6940172de595ef0edd62("wikimedia/less.php@v3.0.0");',
            '        8ac966e5122efcb564de8239109e01b3("jbzoo/http-client@3.0.x-dev");',
            '        b0ccab8b9f0997e1142c95d922a3e68b("jbzoo/utils@4.0.x-dev");',
            '        b639e1b42e0f7a49ac0e6eec145977b3("jbzoo/event@3.0.x-dev");',
            '        c9ad71e94635c4f947fb97fadc8692c3("jbzoo/path@3.0.x-dev");',
            '        c9e5fecbbf5b863056a58a6ae56c9f30("jbzoo/mermaid-php@2.0.x-dev");',
            '        e74cdd8ebfa8918921215b9c2bb2044d("jbzoo/assets@3.0.x-dev");',
            '        f25de7eb32948d383fe0acee3f2f839c("jbzoo/less@3.0.x-dev");',
            '        33e43173c7d1f8ec43e3537370f3eb50-. "suggest" .-> b0ccab8b9f0997e1142c95d922a3e68b;',
            '        40a19d56bb68e2ffd5789d83252980c7-->|"4.0.x-dev"|33e43173c7d1f8ec43e3537370f3eb50;',
            '        40a19d56bb68e2ffd5789d83252980c7-->|"4.0.x-dev"|b0ccab8b9f0997e1142c95d922a3e68b;',
            '        8ac966e5122efcb564de8239109e01b3-->|"4.0.x-dev"|33e43173c7d1f8ec43e3537370f3eb50;',
            '        8ac966e5122efcb564de8239109e01b3-. "suggest" .-> b639e1b42e0f7a49ac0e6eec145977b3;',
            '        b0ccab8b9f0997e1142c95d922a3e68b-. "suggest" .-> 33e43173c7d1f8ec43e3537370f3eb50;',
            '        c9ad71e94635c4f947fb97fadc8692c3-->|"4.0.x-dev"|33e43173c7d1f8ec43e3537370f3eb50;',
            '        c9ad71e94635c4f947fb97fadc8692c3-->|"4.0.x-dev"|b0ccab8b9f0997e1142c95d922a3e68b;',
            '        e74cdd8ebfa8918921215b9c2bb2044d-->|"3.0.x-dev"|c9ad71e94635c4f947fb97fadc8692c3;',
            '        e74cdd8ebfa8918921215b9c2bb2044d-->|"3.0.x-dev"|f25de7eb32948d383fe0acee3f2f839c;',
            '        e74cdd8ebfa8918921215b9c2bb2044d-->|"4.0.x-dev"|33e43173c7d1f8ec43e3537370f3eb50;',
            '        e74cdd8ebfa8918921215b9c2bb2044d-->|"4.0.x-dev"|b0ccab8b9f0997e1142c95d922a3e68b;',
            '        f06c5a752b1bc11a09b156111180758f == "2.0.x-dev" ==> c9e5fecbbf5b863056a58a6ae56c9f30;',
            '        f06c5a752b1bc11a09b156111180758f == "3.0.x-dev" ==> 8ac966e5122efcb564de8239109e01b3;',
            '        f06c5a752b1bc11a09b156111180758f == "3.0.x-dev" ==> b639e1b42e0f7a49ac0e6eec145977b3;',
            '        f06c5a752b1bc11a09b156111180758f == "3.0.x-dev" ==> c9ad71e94635c4f947fb97fadc8692c3;',
            '        f06c5a752b1bc11a09b156111180758f == "3.0.x-dev" ==> e74cdd8ebfa8918921215b9c2bb2044d;',
            '        f06c5a752b1bc11a09b156111180758f == "3.0.x-dev" ==> f25de7eb32948d383fe0acee3f2f839c;',
            '        f06c5a752b1bc11a09b156111180758f == "4.0.x-dev" ==> 33e43173c7d1f8ec43e3537370f3eb50;',
            '        f06c5a752b1bc11a09b156111180758f == "4.0.x-dev" ==> b0ccab8b9f0997e1142c95d922a3e68b;',
            '        f06c5a752b1bc11a09b156111180758f == "5.0.x-dev" ==> 40a19d56bb68e2ffd5789d83252980c7;',
            '        f25de7eb32948d383fe0acee3f2f839c-->|"4.0.x-dev"|33e43173c7d1f8ec43e3537370f3eb50;',
            '        f25de7eb32948d383fe0acee3f2f839c-->|"4.0.x-dev"|b0ccab8b9f0997e1142c95d922a3e68b;',
            '        f25de7eb32948d383fe0acee3f2f839c-->|"^3.0.0"|6bc45a25e39d6940172de595ef0edd62;',
            '    end',
        ]), file_get_contents($output));
    }

    public function testJBZooToolboxNoSuggest()
    {
        $output = PROJECT_ROOT . '/build/testJBZooToolbox-no-suggest.html';
        $fixturesPath = PROJECT_ROOT . '/tests/fixtures/testJBZooToolbox';

        $result = CliRunner::task('', [
            'composer-json' => "{$fixturesPath}/composer.json",
            'composer-lock' => "{$fixturesPath}/composer.lock",
            'output'        => $output,
            'no-dev'        => null,
            '-vvv'          => null,
        ]);

        isContain('Report is ready', $result);

        isContain(implode("\n", [
            'graph LR;',
            '    subgraph "Your package"',
            '        f06c5a752b1bc11a09b156111180758f("jbzoo/toolbox");',
            '    end',
            '    subgraph "Required"',
            '        33e43173c7d1f8ec43e3537370f3eb50("jbzoo/data@4.0.x-dev");',
            '        40a19d56bb68e2ffd5789d83252980c7("jbzoo/image@5.0.x-dev");',
            '        6bc45a25e39d6940172de595ef0edd62("wikimedia/less.php@v3.0.0");',
            '        8ac966e5122efcb564de8239109e01b3("jbzoo/http-client@3.0.x-dev");',
            '        b0ccab8b9f0997e1142c95d922a3e68b("jbzoo/utils@4.0.x-dev");',
            '        b639e1b42e0f7a49ac0e6eec145977b3("jbzoo/event@3.0.x-dev");',
            '        c9ad71e94635c4f947fb97fadc8692c3("jbzoo/path@3.0.x-dev");',
            '        c9e5fecbbf5b863056a58a6ae56c9f30("jbzoo/mermaid-php@2.0.x-dev");',
            '        e74cdd8ebfa8918921215b9c2bb2044d("jbzoo/assets@3.0.x-dev");',
            '        f25de7eb32948d383fe0acee3f2f839c("jbzoo/less@3.0.x-dev");',
            '        33e43173c7d1f8ec43e3537370f3eb50-. "suggest" .-> b0ccab8b9f0997e1142c95d922a3e68b;',
            '        40a19d56bb68e2ffd5789d83252980c7-->|"4.0.x-dev"|33e43173c7d1f8ec43e3537370f3eb50;',
            '        40a19d56bb68e2ffd5789d83252980c7-->|"4.0.x-dev"|b0ccab8b9f0997e1142c95d922a3e68b;',
            '        8ac966e5122efcb564de8239109e01b3-->|"4.0.x-dev"|33e43173c7d1f8ec43e3537370f3eb50;',
            '        8ac966e5122efcb564de8239109e01b3-. "suggest" .-> b639e1b42e0f7a49ac0e6eec145977b3;',
            '        b0ccab8b9f0997e1142c95d922a3e68b-. "suggest" .-> 33e43173c7d1f8ec43e3537370f3eb50;',
            '        c9ad71e94635c4f947fb97fadc8692c3-->|"4.0.x-dev"|33e43173c7d1f8ec43e3537370f3eb50;',
            '        c9ad71e94635c4f947fb97fadc8692c3-->|"4.0.x-dev"|b0ccab8b9f0997e1142c95d922a3e68b;',
            '        e74cdd8ebfa8918921215b9c2bb2044d-->|"3.0.x-dev"|c9ad71e94635c4f947fb97fadc8692c3;',
            '        e74cdd8ebfa8918921215b9c2bb2044d-->|"3.0.x-dev"|f25de7eb32948d383fe0acee3f2f839c;',
            '        e74cdd8ebfa8918921215b9c2bb2044d-->|"4.0.x-dev"|33e43173c7d1f8ec43e3537370f3eb50;',
            '        e74cdd8ebfa8918921215b9c2bb2044d-->|"4.0.x-dev"|b0ccab8b9f0997e1142c95d922a3e68b;',
            '        f06c5a752b1bc11a09b156111180758f == "2.0.x-dev" ==> c9e5fecbbf5b863056a58a6ae56c9f30;',
            '        f06c5a752b1bc11a09b156111180758f == "3.0.x-dev" ==> 8ac966e5122efcb564de8239109e01b3;',
            '        f06c5a752b1bc11a09b156111180758f == "3.0.x-dev" ==> b639e1b42e0f7a49ac0e6eec145977b3;',
            '        f06c5a752b1bc11a09b156111180758f == "3.0.x-dev" ==> c9ad71e94635c4f947fb97fadc8692c3;',
            '        f06c5a752b1bc11a09b156111180758f == "3.0.x-dev" ==> e74cdd8ebfa8918921215b9c2bb2044d;',
            '        f06c5a752b1bc11a09b156111180758f == "3.0.x-dev" ==> f25de7eb32948d383fe0acee3f2f839c;',
            '        f06c5a752b1bc11a09b156111180758f == "4.0.x-dev" ==> 33e43173c7d1f8ec43e3537370f3eb50;',
            '        f06c5a752b1bc11a09b156111180758f == "4.0.x-dev" ==> b0ccab8b9f0997e1142c95d922a3e68b;',
            '        f06c5a752b1bc11a09b156111180758f == "5.0.x-dev" ==> 40a19d56bb68e2ffd5789d83252980c7;',
            '        f25de7eb32948d383fe0acee3f2f839c-->|"4.0.x-dev"|33e43173c7d1f8ec43e3537370f3eb50;',
            '        f25de7eb32948d383fe0acee3f2f839c-->|"4.0.x-dev"|b0ccab8b9f0997e1142c95d922a3e68b;',
            '        f25de7eb32948d383fe0acee3f2f839c-->|"^3.0.0"|6bc45a25e39d6940172de595ef0edd62;',
            '    end',
            '    subgraph "PHP Platform"',
            '        11dcb9ef2d24b544b26fd67ec7bacf64("ext-ctype");',
            '        18c6b06bcc47ea916fdabe925269e58e("ext-gd");',
            '        196303969ff394bf86c4b4bf8a6ae670("ext-exif");',
            '        2d357c72af4d26ef3848b0eebf4d85e7("ext-posix");',
            '        4d48b207cdf39d7efea6cb77e287a47d("ext-json");',
            '        5ff8eb852be0cf3abefb308c5cb6ad4c("ext-mbstring");',
            '        cab3b1140b9b2b0a32f96de84c0f6479("ext-intl");',
            '        e1bfd762321e409cee4ac0b6e841963c("php");',
            '        f060a75acdeea8fa1e411563c2d8f7be("ext-filter");',
            '        33e43173c7d1f8ec43e3537370f3eb50-->4d48b207cdf39d7efea6cb77e287a47d;',
            '        33e43173c7d1f8ec43e3537370f3eb50-->|"^7.2"|e1bfd762321e409cee4ac0b6e841963c;',
            '        40a19d56bb68e2ffd5789d83252980c7-->11dcb9ef2d24b544b26fd67ec7bacf64;',
            '        40a19d56bb68e2ffd5789d83252980c7-->18c6b06bcc47ea916fdabe925269e58e;',
            '        40a19d56bb68e2ffd5789d83252980c7-->196303969ff394bf86c4b4bf8a6ae670;',
            '        40a19d56bb68e2ffd5789d83252980c7-->|">=7.2"|e1bfd762321e409cee4ac0b6e841963c;',
            '        6bc45a25e39d6940172de595ef0edd62-->|">=7.2.9"|e1bfd762321e409cee4ac0b6e841963c;',
            '        8ac966e5122efcb564de8239109e01b3-->4d48b207cdf39d7efea6cb77e287a47d;',
            '        8ac966e5122efcb564de8239109e01b3-->|">=7.2"|e1bfd762321e409cee4ac0b6e841963c;',
            '        b0ccab8b9f0997e1142c95d922a3e68b-->18c6b06bcc47ea916fdabe925269e58e;',
            '        b0ccab8b9f0997e1142c95d922a3e68b-->2d357c72af4d26ef3848b0eebf4d85e7;',
            '        b0ccab8b9f0997e1142c95d922a3e68b-->5ff8eb852be0cf3abefb308c5cb6ad4c;',
            '        b0ccab8b9f0997e1142c95d922a3e68b-->cab3b1140b9b2b0a32f96de84c0f6479;',
            '        b0ccab8b9f0997e1142c95d922a3e68b-->f060a75acdeea8fa1e411563c2d8f7be;',
            '        b0ccab8b9f0997e1142c95d922a3e68b-->|">=7.2"|e1bfd762321e409cee4ac0b6e841963c;',
            '        b0ccab8b9f0997e1142c95d922a3e68b-. "suggest" .-> 5ff8eb852be0cf3abefb308c5cb6ad4c;',
            '        b639e1b42e0f7a49ac0e6eec145977b3-->|">=7.2"|e1bfd762321e409cee4ac0b6e841963c;',
            '        c9ad71e94635c4f947fb97fadc8692c3-->|">=7.2"|e1bfd762321e409cee4ac0b6e841963c;',
            '        c9e5fecbbf5b863056a58a6ae56c9f30-->4d48b207cdf39d7efea6cb77e287a47d;',
            '        c9e5fecbbf5b863056a58a6ae56c9f30-->|">=7.2"|e1bfd762321e409cee4ac0b6e841963c;',
            '        e74cdd8ebfa8918921215b9c2bb2044d-->|">=7.2"|e1bfd762321e409cee4ac0b6e841963c;',
            '        f06c5a752b1bc11a09b156111180758f == ">=7.2" ==> e1bfd762321e409cee4ac0b6e841963c;',
            '        f25de7eb32948d383fe0acee3f2f839c-->|">=7.2"|e1bfd762321e409cee4ac0b6e841963c;',
            '    end'
        ]), file_get_contents($output));
    }

    public function testRealProject()
    {
        $fixturesPath = PROJECT_ROOT . '/tests/fixtures/' . __FUNCTION__;

        $output = PROJECT_ROOT . '/build/' . __FUNCTION__ . '_full.html';
        $result = CliRunner::task('', [
            'composer-json' => "{$fixturesPath}/composer.json",
            'composer-lock' => "{$fixturesPath}/composer.lock",
            'output'        => $output,
        ]);
        isContain('jbzoo/toolbox - Graph of Dependencies', file_get_contents($output));
        isContain('Report is ready', $result);

        $output = PROJECT_ROOT . '/build/' . __FUNCTION__ . '_min-dev.html';
        $result = CliRunner::task('-vvv --no-php --no-ext', [
            'composer-json' => "{$fixturesPath}/composer.json",
            'composer-lock' => "{$fixturesPath}/composer.lock",
            'output'        => $output,
            'link-version'  => 'false',
            'lib-version'   => 'false',
        ]);
        isContain('jbzoo/toolbox - Graph of Dependencies', file_get_contents($output));
        isContain('Report is ready', $result);

        $output = PROJECT_ROOT . '/build/' . __FUNCTION__ . '_min_no-dev.html';
        $result = CliRunner::task('-vvv --no-php --no-ext --no-dev', [
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
