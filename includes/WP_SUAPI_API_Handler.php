<?php
namespace WP_SUAPI;

use Httpful\Request;
use WP_SUAPI\Exception\WP_SUAPI_Api_Exception;

require_once('object/WP_SUAPI_API_Object.php');
use WP_SUAPI\Object\Club;
use WP_SUAPI\Object\Team;

if (!defined('ABSPATH')) exit;

define('WP_SUAPI_ENDPOINT_CLUBS', 'clubs');
define('WP_SUAPI_ENDPOINT_TEAMS', 'teams');

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
     * Get all clubs
     * @return Array(WP_SUAPI\Object\Club)
     */
    public function getClubs()
    {
        $response = Request::get($this->getApiUri() . WP_SUAPI_ENDPOINT_CLUBS)->send();
        if ($response->code !== 200) {
            throw new WP_SUAPI_Api_Exception($response->raw_body);
        }
        $map = function ($item) {
            return new Club($item->set_in_context->club_id, $item->text);
        };
        return array_map($map, $response->body->entries);
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
        $map = function ($item) {
            return new Team($item->set_in_context->team_id, $item->text);
        };
        return array_map($map, $response->body->entries);
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
    protected function getApiUri() {
        return $this->uri;
    }

}
