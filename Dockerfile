## Build local:
# docker build . -t local/swagger-php
# docker run --rm -it local/swagger-php openapi -h
# docker run --rm -it local/swagger-php sh

FROM composer as build

COPY . /swagger-php
RUN composer install --no-dev

FROM php:7.1-cli-alpine

COPY ./bin /swagger-php/bin
COPY ./src /swagger-php/src
COPY --from=build /swagger-php/vendor /swagger-php/vendor

RUN ln -s /swagger-php/bin/openapi /bin/openapi

RUN mkdir /app
WORKDIR /app
