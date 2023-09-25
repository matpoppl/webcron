FROM node:lts-alpine

EXPOSE 8080/tcp
EXPOSE 35729/tcp

ENV LIVERELOAD_HOST="0.0.0.0"
ENV LIVERELOAD_PORT=35729
ENV DEV_ASSETS_HOST="0.0.0.0"
ENV DEV_ASSETS_PORT=8080

WORKDIR /var/www/html/_dev

COPY package.json package.json
COPY package-lock.json package-lock.json

RUN set -xe; \
	npm ci --foreground-scripts

COPY . .

CMD set -xe; \
	npm run dev
