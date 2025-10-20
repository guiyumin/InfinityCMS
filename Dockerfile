FROM php:8.2-fpm-alpine

ARG UID
ARG GID

ENV UID=${UID}
ENV GID=${GID}

RUN mkdir -p /var/www/html

WORKDIR /var/www/html

RUN addgroup -g ${GID} --system infinitycms
RUN adduser -G infinitycms --system -D -s /bin/sh -u ${UID} infinitycms

RUN sed -i "s/user = www-data/user = infinitycms/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/group = www-data/group = infinitycms/g" /usr/local/etc/php-fpm.d/www.conf
RUN echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf

RUN apk add --no-cache libpng libpng-dev libsodium libsodium-dev oniguruma-dev
RUN docker-php-ext-install pdo pdo_mysql mbstring gd sodium

COPY . .

USER infinitycms

CMD ["php-fpm", "-y", "/usr/local/etc/php-fpm.conf", "-R"]
