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
 * Class ComplexTest
 *
 * @package JBZoo\PHPUnit
 */
class ComplexTest extends AbstractGraphTest
{
    public function testMinimal()
    {
        isSame(implode("\n", [
            'graph LR;',
            '    test__main ==> jbzoo__data;',
            '',
            '    subgraph "Your Package"',
            '        test__main("test/main");',
            '    end',
            '    subgraph "Required"',
            '        jbzoo__data("jbzoo/data");',
            '    end',
        ]), $this->buildGraph());
    }

    public function testShowLinkVersions()
    {
        isSame(implode("\n", [
            'graph LR;',
            '    test__main == "4.0.x-dev" ==> jbzoo__data;',
            '',
            '    subgraph "Your Package"',
            '        test__main("test/main");',
            '    end',
            '    subgraph "Required"',
            '        jbzoo__data("jbzoo/data");',
            '    end',
        ]), $this->buildGraph([
            'show-link-versions' => null,
        ]));
    }

    public function testShowPackageVersions()
    {
        isSame(implode("\n", [
            'graph LR;',
            '    test__main ==> jbzoo__data;',
            '',
            '    subgraph "Your Package"',
            '        test__main("test/main");',
            '    end',
            '    subgraph "Required"',
            '        jbzoo__data("jbzoo/data@4.0.x-dev");',
            '    end',
        ]), $this->buildGraph([
            'show-lib-versions' => null,
        ]));
    }

    public function testShowAllVersion()
    {
        isSame(implode("\n", [
            'graph LR;',
            '    test__main == "4.0.x-dev" ==> jbzoo__data;',
            '',
            '    subgraph "Your Package"',
            '        test__main("test/main");',
            '    end',
            '    subgraph "Required"',
            '        jbzoo__data("jbzoo/data@4.0.x-dev");',
            '    end',
        ]), $this->buildGraph([
            'show-lib-versions'  => null,
            'show-link-versions' => null,
        ]));
    }

    public function testShowSuggests()
    {
        isSame(implode("\n", [
            'graph LR;',
            '    jbzoo__data-.->symfony__yaml;',
            '    test__main ==> jbzoo__data;',
            '    test__main-.->symfony__var_dumper;',
            '',
            '    subgraph "Your Package"',
            '        test__main("test/main");',
            '    end',
            '    subgraph "Required"',
            '        jbzoo__data("jbzoo/data");',
            '        symfony__var_dumper(["symfony/var-dumper"]);',
            '        symfony__yaml(["symfony/yaml"]);',
            '    end',
        ]), $this->buildGraph([
            'show-suggests' => null,
        ]));
    }

    public function testShowExt()
    {
        isSame(implode("\n", [
            'graph LR;',
            '    jbzoo__data-->ext_json;',
            '    test__main ==> jbzoo__data;',
            '    test__main-->ext_bz2;',
            '    test__main-.->ext_core;',
            '    test__main-.->ext_curl;',
            '',
            '    subgraph "Your Package"',
            '        test__main("test/main");',
            '    end',
            '    subgraph "Required"',
            '        jbzoo__data("jbzoo/data");',
            '    end',
            '    subgraph "PHP Platform"',
            '        ext_bz2("ext-bz2");',
            '        ext_core("ext-core");',
            '        ext_curl("ext-curl");',
            '        ext_json("ext-json");',
            '    end',
        ]), $this->buildGraph([
            'show-ext' => null,
        ]));
    }

    public function testShowDev()
    {
        isSame(implode("\n", [
            'graph LR;',
            '    jbzoo__utils-.->jbzoo__data;',
            '    test__main ==> jbzoo__data;',
            '    test__main ==> jbzoo__utils;',
            '',
            '    subgraph "Your Package"',
            '        test__main("test/main");',
            '    end',
            '    subgraph "Required"',
            '        jbzoo__data("jbzoo/data");',
            '    end',
            '    subgraph "Required Dev"',
            '        jbzoo__utils("jbzoo/utils");',
            '    end',
        ]), $this->buildGraph([
            'show-dev' => null,
        ]));
    }

    public function testShowDevAndVersions()
    {
        isSame(implode("\n", [
            'graph LR;',
            '    jbzoo__utils-. "suggest" .-> jbzoo__data;',
            '    test__main == "3.0.x-dev" ==> jbzoo__utils;',
            '    test__main == "4.0.x-dev" ==> jbzoo__data;',
            '',
            '    subgraph "Your Package"',
            '        test__main("test/main");',
            '    end',
            '    subgraph "Required"',
            '        jbzoo__data("jbzoo/data@4.0.x-dev");',
            '    end',
            '    subgraph "Required Dev"',
            '        jbzoo__utils("jbzoo/utils@4.0.x-dev");',
            '    end',
        ]), $this->buildGraph([
            'show-dev'           => null,
            'show-lib-versions'  => null,
            'show-link-versions' => null,
        ]));
    }

    public function testShowDevAndExt()
    {
        isSame(implode("\n", [
            'graph LR;',
            '    jbzoo__data-->ext_json;',
            '    jbzoo__data-.->ext_filter;',
            '    jbzoo__utils-->ext_filter;',
            '    jbzoo__utils-->ext_gd;',
            '    jbzoo__utils-->ext_intl;',
            '    jbzoo__utils-->ext_mbstring;',
            '    jbzoo__utils-->ext_posix;',
            '    jbzoo__utils-.->jbzoo__data;',
            '    test__main ==> jbzoo__data;',
            '    test__main ==> jbzoo__utils;',
            '    test__main-->ext_bz2;',
            '    test__main-->ext_curl;',
            '    test__main-.->ext_core;',
            '',
            '    subgraph "Your Package"',
            '        test__main("test/main");',
            '    end',
            '    subgraph "Required"',
            '        jbzoo__data("jbzoo/data");',
            '    end',
            '    subgraph "Required Dev"',
            '        jbzoo__utils("jbzoo/utils");',
            '    end',
            '    subgraph "PHP Platform"',
            '        ext_bz2("ext-bz2");',
            '        ext_core("ext-core");',
            '        ext_curl("ext-curl");',
            '        ext_filter("ext-filter");',
            '        ext_gd("ext-gd");',
            '        ext_intl("ext-intl");',
            '        ext_json("ext-json");',
            '        ext_mbstring("ext-mbstring");',
            '        ext_posix("ext-posix");',
            '    end',
        ]), $this->buildGraph([
            'show-dev' => null,
            'show-ext' => null,
        ]));
    }

    public function testShowDevAndSuggests()
    {
        isSame(implode("\n", [
            'graph LR;',
            '    jbzoo__data-.->symfony__yaml;',
            '    jbzoo__utils-.->jbzoo__data;',
            '    jbzoo__utils-.->symfony__polyfill_mbstring;',
            '    jbzoo__utils-.->symfony__process;',
            '    test__main ==> jbzoo__data;',
            '    test__main ==> jbzoo__utils;',
            '    test__main-.->symfony__var_dumper;',
            '',
            '    subgraph "Your Package"',
            '        test__main("test/main");',
            '    end',
            '    subgraph "Required"',
            '        jbzoo__data("jbzoo/data");',
            '        symfony__var_dumper(["symfony/var-dumper"]);',
            '        symfony__yaml(["symfony/yaml"]);',
            '    end',
            '    subgraph "Required Dev"',
            '        jbzoo__utils("jbzoo/utils");',
            '        symfony__polyfill_mbstring(["symfony/polyfill-mbstring"]);',
            '        symfony__process(["symfony/process"]);',
            '    end',
        ]), $this->buildGraph([
            'show-dev'      => null,
            'show-suggests' => null,
        ]));
    }

    public function testFull()
    {
        isSame(implode("\n", [
            'graph LR;',
            '    jbzoo__data-->ext_json;',
            '    jbzoo__data-->|"^7.2"|PHP;',
            '    jbzoo__data-. "suggest" .-> ext_filter;',
            '    jbzoo__data-. "suggest" .-> symfony__yaml;',
            '    jbzoo__utils-->ext_filter;',
            '    jbzoo__utils-->ext_gd;',
            '    jbzoo__utils-->ext_intl;',
            '    jbzoo__utils-->ext_mbstring;',
            '    jbzoo__utils-->ext_posix;',
            '    jbzoo__utils-->|">=7.2"|PHP;',
            '    jbzoo__utils-. "suggest" .-> jbzoo__data;',
            '    jbzoo__utils-. "suggest" .-> symfony__polyfill_mbstring;',
            '    jbzoo__utils-. "suggest" .-> symfony__process;',
            '    test__main == "3.0.x-dev" ==> jbzoo__utils;',
            '    test__main == "4.0.x-dev" ==> jbzoo__data;',
            '    test__main-->ext_bz2;',
            '    test__main-->ext_curl;',
            '    test__main-. "suggest" .-> ext_core;',
            '    test__main-. "suggest" .-> symfony__var_dumper;',
            '',
            '    subgraph "Your Package"',
            '        test__main("test/main");',
            '    end',
            '    subgraph "Required"',
            '        jbzoo__data("jbzoo/data@4.0.x-dev");',
            '        symfony__var_dumper(["symfony/var-dumper"]);',
            '        symfony__yaml(["symfony/yaml"]);',
            '    end',
            '    subgraph "Required Dev"',
            '        jbzoo__utils("jbzoo/utils@4.0.x-dev");',
            '        symfony__polyfill_mbstring(["symfony/polyfill-mbstring"]);',
            '        symfony__process(["symfony/process"]);',
            '    end',
            '    subgraph "PHP Platform"',
            '        PHP("PHP");',
            '        ext_bz2("ext-bz2");',
            '        ext_core("ext-core");',
            '        ext_curl("ext-curl");',
            '        ext_filter("ext-filter");',
            '        ext_gd("ext-gd");',
            '        ext_intl("ext-intl");',
            '        ext_json("ext-json");',
            '        ext_mbstring("ext-mbstring");',
            '        ext_posix("ext-posix");',
            '    end',
        ]), $this->buildGraph([
            'show-php'           => null,
            'show-ext'           => null,
            'show-dev'           => null,
            'show-suggests'      => null,
            'show-link-versions' => null,
            'show-lib-versions'  => null,
        ]));
    }

    public function testShowPhp()
    {
        isSame(implode("\n", [
            'graph LR;',
            '    jbzoo__data-->PHP;',
            '    test__main ==> jbzoo__data;',
            '',
            '    subgraph "Your Package"',
            '        test__main("test/main");',
            '    end',
            '    subgraph "Required"',
            '        jbzoo__data("jbzoo/data");',
            '    end',
            '    subgraph "PHP Platform"',
            '        PHP("PHP");',
            '    end',
        ]), $this->buildGraph([
            'show-php' => null,
        ]));
    }

    public function testShowPhpAndDev()
    {
        isSame(implode("\n", [
            'graph LR;',
            '    jbzoo__data-->PHP;',
            '    jbzoo__utils-->PHP;',
            '    jbzoo__utils-.->jbzoo__data;',
            '    test__main ==> jbzoo__data;',
            '    test__main ==> jbzoo__utils;',
            '',
            '    subgraph "Your Package"',
            '        test__main("test/main");',
            '    end',
            '    subgraph "Required"',
            '        jbzoo__data("jbzoo/data");',
            '    end',
            '    subgraph "Required Dev"',
            '        jbzoo__utils("jbzoo/utils");',
            '    end',
            '    subgraph "PHP Platform"',
            '        PHP("PHP");',
            '    end',
        ]), $this->buildGraph([
            'show-php' => null,
            'show-dev' => null,
        ]));
    }

    public function testShowPhpAndDevAndSuggests()
    {
        isSame(implode("\n", [
            'graph LR;',
            '    jbzoo__data-->PHP;',
            '    jbzoo__data-.->symfony__yaml;',
            '    jbzoo__utils-->PHP;',
            '    jbzoo__utils-.->jbzoo__data;',
            '    jbzoo__utils-.->symfony__polyfill_mbstring;',
            '    jbzoo__utils-.->symfony__process;',
            '    test__main ==> jbzoo__data;',
            '    test__main ==> jbzoo__utils;',
            '    test__main-.->symfony__var_dumper;',
            '',
            '    subgraph "Your Package"',
            '        test__main("test/main");',
            '    end',
            '    subgraph "Required"',
            '        jbzoo__data("jbzoo/data");',
            '        symfony__var_dumper(["symfony/var-dumper"]);',
            '        symfony__yaml(["symfony/yaml"]);',
            '    end',
            '    subgraph "Required Dev"',
            '        jbzoo__utils("jbzoo/utils");',
            '        symfony__polyfill_mbstring(["symfony/polyfill-mbstring"]);',
            '        symfony__process(["symfony/process"]);',
            '    end',
            '    subgraph "PHP Platform"',
            '        PHP("PHP");',
            '    end',
        ]), $this->buildGraph([
            'show-php'      => null,
            'show-dev'      => null,
            'show-suggests' => null,
        ]));
    }

    public function testShowSuggestsAndExt()
    {
        isSame(implode("\n", [
            'graph LR;',
            '    jbzoo__data-->ext_json;',
            '    jbzoo__data-.->symfony__yaml;',
            '    test__main ==> jbzoo__data;',
            '    test__main-->ext_bz2;',
            '    test__main-.->ext_core;',
            '    test__main-.->ext_curl;',
            '    test__main-.->symfony__var_dumper;',
            '',
            '    subgraph "Your Package"',
            '        test__main("test/main");',
            '    end',
            '    subgraph "Required"',
            '        jbzoo__data("jbzoo/data");',
            '        symfony__var_dumper(["symfony/var-dumper"]);',
            '        symfony__yaml(["symfony/yaml"]);',
            '    end',
            '    subgraph "PHP Platform"',
            '        ext_bz2("ext-bz2");',
            '        ext_core("ext-core");',
            '        ext_curl("ext-curl");',
            '        ext_json("ext-json");',
            '    end',
        ]), $this->buildGraph([
            'show-ext'      => null,
            'show-suggests' => null,
        ]));
    }
}
