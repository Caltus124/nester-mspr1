# nester server

## Building Dockerfile

```
sudo docker build -t nester:1.0.0 - < Dockerfile
sudo docker run -d -p 8000:80 -p 55000:55000 nester:1.0.0
```

## Admin Login

Navigate to the following URL in your web browser:

```
http://localhost:8000/index.php
```

Use the following credentials to log in:

- **Username:** admin
- **Password:** admin

## Docker Shell Access

To access the Docker shell, execute the following command:

```
docker exec -it ID /bin/bash
```

Replace `ID` with the ID of the running Docker container.

## Enabling Listener and Keepalive in Docker

Execute the following commands within the Docker shell to activate the listener and keepalive functionalities:

```
pm2 start /var/www/html/ecouteur.php --name ecouteur --no-autorestart --user root
pm2 start /var/www/html/keepalive.php --name keepalive --no-autorestart --user root
```

These commands start the listener and keepalive processes using PM2, ensuring they run persistently and under the root user within the Docker container.
