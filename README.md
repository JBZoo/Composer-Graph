# JBZoo / Composer-Graph

[![Build Status](https://travis-ci.org/JBZoo/Composer-Graph.svg?branch=master)](https://travis-ci.org/JBZoo/Composer-Graph)    [![Coverage Status](https://coveralls.io/repos/JBZoo/Composer-Graph/badge.svg)](https://coveralls.io/github/JBZoo/Composer-Graph)    [![Psalm Coverage](https://shepherd.dev/github/JBZoo/Composer-Graph/coverage.svg)](https://shepherd.dev/github/JBZoo/Composer-Graph)    [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jbzoo/composer-graph/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jbzoo/composer-graph/?branch=master)    [![CodeFactor](https://www.codefactor.io/repository/github/jbzoo/composer-graph/badge)](https://www.codefactor.io/repository/github/jbzoo/composer-graph/issues)    [![PHP Strict Types](https://img.shields.io/badge/strict__types-%3D1-brightgreen)](https://www.php.net/manual/en/language.types.declarations.php#language.types.declarations.strict)    
[![Stable Version](https://poser.pugx.org/jbzoo/composer-graph/version)](https://packagist.org/packages/jbzoo/composer-graph)    [![Latest Unstable Version](https://poser.pugx.org/jbzoo/composer-graph/v/unstable)](https://packagist.org/packages/jbzoo/composer-graph)    [![Dependents](https://poser.pugx.org/jbzoo/composer-graph/dependents)](https://packagist.org/packages/jbzoo/composer-graph/dependents?order_by=downloads)    [![GitHub Issues](https://img.shields.io/github/issues/jbzoo/composer-graph)](https://github.com/JBZoo/Composer-Graph/issues)    [![Total Downloads](https://poser.pugx.org/jbzoo/composer-graph/downloads)](https://packagist.org/packages/jbzoo/composer-graph/stats)    [![GitHub License](https://img.shields.io/github/license/jbzoo/composer-graph)](https://github.com/JBZoo/Composer-Graph/blob/master/LICENSE)


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
  -r, --root=ROOT              The path has to contain "composer.json" and "composer.lock" files [default: "./"]
  -o, --output=OUTPUT          Path to html output. [default: "./build/composer-graph.html"]
  -f, --format=FORMAT          Output format. Available options: html,mermaid [default: "html"]
  -D, --direction=DIRECTION    Direction of graph. Available options: LR,TB,BT,RL [default: "LR"]
  -p, --show-php               Show PHP-node
  -e, --show-ext               Show all ext-* nodes (PHP modules)
  -d, --show-dev               Show all dev dependencies
  -s, --show-suggests          Show not installed suggests packages
  -l, --show-link-versions     Show version requirements in links
  -P, --show-package-versions  Show version of packages
  -O, --abc-order              Strict ABC ordering nodes in graph. It's fine tuning, sometimes it useful.
  -h, --help                   Display this help message
  -q, --quiet                  Do not output any message
  -V, --version                Display this application version
      --ansi                   Force ANSI output
      --no-ansi                Disable ANSI output
  -n, --no-interaction         Do not ask any interactive question
  -v|vv|vvv, --verbose         Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

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


### License
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
