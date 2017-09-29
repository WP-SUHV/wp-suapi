#!/bin/sh

xdebug_listener_ip=`ifconfig | grep "inet " | grep -Fv 127.0.0.1 | awk '{print $2}'`
docker run -it --volume $PWD:/var/www/html -e XDEBUG_CONFIG="remote_host=$xdebug_listener_ip remote_port=9000 remote_enable=1 idekey=PHPSTORM" -e PHP_IDE_CONFIG="serverName=docker-xdebug" phpdocker/phpdocker /bin/bash

