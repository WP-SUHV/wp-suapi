<?php
namespace WP_SUAPI;

use SUHV\Suapi\ApiHandler;
use SUHV\Suapi\dto\Team;
use Twig_Environment;
use Twig_Loader_Filesystem;
use WP_SUAPI_Helper;

class WP_SUAPI_Shortcode_Manager
{
  private $twig;

  public function __construct()
  {
    $loader = new Twig_Loader_Filesystem(__DIR__ . '/templates/');
    $this->twig = new Twig_Environment($loader, array(//'cache' => 'C:\xampp\apps\dev\cache'
    ));
    // Add Shortcode
    add_shortcode('wp-suapi-rankingtable', (array($this, 'rankingTable')));
    add_shortcode('wp-suapi-fixturetable', (array($this, 'fixturesTable')));
  }

  /*
   * Shortcode function for ranking tables
   * Attributes:
   *  year: Year for query (e.g. 2014)
   *  team: swiss unihockey Team ID
   *  highlight: highlight given team in table (1 = true, 0 = false)
   *  type: Table Type
   *        |-1: Rg, Team, Sp, P
   *        |-2: Rg, Team, Sp, S, U, N, T, TD, P
   *        |-3: Rg, Team, Sp, S, SnV, NnV, N, T, TD, P
   */
  public function rankingTable($atts)
  {
    $a = shortcode_atts(array(
        'year' => 0,
        'team' =>  0,
        'type' => 1,
        'highlight' => 1,
    ), $atts);
    if ($a['year'] == 0 || $a['team'] == 0)
      return "";

    $apiHandler = WP_SUAPI_Helper::GET_INITIALIZED_API_HANDLER();
    $apiHandler->setYearForQuery($a['year']);
    $rankingsTable = $apiHandler->getRankingForTeam(new Team($a['team'], ''));

    $args = array('rankings' => $rankingsTable->getRankings());

    if($a['highlight'] == 1)
      $args = array_merge($args, array('team' => $apiHandler->getTeamById($a['team'])));

    switch ($a['type']) {
      case 1:
        return $this->twig->render('wp-suapi-rankingtable-1.twig.html', $args);
      case 2:
        return $this->twig->render('wp-suapi-rankingtable-2.twig.html', $args);
      case 3:
        return $this->twig->render('wp-suapi-rankingtable-3.twig.html', $args);
      default:
        return "";
    }
  }

  /*
   * Shortcode function for game fixtures tables
   * Attributes:
   *  year: Year for query (e.g. 2014)
   *  team: swiss unihockey Team ID
   *  type: Table Type
   *        |-1: Date, Home Team, Away Team, Location, Result
   */
  public function fixturesTable($atts)
  {
    $a = shortcode_atts(array(
        'year' => 0,
        'team' =>  0,
        'type' => 1,
    ), $atts);
    if ($a['year'] == 0 || $a['team'] == 0)
      return "";

    $apiHandler = WP_SUAPI_Helper::GET_INITIALIZED_API_HANDLER();
    $apiHandler->setYearForQuery($a['year']);
    $rankingsTable = $apiHandler->getFixtureListForTeam(new Team($a['team'], ''));

    $args = array('fixtures' => $rankingsTable->getFixtures());

    switch ($a['type']) {
      case 1:
        return $this->twig->render('wp-suapi-fixturestable-1.twig.html', $args);
      default:
        return "";
    }
  }
}
?>
