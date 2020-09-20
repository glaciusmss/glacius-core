FROM php:7.4-fpm

LABEL php.version="7.4"

ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS=1 \
    PHP_OPCACHE_MAX_ACCELERATED_FILES=80000

RUN set -eux \
    && apt-get update \
    && apt-get upgrade -y \
    && apt-get install -y --no-install-recommends \
    git \
    zip \
    unzip \
    libzip-dev \
    nginx \
    supervisor \
    procps \
    libpng-dev \
    zlib1g-dev \
    libicu-dev \
    g++ \
    && pecl install redis \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql pcntl bcmath gd zip intl exif \
    && docker-php-ext-enable opcache redis exif

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && chmod +x /usr/local/bin/composer

RUN rm -rf /var/lib/apt/lists/* \
    && apt-get update -yqq \
    && apt-get install -y --no-install-recommends \
    openssh-server

COPY ./composer.json ./composer.lock /var/www/html/

# PHP
COPY ./docker/config/php/conf.d/*.ini /usr/local/etc/php/conf.d/
COPY ./docker/config/php/php.ini /usr/local/etc/php/php.ini
COPY ./docker/config/php/php-fpm.d/docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

# Supervisor
COPY ./docker/config/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY ./docker/config/supervisor/conf.d/*.conf /etc/supervisor/conf.d-available/

# Nginx
COPY ./docker/config/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/config/nginx/default /etc/nginx/sites-available/default
COPY ./docker/config/nginx/h5bp/ /etc/nginx/h5bp
COPY ./docker/start.sh /usr/local/bin/start

COPY . /var/www/html

RUN composer install \
    && chown -R www-data:www-data /var/www/html \
    && chmod +x /usr/local/bin/start \
    && ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stdout /var/log/nginx/error.log

# Cleanup
RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

CMD [ "/usr/local/bin/start" ]
