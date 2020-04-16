<?php 
<?php $this->layout('template', ['title' => 'Score Page']); ?>
if (isset($_GET["answer"])) {
	if ($_GET["answer"] == 1 || $_GET["answer"] == 2) {
		//For manually answered questions
		$round = $database->select($_GET["currentround"], 
			['*'],
			['idnum' => $_GET["idnum"], 'checked' => 0]
		);
		$cround=$round["round"];
		$teampoints = $database->select("points", 
			["$cround", 'total'],
			['idnum' => $round["teamid"]]
		);
		if ($_GET["answer"] == 2) {
			$database->update($_GET["currentround"], 
				['checked' => 1, 'correct' => 1], 
				['idnum' => $_GET["idnum"]]
			);
			if ($round["round"] == 'firsthalf' || $round["round"] == 'secondhalf' || $round["round"] == 'wager') { $points=$round["wager"]; }
			if ($round["round"] == 'picture' || $round["round"] == 'id') { $points=2; }
			if ($round["round"] == 'currentevents') { $points=1; }
			if ($round["round"] == 'wager') {
				if ($points > $teampoints["total"]) { $points=$teampoints["total"]; }
			}
			if ($round["wager"] > 0) {
				for($i=1; $i<$round["question"];$i++) {
					$duplicates = $database->select('$_GET["currentround"]', 
						['wager'],
						['round' => $round["round"], 'teamid' => $round["teamid"], 'question' => $i]
					);
					if ($duplicates["wager"] == $round["wager"] && $round["round"] != 'wager') { $points=0; }
				}
			}
			$newpoints=$teampoints["$cround"]+$points;
			$totalpoints=$teampoints["total"]+$points;
			$database->update('points', 
				[$round["round"] => $newpoints, 'total' => $totalpoints], 
				['idnum' => $round["teamid"]]
			);
		} elseif ($_GET["answer"] == 1) {
			$database->update($_GET["currentround"], 
				['checked' => 1, 'correct' => 0], 
				['idnum' => $_GET["idnum"]]
			);
			if ($round["round"] == 'wager') {
				if ($round["wager"] > $teampoints["total"]) { $round["wager"]=$teampoints["total"]; }
				$roundpoints=$teampoints["wager"]-$round["wager"];
				$totalpoints=$teampoints["total"]-$round["wager"];
				$database->update('points', 
					[$round["round"] => $roundpoints, 'total' => $totalpoints], 
					['idnum' => $round["teamid"]]
				);
			}
		}
	}
}
$round = $database->select('round', 
	'*',
	['id[>]' => 0]
);

$firsthalf = (isset($round["firsthalf"]) && $round["firsthalf"] == 1) ? 'checked' : '';
$picture = (isset($round["picture"]) && $round["picture"] == 1) ? 'checked' : '';
$secondhalf = (isset($round["secondhalf"]) && $round["secondhalf"] == 1) ? 'checked' : '';
$id = (isset($round["id"]) && $round["id"] == 1) ? 'checked' : '';
$currentevents = (isset($round["currentevents"]) && $round["currentevents"] == 1) ? 'checked' : '';
$wager = (isset($round["wager"]) && $round["wager"] == 1) ? 'checked' : '';
$scores = (isset($round["scores"]) && $round["scores"] == 1) ? 'checked' : '';

echo '<div align=center><table align=center width=98% class=answersheet>
<tr><td class=answersheet colspan=4>Current Round: <label><input type=radio name=firsthalf value=firsthalf '.$firsthalf.'>First Half</label>
<label><input type=radio name=firsthalf value=picture  '.$picture.'>Picture Round</label>
<label><input type=radio name=firsthalf value=secondhalf '.$secondhalf.'>Second Half</label>
<label><input type=radio name=firsthalf value=id '.$id.'>ID Round</label>
<label><input type=radio name=firsthalf value=currentevents '.$currentevents.'>Social</label>
<label><input type=radio name=firsthalf value=wager '.$wager.'>Final Questions</label>
<label><input type=radio name=firsthalf value=scores '.$scores.'>Scores</label>
<input type=submit value=Submit name=btn-submit id=san-button></form></td></tr>';


if (isset($_POST["firsthalf"])) {
	//For auto answered
	$anyleft = $database->count('answers_r1', ['checked' => 2]); if ($anyleft > 0) { $checkanswerround='answers_r1'; }
	$anyleft2 = $database->count('answers_r2', ['checked' => 2]); if ($anyleft2 > 0) { $checkanswerround='answers_r2'; }
	$anyleft3 = $database->count('answers_r3', ['checked' => 2]); if ($anyleft3 > 0) { $checkanswerround='answers_r3'; }
	$anyleft4 = $database->count('answers_r4', ['checked' => 2]); if ($anyleft4 > 0) { $checkanswerround='answers_r4'; }
	$anyleft5 = $database->count('answers_r5', ['checked' => 2]); if ($anyleft5 > 0) { $checkanswerround='answers_r5'; }
	$anyleft6 = $database->count('answers_r6', ['checked' => 2]); if ($anyleft6 > 0) { $checkanswerround='answers_r6'; }
	if ($checkanswerround != null)  {
		$answer = $database->select($checkanswerround,
			['idnum', 'teamid', 'round','wager', 'question'],
			['checked' => 2] 
		);
		foreach($answer AS $round) {
			$cround=$round["round"];
			$teampoints = $database->select('points',
				[$cround, 'total'],
				['idnum' => $round["teamid"]] 
			);
			$database->update($checkanswerround, 
				['checked' => 1, 'correct' => 2], 
				['idnum' => $round["idnum"]]
			);
			if ($round["round"] == 'firsthalf' || $round["round"] == 'secondhalf' || $round["round"] == 'wager') { $points=$round["wager"]; }
			if ($round["round"] == 'picture' || $round["round"] == 'id') { $points=2; }
			if ($round["round"] == 'currentevents') { $points=1; }
			if ($round["round"] == 'wager') {
				if ($points > $teampoints["total"]) { $points=$teampoints["total"]; }
			}
			if ($round["wager"] > 0) {
				for($i=1; $i<$round["question"];$i++) {
					$duplicates = $database->select($checkanswerround,
						['wager'],
						['round' => $round["round"], 'teamid' => $round["teamid"], 'question' => $i] 
					);
					if ($duplicates["wager"] == $round["wager"] && $round["round"] != 'wager') { $points=0; } //wager round you can bet 12 and 12 for example
				}
			}

			$newpoints=$teampoints["$cround"]+$points;
			$totalpoints=$teampoints["total"]+$points;
			$database->update('points', 
				[$round["round"] => $newpoints, 'total' => $totalpoints], 
				['idnum' => $round["teamid"]]
			);
		}
	}
	//update current round
	if ($_POST["firsthalf"] == 'firsthalf') { $current=1; }
	if ($_POST["firsthalf"] == 'picture') { $current=2; }
	if ($_POST["firsthalf"] == 'secondhalf') { $current=3; }
	if ($_POST["firsthalf"] == 'id') { $current=4; }
	if ($_POST["firsthalf"] == 'currentevents') { $current=5; }
	if ($_POST["firsthalf"] == 'wager') { $current=6; }
	if ($_POST["firsthalf"] == 'scores') { $current=$currentround["current"]; }
	$database->update('round', 
		['firsthalf' => 0, 'picture' => 0, 'secondhalf' => 0, 'id' => 0, 'currentevents' => 0, 'wager' => 0, 'scores' => 0, $_POST["firsthalf"] => 1, 'current' => $current]
	);
}

if (isset($_POST["r1"])) {
	$total=$_POST["r1"]+$_POST["r2"]+$_POST["r3"]+$_POST["r4"]+$_POST["r5"]+$_POST["r6"];
	$database->update('points', 
		['firsthalf' => $_POST["r1"], 'picture' => $_POST["r2"], 'secondhalf' => $_POST["r3"], 'id' => $_POST["r4"], 'currentevents' => $_POST["r5"], 'wager' => $_POST["r6"], 'total' => $total], 
		['idnum' => $round["teamid"]]
	);
	echo '<tr><td class=answersheet colspan=4><br><br>Score updated.</td></tr>';
}

//Flags questions for recheck
if (isset($_GET["flag"])) {
	$flagged = $database->select($_GET["currentround"],
		['idnum', 'teamid', 'round','wager', 'question', 'correct'],
		['idnum' => $_GET["flag"], 'checked' => 1] 
	);
	if ($flagged["idnum"] > 0) {
		if ($flagged["round"] == 'firsthalf' || $flagged["round"] == 'secondhalf' || $flagged["round"] == 'wager') { $points=$flagged["wager"]; }
		if ($flagged["round"] == 'picture' || $flagged["round"] == 'id') { $points=2; }
		if ($flagged["round"] == 'currentevents') { $points=1; }
		if ($flagged["round"] == 'wager') {
			$teampoints = $database->select('points',
				['total'],
				['idnum' => $flagged["teamid"]] 
			);
			if ($points > $teampoints["total"]) { $points=$teampoints["total"]; }
		}
		if ($flagged["wager"] > 0) {
			for($i=1; $i<$flagged["question"];$i++) {
				$duplicates = $database->select($_GET["currentround"],
					['wager'],
					['teamid' => $flagged["teamid"], 'question' => $i] 
				);
				if ($duplicates["wager"] == $flagged["wager"] && $flagged["round"] != 'wager') { $points=0; } //wager round you can bet 12 and 12 for example
			}
		}
		$cround = $flagged["round"];
		$flaggedpoints = $database->select('points',
			[$cround, 'total'],
			['idnum' => $flagged["teamid"]] 
		);
		if ($flagged["correct"] == 1) {
			if ($flaggedpoints["$cround"] >= $points or $flagged["round"] == 'wager') { $newround=$flaggedpoints["$cround"]-$points; $newtotal=$flaggedpoints["total"]-$points;
			} else { $newround=$flaggedpoints["$cround"]; $newtotal=$flaggedpoints["total"]; }
		} elseif ($flagged["correct"] == 0 && $flagged["round"] == 'wager') {
			$newround=$flaggedpoints["$cround"]+$points; $newtotal=$flaggedpoints["total"]+$points;
		} else { $newround=$flaggedpoints["$cround"]; $newtotal=$flaggedpoints["total"]; }
		$database->update('points', 
			[$flagged["round"] => $newround, 'total' => $newtotal], 
			['idnum' => $flagged["teamid"]]
		);
		$database->update($_GET["currentround"], 
			['checked' => 0, 'correct' => 0], 
			['idnum' => $_GET["flag"]]
		);
		echo '<tr><td class=answersheet colspan=4>Previous answer flagged, please recheck.<br></td></tr>';
	}
}

if (isset($_GET["delete"]) == 1) {
	echo '<tr><td class=answersheet colspan=4><a href=scorekeeper.php?delete=2>Are you sure?</a></td></tr>';
	exit;
}
if (isset($_GET["delete"]) == 2) {
	//mysqli_query($con,"ALTER TABLE answers_r1 AUTO_INCREMENT = 1");
	//mysqli_query($con,"ALTER TABLE answers_r2 AUTO_INCREMENT = 1");
	//mysqli_query($con,"ALTER TABLE answers_r3 AUTO_INCREMENT = 1");
	//mysqli_query($con,"ALTER TABLE answers_r4 AUTO_INCREMENT = 1");
	//mysqli_query($con,"ALTER TABLE answers_r5 AUTO_INCREMENT = 1");
	//mysqli_query($con,"ALTER TABLE answers_r6 AUTO_INCREMENT = 1");
	//mysqli_query($con,"ALTER TABLE points AUTO_INCREMENT = 1");
	$database->delete('answers_r1', ['idnum[>]' => 0]);
	$database->delete('answers_r2', ['idnum[>]' => 0]);
	$database->delete('answers_r3', ['idnum[>]' => 0]);
	$database->delete('answers_r4', ['idnum[>]' => 0]);
	$database->delete('answers_r5', ['idnum[>]' => 0]);
	$database->delete('answers_r6', ['idnum[>]' => 0]);
	$database->delete('points', ['teamid[>]' => 0]);
	$database->update('round', 
		['firsthalf' => 1, 'picture' => 0, 'secondhalf' => 0, 'id' => 0, 'currentevents' => 0, 'wager' => 0, 'scores' => 0, 'current' => 1], 
		[]
	);
	echo '<tr><td class=answersheet colspan=4><a href=scorekeeper.php>Deleted. Go Back?</a></td></tr>';
	exit;
}

$cround = $database->select('round', '*');

for($i=1; $i<=15;$i++) {
	$anyleft = $database->count('answers_r1', ['checked' => 0]); if ($anyleft > 0) { $checkanswerround='answers_r1'; }
	$anyleft2 = $database->count('answers_r2', ['checked' => 0]); if ($anyleft2 > 0) { $checkanswerround='answers_r2'; }
	$anyleft3 = $database->count('answers_r3', ['checked' => 0]); if ($anyleft3 > 0) { $checkanswerround='answers_r3'; }
	$anyleft4 = $database->count('answers_r4', ['checked' => 0]); if ($anyleft4 > 0) { $checkanswerround='answers_r4'; }
	$anyleft5 = $database->count('answers_r5', ['checked' => 0]); if ($anyleft5 > 0) { $checkanswerround='answers_r5'; }
	$anyleft6 = $database->count('answers_r6', ['checked' => 0]); if ($anyleft6 > 0) { $checkanswerround='answers_r6'; }
	if (isset($checkanswerround) && $checkanswerround != null)  {
		$answer = $database->select($checkanswerround,
			['idnum', 'teamid', 'round','wager', 'question'],
			['checked' => 0, 'question' => $i] 
		);
		foreach($answer AS $currentanswer) {
			$uncodedanswer=str_replace($currentanswer["teamid"],'',$currentanswer["answer"]);
			$q='q'.$i.'q';
			$q2=substr('$uncodedanswer', 2, 1);
			if (is_numeric($q2)) { $q=$currentanswer["teamid"].$q.$q2; } else { $q=$currentanswer["teamid"].$q; }
			$uncodedanswer=str_replace($q,'',$currentanswer["answer"]);
			if (empty($_GET["idnum"])) { $currentround =$currentanswer["idnum"]; } else { $currentround = $_GET["idnum"]; }
			if (!empty($_GET["previdnum"])) {
				$prevanswer = $database->select($checkanswerround,
					['question'],
					['idnum' => $_GET["previdnum"]] 
				);
			} else { 
				$prevanswer = $database->select($checkanswerround,
					['question'],
					['idnum' => $currentanswer["idnum"]] 
				);
			}
			$answerkey = $database->select('answerkey',
				['answer'],
				['question' => $currentanswer["question"], 'round' => $currentanswer["round"]] 
			);
			if ($currentanswer["question"] != $prevanswer["question"]) {
				echo '<tr><td class=answersheet colspan=4><br><br>Next question.  The answer for #' .$currentanswer["question"]. 'is '.$answerkey["answer"].'<br>
				<a href=scorekeeper.php id=refresh>Begin</a></td></tr>';
				exit;
			}
			echo '<tr><td class=answersheet colspan=4><br><br>
			<a href=scorekeeper.php?idnum='.$currentanswer["idnum"].'&previdnum='.$currentround.'&answer=2&currentround='.$checkanswerround.' id=correct><img src=/img/correct.png></a> 
			&nbsp;<a href=scorekeeper.php?idnum='.$currentanswer["idnum"].'&previdnum='.$currentround.'&answer=1&currentround='.$checkanswerround.' id=incorrect><img src=/img/incorrect.png></a>
			&nbsp;<a href=scorekeeper.php?flag='.$currentround.'&currentround='.$checkanswerround.' id=flag><img src=/img/flag.png></a><br>
			<br>Round: '.$currentanswer["round"].' &nbsp; | &nbsp; Answer: '.$answerkey["answer"].'<br><br>
			<b>#'.$currentanswer["question"].'</b>:   &nbsp; '.$uncodedanswer.'</td></tr>';	
			$checkround	= $i;
			exit;
		}
	}
}

if (isset($_GET["dispute"])) {
	$currentteam = $database->select($_GET["currentround"],
		['correct'],
		['idnum' => $_GET["dispute"], 'checked' => 1] 
	);
	if ($currentteam["correct"] > 0) {
		$currentteam = $database->select($_GET["currentround"],
			['teamid', 'round', 'question', 'wager'],
			['idnum' => $_GET["dispute"], 'correct' => 1, 'checked' => 1] 
		);
		$database->update($_GET["currentround"], 
			['correct' => 0], 
			['idnum' => $_GET["dispute"]]
		);
		if ($currentteam["round"] == 'firsthalf' || $currentteam["round"] == 'secondhalf' || $currentteam["round"] == 'wager') { $points=$currentteam["wager"]; }
		if ($currentteam["round"] == 'picture' || $currentteam["round"] == 'id') { $points=2; }
		if ($currentteam["round"] == 'currentevents') { $points=1; }
		if ($currentteam["round"] == 'wager') {
			$teampoints = $database->select('points',
				['total'],
				['idnum' => $currentteam["teamid"]] 
			);
			if ($points > $teampoints["total"]) { $points=$teampoints["total"]; }
		}
		if ($currentteam["wager"] > 0) {
			for($i=1; $i<$round["question"];$i++) {
				$duplicates = $database->select($_GET["currentround"],
					['wager'],
					['round' => $currentteam["round"], 'teamid' => $currentteam["teamid"], 'question' => $i] 
				);
				if ($duplicates["wager"] == $currentteam["wager"] && $currentteam["round"] != 'wager') { $points=0; } //wager round you can bet 12 and 12 for example
			}
		}
		$cround = $currentteam["round"];
		$currentteampoints = $database->select('points',
			[$cround, 'total'],
			['idnum' => $currentteam["teamid"]] 
		);
		if ($currentteam["round"] != 'wager') {
			$newround=$currentteampoints["$cround"]-$points;
			$newtotal=$currentteampoints["total"]-$points;
		} else {
			$incorrect = $database->select($_GET["currentround"],
				['wager'],
				['teamid' => $currentteam["teamid"], 'correct' => 0] 
			);
			foreach($incorrect AS $allincorrect) {
				$points=$allincorrect["wager"];
				$newround=$newround-$points;
			}
			$correct = $database->select($_GET["currentround"],
				['wager'],
				['teamid' => $currentteam["teamid"], 'correct' => 1] 
			);
			foreach($correct AS $allcorrect) {
				$points=$allcorrect["wager"];
				$newpoints=$newpoints+$points;
			}
			$ototal=$currentteampoints["firsthalf"]+$currentteampoints["picture"]+$currentteampoints["secondhalf"]+$currentteampoints["id"]+$currentteampoints["currentevents"];
			$newtotal= $ototal + $newround + $newpoints;
			$newround=$newround+$newpoints;
		}
		$database->update('points', 
			[$currentteam["round"] => $newround, 'total' => $newtotal], 
			['idnum' => $currentteam["teamid"]]
		);
		$message='Removed $points from $currentteam["round"] for team $currentteam["teamname"]. Their new totals are $newround for round $currentteam["round"] and $newtotal overall.';
	} else {
		$currentteam = $database->select($_GET["currentround"],
			['teamid', 'round', 'question', 'wager'],
			['idnum' => $_GET["dispute"], 'correct' => 0, 'checked' => 1] 
		);
		$database->update($_GET["currentround"], 
			['correct' => 1], 
			['idnum' => $_GET["dispute"]]
		);
		if ($currentteam["round"] == 'firsthalf' || $currentteam["round"] == 'secondhalf' || $currentteam["round"] == 'wager') { $points=$currentteam["wager"]; }
		if ($currentteam["round"] == 'picture' || $currentteam["round"] == 'id') { $points=2; }
		if ($currentteam["round"] == 'currentevents') { $points=1; }
		if ($currentteam["round"] == 'wager') {
			$teampoints = $database->select('points',
				['total'],
				['idnum' => $currentteam["teamid"]] 
			);
			if ($points > $teampoints["total"]) { $points=$teampoints["total"]; }
		}
		if ($currentteam["wager"] > 0) {
			for($i=1; $i<$currentteam["question"];$i++) {
				$duplicates = $database->select($_GET["currentround"],
					['wager'],
					['round' => $currentteam["round"], 'teamid' => $currentteam["teamid"], 'question' => $i] 
				);
				if ($duplicates["wager"] == $currentteam["wager"] && $currentteam["round"] != 'wager') { $points=0; }
			}
		}
		$cround=$currentteam["round"];
		$teampoints = $database->select('points',
				[$cround, 'total'],
				['idnum' => $currentteam["teamid"]] 
		);
		if ($currentteam["round"] != 'wager') {
			$newpoints=$teampoints["$cround"]+$points;
			$totalpoints=$teampoints["total"]+$points;
		} else {
			$incorrect = $database->select($_GET["currentround"],
				['wager'],
				['teamid' => $currentteam["teamid"], 'correct' => 0] 
			);
			foreach($incorrect AS $allincorrect) {
				$points=$allincorrect["wager"];
				$newround=$newround-$points;
			}
			$correct = $database->select($_GET["currentround"],
				['wager'],
				['teamid' => $currentteam["teamid"], 'correct' => 1] 
			);
			foreach($correct AS $allcorrect) {
				$points=$allcorrect["wager"];
				$newpoints=$newpoints+$points;
			}
			$ototal=$teampoints["firsthalf"]+$teampoints["picture"]+$teampoints["secondhalf"]+$teampoints["id"]+$teampoints["currentevents"];
			$totalpoints=$ototal+$newround+$newpoints;
			$newpoints=$newround+$newpoints;
		}
		$database->update('points', 
			[$currentteam["round"] => $newpoints, 'total' => $totalpoints], 
			['idnum' => $currentteam["teamid"]]
		);
		$message='Added $points to $currentteam["round"]. Their new totals are $newpoints for round $currentteam["round"] and $totalpoints overall.';
	}
	echo '<tr><td class=answersheet colspan=4><br>'.$message.'<br><br></td></tr>';
}

if (isset($_GET["update"])) {
	$teampoints = $database->select('points',
		['*'],
		['idnum' => $_GET["update"]] 
	);
	if ($_GET["round"] == 'all') {
		echo '<form method=post action=scorekeeper.php><input type=hidden value='.$_GET["update"].' name=teamid>
		<tr><td class=rank colspan=4 id=scores_header><b>'.$teampoints["teamname"].'</b></td></tr>
		<tr><td class=rank_right id=scores1 colspan=2>First Half:</td><td class=rank_left  id=scores1 colspan=2><input type=text name=r1 value='.$teampoints["firsthalf"].'></td></tr>
		<tr><td class=rank_right id=scores2 colspan=2>Picture Round:</td><td class=rank_left  id=scores2 colspan=2><input type=text name=r2 value='.$teampoints["picture"].'></td></tr>
		<tr><td class=rank_right id=scores1 colspan=2>Second Half:</td><td class=rank_left  id=scores1 colspan=2><input type=text name=r3 value='.$teampoints["secondhalf"].'></td></tr>
		<tr><td class=rank_right id=scores2 colspan=2>ID Round:</td><td class=rank_left  id=scores2 colspan=2><input type=text name=r4 value='.$teampoints["id"].'></td></tr>
		<tr><td class=rank_right id=scores1 colspan=2>Social Bonus:</td><td class=rank_left  id=scores1 colspan=2><input type=text name=r5 value='.$teampoints["currentevents"].'></td></tr>
		<tr><td class=rank_right id=scores2 colspan=2>Final Questions:</td><td class=rank_left  id=scores2 colspan=2><input type=text name=r6 value='.$teampoints["wager"].'></td></tr>
		<tr><td class=rank colspan=4 id><input type=submit value=Submit name=btn-submit id=san-button></form></td></tr>';
	} else {
		echo '<tr><td colspan=4 id=scores_header><b>Team: '.$teampoints["teamname"].'</b><br><br></td></tr>
		<tr><td class=rank_header id=scores_header>Round</td>
		<td class=rank_header id=scores_header>Question</td>
		<td class=rank_header id=scores_header>Answer(Wager)</td>
		<td class=rank_header id=scores_header>Status</td></tr>';
	}
	if ($_GET["round"] == 'total') {
		//$fromwhere=" and checked='1'";
		$cround='total'; 
	} else {
		//$fromwhere="and round ='".$_GET["round"]."' and checked='1'";
		$cround=$_GET["round"];
		if ($cround == 'firsthalf') { $currentround='answers_r1'; }
		if ($cround == 'picture') { $currentround='answers_r2'; }
		if ($cround == 'secondhalf') { $currentround='answers_r3'; }
		if ($cround == 'id') { $currentround='answers_r4'; }
		if ($cround == 'currentevents') { $currentround='answers_r5'; }
		if ($cround == 'wager') { $currentround='answers_r6'; }
	}
	$teampoints = $database->select('points',
		['idnum','teamname', $cround, 'total'],
		['idnum' => $_GET["update"]] 
	);
	$teams = $database->select($currentround,
		['idnum', 'teamid', 'round', 'question', 'answer', 'correct', 'wager'],
		["ORDER" => ["round" => "ASC"]],
		['teamid' => $_GET["update"], 'round' => $_GET["round"], 'checked' => 1] 
	);
	foreach($teams AS $allteams) {
		if ($alternate == 1) { $alternate=2; } else { $alternate=1; }
		if ($allteams["correct"] == '0') {
			$option="<img src=/img/incorrect.png></a>";
		} else {
			$option='<img src=/img/correct.png>'; 
		}
		if ($allteams["wager"] > 0) { $wager='('.$allteams["wager"].')'; } else { $wager=''; }
		$uncodedanswer=str_replace($teampoints["idnum"],'',$allteams["answer"]);
		$q='q'.$allteams["question"].'q';
		$q2=substr('$uncodedanswer', 2, 1);
		if (is_numeric($q2)) { $q=$teampoints["idnum"].$q.$q2; } else { $q=$teampoints["idnum"].$q; }
		$uncodedanswer=str_replace($q,'',$allteams["answer"]);
		echo '<tr><td class=rank id=scores$alternate>'.$allteams["round"].'</td>
		<td class=rank id=scores$alternate>'.$allteams["question"].'</td>
		<td class=rank id=scores$alternate>'.$uncodedanswer.$wager.'</td>
		<td class=rank id=scores$alternate><a href=scorekeeper.php?dispute='.$allteams["idnum"].'&update='.$allteams["teamid"].'&round='.$allteams["round"].'&currentround='.$currentround.'>'.$option.'</a></td></tr>';
	}
	if ($alternate == 1) { $alternate=2; } else { $alternate=1; }
	if ($_GET["round"] == 'all') { $showwhatpoints=$teampoints["total"]; } else { $showwhatpoints=$teampoints["$cround"]; }
	echo '<td class=rank id=scores'.$alternate.' colspan=4>'.$_GET["round"].' points: '.$showwhatpoints.'</td></tr>';
}

echo '<tr><td class=answersheet colspan=4><br><br><a href=scorekeeper.php id=refresh>Refresh</a> | <a href=answerkey.php>Answer Sheet</a> | <a href=hosts.php>Hosts</a>  | <a href=scorekeeper.php?delete=1>Delete All</a> </td></tr></table>
<br><br><table align=center width=98% class=answersheet>
<tr><td class=rank_header id=scores_header>Rank</td>
<td class=rank_header id=scores_header>Team Name</td>
<td class=rank_header id=scores_header>Total</td>
<td class=rank_header id=scores_header>First Half</td>
<td class=rank_header id=scores_header>Picture Round</td>
<td class=rank_header id=scores_header>Second Half</td>
<td class=rank_header id=scores_header>ID Round</td>
<td class=rank_header id=scores_header>Social Bonus</td>
<td class=rank_header id=scores_header>Final Questions</td></tr>';

$rank=0;
$alternate=1;
$teams = $database->select('points',
	'*',
	["ORDER" => ["total" => "DESC"]] 
);
foreach($teams AS $allteams) {
	$rank=$rank+1;
	if ($alternate == 1) { $alternate=2; } else { $alternate=1; }
	echo '<tr><td class=rank id=scores'.$alternate.'>'.$rank.'</td>
	<td class=rank id=scores'.$alternate.'><a href=scorekeeper.php?update='.$allteams["idnum"].'&round=all  class=rank>'.$allteams["teamname"].'</a></td>
	<td class=rank id=scores'.$alternate.'><a href=scorekeeper.php?update='.$allteams["idnum"].'&round=total  class=rank>'.$allteams["total"].'</a></td>
	<td class=rank id=scores'.$alternate.'><a href=scorekeeper.php?update='.$allteams["idnum"].'&round=firsthalf  class=rank>'.$allteams["firsthalf"].'</a></td>
	<td class=rank id=scores'.$alternate.'><a href=scorekeeper.php?update='.$allteams["idnum"].'&round=picture  class=rank>'.$allteams["picture"].'</a></td>
	<td class=rank id=scores'.$alternate.'><a href=scorekeeper.php?update='.$allteams["idnum"].'&round=secondhalf  class=rank>'.$allteams["secondhalf"].'</a></td>
	<td class=rank id=scores'.$alternate.'><a href=scorekeeper.php?update='.$allteams["idnum"].'&round=id  class=rank>'.$allteams["id"].'</a></td>
	<td class=rank id=scores'.$alternate.'><a href=scorekeeper.php?update='.$allteams["idnum"].'&round=currentevents  class=rank>'.$allteams["currentevents"].'</a></td>
	<td class=rank id=scores'.$alternate.'><a href=scorekeeper.php?update='.$allteams["idnum"].'&round=wager  class=rank>'.$allteams["wager"].'</a></td></tr>';
}
echo '</td></tr></table>';
?>

<script>
if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}
</script>

<script type="text/javascript">
$(document).ready(function() {
    $('#body').show();
    $('#msg').hide();
});
</script>

<script type="text/javascript">
document.addEventListener("keydown",function(e){
   var key = e.which||e.keyCode;
   switch(key){
      //1
      case 49:
         document.getElementById("correct").click();
      break;
	  case 97:
         document.getElementById("correct").click();
      break;
      //2
      case 50:
         document.getElementById("incorrect").click();
	  case 98:
         document.getElementById("incorrect").click();
	  break;
	  //3
      case 51:
         document.getElementById("flag").click();
	  case 99:
         document.getElementById("flag").click();
      break;
	  //spacebar
      case 32:
         document.getElementById("refresh").click();

   }
});
</script>