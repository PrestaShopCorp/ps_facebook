#!/bin/sh
#
# This is an init-script for prestashop-flashlight.
#
# Storing a folder in /var/www/html/modules is not enough to register the module
# into PrestaShop, hence why we have to call the console install CLI.
#
set -eu

error() {
  printf "\e[1;31m%s\e[0m\n" "${1:-Unknown error}"
  exit "${2:-1}"
}

# we also decide to clear the cache after the installation of the module
# because sometimes some issues occurs when trying to go to the configuration page of the module
ps_facebook_install() {
  echo "* [ps_facebook] installing the module..."
  [ ! -d "./modules/ps_facebook/vendor" ] && error "please install composer dependencies first" 2
  php -d memory_limit=-1 bin/console prestashop:module --no-interaction install "ps_facebook"
  php -d memory_limit=-1 bin/console cache:clear
}

# install modules
ps_facebook_install
