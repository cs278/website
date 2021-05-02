FROM caddy:2-alpine

RUN : \
    && sed --in-place 's/v3\.12/v3.13/g' /etc/apk/repositories \
    && apk update \
    && apk upgrade \
    && apk add \
        php7-fpm php-ctype php7-dom php7-iconv php7-intl php7-json php7-mbstring php7-session php7-tokenizer php7-xml \
        supervisor \
    && apk add --virtual .build \
        composer \
        nodejs npm \
    && rm /var/cache/apk/*

RUN : \
    && mkdir -p /srv/var/cache /srv/var/logs /srv/var/sessions \
    && chown nobody: /srv/var/cache /srv/var/logs /srv/var/sessions

COPY bin /srv/bin
COPY config /srv/config
# COPY vendor /srv/vendor
# COPY node_modules /srv/node_modules

COPY package*.json /srv/
RUN npm i --production

# @todo Move this down when autoloading is only using PSR-4
COPY src /srv/src

COPY composer.* /srv/
RUN composer install --prefer-dist --classmap-authoritative --no-cache --no-interaction --no-dev

RUN apk del --no-cache .build

COPY cv.json /srv/
COPY supervisord.conf /etc/supervisord.conf
COPY php-fpm.conf /etc/php-fpm.conf
COPY Caddyfile /etc/caddy/Caddyfile

COPY web /srv/web
COPY templates /srv/templates
COPY .user.ini /srv/web

HEALTHCHECK \
  --interval=10s \
  --timeout=2s \
  --start-period=2s \
  CMD wget -O- "http://127.0.0.1/" || exit 1

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
