FROM caddy:2-alpine

RUN : \
    && sed --in-place 's/v3\.12/v3.13/g' /etc/apk/repositories \
    && apk update \
    && apk upgrade \
    && apk add php7-fpm php7-dom php7-json php7-session php7-tokenizer composer supervisor \
    && rm /var/cache/apk/*

RUN : \
    && mkdir -p /srv/var/cache /srv/var/logs /srv/var/sessions \
    && chown nobody: /srv/var/cache /srv/var/logs /srv/var/sessions

COPY bin /srv/bin
COPY config /srv/config
COPY vendor /srv/vendor
COPY node_modules /srv/node_modules

COPY cv.json /srv/
COPY supervisord.conf /etc/supervisord.conf
COPY php-fpm.conf /etc/php-fpm.conf
COPY Caddyfile /etc/caddy/Caddyfile

COPY web /srv/web
COPY templates /srv/templates
COPY src /srv/src



CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
