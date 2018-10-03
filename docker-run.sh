#!/usr/bin/env bash

docker run --name mysql-XCust-1 \
    -p 3307:3306 \
    -e MYSQL_ROOT_PASSWORD=root \
    -e MYSQL_DATABASE=blogdb \
    -e MYSQL_USER=client \
    -e MYSQL_PASSWORD=heavypwd \
    -d mysql:5.7
