FROM alpine:3.13

RUN : \
    && apk update \
    && apk add \
        php8-fpm php8-ctype php8-dom php8-fileinfo php8-iconv php8-intl php8-mbstring php8-opcache php8-session php8-tokenizer php8-simplexml php8-xml \
        nginx \
        supervisor \
    && apk add --virtual .build \
        php8-cli php8-curl php8-openssl php8-phar \
        composer \
        nodejs npm \
    && rm /var/cache/apk/* \
    && ln -sf php8 /usr/bin/php

WORKDIR /srv

RUN : \
    && mkdir -p /srv/var/cache /srv/var/logs /srv/var/sessions /srv/tmp \
    && chown nobody: /srv/var/cache /srv/var/logs /srv/var/sessions /srv/tmp

COPY package*.json /srv/
RUN npm i

COPY composer.* /srv/
RUN composer install --prefer-dist --no-cache --no-interaction --no-dev

COPY assets /srv/assets
COPY web /srv/web
COPY webpack.config.js /srv/

RUN : \
    && npm run build \
    && rm -rf assets \
    && rm -rf node_modules \
    && rm webpack.config.js \
    && apk del --no-cache .build

COPY bin /srv/bin
COPY config /srv/config

COPY supervisord.conf /etc/supervisord.conf
COPY php-fpm.conf /etc/php-fpm.conf
COPY nginx.conf /etc/nginx/nginx.conf

COPY resources/cv.json /srv/resources/
COPY src /srv/src
COPY templates /srv/templates

RUN ( \
        echo "expose_php=off"; \
        echo "session.hash_function=sha256"; \
        echo "session.hash_bits_per_character=6"; \
    ) > /etc/php8/conf.d/custom.ini

# @todo Need to do this...
#RUN composer dump-autoload --classmap-authoritative --no-interaction

HEALTHCHECK \
  --interval=10s \
  --timeout=2s \
  --start-period=2s \
  CMD wget -O- "http://localhost/" || exit 1

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
