FROM php:8.3-fpm

ARG user=user
ARG uid=1000

RUN apt-get update && apt-get install -y \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

RUN pecl install xdebug && docker-php-ext-enable xdebug
RUN echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN useradd -G www-data,root -u $uid -d /home/$user $user

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN mkdir -p /home/$user/.composer && chown -R $user:$user /home/$user

RUN ln -s /var/www/vendor/bin/phpunit /usr/local/bin/phpunit

WORKDIR /var/www

COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

COPY start.sh /start.sh
RUN chmod +x /start.sh

USER $user
CMD ["/start.sh"]

