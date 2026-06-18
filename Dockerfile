FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
        libzip-dev zip unzip \
    && docker-php-ext-install \
        mysqli \
        pdo_mysql \
        zip \
        opcache \
    && a2enmod rewrite headers \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN printf '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html\n\
    <Directory /var/www/html>\n\
        Options -Indexes +FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>\n' > /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
COPY . .

RUN rm -f composer.phar error_log \
    && chown -R www-data:www-data /var/www/html

EXPOSE 80
