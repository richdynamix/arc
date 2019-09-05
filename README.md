# Arc

Bootstrap your new Laravel projects with a highly configurable, production ready Docker environment. Automated build, test and deploy to [Kubernetes](https://kubernetes.io/) using [Travis CI](https://travis-ci.com/)

## Overview

Arc is a simple Laravel package to add the ultimate Docker environment for the majority of your applications. Built upon the amazing [ContinuousPipe Dockerfiles](https://github.com/continuouspipe/dockerfiles), this package will add the correct Dockerfile, Docker Compose and configurations into your Laravel project.

Additionally - if selected, it can also add a Continuous Integration & Continuous Deployment configuration to work with Travis CI and Kubernetes.

The Docker configuration provides the following:

- PHP 7.3 (Configurable for 5.6, 7.0, 7.1, 7.2 & 7.3)
- NodeJS 11 inc NPM
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

CI/CD Configuration contains the following:

- Travis CI YAML configuration
- **Build** and **Push** your Docker image to [Docker Hub](https://hub.docker.com/)
- Automated BASH file & Dockerfile linting
- Automated [PHP CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer), [PHPMD](https://github.com/phpmd/phpmd) & [PHPStan](https://github.com/phpstan/phpstan) static code analysis
- Automated [PHP Unit](https://phpunit.de/) tests
- Configure **Kubernetes** cluster context via the [Kubectl](https://kubernetes.io/docs/reference/kubectl/overview/) tool
- Configure and deploy your application to Kubernetes with the use of [Helm](https://www.helm.sh/) & [Helm Charts](https://helm.sh/docs/developing_charts/)

## Requirements

This package is intended for new Laravel 6 projects.

## Installation

#### Require the package in your project

```bash
    composer require richdynamix/arc
```
#### Run the installer
```bash
    php artisan arc:install
```

You will be prompted to choose if you want CI/CD configurations.

```bash
    Would you like a Travis CI and K8s CD configuration? (yes/no) [no]:
```

Once complete the installer will remove Arc as a composer dependency.

**Please Note: If you choose to use the CI/CD configurations then there are some local environment variables that are required before you can run the containers**

```bash
    export DOCKER_USERNAME=myUsername
    export DOCKER_REPO=my-repo
    export TAG=latest

    export GITHUB_TOKEN=1234567890 # optional
```

#### Run the Container

```bash
    docker-compose up
```

### Continuous Integration and Deployment

Required environment variables to be added to your Travis CI repository settings -

Variable | Description | Expected values | Default
--- | --- | --- | ----
$GITHUB_TOKEN | Personal Access Token used to access a private repository | string/null | null
$DOCKER_EMAIL | The email address of your Docker account | string | null
$DOCKER_PASSWORD | Docker account password to push and pull your image | string | null
$DOCKER_REPO | The name of your Docker repository to be pushed to | string | null
$DOCKER_USERNAME | The username of your Docker account to push and pull images | string  | null
$K8S_CLUSTER | The name of the cluster in your Kubectl configuration | string | null
$K8S_CLUSTER_API | Kubernetes API endpoint URL | FQDN string | null
$K8S_PASSWORD | The password of the Kubernetes user to access the cluster | string | null
$K8S_USERNAME | The username of the Kubernetes user to access the cluster | string | null

### Static Code Analysis

- PHP CodeSniffer has been configured with a `phpcs.xml` file that will be in the root of your project. This will follow PSR2 coding styles within your `app` folder only.
- PHPMD has been configured to use a `ruleset.xml` file within the root of your project that follows best practices.
- PHPStan has been configured on the lowest possible level `0`. If you wish to increase this level you may modify the `tools/docker/usr/local/share/container/plan.sh` file at the function `do_phpstan`

*You may use your phpcs.xml and ruleset.xml file to configure your IDE such as PHPStorm to automatically check your code during development*

From within the container, you may run any of your static code analysis tools at any time using the following commands:

- `container phpcs`
- `container phpmd`
- `container phpstan`

Additionally, you can run `container phpunit` to run your test suite within the container.

#### Connecting to the Web Container

As part of the Arc configurations there is a simple bash script added to the root of your project which allows you to call -
```bash
    ./ssh web
```
This is simply a wrapper script for convenience. Under the hood its simply calling `docker exec -it web bash`

Addtionally, you may swap the `web` argument for any of the container names i.e. `./ssh database`, `./ssh redis`


## Configuration

Arc is nothing more than a way to automatically copy the correct Docker configuration into your Laravel project. While you may have come across several Laravel based Docker environments, none of them cover the production ready setup that the ContinuousPipe Dockerfile provides.

ContinuousPipe Dockerfile offers an extremely flexible and solid infrastructure setup using a simple system of ConfD for templates and SupervisorD for controlling the start of services. The contents of `tools/docker/etc` and `tools/docker/usr` are copied into the container at build time which means that we can influence any environment variable and configuration.

Rather that explain all the functions and environment variables available to Arc, it is easier to point you to the documentation for the parent images:

- [PHP NGINX](https://github.com/continuouspipe/dockerfiles/tree/master/php/nginx) - The direct parent of the Arc Docker file. By default it uses PHP 7.1 but you can change this within the Arc Dockerfile to match your needs. (Available versions are 5.6, 7.0 and 7.1)
- [Ubuntu 16.04](https://github.com/continuouspipe/dockerfiles/tree/master/ubuntu/16.04) - This is the base image which the PHP image extends. This sets up all the ConfD and SupervisorD configuration.

To manipulate your environment you can add values for any of the environment variables for any of the above. Additionally there are a few specific to Arc which are all defined in `tools/docker/usr/local/share/env/20-arc-env`.

Variable | Description | Expected values | Default
--- | --- | --- | ----
START_QUEUE | Should the Laravel Queue worker be started. | true/false | true
RUN_LARAVEL_CRON | Should the Laravel Queue worker be started. | true/false | false
START_HORIZON | Should Laravel Horizon worker be started. Do not start START_QUEUE & START_HORIZON at the same time. | true/false | false
COMPOSER_INSTALL_FLAGS | Allow the override of composer flags during installation | string | '--no-interaction --optimize-autoloader --ignore-platform-reqs'

## Credits

This package would not have been possible would it not have been for the amazing work of the ContinuousPipe team. Also a special thanks to Samuel for the introduction of ContinuousPipe and Kubernetes. His ability to teach Docker concepts has been of great value. A huge thanks to Kieren for his attention to detail and everlasting patience when helping me to understand the ContinuousPipe Dockerfiles and setup.

- [Samuel ROZE](https://github.com/sroze)
- [Kieren Evans](https://github.com/kierenevans)

## License

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