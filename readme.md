# WP SUAPI #
**Contributors:**      Philipp Meier
**Tags:**              suhv, swiss unihockey, swissunihockey
**Requires at least:** 4.1.1
**Tested up to:**      4.1.1
**Stable tag:**        0.1.0

Wordpress plugin to connect with the swiss unihockey API v2.

## Description ##

## Installation ##

Installing "wp-suapi" can be done either by searching for "wp-suapi" via the "Plugins > Add New" screen in your WordPress dashboard, or by using the following steps:

1. Download the plugin via WordPress.org
1. Upload the ZIP file through the 'Plugins > Add New > Upload' screen in your WordPress dashboard
1. Activate the plugin through the 'Plugins' menu in WordPress

### Manual with composer ###

1. Upload the entire `/wp-suapi` directory to the `/wp-content/plugins/` directory.
1. Get dependencies with `composer install`
1. Activate the plugin through the 'Plugins' menu in WordPress

### Testing ###

1. Install dependencies with composer `composer install` / `composer update`
1. Run tests `vendor/bin/phpunit --configuration phpunit.xml --testsuite unit`
1. Run specific test `php -d$XDEBUG_EXT vendor/bin/phpunit --configuration phpunit.xml --testsuite unit --filter testGetRankingsForLigaTeam`

## Development with Docker

Use this command to bring up the Docker container with `PHP`, `xDebug`, `PHPUnit`, `Composer` and `Grunt`

```
xdebug_listener_ip=`ifconfig | grep "inet " | grep -Fv 127.0.0.1 | awk '{print $2}'`
docker run -it --volume $PWD:/var/www/html -e XDEBUG_CONFIG="remote_host=$xdebug_listener_ip remote_port=9000 remote_enable=1 idekey=PHPSTORM" -e PHP_IDE_CONFIG="serverName=docker-xdebug" phpdocker/phpdocker /bin/bash
```

## Screenshots ##

## Frequently Asked Questions ##

### What is the plugin for? ###

Connects your Wordpress site with the new swiss unihockey API v2.

## Changelog ##

### 0.1.0 ###
* First release

## Upgrade Notice ##

### 0.1.0 ###
First Release

