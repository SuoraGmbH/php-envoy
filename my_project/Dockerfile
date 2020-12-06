FROM composer
COPY . /app
ENV APP_ENV=prod
RUN cd /app && composer install

FROM php:7.4-fpm
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
ENV APP_ENV=prod
COPY --from=0 /app /app
COPY zz-www.conf /usr/local/etc/php-fpm.d/zz-www.conf
WORKDIR /app
