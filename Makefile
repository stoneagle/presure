.PHONY: run-web php-composer

PWD := $(shell pwd)
USER := $(shell id -u)
GROUP := $(shell id -g)
#BRANCH := $(shell git rev-parse --abbrev-ref HEAD)
#VERSION := $(shell git describe --always --tags | grep -Eo "[0-9]+\.[0-9]+\.[0-9]+")

all: run-web

run-web:
	cd docker && sudo docker-compose -p "fpm-pressure-$(USER)" up

php-composer:
	sudo docker run -it --rm \
		-u $(USER):$(GROUP) \
		-v $(PWD)/www:/app \
		composer:1.1-php5 \
		install
