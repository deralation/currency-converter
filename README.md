# Currency Converter

## Local Environment
[Docker](https://www.docker.com/products/docker-desktop) must be installed to run the project.

*Docker Services*

1. [Nginx](https://www.nginx.com/) for Web server local.currency-converter.com
2. [Php](https://www.php.net/) 8.0-fpm 
3. [Mysql](https://www.mysql.com/) 8.0 for Database
4. [Composer](https://getcomposer.org/) to install dependecies
5. [Phinx](https://github.com/cakephp/phinx) For Database Migration
> For mac user *sudo nano /etc/hosts* to add custom local environment domain 127.0.0.1 *local.currency-converter.com*

*docker-compose up* to start the local environment services

*App Running*
To convert currency request converter.php endpoint with required parameters; action, sourceCurrency, targetCurrency and sourceAmount parameter
>http://local.currency-converter.com/api/v1/converter.php?action=getExchangeRate&sourceCurrency=DKK&targetCurrency=USD&sourceAmount=60

To run test
with Docker below command to check target amount test
>docker exec php-container /var/www/html/currency-converter/vendor/bin/phpunit /var/www/html/currency-converter/tests/ConverterTest.php
