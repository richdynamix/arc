#!/bin/bash

set -e

docker login -u "$DOCKER_USERNAME" -p "$DOCKER_PASSWORD" && \
docker run --rm -i "$DOCKER_USERNAME"/"$DOCKER_REPO":"$TRAVIS_COMMIT" container phpunit