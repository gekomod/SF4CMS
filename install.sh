#!/bin/bash

print_usage() {
  echo "Usage:

  $0 {a|b|c} [ARG...]

Options:

  --install
  -i
    Instalacja Usługi GekoCMS

  --update
  -u
    Aktualizacja projektu z GIT

  --prefix ARG1
  -p ARG1
    Prefix Instalacji

" >&2
}

if [ $# -le 0 ]; then
  print_usage
  exit 1
fi

DEBUG=NO FORCE=NO UPDATE=NO INSTALL=NO PREFIX=sf4cms/
yesno=n
MYSQL_HOST='localhost';
MYSQL_USERNAME=none;
MYSQL_PASSWORD=none;
MYSQL_PORT=3306;
MYSQL_DATABASE=none;
GIT_VERSION="$(git --version)"

set_patch() {
    echo "DUPA :". ${PREFIX}
}

set_install() {
    echo "INSTALACJA :". ${INSTALL}
    while [ "$yesno" = "n" ];
    do
    {
    	echo -n "Please enter MySql Host : ";
    	read MYSQL_HOST;

    	echo -n "Please enter MySql Username: ";
    	read MYSQL_USERNAME;

    	echo -n "Please enter MySql Password : ";
    	read MYSQL_PASSWORD;

    	echo -n "Please enter MySql Database : ";
    	read MYSQL_DATABASE;

    	echo -n "Please enter MySql Port : ";
    	read MYSQL_PORT;

    	echo "Mysql Host:  $MYSQL_HOST";
    	echo "Mysql Username: $MYSQL_USERNAME";
    	echo "Mysql Password: $MYSQL_PASSWORD";
    	echo "Mysql Port: $MYSQL_PORT";
    	echo -n "Is this correct? (y,n) : ";
    	read yesno;
    }
    done;
}

set_createfile() {

sudo dd of=.env << EOF
# This file is a "template" of which env vars need to be defined for your application
# Copy this file to .env file for development, create environment variables when deploying to production
# https://symfony.com/doc/current/best_practices/configuration.html#infrastructure-related-configuration

###> symfony/framework-bundle ###
APP_ENV=dev
APP_SECRET=964f0359a5e14dd8395fe334867e9709
#TRUSTED_PROXIES=127.0.0.1,127.0.0.2
#TRUSTED_HOSTS=localhost,example.com
###< symfony/framework-bundle ###

###> symfony/swiftmailer-bundle ###
# For Gmail as a transport, use: "gmail://username:password@localhost"
# For a generic SMTP server, use: "smtp://localhost:25?encryption=&auth_mode="
# Delivery is disabled by default via "null://localhost"
MAILER_URL=null://localhost
###< symfony/swiftmailer-bundle ###

###> doctrine/doctrine-bundle ###
# Format described at http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url
# For an SQLite database, use: "sqlite:///%kernel.project_dir%/var/data.db"
# Configure your db driver and server_version in config/packages/doctrine.yaml
DATABASE_URL=mysql://${MYSQL_USERNAME}:${MYSQL_PASSWORD}@${MYSQL_HOST}:${MYSQL_PORT}/${MYSQL_DATABASE}
###< doctrine/doctrine-bundle ###
EOF

}

set_downloadComposer() {
     wget --progress=dot -O composer https://getcomposer.org/download/1.7.2/composer.phar 2>&1
     chmod 777 composer
}

set_downloadFromGit() {
     git clone https://github.com/gekomod/SF4CMS.git ${PREFIX}
     cd ${PREFIX}
}

run_composerinstall() {
     php composer install
     wget -O FormatterMediaExtension.php https://gist.githubusercontent.com/gekomod/6d837d26546c1417573bec66c21728c9/raw/7c1520c3780f37ab3ebf3954939a8e66fd8d6433/FormatterMediaExtension.php
     rm -fr vendor/sonata-project/media-bundle/src/Twig/Extension/FormatterMediaExtension.php
     mv FormatterMediaExtension.php vendor/sonata-project/media-bundle/src/Twig/Extension/FormatterMediaExtension.php
     echo "====== START COMPOSER AFTER ERROR ======"
     php composer install
}

check_isInstalled() {
 if [ "$GIT_VERSION" != "command not found" ];
 then
   echo "===== GIT READY ====="
 else
   echo "git is missing"
   exit 0
 fi
}

for i in "$@"
do
case $i in
    -p=*|--prefix=*)
    PREFIX="${i#*=}"
    set_patch
    ;;
    -i|--install)
    INSTALL=YES
    check_isInstalled
    set_install
    if [ "$yesno" != "y" ]
    then
           exit 0
    else
           set_downloadFromGit
           set_createfile
           set_downloadComposer
           run_composerinstall
    fi
    ;;
    -u|--update)
    UPDATE=YES
    ;;
    -d|--debug)
    DEBUG=YES
    shift
    ;;
    -f|--force)
    FORCE=YES
    shift
    ;;
    --default)
    DEFAULT=YES
    ;;
    h*|\?*|-help)

      print_usage
      exit 0
      ;;

    *)
            # unknown option
    ;;
esac
done