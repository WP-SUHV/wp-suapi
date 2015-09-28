<?php
namespace WP_SUAPI;

require_once('object/WP_SUAPI_API_Object.php');

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\DoctrineCacheStorage;
use Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;
use Doctrine\Common\Cache\FilesystemCache;
use WP_SUAPI\Object\Club;
use WP_SUAPI\Object\Game;
use WP_SUAPI\Object\LeagueAndGroup;
use WP_SUAPI\Object\Location;
use WP_SUAPI\Object\Ranking;
use WP_SUAPI\Object\RankingTable;
use WP_SUAPI\Object\Team;
use WP_SUAPI\Exception\WP_SUAPI_Api_Exception;

if (!defined('ABSPATH')) {
  exit;
}

define('WP_SUAPI_ENDPOINT_CLUBS', 'clubs');
define('WP_SUAPI_ENDPOINT_TEAMS', 'teams');
define('WP_SUAPI_ENDPOINT_GAMES', 'games');
define('WP_SUAPI_ENDPOINT_RANKINGS', 'rankings');

class WP_SUAPI_API_Handler
{

  /*
   * Uri
   * @var string $uri
   */
  private $uri;

  /*
   * Key
   * @var string $key
   */
  private $key;

  /*
   * APIVersion
   * @var string $apiVersion
   */
  private $apiVersion;

  /*
   * YearForQuery
   * @var int $yearForQuery
   */
  private $yearForQuery;

  /**
   * HTTP-Client
   * @var GuzzleHttp\Client
   */
  private $guzzle;

  public function __construct($uri, $key, $apiVersion, $useCache = false)
  {
    $this->uri = $uri;
    $this->key = $key;
    $this->apiVersion = $apiVersion;
    $this->yearForQuery = date("Y");
    // Create default HandlerStack
    $stack = HandlerStack::create();
    if ($useCache) {
      $stack->push(new CacheMiddleware(
        new PrivateCacheStrategy(
          new DoctrineCacheStorage(
            new FilesystemCache('cache/')
          )
        )), 'cache');
    }
    $this->guzzle = new Client([
      'base_uri' => $this->getApiUri()
      , 'handler' => $stack
      , 'timeout' => 15.0
      // , 'debug' => true
    ]);
  }


  /**
   * Uses get_options and initialize API_HANDLER
   * @return WP_SUAPI_API_Handler
   */
  public static function GET_INITIALIZED_API_HANDLER()
  {
    return new WP_SUAPI_API_Handler(get_option("wp-suapi_api-url"), get_option("wp-suapi_api-key"), get_option("wp-suapi_api-version"), get_option("wp-suapi_extra-usecache"));
  }

  /**
   * Get all clubs
   * @return Array(WP_SUAPI\Object\Club)
   */
  public function getClubs()
  {
    $response = $this->guzzle->get(WP_SUAPI_ENDPOINT_CLUBS);
    if ($response->getStatusCode() !== 200) {
      throw new WP_SUAPI_Api_Exception($response->getBody());
    }
    return array_map(function ($item) {
      return new Club($item->set_in_context->club_id, $item->text);
    }, json_decode($response->getBody())->entries);
  }

  /**
   * Get all clubs
   * @return Array(WP_SUAPI\Object\Team)
   */
  public function getTeamsForClub($club)
  {
    $response = $this->guzzle->get(
      WP_SUAPI_ENDPOINT_TEAMS
      . "?season=" . $this->yearForQuery
      . "&mode=by_club"
      . "&club_id=" . $club->getClubId());
    if ($response->getStatusCode() !== 200) {
      throw new WP_SUAPI_Api_Exception($response->getBody());
    }
    return array_map(function ($item) {
      return new Team($item->set_in_context->team_id, $item->text);
    }, json_decode($response->getBody())->entries);
  }

  /**
   * Get Team Details by team ID
   * @param $teamId
   * @return Team
   * @throws WP_SUAPI_Api_Exception
   */
  public function getTeamById($teamId)
  {
    $response = $this->guzzle->get(
        WP_SUAPI_ENDPOINT_TEAMS
        . "/" . $teamId
    );
    if($response->getStatusCode() !== 200) {
      throw new WP_SUAPI_Api_Exception($response->getBody());
    }
    $team = new Team($teamId, "");
    $team->setTeamTitle(json_decode($response->getBody())->data->title);
    return $team;
  }

  /**
   * Get all games for team
   * @return Array(WP_SUAPI\Object\Games)
   */
  public function getGamesForTeam($team)
  {
    $response = $this->guzzle->get(
      WP_SUAPI_ENDPOINT_GAMES
      . "?season=" . $this->yearForQuery
      . "&mode=team"
      . "&view=full"
      . "&order=natural"
      . "&team_id=" . $team->getTeamId());
    if ($response->getStatusCode() !== 200) {
      throw new WP_SUAPI_Api_Exception($response->getBody());
    }

    return array_map(function ($item) {
      $id = $item->link->ids[0];
      $date = $item->cells[0]->text[0];
      $time = $item->cells[0]->text[1];
      $location = new Location($item->cells[1]->text[0], $item->cells[1]->text[1]);
      $location->setLocationLongitude($item->cells[1]->link->x);
      $location->setLocationLatitude($item->cells[1]->link->y);
      $teamHome = $item->cells[2]->text[0];
      $teamAway = $item->cells[3]->text[0];
      $result = $item->cells[4]->text[0];
      return new Game($id, $date, $time, $location, $teamHome, $teamAway);
    }, json_decode($response->getBody())->data->regions[0]->rows);
  }

  /**
   * Getranking for team
   * @return RankingTable
   */
  public function getRankingForTeam($team)
  {
    $team->setLeague($this->getLeagueByTeam($team));
    $response = $this->guzzle->get(
      WP_SUAPI_ENDPOINT_RANKINGS
      . "?season=" . $this->yearForQuery
      . "&league=" . $team->getLeague()->getLeagueId()
      . "&game_class=" . $team->getLeague()->getLeagueGameClassId()
      . "&group=Gruppe+" . $team->getLeague()->getLeagueGroup()
      . "&view=full");
    if ($response->getStatusCode() !== 200) {
      throw new WP_SUAPI_Api_Exception($response->getBody());
    }

    $cleanedRankingResults = array_filter(json_decode($response->getBody())->data->regions[0]->rows, function ($item) {
      return property_exists($item, 'data'); //Remove items used as separator
    });
    $rankings = array_map(function ($rankingInput) use ($team) {
      $ranking = new Ranking($team->getLeague(),
        $rankingInput->data->rank,
        $rankingInput->data->team->name,
        $rankingInput->cells[2]->text[0],
        $rankingInput->cells[3]->text[0],
        $rankingInput->cells[4]->text[0],
        $rankingInput->cells[5]->text[0],
        $rankingInput->cells[6]->text[0],
        $rankingInput->cells[7]->text[0],
        $rankingInput->cells[8]->text[0]
      );
      return $ranking;
    }, $cleanedRankingResults);

    // Find the separator and set it in the ranking
    $rankingTable = new RankingTable($team->getLeague());
    $rankingTable->setRankings($rankings);
    array_map(function ($item, $key) use ($rankingTable) {
      if (property_exists($item, 'separator')) {
        $rankingTable->setRankingSeparator($key);
      }
    }, json_decode($response->getBody())->data->regions[0]->rows, array_keys(json_decode($response->getBody())->data->regions[0]->rows));
    return $rankingTable;
  }

  /**
   * @return boolean
   */
  public function isConnected()
  {
    try {
      $response = $this->guzzle->get(WP_SUAPI_ENDPOINT_CLUBS);
      return ($response->getStatusCode() === 200) ? true : false;
    } catch (\GuzzleHttp\Exception\RequestException $e) {
      return false;
    }
  }

  /**
   * @return int
   */
  public function getYearForQuery()
  {
    return $this->yearForQuery;
  }

  /**
   * @param int $yearForQuery
   */
  public function setYearForQuery($yearForQuery)
  {
    $this->yearForQuery = $yearForQuery;
  }

  /**
   * @return composite uri to query the API
   */
  protected function getApiUri()
  {
    return $this->uri;
  }

  public function getLeagueByTeam($team)
  {
    $response = $this->guzzle->get(
      WP_SUAPI_ENDPOINT_GAMES
      . "?season=" . $this->yearForQuery
      . "&mode=team"
      . "&team_id=" . $team->getTeamId());
    if ($response->getStatusCode() !== 200) {
      throw new WP_SUAPI_Api_Exception($response->getBody());
    }
    $leagueAndGroup = new LeagueAndGroup($team->getTeamName());
    $leagueId = json_decode($response->getBody())->data->tabs[0]->link->ids[1];
    $gameClassId = json_decode($response->getBody())->data->tabs[0]->link->ids[2];
    $leagueAndGroup->setLeagueId($leagueId);
    $leagueAndGroup->setLeagueGameClassId($gameClassId);
    $parsedLeagueGroup = array();
    if (preg_match("/.*Gr\.\s(\d*)/", json_decode($response->getBody())->data->title, $parsedLeagueGroup)) {
      $leagueGroup = $parsedLeagueGroup[1];
      $leagueAndGroup->setLeagueGroup($leagueGroup);
    }
    return $leagueAndGroup;
  }
}
