<?php
namespace WP_SUAPI\Object;


class Club
{
    private $clubId;
    private $clubName;

    function __construct($clubId, $clubName)
    {
        $this->clubId = $clubId;
        $this->clubName = $clubName;
    }

    public function __toString()
    {
        return $this->clubName . "(" . $this->clubId . ")";
    }

    public function equals(Club $club)
    {
        return ($this->getClubId() == $club->getClubId());
    }

    /**
     * @return mixed
     */
    public function getClubName()
    {
        return $this->clubName;
    }

    /**
     * @param mixed $clubName
     */
    public function setClubName($clubName)
    {
        $this->clubName = $clubName;
    }

    /**
     * @return mixed
     */
    public function getClubId()
    {
        return $this->clubId;
    }

    /**
     * @param mixed $clubId
     */
    public function setClubId($clubId)
    {
        $this->clubId = $clubId;
    }
}

class Team
{
    private $teamId;
    private $teamName;

    function __construct($teamId, $teamName)
    {
        $this->teamId = $teamId;
        $this->teamName = $teamName;
    }

    public function __toString()
    {
        return $this->teamName . "(" . $this->teamId . ")";
    }

    public function equals(Team $team)
    {
        return ($this->getTeamId() == $team->getClubId());
    }

    /**
     * @return mixed
     */
    public function getTeamId()
    {
        return $this->teamId;
    }

    /**
     * @param mixed $teamId
     */
    public function setTeamId($teamId)
    {
        $this->teamId = $teamId;
    }

    /**
     * @return mixed
     */
    public function getTeamName()
    {
        return $this->teamName;
    }

    /**
     * @param mixed $teamName
     */
    public function setTeamName($teamName)
    {
        $this->teamName = $teamName;
    }

}

class Game
{
    private $gameId;
    private $gameDate;
    private $gameTime;
    private $gameTeamHome;
    private $gameTeamAway;
    private $gameLocation;
    private $gameResult;

    /**
     * Game constructor.
     * @param $gameId
     * @param $gameDate
     * @param $gameTime
     * @param $gameTeamHome
     * @param $gameTeamAway
     */
    public function __construct($gameId, $gameDate, $gameTime, $gameLocation, $gameTeamHome, $gameTeamAway)
    {
        $this->gameId = $gameId;
        $this->gameDate = $gameDate;
        $this->gameTime = $gameTime;
        $this->gameLocation = $gameLocation;
        $this->gameTeamHome = $gameTeamHome;
        $this->gameTeamAway = $gameTeamAway;
    }

    public function __toString()
    {
        return $this->gameTeamHome . " vs. " . $this->gameTeamAway . "(" . $this->teamId . ")";
    }

    public function equals(Game $game)
    {
        return ($this->getGameId() == $game->getGameId());
    }

    /**
     * @return mixed
     */
    public function getGameId()
    {
        return $this->gameId;
    }

    /**
     * @param mixed $gameId
     */
    public function setGameId($gameId)
    {
        $this->gameId = $gameId;
    }

    /**
     * @return mixed
     */
    public function getGameDate()
    {
        return $this->gameDate;
    }

    /**
     * @param mixed $gameDate
     */
    public function setGameDate($gameDate)
    {
        $this->gameDate = $gameDate;
    }

    /**
     * @return mixed
     */
    public function getGameTime()
    {
        return $this->gameTime;
    }

    /**
     * @param mixed $gameTime
     */
    public function setGameTime($gameTime)
    {
        $this->gameTime = $gameTime;
    }

    /**
     * @return mixed
     */
    public function getGameTeamHome()
    {
        return $this->gameTeamHome;
    }

    /**
     * @param mixed $gameTeamHome
     */
    public function setGameTeamHome($gameTeamHome)
    {
        $this->gameTeamHome = $gameTeamHome;
    }

    /**
     * @return mixed
     */
    public function getGameTeamAway()
    {
        return $this->gameTeamAway;
    }

    /**
     * @param mixed $gameTeamAway
     */
    public function setGameTeamAway($gameTeamAway)
    {
        $this->gameTeamAway = $gameTeamAway;
    }

    /**
     * @return mixed
     */
    public function getGameLocation()
    {
        return $this->gameLocation;
    }

    /**
     * @param mixed $gameLocation
     */
    public function setGameLocation($gameLocation)
    {
        $this->gameLocation = $gameLocation;
    }

    /**
     * @return mixed
     */
    public function getGameResult()
    {
        return $this->gameResult;
    }

    /**
     * @param mixed $gameResult
     */
    public function setGameResult($gameResult)
    {
        $this->gameResult = $gameResult;
    }
}

class Location
{
    private $locationName;
    private $locationCity;
    private $locationLongitude;
    private $locationLatitude;

    /**
     * Location constructor.
     * @param $locationName
     * @param $locationCity
     */
    public function __construct($locationName, $locationCity)
    {
        $this->locationName = $locationName;
        $this->locationCity = $locationCity;
    }


    public function __toString()
    {
        return $this->locationName . ", " . $this->locationCity;
    }

    public function equals(Location $location)
    {
        return ($this->getLocationName() == $location->getLocationName());
    }

    /**
     * @return mixed
     */
    public function getLocationName()
    {
        return $this->locationName;
    }

    /**
     * @param mixed $locationName
     */
    public function setLocationName($locationName)
    {
        $this->locationName = $locationName;
    }

    /**
     * @return mixed
     */
    public function getLocationCity()
    {
        return $this->locationCity;
    }

    /**
     * @param mixed $locationCity
     */
    public function setLocationCity($locationCity)
    {
        $this->locationCity = $locationCity;
    }

    /**
     * @return mixed
     */
    public function getLocationLongitude()
    {
        return $this->locationLongitude;
    }

    /**
     * @param mixed $locationLongitude
     */
    public function setLocationLongitude($locationLongitude)
    {
        $this->locationLongitude = $locationLongitude;
    }

    /**
     * @return mixed
     */
    public function getLocationLatitude()
    {
        return $this->locationLatitude;
    }

    /**
     * @param mixed $locationLatitude
     */
    public function setLocationLatitude($locationLatitude)
    {
        $this->locationLatitude = $locationLatitude;
    }


}