symfony-test
============

A Symfony project created on March 21, 2017, 4:07 pm.

Features
========

* Symfony Guard Authenticator For Web Interface
* JWT Token Authenticator For API Interfaces
* HATEOAS Serialiser To build standardised API
* Pagerfanta integration for beautiful pagination
* Custom classes to handle Api problems and Exceptions
* Custom ApiTestcase to handle API testing and debugging
* Custom ResponseAsserter helper class to ease Guzzle response testing
* Symfony test enviroinment (app_test.php)
* Doctrine Data Fixtures

Sample Pages
============

There are several sample pages to showcase all the features. Inside you can find a simple Blog implementation 
including an admin area to handle blog posts creation and editing. There are also several REST Api endpoints 
implemented in `src/AppBundle/Controller/Api`


Initial configuration
=====================
1. Run `composer install` to install dependecies 
2. Make sure `parameters.yml` is filled with valid values
3. Configure test database name in `config_test.yml`
3. Run `bin/console doctrine:database:create` to create default database
4. Run `bin/console doctrine:database:create --env=prod` to create test database
5. To create database tables you can use one of the following commands:
    1. `bin/console doctrine:schema:update --force` updates the database from entities. Delete migration files if you 
    use this option. If you don't you will get errors if you decide to use doctrine migrations in the future.
    2. `bin/console doctrine:migrations:migrate` executes migration files to create tables.
6. To load the sample data through doctrine data fixtures run `bin/console doctrine:fixtures:load`. You can find 
fixtures definitions in `src/AppBundle/DataFixtures/ORM`

Third Party
===========

HTML template courtesy of Blackrock Digital: https://github.com/BlackrockDigital/startbootstrap-blog-post