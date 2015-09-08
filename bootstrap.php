<?php
if (!defined('PROJECT')) {
  define('PROJECT', __DIR__ . '/includes/');
}

if (!defined('WP_SUAPI_DIR')) {
  define('WP_SUAPI_DIR', __DIR__ . '/');
}

// Place any additional bootstrapping requirements here for PHP Unit.
if (!defined('WP_LANG_DIR')) {
  define('WP_LANG_DIR', 'lang');
}

if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
  throw new PHPUnit_Framework_Exception(
    'ERROR' . PHP_EOL . PHP_EOL .
    'You must use Composer to install the test suite\'s dependencies!' . PHP_EOL
  );
}

require_once __DIR__ . '/vendor/autoload.php';

