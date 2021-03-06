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
  private $teamTitle;
  private $league;

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

  /**
   * @return mixed
   */
  public function getLeague()
  {
    return $this->league;
  }

  /**
   * @param mixed $league
   */
  public function setLeague($league)
  {
    $this->league = $league;
  }

  /**
   * @return mixed
   */
  public function getTeamTitle()
  {
    return $this->teamTitle;
  }

  /**
   * @param mixed $teamTitle
   */
  public function setTeamTitle($teamTitle)
  {
    $this->teamTitle = $teamTitle;
  }
}

class LeagueAndGroup
{
  private $leagueName;
  private $leagueGroup;
  private $leagueType;
  private $leagueId;
  private $leagueGameClassId;

  /**
   * LeagueAndGroup constructor.
   * @param $leagueName
   */
  public function __construct($leagueName)
  {
    $this->leagueName = $leagueName;
  }

  public static function CreateFromTeamName($teamName)
  {
    preg_match("/([a-zA-Z]+)\s(\d*)\.\sLiga\sGruppe\s(\d*)/", $teamName, $parsed);
    $league = new LeagueAndGroup($parsed[1] . " " . $parsed[2] . ". Liga");
    $league->setLeagueId(self::nonNlaOrNlbLeague($parsed[2]));
    $league->setLeagueGroup($parsed[3]);
    return $league;
  }

  public static function CreateFromLeagueName($leagueName)
  {
    $league = new LeagueAndGroup($leagueName);
    //print_r($leagueName);
    if (preg_match("/([a-zA-Z]+)\s[a-zA-Z]+\s([a-zA-Z]+)\s(\d*)\.\sLiga/", $leagueName, $parsed)) { //Herren Aktive GF 1. Liga
      //print_r($parsed);
      $league->setLeagueType($parsed[2]);
      $league->setLeagueId(self::nonNlaOrNlbLeague($parsed[3]));
    } else if (preg_match("/(Junioren|Juniorinnen)\s(\S)(\d\d)\s(.*)/", $leagueName, $parsed)) { //Junioren U21 B
      $league->setLeagueType("GF");
      //print_r($parsed);
    } else if (preg_match("/(Junioren|Juniorinnen)\s(\S)\s(.*)/", $leagueName, $parsed)) { //Junioren E Regional
      $league->setLeagueType("KF");
      //print_r($parsed);
    } else if (preg_match("/(\S)(\d\d)\s(.*)/", $leagueName, $parsed)) {//U17 Trophy Interregional
      //print_r($parsed);
      $league->setLeagueType("GF");
    }
    return $league;
  }

  /**
   * Add two because NLA and NLB are 1 and 2
   * @param $leagueId
   * @return int
   */
  public static function nonNlaOrNlbLeague($leagueId)
  {
    return $leagueId + 2;
  }

  public function __toString()
  {
    return $this->leagueName . "(" . $this->leagueId . ")";
  }

  public function equals(League $leauge)
  {
    return ($this->getLeagueId() == $leauge->getLeagueId());
  }

  /**
   * @return mixed
   */
  public function getLeagueName()
  {
    return $this->leagueName;
  }

  /**
   * @param mixed $leagueName
   */
  public function setLeagueName($leagueName)
  {
    $this->leagueName = $leagueName;
  }

  /**
   * @return mixed
   */
  public function getLeagueGroup()
  {
    return $this->leagueGroup;
  }

  /**
   * @param mixed $leagueGroup
   */
  public function setLeagueGroup($leagueGroup)
  {
    $this->leagueGroup = $leagueGroup;
  }

  /**
   * @return mixed
   */
  public function getLeagueType()
  {
    return $this->leagueType;
  }

  /**
   * @param mixed $leagueType
   */
  public function setLeagueType($leagueType)
  {
    $this->leagueType = $leagueType;
  }

  /**
   * @return mixed
   */
  public function getLeagueId()
  {
    return $this->leagueId;
  }

  /**
   * @param mixed $leagueId
   */
  public function setLeagueId($leagueId)
  {
    $this->leagueId = $leagueId;
  }

  /**
   * @return mixed
   */
  public function getLeagueGameClassId()
  {
    return $this->leagueGameClassId;
  }

  /**
   * @param mixed $leagueGameClassId
   */
  public function setLeagueGameClassId($leagueGameClassId)
  {
    $this->leagueGameClassId = $leagueGameClassId;
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

class Ranking
{
  private $rankingLeague;
  private $rankingNr;
  private $rankingTeamName;
  private $rankingGamesCount;
  private $rankingGamesWon;
  private $rankingGamesWonAfterOvertime;
  private $rankingGamesLose;
  private $rankingGamesLoseAfterOvertime;
  private $rankingGamesDraw;
  private $rankingGoals;
  private $rankingGoalsDifference;
  private $rankingPoints;

  /**
   * Ranking constructor.
   * @param $rankingLeague
   * @param $rankingNr
   * @param $rankingTeamName
   * @param $rankingGamesCount
   * @param $rankingGamesWon
   * @param $rankingGamesLose
   * @param $rankingGoals
   * @param $rankingGoalsDifference
   * @param $rankingPoints
   * @param $rankingGamesDraw
   */
  public function __construct($rankingLeague, $rankingNr, $rankingTeamName, $rankingGamesCount, $rankingGamesWon, $rankingGamesLose, $rankingGamesDraw, $rankingGoals, $rankingGoalsDifference, $rankingPoints, $rankingGamesWonAfterOvertime = '', $rankingGamesLoseAfterOvertime = '')
  {
    $this->rankingLeague = $rankingLeague;
    $this->rankingNr = $rankingNr;
    $this->rankingTeamName = $rankingTeamName;
    $this->rankingGamesCount = $rankingGamesCount;
    $this->rankingGamesWon = $rankingGamesWon;
    $this->rankingGamesWonAfterOvertime = $rankingGamesWonAfterOvertime;
    $this->rankingGamesLose = $rankingGamesLose;
    $this->rankingGamesLoseAfterOvertime = $rankingGamesLoseAfterOvertime;
    $this->rankingGoals = $rankingGoals;
    $this->rankingGoalsDifference = $rankingGoalsDifference;
    $this->rankingPoints = $rankingPoints;
    $this->rankingGamesDraw = $rankingGamesDraw;
  }


  public function __toString()
  {
    return $this->rankingNr . ". " . $this->rankingTeamName;
  }

  public function equals(Ranking $ranking)
  {
    return ($this->getRankingLeague() == $ranking->getRankingLeague() && $this->getRankingGroup() == $ranking->getRankingGroup() && $this->getRankingNr() == $ranking->getRankingNr());
  }

  /**
   * @return mixed
   */
  public function getRankingLeague()
  {
    return $this->rankingLeague;
  }

  /**
   * @param mixed $rankingLeague
   */
  public function setRankingLeague($rankingLeague)
  {
    $this->rankingLeague = $rankingLeague;
  }

  /**
   * @return mixed
   */
  public function getRankingNr()
  {
    return $this->rankingNr;
  }

  /**
   * @param mixed $rankingNr
   */
  public function setRankingNr($rankingNr)
  {
    $this->rankingNr = $rankingNr;
  }

  /**
   * @return mixed
   */
  public function getRankingTeamName()
  {
    return $this->rankingTeamName;
  }

  /**
   * @param mixed $rankingTeamName
   */
  public function setRankingTeamName($rankingTeamName)
  {
    $this->rankingTeamName = $rankingTeamName;
  }

  /**
   * @return mixed
   */
  public function getRankingGamesCount()
  {
    return $this->rankingGamesCount;
  }

  /**
   * @param mixed $rankingGamesCount
   */
  public function setRankingGamesCount($rankingGamesCount)
  {
    $this->rankingGamesCount = $rankingGamesCount;
  }

  /**
   * @return mixed
   */
  public function getRankingGamesWon()
  {
    return $this->rankingGamesWon;
  }

  /**
   * @param mixed $rankingGamesWon
   */
  public function setRankingGamesWon($rankingGamesWon)
  {
    $this->rankingGamesWon = $rankingGamesWon;
  }

  /**
   * @return mixed
   */
  public function getRankingGamesWonAfterOvertime()
  {
    return $this->rankingGamesWonAfterOvertime;
  }

  /**
   * @param mixed $rankingGamesWonAfterOvertime
   */
  public function setRankingGamesWonAfterOvertime($rankingGamesWonAfterOvertime)
  {
    $this->rankingGamesWonAfterOvertime = $rankingGamesWonAfterOvertime;
  }

  /**
   * @return mixed
   */
  public function getRankingGamesLose()
  {
    return $this->rankingGamesLose;
  }

  /**
   * @param mixed $rankingGamesLose
   */
  public function setRankingGamesLose($rankingGamesLose)
  {
    $this->rankingGamesLose = $rankingGamesLose;
  }

  /**
   * @return mixed
   */
  public function getRankingGamesLoseAfterOvertime()
  {
    return $this->rankingGamesLoseAfterOvertime;
  }

  /**
   * @param mixed $rankingGamesLoseAfterOvertime
   */
  public function setRankingGamesLoseAfterOvertime($rankingGamesLoseAfterOvertime)
  {
    $this->rankingGamesLoseAfterOvertime = $rankingGamesLoseAfterOvertime;
  }

  /**
   * @return mixed
   */
  public function getRankingGamesDraw()
  {
    return $this->rankingGamesDraw;
  }

  /**
   * @param mixed $rankingGamesDraw
   */
  public function setRankingGamesDraw($rankingGamesDraw)
  {
    $this->rankingGamesDraw = $rankingGamesDraw;
  }

  /**
   * @return mixed
   */
  public function getRankingGoals()
  {
    return $this->rankingGoals;
  }

  /**
   * @param mixed $rankingGoals
   */
  public function setRankingGoals($rankingGoals)
  {
    $this->rankingGoals = $rankingGoals;
  }

  /**
   * @return mixed
   */
  public function getRankingGoalsDifference()
  {
    return $this->rankingGoalsDifference;
  }

  /**
   * @param mixed $rankingGoalsDifference
   */
  public function setRankingGoalsDifference($rankingGoalsDifference)
  {
    $this->rankingGoalsDifference = $rankingGoalsDifference;
  }

  /**
   * @return mixed
   */
  public function getRankingPoints()
  {
    return $this->rankingPoints;
  }

  /**
   * @param mixed $rankingPoints
   */
  public function setRankingPoints($rankingPoints)
  {
    $this->rankingPoints = $rankingPoints;
  }
}

class RankingTable
{
  /**
   * @var LeagueAndGroup
   */
  private $rankingLeague;

  /**
   * Nr of separator in rankings table.
   * The separator is places after the NR --> Ranking place 8 followed by separator
   * @var int
   */
  private $rankingSeparator;

  /**
   * @var Ranking[]
   */
  private $rankings;

  /**
   * RankingTable constructor.
   * @param LeagueAndGroup $rankingLeague
   */
  public function __construct(LeagueAndGroup $rankingLeague)
  {
    $this->rankingLeague = $rankingLeague;
  }

  public function __toString()
  {
    return $this->rankingLeague . " (" . $this->rankings . ") with separator nr " . $this->rankingSeparator;
  }

  public function equals(RankingTable $rankingTable)
  {
    return ($this->getRankingLeague() == $rankingTable->getRankingLeague() && $this->getRankingSeparator() == $rankingTable->getRankingSeparator());
  }

  /**
   * @return LeagueAndGroup
   */
  public function getRankingLeague()
  {
    return $this->rankingLeague;
  }

  /**
   * @param LeagueAndGroup $rankingLeague
   */
  public function setRankingLeague($rankingLeague)
  {
    $this->rankingLeague = $rankingLeague;
  }

  /**
   * @return int
   */
  public function getRankingSeparator()
  {
    return $this->rankingSeparator;
  }

  /**
   * @param int $rankingSeparator
   */
  public function setRankingSeparator($rankingSeparator)
  {
    $this->rankingSeparator = $rankingSeparator;
  }

  /**
   * @return Ranking[]
   */
  public function getRankings()
  {
    return $this->rankings;
  }

  /**
   * @param Ranking[] $rankings
   */
  public function setRankings($rankings)
  {
    $this->rankings = $rankings;
  }

}

class Fixture
{
  private $fixtureId;
  private $fixtureLeague;
  private $fixtureDatetime;
  private $fixtureLocation;
  private $fixtureHomeTeam;
  private $fixtureAwayTeam;
  private $fixtureResultText;

  private $fixtureHomeTeamGoals;
  private $fixtureAwayTeamGoals;

  /**
   * Fixture constructor.
   * @param $fixtureId
   * @param $fixtureLeague
   * @param $fixtureDatetime
   * @param $fixtureLocation
   * @param $fixtureHomeTeam
   * @param $fixtureAwayTeam
   * @param $fixtureResultText
   * @param $fixtureHomeTeamGoals
   * @param $fixtureAwayTeamGoals
   */
  public function __construct($fixtureId, $fixtureLeague, $fixtureDatetime, $fixtureLocation, $fixtureHomeTeam, $fixtureAwayTeam, $fixtureResultText)
  {
    $this->fixtureId = $fixtureId;
    $this->fixtureLeague = $fixtureLeague;
    $this->fixtureDatetime = $fixtureDatetime;
    $this->fixtureLocation = $fixtureLocation;
    $this->fixtureHomeTeam = $fixtureHomeTeam;
    $this->fixtureAwayTeam = $fixtureAwayTeam;
    $this->fixtureResultText = $fixtureResultText;

    $parsedResult = array();
    if (preg_match("/(\d.*)\:(\d.*)/", $this->fixtureResultText, $parsedResult)) {
      $this->fixtureHomeTeamGoals = $parsedResult[1];
      $this->fixtureAwayTeamGoals = $parsedResult[2];
    }
  }

  public function __toString()
  {
    return $this->fixtureDatetime . ": " . $this->fixtureHomeTeam . "(" . $this->fixtureId . ") vs. " . $this->fixtureAwayTeam;
  }

  public function equals(Fixture $fixture)
  {
    return ($this->getFixtureLeague() == $fixture->getFixtureLeague() && $this->getFixtureDatetime() == $fixture->getFixtureDatetime() && $this->getFixtureHomeTeam() == $fixture->getFixtureHomeTeam() && $this->getFixtureAwayTeam() == $fixture->getFixtureAwayTeam());
  }

  /**
   * @return mixed
   */
  public function getFixtureId()
  {
    return $this->fixtureId;
  }

  /**
   * @param mixed $fixtureId
   */
  public function setFixtureId($fixtureId)
  {
    $this->fixtureId = $fixtureId;
  }

  /**
   * @return mixed
   */
  public function getFixtureLeague()
  {
    return $this->fixtureLeague;
  }

  /**
   * @param mixed $fixtureLeague
   */
  public function setFixtureLeague($fixtureLeague)
  {
    $this->fixtureLeague = $fixtureLeague;
  }

  /**
   * @return mixed
   */
  public function getFixtureDatetime()
  {
    return $this->fixtureDatetime;
  }

  /**
   * @param mixed $fixtureDatetime
   */
  public function setFixtureDatetime($fixtureDatetime)
  {
    $this->fixtureDatetime = $fixtureDatetime;
  }

  /**
   * @return mixed
   */
  public function getFixtureLocation()
  {
    return $this->fixtureLocation;
  }

  /**
   * @param mixed $fixtureLocation
   */
  public function setFixtureLocation($fixtureLocation)
  {
    $this->fixtureLocation = $fixtureLocation;
  }

  /**
   * @return mixed
   */
  public function getFixtureHomeTeam()
  {
    return $this->fixtureHomeTeam;
  }

  /**
   * @param mixed $fixtureHomeTeam
   */
  public function setFixtureHomeTeam($fixtureHomeTeam)
  {
    $this->fixtureHomeTeam = $fixtureHomeTeam;
  }

  /**
   * @return mixed
   */
  public function getFixtureAwayTeam()
  {
    return $this->fixtureAwayTeam;
  }

  /**
   * @param mixed $fixtureAwayTeam
   */
  public function setFixtureAwayTeam($fixtureAwayTeam)
  {
    $this->fixtureAwayTeam = $fixtureAwayTeam;
  }

  /**
   * @return mixed
   */
  public function getFixtureResultText()
  {
    return $this->fixtureResultText;
  }

  /**
   * @param mixed $fixtureResultText
   */
  public function setFixtureResultText($fixtureResultText)
  {
    $this->fixtureResultText = $fixtureResultText;
  }

  /**
   * @return mixed
   */
  public function getFixtureHomeTeamGoals()
  {
    return $this->fixtureHomeTeamGoals;
  }

  /**
   * @param mixed $fixtureHomeTeamGoals
   */
  public function setFixtureHomeTeamGoals($fixtureHomeTeamGoals)
  {
    $this->fixtureHomeTeamGoals = $fixtureHomeTeamGoals;
  }

  /**
   * @return mixed
   */
  public function getFixtureAwayTeamGoals()
  {
    return $this->fixtureAwayTeamGoals;
  }

  /**
   * @param mixed $fixtureAwayTeamGoals
   */
  public function setFixtureAwayTeamGoals($fixtureAwayTeamGoals)
  {
    $this->fixtureAwayTeamGoals = $fixtureAwayTeamGoals;
  }
}

class FixtureList
{
  /**
   * @var LeagueAndGroup
   */
  private $fixtureListLeague;

  /**
   * @var Fixture[]
   */
  private $fixtures;

  /**
   * RankingTable constructor.
   * @param LeagueAndGroup $rankingLeague
   */
  public function __construct(LeagueAndGroup $fixtureListLeague)
  {
    $this->fixtureListLeague = $fixtureListLeague;
  }

  public function __toString()
  {
    return $this->fixtureListLeague . " (" . $this->fixtures . ")";
  }

  public function equals(FixtureList $fixtureList)
  {
    return ($this->getFixtureListLeague() == $fixtureList->getFixtureListLeague() && $this->getFixtures() == $fixtureList->getFixtures());
  }

  /**
   * @return LeagueAndGroup
   */
  public function getFixtureListLeague()
  {
    return $this->fixtureListLeague;
  }

  /**
   * @param LeagueAndGroup $fixtureListLeague
   */
  public function setFixtureListLeague($fixtureListLeague)
  {
    $this->fixtureListLeague = $fixtureListLeague;
  }

  /**
   * @return Fixture[]
   */
  public function getFixtures()
  {
    return $this->fixtures;
  }

  /**
   * @param Fixture[] $fixtures
   */
  public function setFixtures($fixtures)
  {
    $this->fixtures = $fixtures;
  }
}
