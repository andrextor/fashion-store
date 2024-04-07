My-store 😁👚
===

Requirements
===
- PHP 7.3
- PostgreSql 12.1
- Nginx / Apache



Installation from local environment
===

Run the following commands:

```shell

# Clone the repository
$ git clone git@gitlab.com:andrextor/store.git

# enter the app directory
cd my-store

# install the app
composer install

# assign environment file
cp .env.example .env
cp .env .env.testing

# run commant migrations
php artisan migrate

# Run seeders with default productos with images
cp -r resources/images/default_products/ storage/app/public/images/.

# run commant for create products
php artisan db:seed

```

Run tests

```shell

php artisan vendor/bin/phpunit

```

Open app and pay for a product. 💳👕
===# fashion-store
