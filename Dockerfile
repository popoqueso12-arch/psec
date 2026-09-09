FROM php:8.2-fpm-alpine

RUN docker-php-ext-install mysqli

COPY --from=nginx:alpine /usr/sbin/nginx /usr/sbin/nginx
COPY --from=nginx:alpine /etc/nginx /etc/nginx

RUN mkdir -p /var/run/php

COPY nginx.conf /etc/nginx/nginx.conf
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

WORKDIR /var/www/html
COPY . /var/www/html/

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
