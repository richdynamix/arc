#!/bin/bash

alias_function do_start do_start_arc_inner
do_start() {
  do_start_arc_inner
}

alias_function do_development_start do_development_start_arc_inner
do_development_start() {
  do_build_arc
}

alias_function do_build do_build_arc_inner
do_build() {
  do_build_arc_inner
  do_build_arc
}

do_build_arc() {
  do_arc_frontend_build
  do_arc_permissions
}

do_arc_frontend_build() {
  # Build frontend assets
  if [ ! -f /app/public/mix-manifest.json ]; then
    as_code_owner "yarn"
    as_code_owner "yarn run production"
  fi
}

do_arc_permissions() {
  chown -R "${CODE_OWNER}:${APP_GROUP}" /app/bootstrap/cache/ /app/storage
  chmod -R ug+rw,o-w /app/bootstrap/cache/ /app/storage/
  chmod +x /app/storage
}

do_deploy() {
    as_code_owner "php artisan migrate --force"
}

do_phpunit() {
    as_code_owner "vendor/bin/phpunit -c phpunit.xml"
}

do_phpcs() {
    as_code_owner "vendor/bin/phpcs --runtime-set ignore_warnings_on_exit -p --extensions=php app"
}

do_phpmd() {
    as_code_owner "vendor/bin/phpmd app xml ruleset.xml"
}

do_phpstan() {
    as_code_owner "vendor/bin/phpstan analyse app --level=0"
}