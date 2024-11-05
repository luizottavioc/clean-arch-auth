FROM php:8.3-fpm

ARG user=user
ARG uid=1000

RUN apt-get update && apt-get install -y \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

RUN useradd -G www-data,root -u $uid -d /home/$user $user

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN mkdir -p /home/$user/.composer && \
chown -R $user:$user /home/$user

WORKDIR /var/www

COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

COPY start.sh /start.sh
RUN chmod +x /start.sh

USER $user
CMD ["/start.sh"]

