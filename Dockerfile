#
# JBZoo Toolbox - Composer-Graph.
#
# This file is part of the JBZoo Toolbox project.
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.
#
# @license    MIT
# @copyright  Copyright (C) JBZoo.com, All rights reserved.
# @see        https://github.com/JBZoo/Composer-Graph
#

FROM php:8.1-cli-alpine
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions      \
    && sync                                             \
    && install-php-extensions                           \
        opcache                                         \
        gd                                              \
        zip                                             \
        @composer

ENV COMPOSER_ALLOW_SUPERUSER=1
COPY . /app
RUN cd /app                                                           \
    && composer install --no-dev --optimize-autoloader --no-progress  \
    && composer clear-cache

# Experimental. Forced colored output
ENV TERM_PROGRAM=Hyper

ENTRYPOINT ["php", "/app/composer-graph"]
