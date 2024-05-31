# Use an official Alpine as a parent image
FROM alpine:3.18

# Set the environment variable for noninteractive installation
ENV DEBIAN_FRONTEND=noninteractive

# Install system dependencies
RUN apk --no-cache add \
    curl \
    git \
    unzip \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    libzip-dev \
    nginx \
    supervisor \
    vim \
    nodejs \
    npm

# Add repository for PHP 8.2 and install PHP 8.2 and its extensions
RUN apk add --no-cache --repository=http://dl-cdn.alpinelinux.org/alpine/edge/community/ php82 \
    php82-fpm \
    php82-mysqli \
    php82-cli \
    php82-mbstring \
    php82-xml \
    php82-bcmath \
    php82-zip \
    php82-tokenizer \
    php82-dev \
    php82-curl \
    php82-phar \
    php82-session \
    php82-fileinfo \
    php82-dom \
    php82-pdo\
    php82-pdo_mysql\
    php82-xmlwriter

# Create a symlink for php if it doesn't already exist
RUN [ ! -e /usr/bin/php ] && ln -s /usr/bin/php82 /usr/bin/php || true

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
COPY .docker/nginx/nginx.conf /etc/nginx/http.d/default.conf

# Supervisor configuration
COPY .docker/supervisord.conf /etc/supervisord.conf

# Ensure the default site is removed if it exists
RUN rm -f /etc/nginx/http.d/default.conf

# Expose port 80
EXPOSE 80

# Start Nginx and PHP-FPM through Supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
