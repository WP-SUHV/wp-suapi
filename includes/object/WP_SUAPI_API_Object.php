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