# docker build . -t local/swagger-php
# docker run --rm -it local/swagger-php sh
# docker run --rm -it local/swagger-php openapi -h

FROM composer as build

COPY . /app
RUN composer install --no-dev

FROM php:7.1-cli-alpine

COPY ./bin /app/bin
COPY ./src /app/src
COPY --from=build /app/vendor /app/vendor

RUN ln -s /app/bin/openapi /bin/openapi

WORKDIR /app
