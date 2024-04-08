# Utilisez une image de base contenant Apache, PHP et Node.js
FROM php:7.4-apache

# Mettre à jour le gestionnaire de paquets et installer git
RUN apt-get update && apt-get install -y git

# Installer Node.js et npm
RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash -
RUN apt-get install -y nodejs

# Installer PM2
RUN npm install -g pm2

# Clonez le dépôt Git
RUN git clone https://github.com/Caltus124/nester-mspr1.git /tmp/nester-mspr1

# Déplacer les fichiers clonés vers le répertoire HTML d'Apache
RUN mv /tmp/nester-mspr1/* /var/www/html/

# Assurez-vous que les permissions sont correctes pour les fichiers dans le répertoire HTML
RUN chown -R www-data:www-data /var/www/html

# Exposer le port 80 pour le trafic web
EXPOSE 80

# Démarrez Apache en mode foreground au démarrage du conteneur
CMD ["apache2ctl", "-D", "FOREGROUND"]

# Démarrer les fichiers avec PM2 sans redémarrage automatique
RUN pm2 start /var/www/html/ecouteur.php --name ecouteur --no-autorestart
RUN pm2 start /var/www/html/keepalive.php --name keepalive --no-autorestart
