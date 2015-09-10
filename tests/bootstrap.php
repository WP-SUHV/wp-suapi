<?php

/**
 * WP SUAPI Plugin Unit/Integration Tests Bootstrap
 */
class WP_SUAPI_Tests_Bootstrap
{
    protected static $instance = null;
    public $test_suite;
    public $tests_dir;
    public $framework_dir;

    public function __construct()
    {
        ini_set('display_errors', 'on');
        error_reporting(E_ALL);
        $this->tests_dir = dirname(__FILE__);
        $this->framework_dir = dirname($this->tests_dir);

        if (!defined('PROJECT')) {
            define('PROJECT', __DIR__ . '/includes/');
        }

        if (!defined('WP_SUAPI_DIR')) {
            define('WP_SUAPI_DIR', __DIR__ . '/');
        }
        // set test type, default to unit if not set
        $arg_key = array_search('--testsuite', $GLOBALS['argv']) + 1;
        $this->test_suite = ($arg_key > 1) ? $GLOBALS['argv'][$arg_key] : 'unit';

        require_once($this->framework_dir . '/vendor/autoload.php');
        if ($this->is_unit_tests()) {
            if (!defined('ABSPATH')) {
                define('ABSPATH', true);
            }
            if (!defined('WP_LANG_DIR')) {
                define('WP_LANG_DIR', 'lang/');
            }
        }
    }

    public function get_tests_path()
    {
        return $this->tests_dir;
    }

    public function get_framework_path()
    {
        return $this->framework_dir;
    }

    public function is_unit_tests()
    {
        return 'unit' === $this->test_suite;
    }

    public function is_integration_tests()
    {
        return 'integration' === $this->test_suite;
    }

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}

function bootstrap()
{
    return WP_SUAPI_Tests_Bootstrap::instance();
}

bootstrap();
