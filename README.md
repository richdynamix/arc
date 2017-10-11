# Arc (Work In Progress)

Production ready docker based development environment for your Laravel project.

## Overview

Arc is a simple Laravel package to add the ultimate docker environment for the majority of your applications. Built upon the amazing [ContinuousPipe Dockerfiles](https://github.com/continuouspipe/dockerfiles), the package will add the correct Dockerfile, Docker Compose and configurations into your Laravel project.

The Docker configuration provides the following - 

- PHP 7.1 (Configurable for 5.6, 7.0 and 7.1)
- NGINX
- MySQL 5.7
- ConfD templating
- SupervisorD for process management
- Configurable queue workers
- Auto start Laravel Horizon processes
- Auto start CRON
- Easily create a CRON only container. Useful for zero downtime deployments to a Kubernetes cluster
- Easily refresh your DB on every container start. (A development feature only and by default is switched off)
- Configure the entire infrastructure with environment variables.

## Installation

```bash
composer require richdynamix/arc
```

#### Register the Service Provider

Add the following to your providers in `config/app.php` - 

`Richdynamix\Arc\ArcServiceProvider::class`

***Please Note: if you are using Laravel 5.5 or newer, please skip service provider registration.***

#### Publish the Docker Configuration

```bash
php artisan vendor:publish --provider="Richdynamix\Arc\ArcServiceProvider"
```

#### Run the Container

```bash
docker-compose up
```

**Note: Before running your Docker container, ensure you have already run your `composer install` on the host machine, otherwise you will need to pass in any GitHub personal access tokens or BitBucket SSH keys at built time to access the private repositories. See Configuration on how to do this.**

## Configuration

Arc is nothing more than a way to automatically copy the correct Docker configuration into your Laravel project. While you may have come across several Laravel based Docker environments, none of them cover the production ready setup that the ContinuousPipe dockerfiles provide.

ContinuousPipe Dockerfile offer an extremely flexible and solid infrastructure setup using a simple system of ConfD for templates and SupervisorD for controlling the start of services. The contents of `tools/docker/etc` and `tools/docker/usr` are copied into the container at build time which means we can influence any environment variable and configuration.

Rather that explain all the functions and environent variables available to Arc, it is easier to point you to the documentation for the parent images.

- [PHP NGINX](https://github.com/continuouspipe/dockerfiles/tree/master/php/nginx) - The direct parent of the Arc Docker file. By default it uses PHP 7.1 but you can change this within the Arc Dockerfile to match your needs. (Available versions are 5.6, 7.0 and 7.1)
- [Ubuntu 16.04](https://github.com/continuouspipe/dockerfiles/tree/master/ubuntu/16.04) - This is the base image which the PHP image extends. This setups all the ConfD and SupervisorD configuration.

To manipulate your environment you can add values for any of the environment variables for any of the above. Additionally there are a few specific for Arc which are all defined in `tools/docker/usr/local/share/env/20-arc-env`.

Variable | Description | Expected values | Default
--- | --- | --- | ----
START_QUEUE | Should the Laravel Queue worker be started. | true/false | false
RUN_LARAVEL_CRON | Should the Laravel Queue worker be started. | true/false | false
REFRESH_INSTALL_ON_START | Should the DB be cleaned, migrated and seeded on every container start. | true/false | false
START_HORIZON | Should Laravel Horizon worker be started. Do not start START_QUEUE & START_HORIZON at the same time. | true/false | false