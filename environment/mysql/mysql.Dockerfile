FROM mysql:8.0

ADD test_currency_converter.sql /docker-entrypoint-initdb.d