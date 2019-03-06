#!/bin/bash

set -e

convert_to_boolean_string() {
  if [ "$1" == '1' ] || [ "$1" == "true" ]; then
    echo 'true';
  else
    echo 'false';
  fi
}
convert_to_boolean_string_zero_is_true() {
  if [ "$1" == '0' ]; then
    echo 'true';
  elif [ "$1" == '1' ]; then
    echo 'false';
  else
    convert_to_boolean_string "$1"
  fi
}
source ./tools/docker/usr/local/share/env/20-arc-env;
eval "echo \"$(cat ./tools/travis/deploy/env.yaml)\"" >> ./tools/travis/deploy/env_dist.yaml &&

RELEASE_NAME="$(echo "${APP_NAME}-${TRAVIS_BRANCH}" | tr '[:upper:]' '[:lower:]')"
export RELEASE_NAME

helm upgrade --install "$RELEASE_NAME" --namespace="$RELEASE_NAME"  \
-f ./tools/travis/deploy/env_dist.yaml \
--set imageCredentials.username="${DOCKER_USERNAME}" \
--set imageCredentials.password="${DOCKER_PASSWORD}" \
--set image.repository="${DOCKER_USERNAME}/${DOCKER_REPO}" \
--set image.tag="$TRAVIS_COMMIT" \
./tools/helm-chart && \

rm -fr ./tools/travis/deploy/env_dist.yaml