# PHP ची Official Image वापरा
FROM php:8.2-apache

# mysqli Extension Install करा
RUN docker-php-ext-install mysqli

# तुमच्या Project च्या File Copy करा
COPY . /var/www/html/