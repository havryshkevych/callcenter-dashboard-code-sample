# the different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/compose/compose-file/#target

ARG PHP_VERSION=8.1
ARG NGINX_VERSION=1
ARG NODE_VERSION=14

FROM php:${PHP_VERSION}-fpm-alpine AS ext-amqp

ENV EXT_AMQP_VERSION=master

RUN docker-php-source extract \
    && apk -Uu add git rabbitmq-c-dev \
    && git clone --branch $EXT_AMQP_VERSION --depth 1 https://github.com/php-amqp/php-amqp.git /usr/src/php/ext/amqp \
    && cd /usr/src/php/ext/amqp && git submodule update --init \
    && docker-php-ext-install amqp

FROM php:${PHP_VERSION}-fpm-alpine AS php

COPY --from=ext-amqp /usr/local/etc/php/conf.d/docker-php-ext-amqp.ini /usr/local/etc/php/conf.d/docker-php-ext-amqp.ini
COPY --from=ext-amqp /usr/local/lib/php/extensions/no-debug-non-zts-20210902/amqp.so /usr/local/lib/php/extensions/no-debug-non-zts-20210902/amqp.so
# persistent / runtime deps
RUN apk add --no-cache \
		acl \
		file \
		gettext \
		git \
		mysql-client \
		rabbitmq-c-dev \
        libssh-dev \
	;

ARG APCU_VERSION=5.1.21
RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		coreutils \
		freetype-dev \
		icu-dev \
		libtool \
		libzip-dev \
		mysql-dev \
		zlib-dev \
	; \
	\
	docker-php-ext-configure zip; \
	docker-php-ext-install -j$(nproc) \
		exif \
		intl \
		pdo_mysql \
		zip \
	; \
	pecl install \
		apcu-${APCU_VERSION} \
		redis \
	; \
	pecl clear-cache; \
	docker-php-ext-enable \
		apcu \
		opcache \
		redis \
	; \
	\
	runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --no-cache --virtual .phpexts-rundeps $runDeps; \
	\
	apk del .build-deps

RUN cp $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
COPY docker/php/php.ini $PHP_INI_DIR/conf.d/fpm.ini
COPY docker/php/php-cli.ini $PHP_INI_DIR/conf.d/cli.ini
COPY config/preload.php /application/config/

#COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

WORKDIR /application

# build for production
ARG APP_ENV=prod

# prevent the reinstallation of vendors at every changes in the source code
COPY composer.json composer.lock* symfony.lock* ./
RUN set -eux; \
	composer install --prefer-dist --no-autoloader --no-scripts --no-progress --ignore-platform-reqs; \
	composer clear-cache

# copy only specifically what we need
COPY .env ./
COPY bin bin/
COPY config config/
COPY public public/
COPY src src/
COPY migrations migrations/
COPY templates templates/
COPY translations translations/

RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer dump-autoload --classmap-authoritative; \
	APP_SECRET='' composer run-script post-install-cmd; \
	chmod +x bin/console; \
    sync;

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

FROM php AS php-debug
RUN set -eux; \
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS; \
    pecl install xdebug; \
    pecl clear-cache; \
    docker-php-ext-enable xdebug; \
    apk del .build-deps
COPY ./docker/php/xdebug.ini $PHP_INI_DIR/conf.d/xdebug.ini

FROM nginx:${NGINX_VERSION}-alpine AS nginx

COPY docker/nginx/conf.d /etc/nginx/conf.d
COPY docker/nginx/ssl_certs /etc/ssl/certs

WORKDIR /application

COPY --from=php /application/public public/

FROM node:${NODE_VERSION}-alpine AS api_platform_admin_development
WORKDIR /usr/src/admin
COPY ./admin/package.json ./admin/yarn.lock* ./
COPY docker/nginx/ssl_certs /etc/ssl/certs
RUN set -eux; \
	yarn install
COPY ./admin ./
VOLUME /usr/src/admin/node_modules
ENV HTTPS true
CMD ["yarn", "start"]

FROM node:${NODE_VERSION}-alpine AS api_platform_client_development
WORKDIR /usr/src/client
COPY ./client/package.json* ./client/yarn.lock* ./
COPY docker/nginx/ssl_certs /etc/ssl/certs
RUN set -eux; \
	yarn install
COPY ./client ./
VOLUME /usr/src/client/node_modules
ENV HTTPS true
CMD ["yarn", "start"]

# "build" stage
# depends on the "development" stage above
FROM api_platform_admin_development AS api_platform_admin_build
ARG REACT_APP_API_ENTRYPOINT
RUN set -eux; \
	yarn build
WORKDIR /usr/src/admin/build
COPY /usr/src/admin/build ./