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