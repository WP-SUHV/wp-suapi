<?php

/*
 * Shortcode function for ranking tables
 * Attributes:
 *  year: Year for query (e.g. 2014)
 *  team: swiss unihockey Team ID
 *  type: Table Type
 *        |-1: Rg, Team, Sp, S, U, N, T, TD, P
 *        |-2: Rg, Team, Sp, S, SnV, NnV, N, T, TD, P
 */
add_shortcode( 'wp-suapi-rankingtable', 'rankingTable' );
function rankingTable($atts)
{
    $a = shortcode_atts(array(
        'year' => '0',
        'team' => '0',
        'type' => '1'
    ), $atts);

    if($a['year'] == 0 || $a['team'] == 0)
        return;

    $apiHandler = \WP_SUAPI\WP_SUAPI_API_Handler::GET_INITIALIZED_API_HANDLER();
    $apiHandler->setYearForQuery($a['year']);
    $rankings = $apiHandler->getRankingForTeam(new \WP_SUAPI\Object\Team($a['team'], ''));

    if ($a['type'] == '1') {
        return generateRankingTableType1($rankings);
    } else {
        return generateRankingTableType2($rankings);
    }
}

/*
    Generate HTML Code for Ranking Table Type 1
*/
function generateRankingTableType1($rankings){
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

/*
 * Generate HTML Code for Ranking Table Type 2
 */
function generateRankingTableType2($rankings){

    return "";
}