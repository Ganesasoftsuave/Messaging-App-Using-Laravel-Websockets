FROM php:8.1.29-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sockets

# # Install Redis extension and enable
RUN pecl install redis && docker-php-ext-enable redis

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js v21.7.1
#RUN curl -fsSL https://deb.nodesource.com/setup_21.x | bash - && \
 #   apt-get install -y nodejs

# Install Node.js v21.7.1
RUN curl -fsSL https://nodejs.org/dist/v21.7.1/node-v21.7.1-linux-x64.tar.xz -o node-v21.7.1-linux-x64.tar.xz && \
    tar -xf node-v21.7.1-linux-x64.tar.xz && \
    mv node-v21.7.1-linux-x64 /usr/local/node && \
    ln -s /usr/local/node/bin/node /usr/local/bin/node && \
    ln -s /usr/local/node/bin/npm /usr/local/bin/npm && \
    ln -s /usr/local/node/bin/npx /usr/local/bin/npx && \
    rm node-v21.7.1-linux-x64.tar.xz

# Set working directory
WORKDIR /var/www/html/

# COPY the source code into container
COPY . .


RUN export COMPOSER_ALLOW_SUPERUSER=1
#RUN composer install --no-interaction
#RUN chmod -R 777 storage
