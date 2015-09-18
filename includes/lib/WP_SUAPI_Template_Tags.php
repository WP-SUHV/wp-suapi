<?php

/*
 * Template tag which generates ranking table for leagues in category 'HNLA',
 * 'DNLA', 'HNLB', 'DNLB'
 */
function getRankingTable(){
    //TODO
}

/*
 * Template tag which generates ranking table for leagues in category 'others'
 */
function getRankingTableOthers(){
    $yearForQuery = 2014; //TODO: Config?
    $post_meta = get_post_meta(get_the_ID(),'_su_meta_suhvteamid');

    if(sizeof($post_meta) > 0) {
        $teamId = $post_meta[0];
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
    }
    return $html;
}