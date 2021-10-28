#!/bin/bash
docker build -t demo-cli-php:1.0 .
docker run --rm \
  -v "$(pwd)":/var/www/html/www-data \
  -w /var/www/html/www-data \
  -u $(id -u):$(id -g) \
  demo-cli-php:1.0 \
  /bin/bash -c "composer install && php index.php ./input.csv ./output.csv"