#
# JBZoo Toolbox - Composer-Graph
#
# This file is part of the JBZoo Toolbox project.
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.
#
# @package    Composer-Graph
# @license    MIT
# @copyright  Copyright (C) JBZoo.com, All rights reserved.
# @link       https://github.com/JBZoo/Composer-Graph
#


ifneq (, $(wildcard ./vendor/jbzoo/codestyle/src/init.Makefile))
    include ./vendor/jbzoo/codestyle/src/init.Makefile
endif


update: ##@Project Install/Update all 3rd party dependencies
	$(call title,"Install/Update all 3rd party dependencies")
	@echo "Composer flags: $(JBZOO_COMPOSER_UPDATE_FLAGS)"
	@composer update $(JBZOO_COMPOSER_UPDATE_FLAGS)


test-all: ##@Project Run all project tests at once
	@make test
	@make codestyle
	@make test-self


test-self:
	$(call title,"Build composer graph of dependencies")
	@php `pwd`/jbzoo-composer-graph                             \
        --composer-json=`pwd`/composer.json                     \
        --composer-lock=`pwd`/composer.lock                     \
        --output=$(PATH_BUILD)/example.html                     \
        -vvv
	@php `pwd`/jbzoo-composer-graph                             \
        --composer-json=`pwd`/composer.json                     \
        --composer-lock=`pwd`/composer.lock                     \
        --output=$(PATH_BUILD)/example-extend.html              \
        --show-link-versions                                    \
        --show-lib-versions                                     \
        -vvv
	@php `pwd`/jbzoo-composer-graph                             \
        --composer-json=`pwd`/composer.json                     \
        --composer-lock=`pwd`/composer.lock                     \
        --output=$(PATH_BUILD)/example-extend-php.html          \
        --show-link-versions                                    \
        --show-lib-versions                                     \
        --show-suggests                                         \
        --show-ext                                              \
        --show-php                                              \
        -vvv
	@php `pwd`/jbzoo-composer-graph                             \
        --composer-json=`pwd`/composer.json                     \
        --composer-lock=`pwd`/composer.lock                     \
        --output=$(PATH_BUILD)/example-dev-short.html           \
        --show-dev                                              \
        -vvv
	@php `pwd`/jbzoo-composer-graph                             \
        --composer-json=`pwd`/composer.json                     \
        --composer-lock=`pwd`/composer.lock                     \
        --output=$(PATH_BUILD)/example-full.html                \
        --show-link-versions                                    \
        --show-lib-versions                                     \
        --show-ext                                              \
        --show-php                                              \
        --show-dev                                              \
        --show-suggests                                         \
        -vvv
