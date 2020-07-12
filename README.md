# JBZoo / Composer-Graph

[![Build Status](https://travis-ci.org/JBZoo/Composer-Graph.svg?branch=master)](https://travis-ci.org/JBZoo/Composer-Graph)    [![Coverage Status](https://coveralls.io/repos/JBZoo/Composer-Graph/badge.svg)](https://coveralls.io/github/JBZoo/Composer-Graph?branch=master)    [![Psalm Coverage](https://shepherd.dev/github/JBZoo/Composer-Graph/coverage.svg)](https://shepherd.dev/github/JBZoo/Composer-Graph)    
[![Latest Stable Version](https://poser.pugx.org/JBZoo/Composer-Graph/v)](https://packagist.org/packages/JBZoo/Composer-Graph)    [![Latest Unstable Version](https://poser.pugx.org/JBZoo/Composer-Graph/v/unstable)](https://packagist.org/packages/JBZoo/Composer-Graph)    [![Dependents](https://poser.pugx.org/JBZoo/Composer-Graph/dependents)](https://packagist.org/packages/JBZoo/Composer-Graph/dependents?order_by=downloads)    [![GitHub Issues](https://img.shields.io/github/issues/JBZoo/Composer-Graph)](https://github.com/JBZoo/Composer-Graph/issues)    [![Total Downloads](https://poser.pugx.org/JBZoo/Composer-Graph/downloads)](https://packagist.org/packages/JBZoo/Composer-Graph/stats)    [![GitHub License](https://img.shields.io/github/license/JBZoo/Composer-Graph)](https://github.com/JBZoo/Composer-Graph/blob/master/LICENSE)


## Installation

```sh
composer require        jbzoo/composer-graph # For a specific project
composer require global jbzoo/composer-graph # As global tool
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
  -e, --show-ext               Show all ext-* nodes
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
```sh
php ./vendor/bin/composer-graph
```

![Example](https://raw.githubusercontent.com/JBZoo/Composer-Graph/master/resources/jbzoo-minimal.png)



### Default output with PHP extensions (modules)
```sh
php ./vendor/bin/composer-graph  --show-ext
```

![Example](https://raw.githubusercontent.com/JBZoo/Composer-Graph/master/resources/jbzoo-extensions.png)



### Default output with versions of packages and relations
```sh
php ./vendor/bin/composer-graph  --show-link-versions  --show-lib-versions
```

![Example](https://raw.githubusercontent.com/JBZoo/Composer-Graph/master/resources/jbzoo-versions.png)



### Show suggested packages which are not installed
```sh
php ./vendor/bin/composer-graph  --show-suggests
```

![Example](https://raw.githubusercontent.com/JBZoo/Composer-Graph/master/resources/jbzoo-suggests.png)



### Show dev dependencies
```sh
php ./vendor/bin/composer-graph  --show-dev
```

![Example](https://raw.githubusercontent.com/JBZoo/Composer-Graph/master/resources/jbzoo-dev.png)


### Full Report

All options are enabled but `--show-php` (too many packages).
 
```sh
php ./vendor/bin/composer-graph            \
                 --show-ext                \
                 --show-dev                \
                 --show-suggests           \
                 --show-link-versions      \
                 --show-package-versions
```

![Example](https://raw.githubusercontent.com/JBZoo/Composer-Graph/master/resources/jbzoo-full-without-php.png)




## Unit tests and check code style
```sh
make update
make test-all
```


### License
MIT
