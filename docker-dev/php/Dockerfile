FROM php:7-fpm

RUN apt-get update
RUN apt-get -y install cron supervisor

COPY docker-dev/php/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker-dev/php/device_update_daemon.sh /device_update_daemon.sh

RUN docker-php-ext-install pdo pdo_mysql

COPY --chown=www-data:www-data . /code

WORKDIR /code

CMD ["/usr/bin/supervisord"]