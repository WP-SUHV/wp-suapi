<?php

use WP_SUAPI\WP_SUAPI_API_Handler;
use WP_SUAPI\Object\Club;
use WP_SUAPI\Object\Team;
use WP_SUAPI\Object\LeagueAndGroup;

class WP_SUAPI_API_HandlerTest extends PHPUnit_Framework_TestCase
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
     * Test CreateFromTeamName
     * @return void
     */
    public function testCreateLeagueFromTeamName()
    {
        $leagueAndGroup = LeagueAndGroup::CreateFromTeamName("Herren 3. Liga Gruppe 10");
        $this->assertEquals(5, $leagueAndGroup->getLeagueId());
        $this->assertEquals(10, $leagueAndGroup->getLeagueGroup());
    }

    /**
     * Test CreateFromLeagueName
     * @return void
     */
    public function testCreateFromLeagueName()
    {
        $leagueAndGroup = LeagueAndGroup::CreateFromLeagueName("Herren Aktive GF 1. Liga");
        $this->assertEquals(3, $leagueAndGroup->getLeagueId());
        $this->assertEquals("GF", $leagueAndGroup->getLeagueType());
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
     * Test ranking for team query from api handler
     * @return void
     */
    public function testGetRankingsForLigaTeam()
    {
        self::$apiHandler->setYearForQuery(2014);
        $rankings = self::$apiHandler->getRankingForTeam(new Team(428988, "Herren 3. Liga Gruppe 10"));
        $this->assertInstanceOf('WP_SUAPI\Object\Ranking', $rankings[0]);
    }

    /**
     * Test ranking for team query from api handler
     * @return void
     */
    public function testGetRankingsForNLATeam()
    {
        self::$apiHandler->setYearForQuery(2014);
        $rankings = self::$apiHandler->getRankingForTeam(new Team(428535, "Herren NLA Gruppe 1"));
        $this->assertInstanceOf('WP_SUAPI\Object\Ranking', $rankings[0]);
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
