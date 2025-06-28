start: up yarn-dev

rmps:
	docker rm -vf $$(docker ps -aq)

up:
	docker compose up -d

build:
	docker compose up -d --build

buildyarn:
	docker compose exec npm yarn build

yarn-dev:
	docker compose run -p 5173:5173 npm yarn dev

fresh:
	docker compose run --rm artisan migrate:fresh --seed

ngk:
	ngrok http --url=massive-wallaby-specially.ngrok-free.app 8080 --response-header-add='Content-Security-Policy: upgrade-insecure-requests'
