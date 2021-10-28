FROM composer:2.1.9 AS composer
FROM php:7.4-cli

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y git libcurl4-openssl-dev zip
RUN docker-php-ext-install bcmath curl

CMD composer install && php index.php ./input.csv ./output.csv