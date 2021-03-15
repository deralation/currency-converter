FROM php:8.0-fpm

RUN usermod -u 1000 www-data

RUN docker-php-ext-install pdo pdo_mysql

RUN echo "Building migration...";
CMD php /var/www/html/currency-converter/vendor/bin/phinx migrate -e development -c /var/www/html/currency-converter/phinx.yml