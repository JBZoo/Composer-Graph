# JBZoo / Composer-Graph

[![Build Status](https://travis-ci.org/JBZoo/Composer-Graph.svg?branch=master)](https://travis-ci.org/JBZoo/Composer-Graph)    [![Coverage Status](https://coveralls.io/repos/JBZoo/Composer-Graph/badge.svg)](https://coveralls.io/github/JBZoo/Composer-Graph?branch=master)    [![Psalm Coverage](https://shepherd.dev/github/JBZoo/Composer-Graph/coverage.svg)](https://shepherd.dev/github/JBZoo/Composer-Graph)    
[![Latest Stable Version](https://poser.pugx.org/JBZoo/Composer-Graph/v)](https://packagist.org/packages/JBZoo/Composer-Graph)    [![Latest Unstable Version](https://poser.pugx.org/JBZoo/Composer-Graph/v/unstable)](https://packagist.org/packages/JBZoo/Composer-Graph)    [![Dependents](https://poser.pugx.org/JBZoo/Composer-Graph/dependents)](https://packagist.org/packages/JBZoo/Composer-Graph/dependents?order_by=downloads)    [![GitHub Issues](https://img.shields.io/github/issues/JBZoo/Composer-Graph)](https://github.com/JBZoo/Composer-Graph/issues)    [![Total Downloads](https://poser.pugx.org/JBZoo/Composer-Graph/downloads)](https://packagist.org/packages/JBZoo/Composer-Graph/stats)    [![GitHub License](https://img.shields.io/github/license/JBZoo/Composer-Graph)](https://github.com/JBZoo/Composer-Graph/blob/master/LICENSE)


## Usage

```
$ php jbzoo-composer-graph --help

Usage:
  build [options]

Options:
      --composer-json=COMPOSER-JSON  Path to composer.json file [default: "./composer.json"]
      --composer-lock=COMPOSER-LOCK  Path to composer.lock file [default: "./composer.lock"]
      --output=OUTPUT                Path to html output. [default: "./build/jbzoo-composer-graph.html"]
      --no-php                       Exclude PHP
      --no-ext                       Exclude all ext-* nodes
      --no-dev                       Exclude dev requirements
      --no-suggest                   Exclude suggested requirements
      --link-version=LINK-VERSION    Show version requirements in link [default: "true"]
      --lib-version=LIB-VERSION      Show version of package [default: "true"]
      --direction=DIRECTION          Direction of graph. Available LR,TB,BT,RL [default: "LR"]
  -h, --help                         Display this help message
  -q, --quiet                        Do not output any message
  -V, --version                      Display this application version
      --ansi                         Force ANSI output
      --no-ansi                      Disable ANSI output
  -n, --no-interaction               Do not ask any interactive question
  -v|vv|vvv, --verbose               Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```


## Unit tests and check code style
```sh
make update
make test-all
```


### License
MIT
