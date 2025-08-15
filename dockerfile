
FROM php:8.2-apache


WORKDIR /var/www/html

# Instala extensão mysqli
RUN docker-php-ext-install mysqli


COPY . .


RUN a2enmod rewrite


RUN chown -R www-data:www-data /var/www/html


EXPOSE 80
EXPOSE 3306
