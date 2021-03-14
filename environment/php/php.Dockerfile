FROM php:8.0-fpm

ENV php_conf /usr/local/etc/php/php.ini
ENV fpm_conf /usr/local/etc/php/php-fpm.conf
ENV fpm_conf_dir /usr/local/etc/php-fpm.d/

RUN usermod -u 1000 www-data

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN docker-php-ext-install bcmath
RUN docker-php-ext-install calendar
RUN docker-php-ext-install iconv

RUN docker-php-ext-enable bcmath \
    && docker-php-ext-enable calendar \
    && docker-php-ext-enable iconv

ARG USER_ID
ARG GROUP_ID

RUN if [ ${USER_ID:-0} -ne 0 ] && [ ${GROUP_ID:-0} -ne 0 ]; then \
    userdel -f www-data &&\
    if getent group www-data ; then groupdel www-data; fi &&\
    groupadd -g ${GROUP_ID} www-data &&\
    useradd -l -u ${USER_ID} -g www-data www-data &&\
    install -d -m 0755 -o www-data -g www-data /home/www-data &&\
    chown --changes --silent --no-dereference --recursive \
          --from=33:33 ${USER_ID}:${GROUP_ID} \
        /home/www-data \
        /.composer \
        /var/run/php-fpm \
        /var/lib/php/sessions \
;fi

USER www-data

WORKDIR /

COPY php.ini ${php_conf}
COPY www.conf ${fpm_conf_dir}/www.conf


WORKDIR /