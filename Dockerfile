FROM php:8.2-fpm-alpine

RUN docker-php-ext-install mysqli && \
    apk add --no-cache nginx

RUN mkdir -p /var/run/php /var/run/nginx /var/log/nginx

COPY nginx.conf /etc/nginx/nginx.conf
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

WORKDIR /var/www/html
COPY . /var/www/html/

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
