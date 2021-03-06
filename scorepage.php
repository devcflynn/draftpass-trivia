<?php
include("config.php");

//Leave Team
if (isset($_GET["leave"])) {
	if (isset($_POST["teamname"]) && ($_POST["teamname"] == null)) {
		setcookie("teamname", null, time() - 3600, '/');
	}
}

$round = $database->get("round", "*");
$teamname = false;
$answerround='answers_r'.$round["current"];

if(isset($_POST['teamname'])) {
	if(strlen($_POST['teamname']) > 0) {
		setcookie("teamname", '', time() + (86400 * 30), "/"); 
		$uppercaseme = strtoupper($_POST["teamname"]);
		$uppercaseteam = isset($points["teamname"]) ? strtoupper($points["teamname"]) : '';
		if ($uppercaseme == $uppercaseteam) {
			$combineteams = $points["teamname"]; 
		} else {
			$combineteams = substr($_POST["teamname"],0,25);
		}

		if (!isset($_COOKIE["teamname"])) {
			setcookie("teamname", $combineteams, time() + (86400 * 30), "/"); 
		}
		if (isset($_COOKIE["teamname"])) {
			$teamname=substr($_COOKIE["teamname"],0,25);
		}elseif (empty($_COOKIE["teamname"]) && $_POST["teamname"] != null) {
			$teamname=substr($_POST["teamname"],0,25);
		} 
	} else {
		print "No team name was entered. Team names must have letters or numbers in them Please go back and enter a new name before submitting your answers.";
			exit;
	}
} else {

	if (isset($_COOKIE["teamname"])) { 
		$teamname = $_COOKIE["teamname"]; 
	}

	$points = $database->get('points', ["idnum", "teamname", "total", "dispute"], [
		'teamname' => $teamname
	]);

}
echo $templates->render('scorepage', compact(
	'database',
	'round',
	'teamname',
	'answerround',
	'points'
));
?>