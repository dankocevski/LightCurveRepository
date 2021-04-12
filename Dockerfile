FROM php:7.4-fpm
RUN docker-php-ext-install -j$(nproc) mysqli

RUN apt-get update -y \
    && apt-get install -y nginx

RUN mkdir /var/www/html/LightCurveRepository
ADD . /var/www/html/LightCurveRepository
RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
COPY nginx-site.conf /etc/nginx/sites-enabled/default
COPY entrypoint.sh /etc/entrypoint.sh

EXPOSE 80 443
ENTRYPOINT ["/etc/entrypoint.sh"]

