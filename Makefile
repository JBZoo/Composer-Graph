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


test-manual:
	@FIXTURE="lib-tp"       make test-manual-internal
	@FIXTURE="lib-tp-jbzoo" make test-manual-internal


test-manual-internal:
	@php `pwd`/jbzoo-composer-graph                                    \
        --composer-json=`pwd`/tests/fixtures/$(FIXTURE)/composer.json  \
        --composer-lock=`pwd`/tests/fixtures/$(FIXTURE)/composer.lock  \
        --output=$(PATH_BUILD)/$(FIXTURE)-manual-full.html             \
        --no-php --no-ext -vvv
	@php `pwd`/jbzoo-composer-graph                                    \
        --composer-json=`pwd`/tests/fixtures/$(FIXTURE)/composer.json  \
        --composer-lock=`pwd`/tests/fixtures/$(FIXTURE)/composer.lock  \
        --output=$(PATH_BUILD)/$(FIXTURE)-manual-full-minimal.html     \
        --link-version=false                                           \
        --lib-version=false                                            \
        --no-php --no-ext -vvv
	@php `pwd`/jbzoo-composer-graph                                    \
        --composer-json=`pwd`/tests/fixtures/$(FIXTURE)/composer.json  \
        --composer-lock=`pwd`/tests/fixtures/$(FIXTURE)/composer.lock  \
        --output=$(PATH_BUILD)/$(FIXTURE)-manual-no-dev.html           \
        --no-dev                                                       \
        --no-php --no-ext -vvv
	@php `pwd`/jbzoo-composer-graph                                    \
        --composer-json=`pwd`/tests/fixtures/$(FIXTURE)/composer.json  \
        --composer-lock=`pwd`/tests/fixtures/$(FIXTURE)/composer.lock  \
        --output=$(PATH_BUILD)/$(FIXTURE)-manual-no-dev-minimal.html   \
        --link-version=false                                           \
        --lib-version=false                                            \
        --no-dev                                                       \
        --no-php --no-ext -vvv

