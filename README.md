# Dashboard callcenter

Пример с двума фронт-частями  
* admin - react-admin panel  
* client - Material Dashboard React

у проекта есть плохие решения о которых я в принципе знаю,
но как пример кода пойдет :)

## requirements:
* docker
* docker-compose

## config:
write access is required for mysql volume `/opt/docker/callcenter/mysql`,  
defined in `docker-compose.services.yml`

## start:

- Make .env.local 

- service containers:
    ```
    docker-compose -f docker-compose.services.yml -p callcenter-services up -d
    ```
- php and nginx containers:
    ```
    docker-compose up -d
    ```

## build
```
docker-compose build
```

## push to Harbor
```
docker-compose push
```

## restart
```
docker-compose pull \
&& docker-compose -f docker-compose.consumers.yml -p callcenter-consumers down && docker-compose down \
&& docker-compose up -d && docker-compose -f docker-compose.consumers.yml -p callcenter-consumers up -d
```
