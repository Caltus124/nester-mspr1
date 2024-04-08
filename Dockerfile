FROM php:7.4-apache
RUN apt-get update && apt-get install -y git
RUN docker-php-ext-install sockets
RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash -
RUN apt-get install -y nodejs
RUN npm install -g pm2
RUN git clone https://github.com/Caltus124/nester-mspr1.git /tmp/nester-mspr1
RUN mv /tmp/nester-mspr1/* /var/www/html/
RUN chown -R www-data:www-data /var/www/html
EXPOSE 80
CMD ["apache2ctl", "-D", "FOREGROUND"]
RUN pm2 start /var/www/html/ecouteur.php --name ecouteur --no-autorestart --user root
RUN pm2 start /var/www/html/keepalive.php --name keepalive --no-autorestart --user root
