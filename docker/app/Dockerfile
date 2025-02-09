FROM serversideup/php:8.4-fpm-nginx-alpine AS base

WORKDIR /var/www/html

ENV AUTORUN_ENABLED=true
ENV AUTORUN_LARAVEL_MIGRATION=true
ENV AUTORUN_LARAVEL_MIGRATION_ISOLATION=false
ENV AUTORUN_LARAVEL_CONFIG_CACHE=false
ENV AUTORUN_LARAVEL_EVENT_CACHE=false
ENV AUTORUN_LARAVEL_ROUTE_CACHE=false
ENV AUTORUN_LARAVEL_STORAGE_LINK=false
ENV AUTORUN_LARAVEL_VIEW_CACHE=false
ENV APP_VERSION=1.0.0

USER root

COPY ./docker/app/supervisord/supervisord.conf /etc/supervisord.conf
COPY ./docker/app/scripts/healthcheck-messaging /usr/local/bin/healthcheck-messaging
COPY ./docker/app/scripts/healthcheck /usr/local/bin/healthcheck

RUN docker-php-serversideup-dep-install-alpine supervisor && \
    docker-php-serversideup-dep-install-alpine ffmpeg && \
    chmod +x /usr/local/bin/healthcheck-messaging && \
    chmod +x /usr/local/bin/healthcheck

USER www-data

CMD ["supervisord", "-c", "/etc/supervisord.conf"]
HEALTHCHECK CMD "healthcheck"
STOPSIGNAL SIGTERM

FROM base AS development

USER root

RUN install-php-extensions xdebug \
  && echo "xdebug.mode=none" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
  && echo "xdebug.start_with_request=trigger" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
  && echo "xdebug.trigger_value=PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
  && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
  && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
  && mkdir -p /opt/phpstorm-coverage \
  && chmod 777 /opt/phpstorm-coverage

ARG USER_ID=1000
ARG GROUP_ID=1000

RUN docker-php-serversideup-set-id www-data $USER_ID:$GROUP_ID &&\
    docker-php-serversideup-set-file-permissions --owner $USER_ID:$GROUP_ID --service nginx

USER www-data

FROM base AS ci

ENV AUTORUN_ENABLED=false

USER root

RUN install-php-extensions xdebug \
  && echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

USER www-data

COPY --chown=www-data:www-data . /var/www/html

RUN composer install --no-interaction --no-plugins --no-scripts

FROM base AS production

ENV AUTORUN_LARAVEL_CONFIG_CACHE=true
ENV AUTORUN_LARAVEL_EVENT_CACHE=true
ENV PHP_OPCACHE_ENABLE=1
ENV APP_ENV=production

COPY --chown=www-data:www-data . /var/www/html
COPY --chown=www-data:www-data ./.env.production /var/www/html/.env

RUN composer install --no-dev --no-interaction --no-plugins --no-scripts &&\
    composer dump-autoload --optimize