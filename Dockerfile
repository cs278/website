FROM caddy:2-alpine

RUN : \
    && sed --in-place 's/v3\.12/v3.13/g' /etc/apk/repositories \
    && apk update \
    && apk upgrade \
    && apk add \
        php8-fpm php8-ctype php8-dom php8-iconv php8-intl php8-mbstring php8-session php8-tokenizer php8-xml \
        supervisor \
    && apk add --virtual .build \
        php8-cli php8-curl php8-openssl php8-phar \
        composer \
        nodejs npm \
    && rm /var/cache/apk/*

RUN : \
    && mkdir -p /srv/var/cache /srv/var/logs /srv/var/sessions /srv/tmp \
    && chown nobody: /srv/var/cache /srv/var/logs /srv/var/sessions /srv/tmp

COPY package*.json /srv/
RUN npm i --production

COPY composer.* /srv/
RUN php8 $(which composer) install --prefer-dist --no-cache --no-interaction --no-dev

RUN apk del --no-cache .build

COPY bin /srv/bin
COPY config /srv/config

COPY supervisord.conf /etc/supervisord.conf
COPY php-fpm.conf /etc/php-fpm.conf
COPY Caddyfile /etc/caddy/Caddyfile
COPY .user.ini /srv/

COPY resources/cv.json /srv/resources/
COPY src /srv/src
COPY web /srv/web
COPY templates /srv/templates

# @todo Need to do this...
#RUN composer dump-autoload --classmap-authoritative --no-interaction

HEALTHCHECK \
  --interval=10s \
  --timeout=2s \
  --start-period=2s \
  CMD wget -O- "http://127.0.0.1/" || exit 1

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
