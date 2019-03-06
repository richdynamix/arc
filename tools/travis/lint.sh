#!/bin/bash

set -e

if [ -L "$0" ] ; then
    DIR="$(dirname "$(readlink -f "$0")")"
else
    DIR="$(dirname "$0")"
fi

echo "travis_fold:start:pull_test_images"
time docker pull koalaman/shellcheck:v0.4.6
time docker pull lukasmartinelli/hadolint:latest
echo "travis_fold:end:pull_test_images"

run_shellcheck()
{
  set -e
  local script="$1"
  docker run --rm -i koalaman/shellcheck:v0.4.6 --exclude SC1091 - < "$script" && echo "OK"
}
export -f run_shellcheck

echo "travis_fold:start:lint_scripts"
time {
  find "$DIR" -type f ! -path "*.git/*" ! -path "*tools/travis/deploy/*" ! -path "*vendor/*" ! -path "*bootstrap/*" ! -path "*storage/*" ! -name "*.py" ! -name "*.php" \( \
    -perm +111 -or -name "*.sh" -or -wholename "*usr/local/share/env/*" -or -wholename "*usr/local/share/container/*" \
  \) | parallel --no-notice --line-buffer --tag --tagstring "Linting {}:" run_shellcheck
}
echo "travis_fold:end:lint_scripts"

run_hadolint()
{
  set -e
  local dockerfile="$1"
  docker run --rm -i lukasmartinelli/hadolint:latest hadolint --ignore DL3008 --ignore DL3002 --ignore DL3003 --ignore DL4001 --ignore DL3007 --ignore SC2016 - < "$dockerfile" && echo "OK"
}
export -f run_hadolint

echo "travis_fold:start:lint_dockerfiles"
time {
  find "$DIR" -type f -name "Dockerfile*" ! -name "*.tmpl" ! -path "./vendor/*" | parallel --no-notice --line-buffer --tag --tagstring "Linting {}:" run_hadolint
}
echo "travis_fold:end:lint_dockerfiles"