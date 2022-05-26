lint:
	./vendor/bin/phpcs --standard=PSR12 ./src/ ./bin/

test:
	./vendor/bin/phpunit tests


