# apicdynaclient

php install windows.

Download php: https://windows.php.net/download#php-7.4-ts-vc15-x64

Create a directory and unzip php zip file, eg D:\tools\php\php

This is php installation directory.
Place php installation directory on you path.

Change to the php installation directory and copy php.ini-production to php.ini
run : php --ini to see that ini file is discovered.

Open php.ini file and remove the leading semicol to enable openssl extension:
extension=openssl

Copy the php_install_dir\ext directory to C:\php\ext

run: `php --version` command.

php composer.

Create composer directory, eg: c:\tools\php\composer
Download php composer installer: https://getcomposer.org/download/

Check the website how to verify installer integrity.

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"

To use composer: php path-to-composer-dir\composer.phar

Running the portal test client.
Change to the portal-test-client directory.

Install composer dependencies:
php path-to-composer-dir\composer.phar install

Define endpoints in the drclient.php file.
Substitute values in define() statements.

Run: php drclient.php
