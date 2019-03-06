#!/bin/bash

set -e

if [ -L "$0" ] ; then
    DIR="$(dirname "$(readlink -f "$0")")"
else
    DIR="$(dirname "$0")"
fi

build_images()
{
  DOCKER_BUILD_FLAGS=(--force-rm)
  if [ "$PARALLEL" != "false" ]; then
    DOCKER_BUILD_FLAGS+=(--parallel)
  fi
  echo "Building images:"; echo
  (cd "$DIR" && docker-compose -f docker-compose.yml build "${DOCKER_BUILD_FLAGS[@]}" web)
}
export -f build_images

publish_images()
{
  if [ -z "$DO_PUBLISH" ]; then
    read -r -p "Would you like to publish the images? [Y/n] " DO_PUBLISH
  fi

  if [ -z "$DO_PUBLISH" ]; then
    DO_PUBLISH='n'
  fi

  DO_PUBLISH="$(echo "$DO_PUBLISH" | tr '[:upper:]' '[:lower:]')"
  if [ "$DO_PUBLISH" = 'y' ]; then
    echo "Pushing our images:"; echo
    parallel --no-notice --line-buffer --tag --tagstring "Pushing {}:" docker-compose -f docker-compose.yml push ::: web
  else
    echo "Not Pushing our images."; echo
  fi
}
export -f publish_images

run_build()
(
  set -e
  time {
    build_images
  }
)
export -f run_build

run_publish()
(
  set -e
  time {
    publish_images
  }
)
export -f run_publish

main()
{
  echo "travis_fold:start:build"
  parallel --no-notice --line-buffer --tag --link ::: run_build ::: ""
  echo "travis_fold:end:build"

  echo "travis_fold:start:push"
  run_publish
  echo "travis_fold:end:push"

  echo "Done!"
}

main
