FROM php:8.0-fpm

RUN docker-php-ext-install pdo_mysql

RUN echo "Building migration...";
CMD php /var/www/html/currency-converter/vendor/bin/phinx migrate -e development -c /var/www/html/site/phinx.yml