#syntax=docker/dockerfile:1

# ---- Builder ----------------------------------------------------------------

FROM dunglas/frankenphp:1-php8.5

SHELL ["/bin/bash", "-euxo", "pipefail", "-c"]

WORKDIR /app

RUN <<-EOF
	apt-get update
	apt-get install -y --no-install-recommends \
		file \
		git
	install-php-extensions \
		@composer \
		apcu \
		intl \
		opcache \
		zip \
		pdo_pgsql \
		pgsql
	rm -rf /var/lib/apt/lists/*
EOF

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PHP_INI_SCAN_DIR=":$PHP_INI_DIR/app.conf.d"
ENV APP_ENV=prod

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY --link frankenphp/conf.d/10-app.ini $PHP_INI_DIR/app.conf.d/
COPY --link frankenphp/conf.d/20-app.prod.ini $PHP_INI_DIR/app.conf.d/
COPY --link --chmod=755 frankenphp/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
COPY --link frankenphp/Caddyfile /etc/frankenphp/Caddyfile

COPY --link composer.* symfony.* ./
RUN composer install --no-cache --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress

COPY --link --exclude=frankenphp/ . ./

RUN <<-EOF
	mkdir -p var/cache var/log var/share
	composer dump-autoload --classmap-authoritative --no-dev
	composer dump-env prod
	composer run-script --no-dev post-install-cmd
	if [ -f importmap.php ]; then
		php bin/console asset-map:compile
	fi
	chmod +x bin/console
	chmod -R g=u var
	sync
EOF

# Collect shared libraries needed by FrankenPHP and PHP extensions
# hadolint ignore=DL3008,SC3054,DL4006
RUN <<-'EOF'
	apt-get update
	apt-get install -y --no-install-recommends libtree
	mkdir -p /tmp/libs
	BINARIES=(frankenphp php file)
	for target in $(printf '%s\n' "${BINARIES[@]}" | xargs -I{} which {}) \
		$(find "$(php -r 'echo ini_get("extension_dir");')" -maxdepth 2 -name "*.so"); do
		libtree -pv "$target" 2>/dev/null | grep -oP '(?:── )\K/\S+(?= \[)' | while IFS= read -r lib; do
			[ -f "$lib" ] && cp -n "$lib" /tmp/libs/
		done
	done
	rm -rf /var/lib/apt/lists/*
EOF

# ---- Production image -------------------------------------------------------

FROM debian:13-slim

SHELL ["/bin/bash", "-euxo", "pipefail", "-c"]

ENV APP_ENV=prod
ENV PHP_INI_SCAN_DIR=":/usr/local/etc/php/app.conf.d"
ENV OPENSSL_CONF=/etc/ssl/openssl.cnf
ENV XDG_CONFIG_HOME=/config
ENV XDG_DATA_HOME=/data

COPY --from=0 /usr/local/bin/frankenphp            /usr/local/bin/frankenphp
COPY --from=0 /usr/local/bin/php                   /usr/local/bin/php
COPY --from=0 /usr/local/bin/docker-php-entrypoint /usr/local/bin/docker-php-entrypoint
COPY --from=0 /usr/local/lib/php/extensions        /usr/local/lib/php/extensions
COPY --from=0 /tmp/libs                            /usr/lib

COPY --from=0 /usr/local/etc/php/conf.d     /usr/local/etc/php/conf.d
COPY --from=0 /usr/local/etc/php/php.ini    /usr/local/etc/php/php.ini
COPY --from=0 /usr/local/etc/php/app.conf.d /usr/local/etc/php/app.conf.d

COPY --from=0 /etc/frankenphp/Caddyfile /etc/frankenphp/Caddyfile

COPY --from=0 /etc/ssl/certs/ca-certificates.crt /etc/ssl/certs/ca-certificates.crt
COPY --from=0 /etc/ssl/openssl.cnf               /etc/ssl/openssl.cnf
COPY --from=0 /usr/bin/file                      /usr/bin/file
COPY --from=0 /usr/lib/file/magic.mgc            /usr/lib/file/magic.mgc

RUN <<-EOF
	mkdir -p /data/caddy /config/caddy
	chown -R www-data:www-data /data /config
	find / -perm /6000 -type f -exec chmod a-s {} + 2>/dev/null || true
EOF

COPY --link           --exclude=var --from=0 /app /app
COPY --chown=www-data:0              --from=0 /app/var /app/var
RUN chmod g=u /app/var

COPY --link --chmod=755 frankenphp/docker-entrypoint.sh /usr/local/bin/docker-entrypoint

USER www-data

WORKDIR /app

ENTRYPOINT ["docker-entrypoint"]

EXPOSE 80 443 443/udp

HEALTHCHECK --start-period=60s CMD php -r 'exit(false === @file_get_contents("http://localhost:2019/metrics", context: stream_context_create(["http" => ["timeout" => 5]])) ? 1 : 0);'
CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile"]
