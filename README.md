# Arc

Bootstrap your new Laravel projects with a highly configurable production ready Docker environment.

## Overview

Arc is a simple Laravel package to add the ultimate docker environment for the majority of your applications. Built upon the amazing [ContinuousPipe Dockerfiles](https://github.com/continuouspipe/dockerfiles), this package will add the correct Dockerfile, Docker Compose and configurations into your Laravel project.

The Docker configuration provides the following - 

- PHP 7.2 (Configurable for 5.6, 7.0, 7.1 & 7.2)
- NodeJS 10 inc NPM
- NGINX
- MySQL 5.7
- REDIS 3
- ConfD templating
- SupervisorD for process management
- Configurable queue workers
- Auto start Laravel Horizon processes
- Auto start CRON
- Easily create a CRON only container. Useful for zero downtime deployments to a Kubernetes cluster
- Configure the entire infrastructure with environment variables.

## Requirements

This package is intended for new Laravel >5.5 projects.

## Installation

#### Require the package in your project

```bash
    composer require richdynamix/arc
```
#### Run the installer
```bash
    php artisan arc:install
```

Once complete the installer will remove Arc as a composer dependency.

#### Run the Container

```bash
    docker-compose up
```

**Note: Before running your Docker container, ensure you have already run your `composer install` on the host machine, otherwise you will need to pass in any GitHub personal access tokens or BitBucket SSH keys at built time to access the private repositories. See Configuration on how to do this.**

#### Connecting to the Web Container

As part of the Arc configurations there is a simple bash script added to the root of your project which allows you to call -
```bash
    ./ssh
```
This is simply a wrapper script for convenience. Under the hood its simply calling `docker exec -it arc_web_1 bash`


## Configuration

Arc is nothing more than a way to automatically copy the correct Docker configuration into your Laravel project. While you may have come across several Laravel based Docker environments, none of them cover the production ready setup that the ContinuousPipe dockerfiles provide.

ContinuousPipe Dockerfile offer an extremely flexible and solid infrastructure setup using a simple system of ConfD for templates and SupervisorD for controlling the start of services. The contents of `tools/docker/etc` and `tools/docker/usr` are copied into the container at build time which means we can influence any environment variable and configuration.

Rather that explain all the functions and environent variables available to Arc, it is easier to point you to the documentation for the parent images.

- [PHP NGINX](https://github.com/continuouspipe/dockerfiles/tree/master/php/nginx) - The direct parent of the Arc Docker file. By default it uses PHP 7.1 but you can change this within the Arc Dockerfile to match your needs. (Available versions are 5.6, 7.0 and 7.1)
- [Ubuntu 16.04](https://github.com/continuouspipe/dockerfiles/tree/master/ubuntu/16.04) - This is the base image which the PHP image extends. This setups all the ConfD and SupervisorD configuration.

To manipulate your environment you can add values for any of the environment variables for any of the above. Additionally there are a few specific for Arc which are all defined in `tools/docker/usr/local/share/env/20-arc-env`.

Variable | Description | Expected values | Default
--- | --- | --- | ----
START_QUEUE | Should the Laravel Queue worker be started. | true/false | true
RUN_LARAVEL_CRON | Should the Laravel Queue worker be started. | true/false | false
START_HORIZON | Should Laravel Horizon worker be started. Do not start START_QUEUE & START_HORIZON at the same time. | true/false | false

**Please Note: `START_QUEUE` & `START_HORIZON` are automatically configured if you install laravel/horizon during installation.** 

## TODO

- Add additional packages that are frequently used
- Easier configuration of packages that require some manual setup

## Credits

This package would not have been possible would it not have been for the amazing work of the ContinuousPipe team. But a special thanks to Samuel for the introduction of ContinuousPipe and Kubernetes. His ability to teach Docker concepts has been of huge value. A huge thanks to Kieren for his attention to detail and everlasting patience when helping me understand the ContinuousPipe Dockerfiles and setup.

- [Samuel ROZE](https://github.com/sroze)
- [Kieren Evans](https://github.com/kierenevans)


## Licence

MIT License

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.