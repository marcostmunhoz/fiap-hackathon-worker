FROM serversideup/php:8.4-cli-alpine as base

ENV AUTORUN_ENABLED=true
ENV AUTORUN_LARAVEL_MIGRATION=true
ENV AUTORUN_LARAVEL_MIGRATION_ISOLATION=false
ENV AUTORUN_LARAVEL_CONFIG_CACHE=false
ENV AUTORUN_LARAVEL_EVENT_CACHE=false
ENV AUTORUN_LARAVEL_ROUTE_CACHE=false
ENV AUTORUN_LARAVEL_STORAGE_LINK=false
ENV AUTORUN_LARAVEL_VIEW_CACHE=false

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

FROM base as development

USER root

RUN install-php-extensions xdebug \
  && echo "xdebug.mode=none" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
  && echo "xdebug.start_with_request=trigger" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
  && echo "xdebug.trigger_value=PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
  && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
  && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
  && mkdir -p /opt/phpstorm-coverage \
  && chmod 777 /opt/phpstorm-coverage

ARG USER_ID
ARG GROUP_ID

RUN docker-php-serversideup-set-id www-data $USER_ID:$GROUP_ID

USER www-data

FROM base as production

ENV AUTORUN_LARAVEL_CONFIG_CACHE=true
ENV AUTORUN_LARAVEL_EVENT_CACHE=true
ENV PHP_OPCACHE_ENABLE=1

COPY --chown=www-data:www-data . /var/www/html
