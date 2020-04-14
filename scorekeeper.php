<?php include("config.php"); ?>

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
<?php
if ($_GET[answer] == 1 or $_GET[answer] == 2) {
	//For manually answered questions
	$round = mysqli_query($con,"select * from $_GET[currentround] where idnum='$_GET[idnum]' and checked ='0'");
	$round = mysqli_fetch_array($round);
	$cround=$round[round];
	$teampoints = mysqli_query($con,"select $cround, total from points where idnum='$round[teamid]'");
	$teampoints = mysqli_fetch_array($teampoints);
	if ($_GET[answer] == 2) {
		mysqli_query($con,"update $_GET[currentround] set checked='1', correct='1' where idnum='$_GET[idnum]'");
		if ($round[round] == 'firsthalf' || $round[round] == 'secondhalf' || $round[round] == 'wager') { $points=$round[wager]; }
		if ($round[round] == 'picture' || $round[round] == 'id') { $points=2; }
		if ($round[round] == 'currentevents') { $points=1; }
		if ($round[round] == 'wager') {
			if ($points > $teampoints[total]) { $points=$teampoints[total]; }
		}
		if ($round[wager] > 0) {
			for($i=1; $i<$round[question];$i++) {
				$duplicates = mysqli_fetch_array(mysqli_query($con,"select wager from $_GET[currentround] where round='$round[round]' and teamid='$round[teamid]' and question='$i'"));
				if ($duplicates[wager] == $round[wager] && $round[round] != 'wager') { $points=0; }
			}
		}
		$newpoints=$teampoints[$cround]+$points;
		$totalpoints=$teampoints[total]+$points;
		mysqli_query($con,"update points set $round[round]=$newpoints where idnum='$round[teamid]'");
		mysqli_query($con,"update points set total=$totalpoints where idnum='$round[teamid]'");
	} elseif ($_GET[answer] == 1) {
		mysqli_query($con,"update $_GET[currentround] set checked='1', correct='0' where idnum='$_GET[idnum]'");
		if ($round[round] == 'wager') {
			if ($round[wager] > $teampoints[total]) { $round[wager]=$teampoints[total]; }
			$roundpoints=$teampoints[wager]-$round[wager];
			$totalpoints=$teampoints[total]-$round[wager];
			mysqli_query($con,"update points set $round[round]=$roundpoints where idnum='$round[teamid]'");
			mysqli_query($con,"update points set total=$totalpoints where idnum='$round[teamid]'");
		}
	}
}

$round = mysqli_fetch_array(mysqli_query($con,"select * from round"));
if ($round[firsthalf] ==1) { $firsthalf='checked'; }
if ($round[picture] ==1) { $picture='checked'; }
if ($round[secondhalf] ==1) { $secondhalf='checked'; }
if ($round[id] ==1) { $id='checked'; }
if ($round[currentevents] ==1) { $currentevents='checked'; }
if ($round[wager] ==1) { $wager='checked'; }
if ($round[scores] ==1) { $scores='checked'; }

print "<form method=post action=scorekeeper.php>
<div align=center><table align=center width=98% class=answersheet>
<tr><td class=answersheet colspan=4>Current Round: <label><input type='radio' name='firsthalf' value='firsthalf' $firsthalf>First Half</label>
<label><input type='radio' name='firsthalf' value='picture'  $picture>Picture Round</label>
<label><input type='radio' name='firsthalf' value='secondhalf' $secondhalf>Second Half</label>
<label><input type='radio' name='firsthalf' value='id' $id>ID Round</label>
<label><input type='radio' name='firsthalf' value='currentevents' $currentevents>Social</label>
<label><input type='radio' name='firsthalf' value='wager' $wager>Final Questions</label>
<label><input type='radio' name='firsthalf' value='scores' $scores>Scores</label>
<input type=submit value='Submit' name='btn-submit' id='san-button'></form></td></tr>";


if ($_POST[firsthalf]) {
	//For auto answered
	$anyleft = mysqli_num_rows(mysqli_query($con,"select checked from answers_r1 where checked='2'"));  if ($anyleft > 0) { $checkanswerround='answers_r1'; }
	$anyleft2 = mysqli_num_rows(mysqli_query($con,"select checked from answers_r2 where checked='2'")); if ($anyleft2 > 0) { $checkanswerround='answers_r2'; }
	$anyleft3 = mysqli_num_rows(mysqli_query($con,"select checked from answers_r3 where checked='2'")); if ($anyleft3 > 0) { $checkanswerround='answers_r3'; }
	$anyleft4 = mysqli_num_rows(mysqli_query($con,"select checked from answers_r4 where checked='2'")); if ($anyleft4 > 0) { $checkanswerround='answers_r4'; }
	$anyleft5 = mysqli_num_rows(mysqli_query($con,"select checked from answers_r5 where checked='2'")); if ($anyleft5 > 0) { $checkanswerround='answers_r5'; }
	$anyleft6 = mysqli_num_rows(mysqli_query($con,"select checked from answers_r6 where checked='2'")); if ($anyleft6 > 0) { $checkanswerround='answers_r6'; }
	if ($checkanswerround != null)  {
		$answer = mysqli_query($con,"select idnum, teamid, round, wager, question from $checkanswerround where checked='2'");
		while ($round = mysqli_fetch_array($answer)) {
			$cround=$round[round];
			$teampoints = mysqli_fetch_array(mysqli_query($con,"select $cround, total from points where idnum='$round[teamid]'"));
			mysqli_query($con,"update $checkanswerround set checked='1' where idnum='$round[idnum]'");
			mysqli_query($con,"update $checkanswerround set correct='2' where idnum='$round[idnum]'");
			if ($round[round] == 'firsthalf' || $round[round] == 'secondhalf' || $round[round] == 'wager') { $points=$round[wager]; }
			if ($round[round] == 'picture' || $round[round] == 'id') { $points=2; }
			if ($round[round] == 'currentevents') { $points=1; }
			if ($round[round] == 'wager') {
				if ($points > $teampoints[total]) { $points=$teampoints[total]; }
			}
			if ($round[wager] > 0) {
				for($i=1; $i<$round[question];$i++) {
					$duplicates = mysqli_fetch_array(mysqli_query($con,"select wager from $checkanswerround where round='$round[round]' and teamid='$round[teamid]' and question='$i'"));
					if ($duplicates[wager] == $round[wager] && $round[round] != 'wager') { $points=0; } //wager round you can bet 12 and 12 for example
				}
			}

			$newpoints=$teampoints[$cround]+$points;
			$totalpoints=$teampoints[total]+$points;
			mysqli_query($con,"update points set $round[round]=$newpoints where idnum='$round[teamid]'");
			mysqli_query($con,"update points set total=$totalpoints where idnum='$round[teamid]'");
		}
	}
	//update current round
		mysqli_query($con,"update round set firsthalf='0' where firsthalf='1'");
		mysqli_query($con,"update round set picture='0' where picture='1'");
		mysqli_query($con,"update round set secondhalf='0' where secondhalf='1'");
		mysqli_query($con,"update round set id='0' where id='1'");
		mysqli_query($con,"update round set currentevents='0' where currentevents='1'");
		mysqli_query($con,"update round set wager='0' where wager='1'");
		mysqli_query($con,"update round set scores='0' where scores='1'");
		mysqli_query($con,"update round set $_POST[firsthalf]='1' where $_POST[firsthalf]='0'");
		if ($_POST[firsthalf] == 'firsthalf') { $current=1; }
		if ($_POST[firsthalf] == 'picture') { $current=2; }
		if ($_POST[firsthalf] == 'secondhalf') { $current=3; }
		if ($_POST[firsthalf] == 'id') { $current=4; }
		if ($_POST[firsthalf] == 'currentevents') { $current=5; }
		if ($_POST[firsthalf] == 'wager') { $current=6; }
		if ($_POST[firsthalf] == 'scores') { $current=$currentround[current]; }
		mysqli_query($con,"update round set current='$current'");
		$currentround = mysqli_fetch_array(mysqli_query($con,"select current from round"));
}

if ($_POST[r1]) {
	mysqli_query($con,"update points set firsthalf='$_POST[r1]' where idnum='$_POST[teamid]'");
	mysqli_query($con,"update points set picture='$_POST[r2]' where idnum='$_POST[teamid]'");
	mysqli_query($con,"update points set secondhalf='$_POST[r3]' where idnum='$_POST[teamid]'");
	mysqli_query($con,"update points set id='$_POST[r4]' where idnum='$_POST[teamid]'");
	mysqli_query($con,"update points set currentevents='$_POST[r5]' where idnum='$_POST[teamid]'");
	mysqli_query($con,"update points set wager='$_POST[r6]' where idnum='$_POST[teamid]'");
	$total=$_POST[r1]+$_POST[r2]+$_POST[r3]+$_POST[r4]+$_POST[r5]+$_POST[r6];
	mysqli_query($con,"update points set total='$total' where idnum='$_POST[teamid]'");
	print "<tr><td class=answersheet colspan=4><br><br>Score updated.</td></tr>";
}

//Flags questions for recheck
if ($_GET[flag]) {
	$flagged = mysqli_fetch_array(mysqli_query($con,"select idnum, teamid, round, question, wager, correct from $_GET[currentround] where idnum='$_GET[flag]' and checked='1'"));
	if ($flagged[idnum] > 0) {
		if ($flagged[round] == 'firsthalf' || $flagged[round] == 'secondhalf' || $flagged[round] == 'wager') { $points=$flagged[wager]; }
		if ($flagged[round] == 'picture' || $flagged[round] == 'id') { $points=2; }
		if ($flagged[round] == 'currentevents') { $points=1; }
		if ($flagged[round] == 'wager') {
			$teampoints = mysqli_fetch_array(mysqli_query($con,"select total from points where idnum='$flagged[teamid]'"));
			if ($points > $teampoints[total]) { $points=$teampoints[total]; }
		}
		if ($flagged[wager] > 0) {
			for($i=1; $i<$flagged[question];$i++) {
				$duplicates = mysqli_fetch_array(mysqli_query($con,"select wager from $_GET[currentround] where teamid='$flagged[teamid]' and question='$i'"));
				if ($duplicates[wager] == $flagged[wager] && $flagged[round] != 'wager') { $points=0; } //wager round you can bet 12 and 12 for example
			}
		}
		$cround = $flagged[round];
		$flaggedpoints = mysqli_fetch_array(mysqli_query($con,"select $cround, total from points where idnum='$flagged[teamid]'"));
		if ($flagged[correct] == 1) {
			if ($flaggedpoints[$cround] >= $points or $flagged[round] == 'wager') { $newround=$flaggedpoints[$cround]-$points; $newtotal=$flaggedpoints[total]-$points;
			} else { $newround=$flaggedpoints[$cround]; $newtotal=$flaggedpoints[total]; }
		} elseif ($flagged[correct] == 0 && $flagged[round] == 'wager') {
			$newround=$flaggedpoints[$cround]+$points; $newtotal=$flaggedpoints[total]+$points;
		} else { $newround=$flaggedpoints[$cround]; $newtotal=$flaggedpoints[total]; }
		mysqli_query($con,"update points set $flagged[round]='$newround' where idnum='$flagged[teamid]'");
		mysqli_query($con,"update points set total='$newtotal' where idnum='$flagged[teamid]'");
		mysqli_query($con,"update $_GET[currentround] set checked='0' where idnum='$_GET[flag]'");
		mysqli_query($con,"update $_GET[currentround] set correct='0' where idnum='$_GET[flag]'");
		print "<tr><td class=answersheet colspan=4>Previous answer flagged, please recheck.<br></td></tr>";
	}
}

if ($_GET[delete]==1) {
	print "<tr><td class=answersheet colspan=4><a href=scorekeeper.php?delete=2>Are you sure?</a></td></tr>";
	exit;
}
if ($_GET[delete]==2) {
	if (performance.navigation.type != 1) {
	mysqli_query($con,"delete from answers_r1");
	mysqli_query($con,"delete from answers_r2");
	mysqli_query($con,"delete from answers_r3");
	mysqli_query($con,"delete from answers_r4");
	mysqli_query($con,"delete from answers_r5");
	mysqli_query($con,"delete from answers_r6");
	mysqli_query($con,"delete from points");
	mysqli_query($con,"update round set firsthalf='1'");
	mysqli_query($con,"update round set picture='0'");
	mysqli_query($con,"update round set secondhalf='0'");
	mysqli_query($con,"update round set id='0'");
	mysqli_query($con,"update round set currentevents='0'");
	mysqli_query($con,"update round set wager='0'");
	mysqli_query($con,"update round set scores='0'");
	mysqli_query($con,"update round set current='1'");
	mysqli_query($con,"ALTER TABLE answers_r1 AUTO_INCREMENT = 1");
	mysqli_query($con,"ALTER TABLE answers_r2 AUTO_INCREMENT = 1");
	mysqli_query($con,"ALTER TABLE answers_r3 AUTO_INCREMENT = 1");
	mysqli_query($con,"ALTER TABLE answers_r4 AUTO_INCREMENT = 1");
	mysqli_query($con,"ALTER TABLE answers_r5 AUTO_INCREMENT = 1");
	mysqli_query($con,"ALTER TABLE answers_r6 AUTO_INCREMENT = 1");
	mysqli_query($con,"ALTER TABLE points AUTO_INCREMENT = 1");
	print "<tr><td class=answersheet colspan=4><a href=scorekeeper.php>Deleted. Go Back?</a><a href=scorekeeper.php?delete=2></a></td></tr>";
	exit;
	}
}

$cround = mysqli_fetch_array(mysqli_query($con,"select * from round"));
if ($cround[firsthalf] == 1) { $notround='firsthalf'; }
if ($cround[picture] == 1) { $notround='picture'; }
if ($cround[secondhalf] == 1) { $notround='secondhalf'; }
if ($cround[id] == 1) { $notround='id'; }
if ($cround[currentevents] == 1) { $notround='currentevents'; }
if ($cround[wager] == 1) { $notround='wager'; }
$checkanswerround=null;

for($i=1; $i<=15;$i++) {
	$anyleft=mysqli_num_rows(mysqli_query($con,"select checked from answers_r1 where checked='0' and question='$i'"));  if ($anyleft > 0) { $checkanswerround='answers_r1'; }
	$anyleft2=mysqli_num_rows(mysqli_query($con,"select checked from answers_r2 where checked='0' and question='$i'")); if ($anyleft2 > 0) { $checkanswerround='answers_r2'; }
	$anyleft3=mysqli_num_rows(mysqli_query($con,"select checked from answers_r3 where checked='0' and question='$i'")); if ($anyleft3 > 0) { $checkanswerround='answers_r3'; }
	$anyleft4=mysqli_num_rows(mysqli_query($con,"select checked from answers_r4 where checked='0' and question='$i'")); if ($anyleft4 > 0) { $checkanswerround='answers_r4'; }
	$anyleft5=mysqli_num_rows(mysqli_query($con,"select checked from answers_r5 where checked='0' and question='$i'")); if ($anyleft5 > 0) { $checkanswerround='answers_r5'; }
	$anyleft6=mysqli_num_rows(mysqli_query($con,"select checked from answers_r6 where checked='0' and question='$i'")); if ($anyleft6 > 0) { $checkanswerround='answers_r6'; }
	if ($checkanswerround != null)  {
		$answer = mysqli_query($con,"select idnum, teamid, round, question, answer from $checkanswerround where checked='0' and question='$i' and round !='$notround' limit 1");
		while ($currentanswer = mysqli_fetch_array($answer)) {
			$uncodedanswer=str_replace($currentanswer[teamid],'',$currentanswer[answer]);
			$q='q'.$i.'q';
			$q2=substr('$uncodedanswer', 2, 1);
			if (is_numeric($q2)) { $q=$currentanswer[teamid].$q.$q2; } else { $q=$currentanswer[teamid].$q; }
			$uncodedanswer=str_replace($q,'',$currentanswer[answer]);
			if (empty($_GET[idnum])) { $currentround =$currentanswer[idnum]; } else {$currentround = $_GET[idnum]; }
			if (!empty($_GET[previdnum])) { $prevanswer = mysqli_fetch_array(mysqli_query($con,"select question from $checkanswerround where idnum=$_GET[previdnum]")); } else { $prevanswer = mysqli_fetch_array(mysqli_query($con,"select question from $checkanswerround where idnum=$currentanswer[idnum]"));; }
			$answerkey = mysqli_fetch_array(mysqli_query($con,"select answer from answerkey where question='$currentanswer[question]' and round='$currentanswer[round]'"));
			if ($currentanswer[question] != $prevanswer[question]) {
				print "<tr><td class=answersheet colspan=4><br><br>Next question.  The answer for #$currentanswer[question] is $answerkey[answer]<br>
				<a href=scorekeeper.php id=refresh>Begin</a></td></tr>";
				exit;
			}
			print "<tr><td class=answersheet colspan=4><br><br>
			<a href='scorekeeper.php?idnum=$currentanswer[idnum]&previdnum=$currentround&answer=2&currentround=$checkanswerround' id=correct><img src=/img/correct.png></a> 
			&nbsp;<a href='scorekeeper.php?idnum=$currentanswer[idnum]&previdnum=$currentround&answer=1&currentround=$checkanswerround' id=incorrect><img src=/img/incorrect.png></a>
			&nbsp;<a href='scorekeeper.php?flag=$currentround&currentround=$checkanswerround' id=flag><img src=/img/flag.png></a><br>
			<br>Round: $currentanswer[round] &nbsp; | &nbsp; Answer: $answerkey[answer]<br><br>
			<b>#$currentanswer[question]</b>:   &nbsp; $uncodedanswer</td></tr>";	
			$checkround	= $i;
			exit;
		}
	}
}

if ($_GET[dispute]) {
	$currentteam = mysqli_fetch_array(mysqli_query($con,"select correct from $_GET[currentround] where idnum='$_GET[dispute]'  and checked= 1"));
	if ($currentteam[correct] > 0) {
		$currentteam = mysqli_fetch_array(mysqli_query($con,"select teamid, round, question, wager from $_GET[currentround] where idnum='$_GET[dispute]' and correct > 0 and checked= 1"));
		mysqli_query($con,"update $_GET[currentround] set correct='0' where idnum='$_GET[dispute]'");
		if ($currentteam[round] == 'firsthalf' || $currentteam[round] == 'secondhalf' || $currentteam[round] == 'wager') { $points=$currentteam[wager]; }
		if ($currentteam[round] == 'picture' || $currentteam[round] == 'id') { $points=2; }
		if ($currentteam[round] == 'currentevents') { $points=1; }
		if ($currentteam[round] == 'wager') {
			$teampoints = mysqli_fetch_array(mysqli_query($con,"select total from points where idnum='$currentteam[teamid]'"));
			if ($points > $teampoints[total]) { $points=$teampoints[total]; }
		}
		if ($currentteam[wager] > 0) {
			for($i=1; $i<$round[question];$i++) {
				$duplicates = mysqli_fetch_array(mysqli_query($con,"select wager from $_GET[currentround] where round='$currentteam[round]' and teamid='$currentteam[teamid]' and question='$i'"));
				if ($duplicates[wager] == $currentteam[wager] && $currentteam[round] != 'wager') { $points=0; } //wager round you can bet 12 and 12 for example
			}
		}
		$cround = $currentteam[round];
		$currentteampoints = mysqli_fetch_array(mysqli_query($con,"select $cround, total from points where idnum='$currentteam[teamid]'"));
		if ($currentteam[round] != 'wager') {
			$newround=$currentteampoints[$cround]-$points;
			$newtotal=$currentteampoints[total]-$points;
		} else {
			$incorrect = mysqli_query($con,"select wager from $_GET[currentround] where teamid='$currentteam[teamid]' and correct='0'");
			while ($allincorrect = mysqli_fetch_array($incorrect)) {
				$points=$allincorrect[wager];
				$newround=$newround-$points;
			}
			$correct = mysqli_query($con,"select wager from $_GET[currentround] where teamid='$currentteam[teamid]' and correct='1'");
			while ($allcorrect = mysqli_fetch_array($correct)) {
				$points=$allcorrect[wager];
				$newpoints=$newpoints+$points;
			}
			$ototal=$currentteampoints[firsthalf]+$currentteampoints[picture]+$currentteampoints[secondhalf]+$currentteampoints[id]+$currentteampoints[currentevents];
			$newtotal=$ototal+$newround+$newpoints;
			$newround=$newround+$newpoints;
		}
		mysqli_query($con,"update points set $currentteam[round]='$newround' where idnum='$currentteam[teamid]'");
		mysqli_query($con,"update points set total='$newtotal' where idnum='$currentteam[teamid]'");
		$message="Removed $points from $currentteam[round] for team $currentteam[teamname]. Their new totals are $newround for round $currentteam[round] and $newtotal overall.";
	} else {
		$currentteam = mysqli_fetch_array(mysqli_query($con,"select teamid, round, question, wager  from $_GET[currentround] where idnum='$_GET[dispute]' and correct = 0 and checked= 1"));
		mysqli_query($con,"update $_GET[currentround] set correct='1' where idnum='$_GET[dispute]'");
		if ($currentteam[round] == 'firsthalf' || $currentteam[round] == 'secondhalf' || $currentteam[round] == 'wager') { $points=$currentteam[wager]; }
		if ($currentteam[round] == 'picture' || $currentteam[round] == 'id') { $points=2; }
		if ($currentteam[round] == 'currentevents') { $points=1; }
		if ($currentteam[round] == 'wager') {
			$teampoints = mysqli_fetch_array(mysqli_query($con,"select total from points where idnum='$currentteam[teamid]'"));
			if ($points > $teampoints[total]) { $points=$teampoints[total]; }
		}
		if ($currentteam[wager] > 0) {
			for($i=1; $i<$currentteam[question];$i++) {
				$duplicates = mysqli_fetch_array(mysqli_query($con,"select wager from $_GET[currentround] where round='$currentteam[round]' and teamid='$currentteam[teamid]' and question='$i'"));
				if ($duplicates[wager] == $currentteam[wager] && $currentteam[round] != 'wager') { $points=0; }
			}
		}
		$cround=$currentteam[round];
		$teampoints = mysqli_fetch_array(mysqli_query($con,"select $cround, total from points where idnum='$currentteam[teamid]'"));
		if ($currentteam[round] != 'wager') {
			$newpoints=$teampoints[$cround]+$points;
			$totalpoints=$teampoints[total]+$points;
		} else {
			$incorrect = mysqli_query($con,"select wager from $_GET[currentround] where teamid='$currentteam[teamid]' and correct='0'");
			while ($allincorrect = mysqli_fetch_array($incorrect)) {
				$points=$allincorrect[wager];
				$newround=$newround-$points;
			}
			$correct = mysqli_query($con,"select wager from $_GET[currentround] where teamid='$currentteam[teamid]' and correct='1'");
			while ($allcorrect = mysqli_fetch_array($correct)) {
				$points=$allcorrect[wager];
				$newpoints=$newpoints+$points;
			}
			$ototal=$teampoints[firsthalf]+$teampoints[picture]+$teampoints[secondhalf]+$teampoints[id]+$teampoints[currentevents];
			$totalpoints=$ototal+$newround+$newpoints;
			$newpoints=$newround+$newpoints;
		}
		mysqli_query($con,"update points set $currentteam[round]=$newpoints where idnum='$currentteam[teamid]'");
		mysqli_query($con,"update points set total=$totalpoints where idnum='$currentteam[teamid]'");
		$message="Added $points to $currentteam[round]. Their new totals are $newpoints for round $currentteam[round] and $totalpoints overall.";
	}
	print "<tr><td class=answersheet colspan=4><br>$message<br><br></td></tr>";
}

if ($_GET[update]) {
	$teampoints = mysqli_fetch_array(mysqli_query($con,"select * from points where idnum='$_GET[update]'"));
	if ($_GET[round] == 'all') {
		print "<form method=post action=scorekeeper.php><input type='hidden' value='$_GET[update]' name='teamid'>
		<tr><td class=rank colspan=4 id=scores_header><b>$teampoints[teamname]</b></td></tr>
		<tr><td class=rank_right id=scores1 colspan=2>First Half:</td><td class=rank_left  id=scores1 colspan=2><input type=text name='r1' value=\"$teampoints[firsthalf]\"></td></tr>
		<tr><td class=rank_right id=scores2 colspan=2>Picture Round:</td><td class=rank_left  id=scores2 colspan=2><input type=text name='r2' value=\"$teampoints[picture]\"></td></tr>
		<tr><td class=rank_right id=scores1 colspan=2>Second Half:</td><td class=rank_left  id=scores1 colspan=2><input type=text name='r3' value=\"$teampoints[secondhalf]\"></td></tr>
		<tr><td class=rank_right id=scores2 colspan=2>ID Round:</td><td class=rank_left  id=scores2 colspan=2><input type=text name='r4' value=\"$teampoints[id]\"></td></tr>
		<tr><td class=rank_right id=scores1 colspan=2>Social Bonus:</td><td class=rank_left  id=scores1 colspan=2><input type=text name='r5' value=\"$teampoints[currentevents]\"></td></tr>
		<tr><td class=rank_right id=scores2 colspan=2>Final Questions:</td><td class=rank_left  id=scores2 colspan=2><input type=text name='r6' value=\"$teampoints[wager]\"></td></tr>
		<tr><td class=rank colspan=4 id><input type=submit value='Submit' name='btn-submit' id='san-button'></form></td></tr>";
	} else {
		print "<tr><td colspan=4 id=scores_header><b>Team: $teampoints[teamname]</b><br><br></td></tr>
		<tr><td class=rank_header id=scores_header>Round</td>
		<td class=rank_header id=scores_header>Question</td>
		<td class=rank_header id=scores_header>Answer(Wager)</td>
		<td class=rank_header id=scores_header>Status</td></tr>";
	}
	if ($_GET[round] == 'total') {
		$fromwhere=" and checked='1'";
		$cround='total'; 
	} else {
		$fromwhere="and round ='".$_GET[round]."' and checked='1'";
		$cround=$_GET[round];
		if ($cround == 'firsthalf') { $currentround='answers_r1'; }
		if ($cround == 'picture') { $currentround='answers_r2'; }
		if ($cround == 'secondhalf') { $currentround='answers_r3'; }
		if ($cround == 'id') { $currentround='answers_r4'; }
		if ($cround == 'currentevents') { $currentround='answers_r5'; }
		if ($cround == 'wager') { $currentround='answers_r6'; }
	}
	$teampoints = mysqli_fetch_array(mysqli_query($con,"select idnum, teamname, $cround, total from points where idnum='$_GET[update]'"));
	$teams = mysqli_query($con,"select idnum, teamid, round, question, answer, correct, wager from $currentround where teamid='$_GET[update]' $fromwhere  order by round asc, question asc");
	while ($allteams = mysqli_fetch_array($teams)) {
		if ($alternate == 1) { $alternate=2; } else { $alternate=1; }
		if ($allteams[correct] == '0') {
			$option="<img src=/img/incorrect.png></a>";
		} else {
			$option='<img src=/img/correct.png>'; 
		}
		if ($allteams[wager] > 0) { $wager='('.$allteams[wager].')'; } else { $wager=''; }
		$uncodedanswer=str_replace($teampoints[idnum],'',$allteams[answer]);
		$q='q'.$allteams[question].'q';
		$q2=substr('$uncodedanswer', 2, 1);
		if (is_numeric($q2)) { $q=$teampoints[idnum].$q.$q2; } else { $q=$teampoints[idnum].$q; }
		$uncodedanswer=str_replace($q,'',$allteams[answer]);
		print "<tr><td class=rank id=scores$alternate>$allteams[round]</td>
		<td class=rank id=scores$alternate>$allteams[question]</td>
		<td class=rank id=scores$alternate>$uncodedanswer$wager</td>
		<td class=rank id=scores$alternate><a href=scorekeeper.php?dispute=$allteams[idnum]&update=$allteams[teamid]&round=$allteams[round]&currentround=$currentround>$option</a></td></tr>";
	}
	if ($alternate == 1) { $alternate=2; } else { $alternate=1; }
	if ($_GET[round] == 'all') { $showwhatpoints=$teampoints[total]; } else { $showwhatpoints=$teampoints[$cround]; }
	print "<td class=rank id=scores$alternate colspan=4>$_GET[round] points: $showwhatpoints</td></tr>";
}

print "<tr><td class=answersheet colspan=4><br><br><a href=scorekeeper.php id=refresh>Refresh</a> | <a href=answerkey.php>Answer Sheet</a> | <a href=hosts.php>Hosts</a>  | <a href=scorekeeper.php?delete=1>Delete All</a> </td></tr></table>
<br><br><table align=center width=98% class=answersheet>
<tr><td class=rank_header id=scores_header>Rank</td>
<td class=rank_header id=scores_header>Team Name</td>
<td class=rank_header id=scores_header>Total</td>
<td class=rank_header id=scores_header>First Half</td>
<td class=rank_header id=scores_header>Picture Round</td>
<td class=rank_header id=scores_header>Second Half</td>
<td class=rank_header id=scores_header>ID Round</td>
<td class=rank_header id=scores_header>Social Bonus</td>
<td class=rank_header id=scores_header>Final Questions</td></tr>";

$rank=0;
$alternate=1;
$teams = mysqli_query($con,"select * from points order by total desc");
while ($allteams=mysqli_fetch_array($teams)) {
	$rank=$rank+1;
	if ($alternate == 1) { $alternate=2; } else { $alternate=1; }
	print "<tr><td class=rank id=scores$alternate>$rank</td>
	<td class=rank id=scores$alternate><a href=scorekeeper.php?update=$allteams[idnum]&round=all  class=rank>$allteams[teamname]</a></td>
	<td class=rank id=scores$alternate><a href=scorekeeper.php?update=$allteams[idnum]&round=total  class=rank>$allteams[total]</a></td>
	<td class=rank id=scores$alternate><a href=scorekeeper.php?update=$allteams[idnum]&round=firsthalf  class=rank>$allteams[firsthalf]</a></td>
	<td class=rank id=scores$alternate><a href=scorekeeper.php?update=$allteams[idnum]&round=picture  class=rank>$allteams[picture]</a></td>
	<td class=rank id=scores$alternate><a href=scorekeeper.php?update=$allteams[idnum]&round=secondhalf  class=rank>$allteams[secondhalf]</a></td>
	<td class=rank id=scores$alternate><a href=scorekeeper.php?update=$allteams[idnum]&round=id  class=rank>$allteams[id]</a></td>
	<td class=rank id=scores$alternate><a href=scorekeeper.php?update=$allteams[idnum]&round=currentevents  class=rank>$allteams[currentevents]</a></td>
	<td class=rank id=scores$alternate><a href=scorekeeper.php?update=$allteams[idnum]&round=wager  class=rank>$allteams[wager]</a></td></tr>";
}
print "</td></tr></table>";
?>

<script>
if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}
</script>