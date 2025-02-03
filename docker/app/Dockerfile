FROM serversideup/php:8.4-cli-alpine as base

USER root

COPY ./docker/app/scripts/start-queue.sh /usr/local/bin/start-queue
RUN chmod +x /usr/local/bin/start-queue

USER www-data

CMD ["start-queue"]
HEALTHCHECK CMD "healthcheck-queue"
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

ENV PHP_OPCACHE_ENABLE=1

COPY --chown=www-data:www-data . /var/www/html
