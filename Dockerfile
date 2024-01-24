FROM php:8.2-apache

RUN a2enmod rewrite

RUN service apache2 restart

WORKDIR /var/www/html

RUN apt-get update -y && apt-get install -y libmariadb-dev
RUN docker-php-ext-install mysqli


