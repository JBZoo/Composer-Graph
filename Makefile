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

.PHONY: build

ifneq (, $(wildcard ./vendor/jbzoo/codestyle/src/init.Makefile))
    include ./vendor/jbzoo/codestyle/src/init.Makefile
endif


BOX_PHAR = https://github.com/box-project/box/releases/download/3.10.0/box.phar

build: ##@Project Install all 3rd party dependencies
	$(call title,"Install/Update all 3rd party dependencies")
	@rm -f `pwd`/vendor/bin/box.phar
	@make build-phar


update: ##@Project Install/Update all 3rd party dependencies
	$(call title,"Install/Update all 3rd party dependencies")
	@echo "Composer flags: $(JBZOO_COMPOSER_UPDATE_FLAGS)"
	@composer update $(JBZOO_COMPOSER_UPDATE_FLAGS)
	@make build-phar


test-all: ##@Project Run all project tests at once
	@make test
	@make codestyle


prepare-examples:
	@composer install --working-dir="`pwd`/tests/fixtures/testJBZooToolbox" --no-dev
	@make prepare-one-example OUTPUT="tp" TEST_PATH="`pwd`/tests/fixtures/testJBZooToolbox"
	@make prepare-one-example OUTPUT="self"  TEST_PATH="`pwd`"


prepare-one-example:
	$(call title,"Build composer graph in different modes")
	@echo "TEST_PATH=$(TEST_PATH)"
	@echo "OUTPUT=$(OUTPUT)"
	@php `pwd`/composer-graph                                     \
        --root="$(TEST_PATH)"                                     \
        --output="$(PATH_BUILD)/$(OUTPUT)-minimal.html"           \
        -vvv
	@php `pwd`/composer-graph                                     \
        --root="$(TEST_PATH)"                                     \
        --output="$(PATH_BUILD)/$(OUTPUT)-extensions.html"        \
        --show-ext                                                \
        -vvv
	@php `pwd`/composer-graph                                     \
        --root="$(TEST_PATH)"                                     \
        --output="$(PATH_BUILD)/$(OUTPUT)-versions.html"          \
        --show-link-versions                                      \
        --show-package-versions                                   \
        -vvv
	@php `pwd`/composer-graph                                     \
        --root="$(TEST_PATH)"                                     \
        --output="$(PATH_BUILD)/$(OUTPUT)-suggests.html"          \
        --show-suggests                                           \
        -vvv
	@php `pwd`/composer-graph                                     \
        --root="$(TEST_PATH)"                                     \
        --output="$(PATH_BUILD)/$(OUTPUT)-suggests-versions.html" \
        --show-link-versions                                      \
        --show-package-versions                                   \
        --show-suggests                                           \
        -vvv
	@php `pwd`/composer-graph                                     \
        --root="$(TEST_PATH)"                                     \
        --output="$(PATH_BUILD)/$(OUTPUT)-dev.html"               \
        --show-dev                                                \
        -vvv
	@php `pwd`/composer-graph                                     \
        --root="$(TEST_PATH)"                                     \
        --output="$(PATH_BUILD)/$(OUTPUT)-full-without-php.html"  \
        --show-ext                                                \
        --show-dev                                                \
        --show-suggests                                           \
        --show-link-versions                                      \
        --show-package-versions                                   \
        -vvv
