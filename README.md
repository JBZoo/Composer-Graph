# JBZoo / Composer-Graph

[![CI](https://github.com/JBZoo/Composer-Graph/actions/workflows/main.yml/badge.svg?branch=master)](https://github.com/JBZoo/Composer-Graph/actions/workflows/main.yml?query=branch%3Amaster)    [![Coverage Status](https://coveralls.io/repos/github/JBZoo/Composer-Graph/badge.svg?branch=master)](https://coveralls.io/github/JBZoo/Composer-Graph?branch=master)    [![Psalm Coverage](https://shepherd.dev/github/JBZoo/Composer-Graph/coverage.svg)](https://shepherd.dev/github/JBZoo/Composer-Graph)    [![Psalm Level](https://shepherd.dev/github/JBZoo/Composer-Graph/level.svg)](https://shepherd.dev/github/JBZoo/Composer-Graph)    [![CodeFactor](https://www.codefactor.io/repository/github/jbzoo/composer-graph/badge)](https://www.codefactor.io/repository/github/jbzoo/composer-graph/issues)    
[![Stable Version](https://poser.pugx.org/jbzoo/composer-graph/version)](https://packagist.org/packages/jbzoo/composer-graph/)    [![Total Downloads](https://poser.pugx.org/jbzoo/composer-graph/downloads)](https://packagist.org/packages/jbzoo/composer-graph/stats)    [![Dependents](https://poser.pugx.org/jbzoo/composer-graph/dependents)](https://packagist.org/packages/jbzoo/composer-graph/dependents?order_by=downloads)    [![GitHub License](https://img.shields.io/github/license/jbzoo/composer-graph)](https://github.com/JBZoo/Composer-Graph/blob/master/LICENSE)


<!--ts-->
   * [Installation](#installation)
   * [Usage](#usage)
   * [Examples](#examples)
      * [Default output (no args) - minimal view](#default-output-no-args---minimal-view)
      * [Default output with PHP extensions (modules)](#default-output-with-php-extensions-modules)
      * [Default output with versions of packages and relations](#default-output-with-versions-of-packages-and-relations)
      * [Show suggested packages which are not installed](#show-suggested-packages-which-are-not-installed)
      * [Show dev dependencies](#show-dev-dependencies)
      * [Full Report](#full-report)
   * [Unit tests and check code style](#unit-tests-and-check-code-style)
   * [License](#license)
   * [See Also](#see-also)
<!--te-->

## Installation

```shell
composer require        jbzoo/composer-graph # For a specific project
composer global require jbzoo/composer-graph # As global tool

# OR use phar file.
wget https://github.com/JBZoo/Composer-Graph/releases/latest/download/composer-graph.phar
```


## Usage

```
$ php ./vendor/bin/composer-graph --help

Usage:
  build [options]

Options:
  -r, --root=ROOT                The path has to contain "composer.json" and "composer.lock" files [default: "./"]
  -o, --output=OUTPUT            Path to html output. [default: "./build/composer-graph.html"]
  -f, --format=FORMAT            Output format. Available options: html,mermaid [default: "html"]
  -D, --direction=DIRECTION      Direction of graph. Available options: LR,TB,BT,RL [default: "LR"]
  -p, --show-php                 Show PHP-node
  -e, --show-ext                 Show all ext-* nodes (PHP modules)
  -d, --show-dev                 Show all dev dependencies
  -s, --show-suggests            Show not installed suggests packages
  -l, --show-link-versions       Show version requirements in links
  -P, --show-package-versions    Show version of packages
  -O, --abc-order                Strict ABC ordering nodes in graph. It's fine tuning, sometimes it useful.
      --no-progress              Disable progress bar animation for logs. It will be used only for text output format.
      --mute-errors              Mute any sort of errors. So exit code will be always "0" (if it's possible).
                                 It has major priority then --non-zero-on-error. It's on your own risk!
      --stdout-only              For any errors messages application will use StdOut instead of StdErr. It's on your own risk!
      --non-zero-on-error        None-zero exit code on any StdErr message.
      --timestamp                Show timestamp at the beginning of each message.It will be used only for text output format.
      --profile                  Display timing and memory usage information.
      --output-mode=OUTPUT-MODE  Output format. Available options:
                                 text - Default text output format, userfriendly and easy to read.
                                 cron - Shortcut for crontab. It's basically focused on human-readable logs output.
                                 It's combination of --timestamp --profile --stdout-only --no-progress -vv.
                                 logstash - Logstash output format, for integration with ELK stack.
                                  [default: "text"]
      --cron                     Alias for --output-mode=cron. Deprecated!
  -h, --help                     Display help for the given command. When no command is given display help for the build command
  -q, --quiet                    Do not output any message
  -V, --version                  Display this application version
      --ansi|--no-ansi           Force (or disable --no-ansi) ANSI output
  -n, --no-interaction           Do not ask any interactive question
  -v|vv|vvv, --verbose           Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

```


## Examples

All examples are screenshots based on the package [JBZoo/Toolbox](https://github.com/JBZoo/Toolbox).


### Default output (no args) - minimal view
```shell
php ./vendor/bin/composer-graph
```

![Example](https://raw.githubusercontent.com/JBZoo/Composer-Graph/master/resources/jbzoo-minimal.png)



### Default output with PHP extensions (modules)
```shell
php ./vendor/bin/composer-graph  --show-ext
```

![Example](https://raw.githubusercontent.com/JBZoo/Composer-Graph/master/resources/jbzoo-extensions.png)



### Default output with versions of packages and relations
```shell
php ./vendor/bin/composer-graph  --show-link-versions  --show-lib-versions
```

![Example](https://raw.githubusercontent.com/JBZoo/Composer-Graph/master/resources/jbzoo-versions.png)



### Show suggested packages which are not installed
```shell
php ./vendor/bin/composer-graph  --show-suggests
```

![Example](https://raw.githubusercontent.com/JBZoo/Composer-Graph/master/resources/jbzoo-suggests.png)



### Show dev dependencies
```shell
php ./vendor/bin/composer-graph  --show-dev
```

![Example](https://raw.githubusercontent.com/JBZoo/Composer-Graph/master/resources/jbzoo-dev.png)


### Full Report

All options are enabled but `--show-php` (too many packages).
 
```shell
php ./vendor/bin/composer-graph            \
                 --show-ext                \
                 --show-dev                \
                 --show-suggests           \
                 --show-link-versions      \
                 --show-package-versions
```

![Example](https://raw.githubusercontent.com/JBZoo/Composer-Graph/master/resources/jbzoo-full-without-php.png)




## Unit tests and check code style
```shell
make update
make test-all
```


## License
MIT


## See Also

- [CI-Report-Converter](https://github.com/JBZoo/CI-Report-Converter) - Converting different error reports for deep compatibility with popular CI systems.
- [Composer-Diff](https://github.com/JBZoo/Composer-Diff) - See what packages have changed after `composer update`.
- [Mermaid-PHP](https://github.com/JBZoo/Mermaid-PHP) - Generate diagrams and flowcharts with the help of the mermaid script language.
- [Utils](https://github.com/JBZoo/Utils) - Collection of useful PHP functions, mini-classes, and snippets for every day.
- [Image](https://github.com/JBZoo/Image) - Package provides object-oriented way to manipulate with images as simple as possible.
- [Data](https://github.com/JBZoo/Data) - Extended implementation of ArrayObject. Use files as config/array. 
- [Retry](https://github.com/JBZoo/Retry) - Tiny PHP library providing retry/backoff functionality with multiple backoff strategies and jitter support.
- [SimpleTypes](https://github.com/JBZoo/SimpleTypes) - Converting any values and measures - money, weight, exchange rates, length, ...
