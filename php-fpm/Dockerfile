ARG NODE_VERSION=20

FROM node:${NODE_VERSION}-alpine AS node

FROM alpine:3.19

COPY --from=node /usr/lib /usr/lib
COPY --from=node /usr/local/lib /usr/local/lib
COPY --from=node /usr/local/include /usr/local/include
COPY --from=node /usr/local/bin /usr/local/bin

RUN apk add --no-cache \
    coreutils \
    gettext \
    php82-fpm \
    php82-ctype \
    php82-curl \
    php82-dom \
    php82-gd \
    php82-iconv \
    php82-json \
    php82-intl \
    php82-fileinfo\
    php82-mbstring \
    php82-opcache \
    php82-openssl \
    php82-pdo \
    php82-pdo_mysql \
    php82-mysqli \
    php82-xml \
    php82-xsl \
    php82-zlib \
    php82-phar \
    php82-tokenizer \
    php82-session \
    php82-simplexml \
    php82-zip \
    php82-xmlwriter \
    php82-sodium \
    php82-pecl-apcu \
    php82 \
    make \
    curl \
    jpegoptim \
    pngquant \
    optipng \
    gifsicle \
    shadow \
    git

# Enable php8-xdebug if $PHP_XDEBUG_MODE is not empty
ARG PHP_XDEBUG_MODE=off
ARG PHP_XDEBUG_CLIENT_PORT=5902
ARG PHP_XDEBUG_CLIENT_HOST=host.docker.internal
COPY xdebug.ini  /etc/php82/conf.d/xdebug.ini.template
RUN if [[ "$PHP_XDEBUG_MODE" != "" ]]; then \
    apk add --no-cache php82-pecl-xdebug; \
    export PHP_XDEBUG_MODE=$PHP_XDEBUG_MODE; \
    export PHP_XDEBUG_CLIENT_PORT=$PHP_XDEBUG_CLIENT_PORT; \
    export PHP_XDEBUG_CLIENT_HOST=$PHP_XDEBUG_CLIENT_HOST; \
    envsubst < /etc/php82/conf.d/xdebug.ini.template > /etc/php82/conf.d/xdebug.ini; \
    fi
RUN rm -f /etc/php82/conf.d/xdebug.ini.template

RUN curl -sS https://getcomposer.org/installer | tee composer-setup.php \
    && ln -s /usr/bin/php82 /usr/local/bin/php \
    && php composer-setup.php && rm composer-setup.php* \
    && chmod +x composer.phar && mv composer.phar /usr/bin/composer

ARG SYMFONY_CLI_VERSION
RUN wget https://github.com/symfony/cli/releases/download/v$SYMFONY_CLI_VERSION/symfony_linux_amd64.gz \
    && gzip -d symfony_linux_amd64.gz \
    && mv symfony_linux_amd64 symfony \
    && chmod +x symfony \
    && mv symfony /usr/local/bin/

RUN npm install --global pnpm@^8

COPY symfony.ini /etc/php82/conf.d/
COPY symfony.ini /etc/php82/cli/conf.d/

COPY symfony.pool.conf /etc/php82/php-fpm.d/

WORKDIR /var/www/symfony

ARG UID
ARG GID
RUN echo "uid: $UID, gid: $GID"

# by default user on a MAC has GID equal to 20. This GID is already taken by the dialout user on the alpine image
# The code below reassigns GID of dialout user to GID 3000
RUN if [ $(getent group ${GID} | cut -d: -f1) ]; then echo $(getent group $GID | cut -d: -f1) | xargs groupmod -g 3000 && \
    getent group 3000 | cut -d: -f1 | xargs -I % find / -group $GID -exec chgrp -h % {} \; ;fi;

RUN usermod -u $UID nobody && groupmod -g $GID nobody && usermod -a -G root nobody
RUN chown -R nobody:nobody /var/www/symfony && \ 
    chmod -R 775 /var/log && \
    mkdir -p /.composer && chown -R nobody:nobody /.composer && \
    chown -R nobody:nobody /usr/local && \
    mkdir -p /.symfony && chown -R nobody:nobody /.symfony

USER nobody

RUN whoami
RUN groups
RUN ls -la

CMD ["php-fpm82", "-F"]
EXPOSE 9001
