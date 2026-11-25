FROM php:8.3-apache
RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libpq-dev libzip-dev libonig-dev libicu-dev libxml2-dev \
 && docker-php-ext-install pdo_pgsql mbstring bcmath zip intl \
 && apt-get clean && rm -rf /var/lib/apt/lists/* \
 && a2enmod rewrite \
 && sed -ri 's!DocumentRoot /var/www/html!DocumentRoot /var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
 && printf '<Directory /var/www/html/public>\n  AllowOverride All\n  Require all granted\n</Directory>\n' > /etc/apache2/conf-available/laravel.conf \
 && a2enconf laravel
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
WORKDIR /var/www/html
COPY . /var/www/html
RUN composer install --no-interaction --prefer-dist --optimize-autoloader \
 && mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache/data storage/logs bootstrap/cache storage/app/private \
 && chmod -R 775 storage bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache
EXPOSE 80
CMD ["apache2-foreground"]
COPY docker/entrypoint.sh /usr/local/bin/carwash-entrypoint
RUN chmod +x /usr/local/bin/carwash-entrypoint
ENTRYPOINT ["/usr/local/bin/carwash-entrypoint"]
