FROM composer:latest

WORKDIR /app

COPY composer.lock composer.json /app/
 
RUN addgroup -g 1000 alireza && adduser -G alireza -g alireza -s /bin/sh -D alireza
 
COPY . /app/


