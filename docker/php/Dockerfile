FROM php:8.1-fpm

RUN apt-get update && apt-get install -y \
        libpq-dev \
        libzip-dev \
        libwebp-dev \
        libnotify-bin \
        nano \
        zip \
        unzip \
        curl \
        wget \
        git \
        libmagickwand-dev \
        imagemagick \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        libmcrypt-dev \
        libicu-dev \
    && pecl install imagick mcrypt-1.0.5 xdebug \
    && docker-php-ext-configure intl \
    && docker-php-ext-enable imagick mcrypt xdebug \
    && docker-php-ext-configure gd --with-webp=/usr/include/webp --with-jpeg=/usr/include --with-freetype=/usr/include/freetype2/ \
    && docker-php-ext-install -j$(nproc) gd zip iconv mbstring pgsql pdo_pgsql mysqli pdo_mysql intl

    
RUN wget --output-document=/usr/local/bin/composer https://getcomposer.org/composer.phar

RUN mkdir /var/www/composer
RUN chown 1000:www-data /var/www/composer

ADD php.ini /usr/local/etc/php/conf.d/custom.ini

RUN mkdir /var/log/php
RUN chown 1000:1000 -R /var/log/php
RUN chown 1000:1000 /usr/local/bin/composer
RUN chmod 755 /usr/local/bin/composer

WORKDIR /var/www/app

RUN usermod -u 1000 www-data \
  && groupmod -g 1000 www-data

RUN chown 1000:www-data -R /var/www/app

USER 1000

CMD bash -c "php-fpm"