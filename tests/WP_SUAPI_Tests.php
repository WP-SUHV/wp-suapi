<?php

class WP_SUAPI_Tests extends PHPUnit_Framework_TestCase {

    /**
     * Init WP_Mock and API handler
     */
    public function setUp() {
        \WP_Mock::setUp();
    }

    public function tearDown() {
        \WP_Mock::tearDown();
    }
}
