# Use an official PHP-fpm image runtime as a parent image
FROM php:7.2-fpm-stretch

# Install composer
RUN apt-get update -y && apt-get install -y openssl zip unzip curl libcurl3-dev apt-utils libpng-dev libxml2-dev git nano
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install missing php packages
#RUN apt-get update -y && apt-get install -y php7.2-zip php7.2-mbstring php7.2-common php7.2-gd php7.2-cli php7.2-mysqli php7.2-curl php7.2-json php7.2-pdo_mysql php7.2-xml 
RUN docker-php-ext-install calendar curl json mbstring gd mysqli pdo_mysql xml zip


# Set the working directory
RUN mkdir -p /var/www/lab
WORKDIR /var/www/lab

# Add custom php ini files
ADD ./custom.ini /usr/local/etc/php/conf.d/

# Copy the current directory contents into the working directory
#ADD . /var/www/lab

#CMD php /var/www/lab/artisan serve --host=0.0.0.0 --port=7000
#EXPOSE 9000
# 172.17.0.1
