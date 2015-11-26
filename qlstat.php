<?php
set_time_limit(0);   
ini_set('mysql.connect_timeout','0');   
ini_set('max_execution_time', '0');   
class Rating
{

    /**
     * @var int The K Factor used.
     */
    const KFACTOR = 16;

    /**
     * Protected & private variables.
     */
    protected $_ratingA;
    protected $_ratingB;
    
    protected $_scoreA;
    protected $_scoreB;

    protected $_expectedA;
    protected $_expectedB;

    protected $_newRatingA;
    protected $_newRatingB;

    /**
     * Costructor function which does all the maths and stores the results ready
     * for retrieval.
     *
     * @param int Current rating of A
     * @param int Current rating of B
     * @param int Score of A
     * @param int Score of B
     */
    public function  __construct($ratingA,$ratingB,$scoreA,$scoreB)
    {
        $this->_ratingA = $ratingA;
        $this->_ratingB = $ratingB;
        $this->_scoreA = $scoreA;
        $this->_scoreB = $scoreB;

        $expectedScores = $this -> _getExpectedScores($this -> _ratingA,$this -> _ratingB);
        $this->_expectedA = $expectedScores['a'];
        $this->_expectedB = $expectedScores['b'];

        $newRatings = $this ->_getNewRatings($this -> _ratingA, $this -> _ratingB, $this -> _expectedA, $this -> _expectedB, $this -> _scoreA, $this -> _scoreB);
        $this->_newRatingA = $newRatings['a'];
        $this->_newRatingB = $newRatings['b'];
    }

    /**
     * Set new input data.
     *
     * @param int Current rating of A
     * @param int Current rating of B
     * @param int Score of A
     * @param int Score of B
     */
    public function setNewSettings($ratingA,$ratingB,$scoreA,$scoreB)
    {
        $this -> _ratingA = $ratingA;
        $this -> _ratingB = $ratingB;
        $this -> _scoreA = $scoreA;
        $this -> _scoreB = $scoreB;

        $expectedScores = $this -> _getExpectedScores($this -> _ratingA,$this -> _ratingB);
        $this -> _expectedA = $expectedScores['a'];
        $this -> _expectedB = $expectedScores['b'];

        $newRatings = $this ->_getNewRatings($this -> _ratingA, $this -> _ratingB, $this -> _expectedA, $this -> _expectedB, $this -> _scoreA, $this -> _scoreB);
        $this -> _newRatingA = $newRatings['a'];
        $this -> _newRatingB = $newRatings['b'];
    }

    /**
     * Retrieve the calculated data.
     *
     * @return Array An array containing the new ratings for A and B.
     */
    public function getNewRatings()
    {
        return array (
            'a' => $this -> _newRatingA,
            'b' => $this -> _newRatingB
        );
    }

    /**
     * Protected & private functions begin here
     */

    protected function _getExpectedScores($ratingA,$ratingB)
    {
        $expectedScoreA = 1 / ( 1 + ( pow( 10 , ( $ratingB - $ratingA ) / 400 ) ) );
        $expectedScoreB = 1 / ( 1 + ( pow( 10 , ( $ratingA - $ratingB ) / 400 ) ) );

        return array (
            'a' => $expectedScoreA,
            'b' => $expectedScoreB
        );
    }

    protected function _getNewRatings($ratingA,$ratingB,$expectedA,$expectedB,$scoreA,$scoreB)
    {
        $newRatingA = $ratingA + ( self::KFACTOR * ( $scoreA - $expectedA ) );
        $newRatingB = $ratingB + ( self::KFACTOR * ( $scoreB - $expectedB ) );

        return array (
            'a' => $newRatingA,
            'b' => $newRatingB
        );
    }

}

$context = new ZMQContext();
$subscriber = new ZMQSocket($context, ZMQ::SOCKET_SUB);
$host=$argv[1];
echo $host;
$serv=str_replace("tcp://", "", $host);
$ss=str_replace(":",",",$serv);
$subscriber->connect($host);
$subscriber->setSockOpt(ZMQ::SOCKOPT_SUBSCRIBE, "");
while (true) {
    $link = mysqli_connect('localhost', '', '', '');
    mysqli_set_charset($link, "utf8");

    $address = $subscriber->recv(ZMQ::MODE_NOBLOCK);
    $stat = json_decode($address, true);
    if ($stat['TYPE'] == 'PLAYER_CONNECT') {
	mysqli_query($link, "INSERT INTO players (`STEAM_ID`) VALUES ('".$stat['DATA']['STEAM_ID']."')");
    }
    else if ($stat['TYPE'] == 'MATCH_STARTED' && $stat['DATA']['GAME_TYPE'] == 'DUEL'){
	mysqli_query($link, "INSERT INTO matches (`server`,`CAPTURE_LIMIT`,`FACTORY`,`FACTORY_TITLE`,`FRAG_LIMIT`,`GAME_TYPE`,`INFECTED`,`INSTAGIB`,`MAP`,`MATCH_GUID`,`MERCY_LIMIT`,`PLAYERS`,`QUADHOG`,`SCORE_LIMIT`,`SERVER_TITLE`,`TIME_LIMIT`,`TRAINING`) VALUES ('".$serv."', '".$stat['DATA']['CAPTURE_LIMIT']."','".$stat['DATA']['FACTORY']."','".$stat['DATA']['FACTORY_TITLE']."','".$stat['DATA']['FRAG_LIMIT']."','".$stat['DATA']['GAME_TYPE']."','".$stat['DATA']['INFECTED']."','".$stat['DATA']['INSTAGIB']."','".$stat['DATA']['MAP']."','".$stat['DATA']['MATCH_GUID']."','".$stat['DATA']['MERCY_LIMIT']."','".json_encode($stat['DATA']['PLAYERS'])."','".$stat['DATA']['QUADHOG']."','".$stat['DATA']['SCORE_LIMIT']."','".$stat['DATA']['SERVER_TITLE']."','".$stat['DATA']['TIME_LIMIT']."','".$stat['DATA']['TRAINING']."')");
    }
    else if ($stat['TYPE'] == 'PLAYER_MEDAL' && $stat['DATA']['WARMUP'] == False){
	mysqli_query($link, "INSERT INTO medals (`MATCH_GUID`,`MEDAL`,`NAME`,`STEAM_ID`,`TIME`,`TOTAL`,`WARMUP`) VALUES ('".$stat['DATA']['MATCH_GUID']."','".$stat['DATA']['MEDAL']."','".$stat['DATA']['NAME']."','".$stat['DATA']['STEAM_ID']."','".$stat['DATA']['TIME']."','".$stat['DATA']['TOTAL']."','".$stat['DATA']['WARMUP']."')");
    }
    else if ($stat['TYPE'] == 'PLAYER_STATS' &&  $stat['DATA']['WARMUP'] == False){
	mysqli_query($link, "INSERT INTO stats (`server`,`ABORTED`,`BLUE_FLAG_PICKUPS`, `DAMAGE`, `DEATHS`, `HOLY_SHITS`, `KILLS`, `LOSE`, `MATCH_GUID`, `MAX_STREAK`, `MEDALS`, `MODEL`, `NAME`, `NEUTRAL_FLAG_PICKUPS`, `PICKUPS`, `PLAY_TIME`, `QUIT`, `RANK`,`RED_FLAG_PICKUPS`, `SCORE`, `STEAM_ID`,  `TEAM`, `TEAM_JOIN_TIME`,`TEAM_RANK`, `TIED_RANK`, `TIED_TEAM_RANK`,`WARMUP`, `WEAPONS`, `WIN`) VALUES ('".$serv."','".$stat['DATA']['ABORTED']."','".$stat['DATA']['BLUE_FLAG_PICKUPS']."','".json_encode($stat['DATA']['DAMAGE'])."','".$stat['DATA']['DEATHS']."','".$stat['DATA']['HOLY_SHITS']."','".$stat['DATA']['KILLS']."','".$stat['DATA']['LOSE']."','".$stat['DATA']['MATCH_GUID']."','".$stat['DATA']['MAX_STREAK']."','".json_encode($stat['DATA']['MEDALS'])."','".$stat['DATA']['MODEL']."','".$stat['DATA']['NAME']."','".$stat['DATA']['NEUTRAL_FLAG_PICKUPS']."','".json_encode($stat['DATA']['PICKUPS'])."','".$stat['DATA']['PLAY_TIME']."','".$stat['DATA']['QUIT']."','".$stat['DATA']['RANK']."','".$stat['DATA']['RED_FLAG_PICKUPS']."','".$stat['DATA']['SCORE']."','".$stat['DATA']['STEAM_ID']."','".$stat['DATA']['TEAM']."','".$stat['DATA']['TEAM_JOIN_TIME']."','".$stat['DATA']['TEAM_RANK']."','".$stat['DATA']['TIED_RANK']."','".$stat['DATA']['TIED_TEAM_RANK']."','".$stat['DATA']['WARMUP']."','".json_encode($stat['DATA']['WEAPONS'])."','".$stat[DATA][WIN]."')");
    }
    else if ($stat['TYPE'] == 'MATCH_REPORT') {
	mysqli_query($link, "INSERT INTO reports (`server`,`CAPTURE_LIMIT`,`EXIT_MSG`,`FACTORY`,`FACTORY_TITLE`,`FIRST_SCORER`,`FRAG_LIMIT`,`GAME_LENGTH`,`GAME_TYPE`,`INFECTED`,`INSTAGIB`,`LAST_LEAD_CHANGE_TIME`,`LAST_SCORER`,`LAST_TEAMSCORER`,`MAP`,`MATCH_GUID`,`MERCY_LIMIT`,`QUADHOG`,`RESTARTED`,`SCORE_LIMIT`,`SERVER_TITLE`,`TIME_LIMIT`,`TRAINING`,`TSCORE0`,`TSCORE1`) VALUES ('".$serv."','".$stat['DATA']['CAPTURE_LIMIT']."','".$stat['DATA']['EXIT_MSG']."','".$stat['DATA']['FACTORY']."','".$stat['DATA']['FACTORY_TITLE']."','".$stat['DATA']['FIRST_SCORER']."','".$stat['DATA']['FRAG_LIMIT']."','".$stat['DATA']['GAME_LENGTH']."','".$stat['DATA']['GAME_TYPE']."','".$stat['DATA']['INFECTED']."','".$stat['DATA']['INSTAGIB']."','".$stat['DATA']['LAST_LEAD_CHANGE_TIME']."','".$stat['DATA']['LAST_SCORER']."','".$stat['DATA']['LAST_TEAMSCORER']."','".$stat['DATA']['MAP']."','".$stat['DATA']['MATCH_GUID']."','".$stat['DATA']['MERCY_LIMIT']."','".$stat['DATA']['QUADHOG']."','".$stat['DATA']['RESTARTED']."','".$stat['DATA']['SCORE_LIMIT']."','".$stat['DATA']['SERVER_TITLE']."','".$stat['DATA']['TIME_LIMIT']."','".$stat['DATA']['TRAINING']."','".$stat['DATA']['TSCORE0']."','".$stat['DATA']['TSCORE1']."')");

	$win = mysqli_query($link, "SELECT * FROM stats WHERE MATCH_GUID = '".$stat['DATA'][MATCH_GUID]."' AND WIN = 1 AND WARMUP = False");
	$winner = mysqli_fetch_array($win);
	mysqli_query($link, "INSERT INTO players (`STEAM_ID`) VALUES ('".$winner['STEAM_ID']."')");	
	$los = mysqli_query($link, "SELECT * FROM stats WHERE MATCH_GUID = '".$stat['DATA'][MATCH_GUID]."' AND WIN = 0 AND WARMUP = False");
	$loser = mysqli_fetch_array($los);				
	mysqli_query($link, "INSERT INTO players (`STEAM_ID`) VALUES ('".$loser['STEAM_ID']."')");

	$wl = mysqli_query($link, "SELECT * FROM players WHERE STEAM_ID = '".$winner['STEAM_ID']."'");
	$winner_elo = mysqli_fetch_array($wl);		

	$ll = mysqli_query($link, "SELECT * FROM players WHERE STEAM_ID = '".$loser['STEAM_ID']."'");
	$loser_elo = mysqli_fetch_array($ll);	
	$rating = new Rating($winner_elo[DUEL_ELO], $loser_elo[DUEL_ELO], 1, 0);
	$results = $rating->getNewRatings();

	mysqli_query($link, "UPDATE stats SET old_elo='".$winner_elo[DUEL_ELO]."', new_elo='".$results['a']."' WHERE MATCH_GUID = '".$stat['DATA'][MATCH_GUID]."' AND STEAM_ID = '".$winner['STEAM_ID']."'");
	mysqli_query($link, "UPDATE stats SET old_elo='".$loser_elo[DUEL_ELO]."', new_elo='".$results['b']."' WHERE MATCH_GUID = '".$stat['DATA'][MATCH_GUID]."' AND STEAM_ID = '".$loser['STEAM_ID']."'");

	mysqli_query($link, "UPDATE players SET DUEL_ELO='".$results['a']."' WHERE STEAM_ID = '".$winner['STEAM_ID']."'");
	mysqli_query($link, "UPDATE players SET DUEL_ELO='".$results['b']."' WHERE STEAM_ID = '".$loser['STEAM_ID']."'");
    }
    print_r($stat);
    mysqli_close($link);
}
?>
