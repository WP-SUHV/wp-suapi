<?php
namespace WP_SUAPI;

use Httpful\Request;
use WP_SUAPI\Exception\WP_SUAPI_Api_Exception;

require_once('object/WP_SUAPI_API_Object.php');
use WP_SUAPI\Object\Club;
use WP_SUAPI\Object\Game;
use WP_SUAPI\Object\LeagueAndGroup;
use WP_SUAPI\Object\Location;
use WP_SUAPI\Object\Ranking;
use WP_SUAPI\Object\Team;

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

  public function __construct($uri, $key, $apiVersion)
  {
    $this->uri = $uri;
    $this->key = $key;
    $this->apiVersion = $apiVersion;
    $this->yearForQuery = date("Y");
  }

  /**
   * Uses get_options and initialize API_HANDLER
   * @return WP_SUAPI_API_Handler
   */
  public static function GET_INITIALIZED_API_HANDLER()
  {
    return new WP_SUAPI_API_Handler(get_option("wp-suapi_api-url"), get_option("wp-suapi_api-key"), get_option("wp-suapi_api-version"));
  }

  /**
   * Get all clubs
   * @return Array(WP_SUAPI\Object\Club)
   */
  public function getClubs()
  {
    $response = Request::get(
      $this->getApiUri()
      . WP_SUAPI_ENDPOINT_CLUBS
    )->send();
    if ($response->code !== 200) {
      throw new WP_SUAPI_Api_Exception($response->raw_body);
    }

    return array_map(function ($item) {
      return new Club($item->set_in_context->club_id, $item->text);
    }, $response->body->entries);
  }

  /**
   * Get all clubs
   * @return Array(WP_SUAPI\Object\Team)
   */
  public function getTeamsForClub($club)
  {
    $response = Request::get(
      $this->getApiUri()
      . WP_SUAPI_ENDPOINT_TEAMS
      . "?season=" . $this->yearForQuery
      . "&mode=by_club"
      . "&club_id=" . $club->getClubId()
    )->send();
    if ($response->code !== 200) {
      throw new WP_SUAPI_Api_Exception($response->raw_body);
    }

    return array_map(function ($item) {
      return new Team($item->set_in_context->team_id, $item->text);
    }, $response->body->entries);
  }

  /**
   * Get all games for team
   * @return Array(WP_SUAPI\Object\Games)
   */
  public function getGamesForTeam($team)
  {
    $response = Request::get(
      $this->getApiUri()
      . WP_SUAPI_ENDPOINT_GAMES
      . "?season=" . $this->yearForQuery
      . "&mode=team"
      . "&view=full"
      . "&order=natural"
      . "&team_id=" . $team->getTeamId()
    )->send();
    if ($response->code !== 200) {
      throw new WP_SUAPI_Api_Exception($response->raw_body);
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
    }, $response->body->data->regions[0]->rows);
  }

  /**
   * Getranking for team
   * @return Array(WP_SUAPI\Object\Ranking)
   */
  public function getRankingForTeam($team)
  {
    $team->setLeague($this->getLeagueByTeam($team));
    $response = Request::get(
      $this->getApiUri()
      . WP_SUAPI_ENDPOINT_RANKINGS
      . "?season=" . $this->yearForQuery
      . "&league=" . $team->getLeague()->getLeagueId()
      . "&game_class=" . $team->getLeague()->getLeagueGameClassId()
      . "&group=Gruppe+" . $team->getLeague()->getLeagueGroup()
      . "&view=full"
    )->send();
    if ($response->code !== 200) {
      throw new WP_SUAPI_Api_Exception($response->raw_body);
    }

    $cleanedRankingResults = array_filter($response->body->data->regions[0]->rows, function ($item) {
      return property_exists($item, 'data'); //Remove items used as separator
    });
    $rankings = array_map(function ($rankingInput) use ($team, $response) {
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
    return $rankings;
  }

  /**
   * @return boolean
   */
  public function isConnected()
  {
    $response = Request::get(
      $this->getApiUri()
      . WP_SUAPI_ENDPOINT_CLUBS
    )->send();
    if ($response->code === 200) {
      return true;
    } else {
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
    $response = Request::get(
      $this->getApiUri()
      . WP_SUAPI_ENDPOINT_GAMES
      . "?season=" . $this->yearForQuery
      . "&mode=team"
      . "&team_id=" . $team->getTeamId()
    )->send();
    if ($response->code !== 200) {
      throw new WP_SUAPI_Api_Exception($response->raw_body);
    }
    $leagueAndGroup = new LeagueAndGroup($team->getTeamName());
    $leagueId = $response->body->data->tabs[0]->link->ids[1];
    $gameClassId = $response->body->data->tabs[0]->link->ids[2];
    $leagueAndGroup->setLeagueId($leagueId);
    $leagueAndGroup->setLeagueGameClassId($gameClassId);
    $parsedLeagueGroup = array();
    if (preg_match("/.*Gr\.\s(\d*)/", $response->body->data->title, $parsedLeagueGroup)) {
      $leagueGroup = $parsedLeagueGroup[1];
      $leagueAndGroup->setLeagueGroup($leagueGroup);
    }
    return $leagueAndGroup;
  }
}
