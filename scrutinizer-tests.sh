mkdir var/jwt
openssl genrsa -out var/jwt/private.pem -aes256 -passout pass:secret 4096
openssl rsa -pubout -in var/jwt/private.pem -out var/jwt/public.pem -passin pass:secret
bin/console doctrine:database:create --env=test
bin/console doctrine:schema:update --force --env=test
sudo chmod 777 var -R
sudo chown www-data.www-data var/jwt/*.*
vendor/bin/phpunit
vendor/bin/behat