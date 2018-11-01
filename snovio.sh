#!/bin/bash
# Install dependencies

apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 9DA31620334BD75D9DCB49F368818C72E52529D4
echo "deb [ arch=amd64,arm64 ] https://repo.mongodb.org/apt/ubuntu xenial/mongodb-org/4.0 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-4.0.list

sudo apt-get update
apt-get install -y mongodb-org mongodb-org-server

add-apt-repository ppa:ondrej/php
apt-get update
apt-get install -y apache2 git curl npm php7.2 libapache2-mod-php7.2 php7.2-common php7.2-cli php-pear php7.2-dev php7.2-curl php7.2-json php-xdebug

# Configure Apache
echo "<VirtualHost *:80>
    DocumentRoot /var/www/html
    AllowEncodedSlashes On
    SetEnv APPLICATION_ENV "development"

    <Directory /var/www/html>
        Options +Indexes +FollowSymLinks
        DirectoryIndex index.php index.html
        Order allow,deny
        Allow from all
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>" > /etc/apache2/sites-available/000-default.conf

pecl install mongodb
echo "extension=mongodb.so" >> /etc/php/7.2/apache2/php.ini

a2enmod rewrite
service apache2 restart

service mongod start
systemctl enable mongod.service

# Reset home directory of vagrant user
if ! grep -q "cd /var/www" /home/ubuntu/.profile; then
    echo "cd /var/www" >> /home/ubuntu/.profile
fi