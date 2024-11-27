FROM php:8.2-apache

# Set the working directory
WORKDIR /var/www/html

# Install system dependencies and required PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    && docker-php-ext-install \
    intl \
    zip \
    && docker-php-ext-enable intl zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set the document root to Symfony's public directory
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the entire application code
COPY . /var/www/html

# Skip Symfony's @auto-scripts during build
ENV SYMFONY_SKIP_AUTOLOAD=1

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Clear the cache manually
RUN php bin/console cache:clear --env=prod

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/var

# Set environment variables for production
ENV APP_ENV=prod
ENV APP_DEBUG=0

EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
