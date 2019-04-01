# Use 7.1 php image with apache
FROM php:7.1-apache

# Install mysqli PHP drivers
RUN docker-php-ext-install mysqli

# Install a collection of useful plugins
RUN DEBIAN_FRONTEND=noninteractive apt-get update -q \
        && DEBIAN_FRONTEND=noninteractive apt-get dist-upgrade -y \
        && DEBIAN_FRONTEND=noninteractive apt-get install -y \
            libfreetype6-dev \
            libjpeg62-turbo-dev \
            libmcrypt-dev \
            libpng-dev \
            libcurl4-nss-dev \
        && docker-php-ext-install iconv mcrypt \
        && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
        && docker-php-ext-install gd \
        && docker-php-ext-install curl \
        && docker-php-ext-install mysqli \
        && docker-php-ext-install pdo \
        && docker-php-ext-install pdo_mysql \
        && docker-php-ext-install mbstring \
        && docker-php-ext-install json

# Make sure the www-data permissions work
RUN groupmod -g 1000 www-data

# For PHP debugging
RUN yes | pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=on" >> /usr/local/etc/php/conf.d/xdebug.ini

# Install Tidy
RUN apt-get -y install libtidy-dev \
    	&& docker-php-ext-install tidy

# Making sure permissions are ok
RUN usermod -u 1000 www-data
RUN usermod -G staff www-data
