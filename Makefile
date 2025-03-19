start: up yarn-dev

rmps:
	docker rm -vf $$(docker ps -aq)

up:
	docker-compose up -d

yarn-dev:
	docker-compose run -p 5173:5173 npm yarn dev
