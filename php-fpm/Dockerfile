ARG NODE_VERSION=20

FROM node:${NODE_VERSION}-alpine AS node

FROM alpine:3.20

COPY --from=node /usr/lib /usr/lib
COPY --from=node /usr/local/lib /usr/local/lib
COPY --from=node /usr/local/include /usr/local/include
COPY --from=node /usr/local/bin /usr/local/bin

RUN apk add --no-cache \
    bash \
    coreutils \
    gettext \
    php83-fpm \
    php83-ctype \
    php83-curl \
    php83-dom \
    php83-gd \
    php83-iconv \
    php83-json \
    php83-intl \
    php83-fileinfo\
    php83-mbstring \
    php83-opcache \
    php83-openssl \
    php83-pdo \
    php83-pdo_pgsql \
    php83-xml \
    php83-xsl \
    php83-zlib \
    php83-phar \
    php83-tokenizer \
    php83-session \
    php83-simplexml \
    php83-zip \
    php83-xmlwriter \
    php83-sodium \
    php83-pecl-apcu \
    php83 \
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
COPY xdebug.ini  /etc/php83/conf.d/xdebug.ini.template
RUN if [[ "$PHP_XDEBUG_MODE" != "" ]]; then \
    apk add --no-cache php83-pecl-xdebug; \
    export PHP_XDEBUG_MODE=$PHP_XDEBUG_MODE; \
    export PHP_XDEBUG_CLIENT_PORT=$PHP_XDEBUG_CLIENT_PORT; \
    export PHP_XDEBUG_CLIENT_HOST=$PHP_XDEBUG_CLIENT_HOST; \
    envsubst < /etc/php83/conf.d/xdebug.ini.template > /etc/php83/conf.d/xdebug.ini; \
    fi
RUN rm -f /etc/php83/conf.d/xdebug.ini.template

RUN curl -sS https://getcomposer.org/installer | tee composer-setup.php \
    && ln -s /usr/bin/php83 /usr/local/bin/php \
    && php composer-setup.php && rm composer-setup.php* \
    && chmod +x composer.phar && mv composer.phar /usr/bin/composer

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash \
    && apk add symfony-cli

COPY symfony.ini /etc/php83/conf.d/
COPY symfony.ini /etc/php83/cli/conf.d/

COPY symfony.pool.conf /etc/php83/php-fpm.d/

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
    mkdir -p /.symfony5 && chown -R nobody:nobody /.symfony5 && \
    mkdir -p /.npm && chown -R nobody:nobody /.npm

RUN mkdir -p /.config/gcloud && chmod -R 775 /.config/gcloud

USER nobody

RUN whoami
RUN groups
RUN ls -la

CMD ["php-fpm83", "-F"]
EXPOSE 9001
