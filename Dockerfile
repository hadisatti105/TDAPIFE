# Use official PHP with Apache
FROM php:8.2-apache

# Enable common PHP extensions if needed
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy all project files into Apache's web root
COPY . /var/www/html/

# Expose port 80 for web traffic
EXPOSE 80
