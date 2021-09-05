FROM "php:7.4-fpm"
RUN apt-get update -y
RUN apt-get install libcurl4-openssl-dev libonig-dev
RUN docker-php-ext-install pdo_mysql mbstring curl
