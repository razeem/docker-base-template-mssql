# ------------------------------- base image -----------------------------------
# Force arm64 architecture
FROM --platform=linux/arm64 ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive

# Install dependencies for adding PPAs
RUN apt-get update && apt-get install -y --no-install-recommends \
    software-properties-common \
    lsb-release \
    ca-certificates \
    gnupg2 \
    curl \
    wget \
    apt-transport-https \
    && add-apt-repository ppa:ondrej/php -y \
    && apt-get update

# Install libraries required for GD and other PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    libfreetype6-dev \
    libjpeg-dev \
    libpng-dev \
    libwebp-dev \
    libzip-dev \
    unzip \
    git \
    cron \
    logrotate \
    openssh-server \
    apt-utils \
    nginx \
    php8.3 \
    php8.3-cli \
    php8.3-fpm \
    php8.3-mysql \
    php8.3-xml \
    php8.3-mbstring \
    php8.3-curl \
    php8.3-intl \
    php8.3-bcmath \
    php8.3-opcache \
    php8.3-gd \
    php8.3-dev \
    php-pear \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*


# ------------------------------- Composer -------------------------------
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer --version

# Step 1: Microsoft SQL Tools
RUN curl https://packages.microsoft.com/keys/microsoft.asc | tee /etc/apt/trusted.gpg.d/microsoft.asc \
    && curl https://packages.microsoft.com/config/ubuntu/22.04/prod.list | tee /etc/apt/sources.list.d/mssql-release.list \
    && ACCEPT_EULA=Y apt-get update \
    && ACCEPT_EULA=Y apt-get install -y mssql-tools18 unixodbc-dev \
    && echo 'export PATH="$PATH:/opt/mssql-tools18/bin"' >> ~/.bashrc

# Step 2: Install dependencies for sqlsrv/pdo_sqlsrv
RUN apt-get update && apt-get install -y \
        gcc g++ make autoconf libc-dev pkg-config \
        libssl-dev \
        libcurl4-openssl-dev \
        libgssapi-krb5-2 \
        unixodbc-dev

# Step 3: Install PHP SQLSRV extensions
RUN pecl install sqlsrv pdo_sqlsrv

# Step 4: Enable SQLSRV extensions
RUN echo "extension=sqlsrv.so" > /etc/php/8.3/cli/conf.d/20-sqlsrv.ini \
    && echo "extension=pdo_sqlsrv.so" > /etc/php/8.3/cli/conf.d/30-pdo_sqlsrv.ini \
    && echo "extension=sqlsrv.so" > /etc/php/8.3/fpm/conf.d/20-sqlsrv.ini \
    && echo "extension=pdo_sqlsrv.so" > /etc/php/8.3/fpm/conf.d/30-pdo_sqlsrv.ini

# Step 5: Restart PHP-FPM
RUN service php8.3-fpm restart

# Verify PHP extensions
RUN php -v && php -m | grep -E 'gd|sqlsrv|pdo_sqlsrv'

# Copy the package list file into the image
COPY ./Docker/app/apt-packages.env /tmp/apt-packages.env

# Install additional packages from the external file.
RUN apt-get update && \
    xargs -a /tmp/apt-packages.env apt-get install -y && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Copy custom SSH configuration (after SSH is installed)
COPY ./Docker/ssh/sshd_config /etc/ssh/sshd_config

# Set root password for SSH
RUN echo "root:Docker!" | chpasswd

# Generate SSH host keys
RUN ssh-keygen -A

# Create nginx user and directories
RUN mkdir -p /var/log/nginx \
    /var/cache/nginx \
    /var/lib/nginx/body \
    /var/lib/nginx/fastcgi \
    /var/lib/nginx/proxy \
    /var/lib/nginx/scgi \
    /var/lib/nginx/uwsgi

# Create nginx log files and set proper permissions
RUN touch /var/log/nginx/access.log /var/log/nginx/error.log \
    && chown -R www-data:adm /var/log/nginx \
    && chmod -R 755 /var/log/nginx

# Create PHP error log file
RUN touch /var/log/php_errors.log \
    && chown www-data:www-data /var/log/php_errors.log \
    && chmod 666 /var/log/php_errors.log

# Copy PHP configuration files
COPY ./Docker/php/php.ini /etc/php/8.3/fpm/php.ini
COPY ./Docker/php/php.ini /etc/php/8.3/cli/php.ini

# Copy your local Nginx configuration
COPY ./Docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./Docker/nginx/sites-available/ /etc/nginx/sites-available/
COPY ./Docker/nginx/sites-enabled/ /etc/nginx/sites-enabled/

# Optional: Copy additional config files if you have them
# COPY ./Docker/nginx/conf.d/ /etc/nginx/conf.d/

# Create web root directory and copy project files
RUN mkdir -p /var/www/html

# Copy Drupal project files
COPY ./web /var/www/html/web
COPY composer.json /var/www/html/composer.json
COPY composer.lock /var/www/html/composer.lock
COPY config /var/www/html/config
COPY patches /var/www/html/patches

# Set working directory
WORKDIR /var/www/html

# Configure Composer for better performance and timeouts
RUN composer config --global process-timeout 2000 \
    && composer config --global cache-dir /tmp/composer-cache

# Install Composer dependencies with retries and optimizations
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts || \
    composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts

# Copy SSL certificate to the application directory
COPY Docker/app/SslCertificate.crt.pem /app

# Copy Drupal settings
COPY Docker/app/settings.php /var/www/html/web/sites/default/

# create folder for assets and php files outside files folder (default location)
RUN mkdir -p /var/www/html/web/sites/default/dist/assets/css \
    /var/www/html/web/sites/default/dist/assets/js \
    /var/www/html/web/sites/default/dist/php \
    /var/www/html/web/sites/default/files

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 755 /var/www/html/web/sites/default/files \
    && chmod -R 755 /var/www/html/web/sites/default/dist

# Expose port 80, 443, and 2222 for SSH
EXPOSE 80 443 2222

# Test nginx configuration
RUN nginx -t

# Verify PHP configuration
RUN php -v && php -m

# Copy startup script, cron script, and logrotate config
COPY ./Docker/scripts/start.sh /start.sh
COPY ./Docker/scripts/cron.sh /var/www/html/cron.sh
COPY ./Docker/nginx/nginx-logrotate /etc/logrotate.d/nginx
RUN chmod +x /start.sh && chmod +x /var/www/html/cron.sh && chmod 644 /etc/logrotate.d/nginx

# Start services including SSH
CMD ["/start.sh"]
