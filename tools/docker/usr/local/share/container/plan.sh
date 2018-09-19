#!/bin/bash

alias_function do_start do_start_arc_inner
do_start() {
  do_start_arc_inner
  do_setup
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

do_setup() {
  do_arc_fresh_install
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

do_arc_migration() {
  as_code_owner "sleep 5 && php artisan migrate"
}

do_arc_fresh_install() {
    do_arc_migration
}
