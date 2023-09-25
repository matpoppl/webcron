FROM composer:lts

WORKDIR /var/www/html/app

COPY composer.json composer.json
COPY composer.lock composer.lock

RUN set -xe; \
	composer install

COPY . .

CMD set -xe; \
	composer run setup
