ARG SYMFONY_PARAMS="--version=7.0.* --webapp"

FROM php:8.3-fpm

RUN apt update && apt install -y \
    libicu-dev \
    libonig-dev \
    libzip-dev \
    zip \
    unzip \
    curl \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install zip \
    && docker-php-ext-install pdo_mysql
RUN docker-php-ext-enable intl mbstring zip pdo_mysql

ENV SYMFONY_PARAMS_STD="--version=7.0.* --webapp"

