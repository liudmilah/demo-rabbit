init: docker-down-clear docker-pull docker-build docker-up \
    api-init \
    rpc-user-info-init \
    email-sender-init \
    doc-converter-init

api-init: api-composer-install

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build --pull

api-composer-install:
	docker-compose run --rm rabbit-api-php-cli composer install

api-composer-update:
	docker-compose run --rm rabbit-api-php-cli composer update

email-sender-init:
	docker-compose run --rm rabbit-email-sender composer install

doc-converter-init:
	docker-compose run --rm rabbit-doc-converter composer install

rpc-user-info-init:
	docker-compose run --rm rabbit-rpc-user-info composer install