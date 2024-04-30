#!/bin/bash
composer install --working-dir=tools/php-cs-fixer
chmod +x phpcbf.phar
./phpcbf.phar
tools/php-cs-fixer/vendor/bin/php-cs-fixer fix .