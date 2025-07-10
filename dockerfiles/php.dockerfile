FROM php:8.4.10-fpm

ARG UID
ARG GID
ARG APP_PHP_INI_PATH

ENV UID=${UID}
ENV GID=${GID}
ENV APP_PHP_INI_PATH=${APP_PHP_INI_PATH}

RUN mkdir -p /var/www/html

WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# MacOS staff group's gid is 20, so is the dialout group in alpine linux. We're not using it, let's just remove it.
RUN delgroup dialout

# RUN addgroup -g ${GID} --system laravel
# RUN adduser -G laravel --system -D -s /bin/sh -u ${UID} laravel
RUN addgroup --gid ${GID} --system laravel
RUN adduser --ingroup laravel --system --disabled-login -shell /bin/sh -u ${UID} laravel

RUN sed -i "s/user = www-data/user = laravel/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/group = www-data/group = laravel/g" /usr/local/etc/php-fpm.d/www.conf
RUN echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf

RUN cp ${APP_PHP_INI_PATH} /usr/local/etc/php/php.ini
ADD ./php/my.ini /usr/local/etc/php/conf.d/

RUN apt-get update && apt-get upgrade -y
RUN apt-get install -y libzip-dev

RUN docker-php-ext-install pdo pdo_mysql exif zip

RUN pecl install redis && docker-php-ext-enable redis

# git
RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y git

# imagick
RUN apt install -y libmagickwand-dev && \
    git clone https://github.com/Imagick/imagick.git --depth 1 /tmp/imagick && \
    cd /tmp/imagick && \
    git fetch origin master && \
    git switch master && \
    cd /tmp/imagick && \
    phpize && \
    ./configure && \
    make && \
    make install && \
    docker-php-ext-enable imagick

# spatie/laravel-image-optimizer
RUN apt-get install -y jpegoptim
RUN apt-get install -y optipng
RUN apt-get install -y pngquant
RUN apt-get install -y gifsicle
RUN apt-get install -y webp
RUN apt-get install -y libavif-bin
RUN apt-get install -y nodejs npm
RUN npm install -y -g svgo
# gd
RUN apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libxpm-dev \
    zlib1g-dev && \
    docker-php-ext-configure gd --enable-gd --with-webp --with-jpeg \
    --with-xpm --with-freetype && \
    docker-php-ext-install -j$(nproc) gd

USER laravel

CMD ["php-fpm", "-y", "/usr/local/etc/php-fpm.conf", "-R"]
