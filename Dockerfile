FROM php:7.4-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    mysqli \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/application/cache \
    && chmod -R 777 /var/www/html/application/logs \
    && mkdir -p /tmp/ci_sessions \
    && chown www-data:www-data /tmp/ci_sessions \
    && chmod 777 /tmp/ci_sessions

# Apache configuration for CodeIgniter
RUN echo '<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/codeigniter.conf

RUN a2enconf codeigniter

# Copy startup script
COPY docker/scripts/start.sh /usr/local/bin/start.sh
COPY docker/scripts/healthcheck.sh /usr/local/bin/healthcheck.sh
RUN chmod +x /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/healthcheck.sh

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
    CMD /usr/local/bin/healthcheck.sh

# Expose port 80
EXPOSE 80

# Start with custom script
CMD ["/usr/local/bin/start.sh"]