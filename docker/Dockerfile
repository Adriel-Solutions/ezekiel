FROM php:8.0-fpm-alpine

EXPOSE 9000

WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions opcache pdo pdo_pgsql gd zip soap curl

RUN apk --update add postgresql-client
