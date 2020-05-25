FROM php:7.4.3-fpm

#RUN docker-php-ext-install pdo_mysql

RUN pecl install apcu

RUN apt-get update && \
apt-get install -y \
zlib1g-dev



WORKDIR /app


RUN PATH=$PATH:/app/vendor/bin:bin


