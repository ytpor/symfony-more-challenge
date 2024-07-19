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
* Run migration.

```
# Update migration and execute migration
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

* Run the test `vendor/bin/codecept run`

### Docker

To run this in Docker.

* You need not manually create any database. `docker-compose.yaml` already handles this.
* Install the packages
```
docker-compose run --rm php composer install
```

* Run migration.

```
# Update migration and execute migration
docker compose run --rm php php bin/console make:migration
docker compose run --rm php php bin/console doctrine:migrations:migrate
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

* Run the test

```
docker-compose run --rm php vendor/bin/codecept run
```

## Functionality

### 1. User Management (Security Focus)

User authentication and authorization has **not** been put in place. The existance of an API to create users has been crafted.

### 2. Product Management (Data Modeling)

Please refer to the ER Diagram for how the different data models relate to each other.

#### Entity Relationship Diagrams

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

### 3. Purchase History (Data Persistence)

The user purchase are stored in the `order` and `order_product` tables. A snapshot of the product details is being stored in the `order_product`, while it still has a reference to the actual product through `product_id`.

### 4. Recommendation Engine (Algorithmic Thinking)

There is a rudimentary effort done for this topic. The intention is to use a collaborative filtering to achieve this. The code can be found at `src/Service/RecommendService.php`.

### 5. API Design & Scalability (Architecture & Best Practices):

* The documentation for the REST API is generated using  annotations within the controller. It followe the RESTful principles and utilizes proper HTTP status codes. It can be accessed at `api/doc`, eg. http://localhost/api/doc

![API doc](/doc/images/api-doc.png)

* Database migration is done through the use of [Doctrine Migrations Bundle](https://symfony.com/bundles/DoctrineMigrationsBundle/current/index.html)

* `Redis` used as the caching mechanisms. Some common queries are being cached.


```php
// src/Repository/CategoryRepository.php

// ...
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
// ...

    private $cache;
    private $logger;

    public function __construct(
        ManagerRegistry $registry,
        LoggerInterface $logger,
        CacheInterface $cache
    )
    {
        parent::__construct($registry, Category::class);
        $this->cache = $cache;
        $this->logger = $logger;
    }

// ...

    public function findAllBy(): array
    {
        return $this->cache->get('category_all', function (ItemInterface $item) {
            $item->expiresAfter(3600); // Cache for 1 hour

            return $this->findBy([]);
        });
    }

// ...
```

## 6. Unit Tests and API Tests

* Test scripts can be found in the `tests` directory.

```
# Run the test - Make sure the server is running
docker-compose run --rm php vendor/bin/codecept run
```

## 7. Logging/Monitoring

* [Monolog](https://github.com/Seldaek/monolog) through the [Monolog Bundle](https://symfony.com/doc/current/logging.html#monolog) is currently being used. For future expansion, it can send logs to verious web services for application monitoring, eg. [Datadog](https://docs.datadoghq.com/logs/log_collection/php/?tab=phpmonolog)

```php
// src/Repository/CategoryRepository.php

// ...
use Psr\Log\LoggerInterface;
// ...

$this->logger->error($message);
```

## 8. Scalability Design

* This API can run as a Docker container.
