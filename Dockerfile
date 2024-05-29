# Use an official Ubuntu as a parent image
FROM ubuntu:20.04

# Set the environment variable for noninteractive installation
ENV DEBIAN_FRONTEND=noninteractive

# Install system dependencies and software-properties-common for managing PPAs
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    libzip-dev \
    nginx \
    supervisor \
    software-properties-common \
    vim \
    && apt-get clean

# Add repository for PHP 8.2 and update package lists
RUN add-apt-repository ppa:ondrej/php -y && apt-get update

# Install PHP 8.2 and its extensions (including php8.2-curl)
RUN apt-get install -y \
    php8.2 \
    php8.2-fpm \
    php8.2-mysql \
    php8.2-cli \
    php8.2-mbstring \
    php8.2-xml \
    php8.2-bcmath \
    php8.2-zip \
    php8.2-tokenizer \
    php8.2-dev \
    php8.2-curl \
    && apt-get clean

# Install Node.js and npm from NodeSource
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash - \
    && apt-get install -y nodejs

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www

# Set the environment variable to allow Composer plugins as super user
ENV COMPOSER_ALLOW_SUPERUSER=1

# Copy existing application directory contents
COPY . /var/www

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Install Node.js dependencies
RUN npm install

# Copy nginx configuration file
COPY .docker/nginx/nginx.conf /etc/nginx/sites-available/default

# Supervisor configuration
COPY .docker/supervisord.conf /etc/supervisor/supervisord.conf

# Remove the default site
RUN rm /etc/nginx/sites-enabled/default && \
    ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Expose port 80
EXPOSE 80

# Start Nginx and PHP-FPM through Supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
