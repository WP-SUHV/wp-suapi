<?php
/*
 * Plugin Name: WP SUAPI
 * Plugin URI:  https://github.com/WP-SUHV/wp-suapi
 * Description: Wordpress plugin to connect with the swiss unihockey API v2.
 * Version:     0.1.0
 * Author:      Philipp Meier
 * Author URI:  https://github.com/meip
 * Text Domain: suapi
 * Domain Path: /languages
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: wp-suapi
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Philipp Meier
 * @since 1.0.0
 */

if (!defined('ABSPATH')) exit;

// Must be set before the cuztom autoload

define('WP_SUAPI_DIR', dirname(__FILE__));
require __DIR__ . '/vendor/autoload.php';
// Load plugin class files
require_once('includes/class-wp-suapi-settings.php');
require_once('includes/class-wp-suapi.php');

// Load plugin libraries
require_once('includes/lib/class-wp-suapi-admin-api.php');
require_once('includes/lib/class-wp-suapi-taxonomy.php');

require_once('includes/WP_SUAPI_API_Handler.php');
require_once('includes/object/WP_SUAPI_API_Object.php');
/**
 * Returns the main instance of WP_SUAPI to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object WP_SUAPI
 */
function WP_SUAPI()
{
  $instance = WP_SUAPI::instance(__FILE__, '1.0.0');

  if (is_null($instance->settings)) {
    $instance->settings = WP_SUAPI_Settings::instance($instance);
  }

  return $instance;
}

WP_SUAPI();


/*
 * Template function which generates ranking table for leagues in category 'HNLA',
 * 'DNLA', 'HNLB', 'DNLB'
 */
function getRankingTable($teamId, $yearForQuery){

}

/*
 * Template function which generates ranking table for leagues in category 'others'
 */
function getRankingTableOthers($teamId, $yearForQuery){
  $apiHandler = \WP_SUAPI\WP_SUAPI_API_Handler::GET_INITIALIZED_API_HANDLER();
  $apiHandler->setYearForQuery($yearForQuery);
  $rankings = $apiHandler->getRankingForTeam(new \WP_SUAPI\Object\Team($teamId, ''));

  $html = '';
  $html .= '<table>';
  $html .= '<tr>';
  $html .= '<th>Rg.</th>';
  $html .= '<th>Team</th>';
  $html .= '<th>Sp</th>';
  $html .= '<th>S</th>';
  $html .= '<th>U</th>';
  $html .= '<th>N</th>';
  $html .= '<th>T</th>';
  $html .= '<th>TD</th>';
  $html .= '<th>P</th>';
  $html .= '</tr>';

  $html .= array_reduce($rankings,  function ($result, $item) {
    $result .= '<tr>';
    $result .= '<td>' . $item->getRankingNr() . '</td>';
    $result .= '<td>' . $item->getRankingTeamName() . '</td>';
    $result .= '<td>' . $item->getRankingGamesCount() . '</td>';
    $result .= '<td>' . $item->getRankingGamesWon() . '</td>';
    $result .= '<td>' . $item->getRankingGamesDraw() . '</td>';
    $result .= '<td>' . $item->getRankingGamesLose() . '</td>';
    $result .= '<td>' . $item->getRankingGoals() . '</td>';
    $result .= '<td>' . $item->getRankingGoalsDifference() . '</td>';
    $result .= '<td>' . $item->getRankingPoints() . '</td>';
    $result .= '</tr>';
    return $result;
  });

  $html .= '</table>';

  return $html;
}

