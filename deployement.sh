#!/bin/sh

wget https://raw.githubusercontent.com/YummYume/StackUp/master/docker-compose.prod.yml

docker login -u $(DOCKER_USERNAME) -p $(DOCKER_TOKEN)
docker compose pull symfony-app
docker compose up -d --no-deps
docker logout