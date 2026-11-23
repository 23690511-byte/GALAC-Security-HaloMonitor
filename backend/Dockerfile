FROM php:8.2-apache

# Instalar extensiones de PDO MySQL requeridas
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Habilitar módulos de Apache (rewrite y headers)
RUN a2enmod rewrite headers

# Configurar Apache para permitir directivas .htaccess
RUN sed -ri -e 's!/var/www/html!/var/www/html!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/var/www/html!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN echo '<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

# Copiar el código del backend
COPY . /var/www/html/

# Ajustar permisos para Apache www-data
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
