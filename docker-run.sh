#!/usr/bin/env bash

docker run --name mysql \
    -p 3306:3306 \
    -e MYSQL_ROOT_PASSWORD=root \
    -e MYSQL_DATABASE=blogdb \
    -e MYSQL_USER=client \
    -e MYSQL_PASSWORD=heavypwd \
    -d mysql:5.7
