#!/bin/bash

cd /var/www/html/abhishektest/util

mkdir ../log 2> /dev/null

nohup php msg-wsserver.php 2>&1 > ../log/ws.out &
nohup php msg-udpserver.php 2>&1 > ../log/udp.out &
