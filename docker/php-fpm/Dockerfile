FROM yiisoftware/yii-php:8.1-fpm as phpfpm

RUN apt-get update -y && apt-get upgrade -y
RUN apt-get -y install cron
RUN apt-get clean all

COPY app/ /app/
RUN composer install

EXPOSE 9000
