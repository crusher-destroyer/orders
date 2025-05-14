## Order service

Order booking service

Locking stock using Symfony lock and Redis storage

Async stock decreasing using Symfony Messenger component

Modular architecture using Services, Factories, DTO

OpenAPI documentation via NelmioApiDocBundle

## Environment variables

Lock to prevent race conditions on stock items

```
LOCK_ORDER_TTL=5.0
```

Lock dsn for Symfony Lock

```
LOCK_DSN=redis://redis:6379
```

RMQ dsn for Symfony Messenger

```
MESSENGER_TRANSPORT_DSN=amqp://admin:password@rabbitmq:5672
```

Database dsn for Doctrine

```
DATABASE_URL="postgresql://postgres:password@postgres:5432/postgres"
```

All this environment up from environment https://github.com/crusher-destroyer/environment.git

## Installation

make sure 'make' is installed
```
make build
```

For consuming messages from RMQ

```
make consume
```

## Documentation

available after build

http://symfony.local/api/doc
