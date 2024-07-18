# Symfony More Challenge

The challenge is to develop some backend APIs and a recommendation engine

## Prerequisite

### Conventional

* You have an environment that can run Symfony.
* You have installed `composer` in your environment.

### Docker

You will need these if you intend to run this in Docker.

* [Windows WSL](https://learn.microsoft.com/en-us/windows/wsl/install)
* Docker Desktop 4.18.0 or higher
* Docker Compose version v2.17.2 or higher

## Get started

* Clone this repository
* Change directory into the newly cloned repository
* Make a copy of `.env.example`, and name it `.env`.
* Edit the content of `.env` with your environment in mind, for example:

```
# Need this if you run this in a container
MYSQL_DB=challenge_db
MYSQL_HOST=challenge-mysql
MYSQL_PWD=k4sKkEcOWjvm5lIW
MYSQL_ROOT_PWD=Wjvm5lIWk4sKkEcO
MYSQL_USR=challenge_user
# Should use the same credentials as above
DATABASE_URL="mysql://challenge_user:k4sKkEcOWjvm5lIW@challenge-mysql:3306/challenge_db"
# This is the used by api/doc
API_DOC_HTTP_URL=http://localhost
```

### Conventional

If your environment can already run Symfony.

* You need to have a database created using the credentials you have defined in the previous section.
* Install the packages `composer install`
* Run the test `php bin/phpunit`

### Docker

To run this in Docker.

* You need not manually create any database. `docker-compose.yaml` alredy handles this.
* Install the packages
```
docker-compose run --rm php composer install
```
* Start the container
```
docker-compose up -d
```
* You can then access the application through the following URL:

```
http://127.0.0.1:8062

# api/doc can be found here
http://127.0.0.1:8062/api/doc
```

## Notes

* The function can be found at `src/Service/EstimatorService.php`
* The unit and api be found within the `test/` directory
    * Run the test `vendor/bin/codecept run`
* Only entity properties relevant to this challenge is defined.
* Find the entities in `src/Entity`

## Assumptions

In order to avoid making the solution overly complex, here are some assumptions and boundaries.

*

## Entity Relationship Diagrams

```mermaid
---
title: My small shop
---
erDiagram

    user ||--|{ order : places
    order ||--|{ order_product : contains
    category ||--|{ product : contains
    product ||--o{ product_attribute : "might have"
    product_attribute ||--|{ attribute : have
```
