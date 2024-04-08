# nester-mspr1


Build Dockerfile

```
sudo docker build -t nester:1.0.0 - < Dockerfile
sudo docker run -d -p 8000:80 -p 55000:55000 nester:1.0.0
```

Connexion admin
```
http://localhost:8000/index.php
```

Username: admin
Password: admin


API Port: 55000

Docker shell

```
docker exec -it ID /bin/bash
```

Activer l'écouteur et le keepalive dans le docker

```
pm2 start /var/www/html/ecouteur.php --name ecouteur --no-autorestart --user root
pm2 start /var/www/html/keepalive.php --name keepalive --no-autorestart --user root
```