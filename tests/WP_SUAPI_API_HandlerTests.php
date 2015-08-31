<?php

use WP_SUAPI\WP_SUAPI_API_Handler;
use WP_SUAPI\Object\Club;
use WP_SUAPI\Object\Team;

class WP_SUAPI_API_HandlerTests extends PHPUnit_Framework_TestCase
{

    /**
     * Api handler
     * @var ApiHandler $apiHandler
     */
    protected static $apiHandler;

    /**
     * Test all clubs query from api handler
     * @return void
     */
    public function testIsConnected()
    {
        $this->assertTrue(self::$apiHandler->isConnected());
    }

    /**
     * Test all clubs query from api handler
     * @return void
     */
    public function testGetClubs()
    {
        $allClubs = self::$apiHandler->getClubs();
        $this->assertInstanceOf('WP_SUAPI\Object\Club', $allClubs[0]);
    }

    /**
     * Test all teams query from api handler
     * @return void
     */
    public function testGetTeamsForClub()
    {
        $allTeams = self::$apiHandler->getTeamsForClub(new Club(377, "FB Riders DBR"));
        $this->assertInstanceOf('WP_SUAPI\Object\Team', $allTeams[0]);
    }


    /**
     * Test all games query from api handler
     * @return void
     */
    public function testGetGamesForTeam()
    {
        $allGames = self::$apiHandler->getGamesForTeam(new Team(428988, "Herren 3. Liga Gruppe 10"));
        $this->assertInstanceOf('WP_SUAPI\Object\Game', $allGames[0]);
    }

    /**
     * Init WP_Mock and API handler
     */
    public function setUp()
    {
        \WP_Mock::setUp(); // Needed for define('ABSPATH..
        self::$apiHandler = new WP_SUAPI_API_Handler("https://api-v2.swissunihockey.ch/api/", "", "");
    }
}
