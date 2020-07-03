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
	@-make report-merge-coverage
	@make codestyle


test-real:
	@php `pwd`/jbzoo-composer-graph build                                   \
        --composer-json=`pwd`/tests/fixtures/testRealProject/composer.json  \
        --composer-lock=`pwd`/tests/fixtures/testRealProject/composer.lock  \
        --output=$(PATH_BUILD)/manual-test.html                             \
        --link-version=false                                                \
        --lib-version=false                                                 \
        --no-php                                                            \
        --no-ext                                                            \
        -vvv
	@php `pwd`/jbzoo-composer-graph build                                   \
        --composer-json=`pwd`/tests/fixtures/testRealProject/composer.json  \
        --composer-lock=`pwd`/tests/fixtures/testRealProject/composer.lock  \
        --output=$(PATH_BUILD)/manual-test-platform.html                    \
        --link-version=false                                                \
        --lib-version=false                                                 \
        --no-php                                                            \
        --no-dev                                                            \
        -vvv