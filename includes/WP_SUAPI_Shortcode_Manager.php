<?php

namespace WP_SUAPI;

use Twig_Environment;
use Twig_Loader_Filesystem;

use WP_SUAPI\Object\Team;

class WP_SUAPI_Shortcode_Manager
{

    private $twig;

    public function __construct()
    {
        $loader = new Twig_Loader_Filesystem(__DIR__ . '/templates/');
        $this->twig = new Twig_Environment($loader, array(
            //'cache' => 'C:\xampp\apps\dev\cache'
        ));

        // Add Shortcode
        add_shortcode('wp-suapi-rankingtable', (array($this, 'rankingTable')));
    }

    /*
     * Shortcode function for ranking tables
     * Attributes:
     *  year: Year for query (e.g. 2014)
     *  team: swiss unihockey Team ID
     *  type: Table Type
     *        |-1: Rg, Team, Sp, S, U, N, T, TD, P
     *        |-2: Rg, Team, Sp, S, SnV, NnV, N, T, TD, P
     */
    public function rankingTable($atts)
    {
        $a = shortcode_atts(array(
            'year' => '0',
            'team' => '0',
            'type' => '1'
        ), $atts);

        if ($a['year'] == 0 || $a['team'] == 0)
            return "";

        $apiHandler = \WP_SUAPI\WP_SUAPI_API_Handler::GET_INITIALIZED_API_HANDLER();
        $apiHandler->setYearForQuery($a['year']);
        $rankings = $apiHandler->getRankingForTeam(new Team($a['team'], ''));

        if ($a['type'] == '1') {
            return $this->twig->render('wp-suapi-rankingtable-1.twig.html', array('rankings' => $rankings));
        } else {
            return $this->twig->render('wp-suapi-rankingtable-2.twig.html', array('rankings' => $rankings));
        }
    }
}

?>