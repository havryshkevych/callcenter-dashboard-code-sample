#Reviews

##requirements:
* docker
* docker-compose

##config:
write access is required for mysql volume `/opt/docker/callcenter/mysql`,  
defined in `docker-compose.services.yml`

##start:
service containers:
```
docker-compose -f docker-compose.services.yml -p callcenter-services up -d
```
php and nginx containers:
```
docker-compose up -d
```

##build
```
docker-compose build
```

##push to Harbor
```
docker-compose push
```

##restart
```
docker-compose pull \
&& docker-compose -f docker-compose.consumers.yml -p callcenter-consumers down && docker-compose down \
&& docker-compose up -d && docker-compose -f docker-compose.consumers.yml -p callcenter-consumers up -d
```
