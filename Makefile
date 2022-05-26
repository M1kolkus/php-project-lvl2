lint:
	./vendor/bin/phpcs --standard=PSR12 ./src/ ./bin/

test:
	./vendor/bin/phpunit tests

test-coverage:
	./vendor/bin/phpunit tests -- --coverage-clover build/logs/clover.xml

install:
	composer install


