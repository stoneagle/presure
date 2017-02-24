FROM php

# 安装扩展
RUN docker-php-ext-install pdo pdo_mysql ctype mysql mysqli pcntl 
# pdo_sqlite sqlite3 curl ftp gd iconv json ldap mbstring mcrypt sockets xml zip

COPY /wrk /var/www/html/wrk
COPY /www /var/www/html/web

# 根据需要，设定环境权限
# RUN chmod -R 0777 /var/www/html/web
