<?php 
$this->layout('template', ['title' => 'Score Page']); 
if(isset($viewData)) {
	extract($viewData);
}

if(isset($alerts) && count($alerts)) {
	$this->insert('partials/alerts', ['value' => $alerts]);
}

// $data['instructions'];
if (isset($instructions)) {
	$this->insert('partials/scorepage/tablerow', ['value' => $instructions]);
}

print '<form method="post">';
if(isset($teamname)) {
	echo '<a href="#" class="wtf">'.$teamname.'</a>
	<div align="center"><table align="center" width="98%" class="answersheet">';
}

print "<td class=answersheet colspan=6>Team: ";
if (isset($data["leave"]) && $data["leave"] == 1) { //stops from showing old team name
	print "<input type=text name=teamname id='name' value='' onblur=\"validate()\" maxlength='25'>";
} else {
	if(isset($teamname)) {
		echo '<input type="text" name="teamname" id="name" value="'.$teamname.'" onblur="validate()" maxlength="25">';
	}
}

if ($round["scores"] != 1 && (isset($points["total"]) && $points["total"] == 0)) {
	print '<br><a href="/scorepage/?leave=1">(Leave Team)</a> &nbsp;';
	print 'Current Points: ' . $points["total"] . '<br>';
}


if ($prevround > 0) {
	$prevround = 'answers_r' . $prevround;
	$anyleft = $database->count($prevround, ["checked" => 0]);
	if ($anyleft > 0) {
		print "<b>Checking scores still in progress.</b><br><br>";
	}
}
print "</td></tr>";

if ($round["firsthalf"] == 1) {
	print "<input type='hidden' value='firsthalf' name='currentround'>";
	$total = 6;
	for ($i = 1; $i <= $total; $i++) {
		$a = 'a' . $i;
		$w = 'w' . $i;
		$answer = $database->select(
			$answerround,
			['answer', 'wager', 'checked'],
			['teamid' => $points["idnum"], 'round' => 'firsthalf', 'question' => $i]
		);
		$uncodedanswer = str_replace($points["idnum"], '', $answer["answer"]);
		$q = 'q' . $i . 'q';
		$q2 = substr('$uncodedanswer', 2, 1);
		if (is_numeric($q2)) {
			$q = $points["idnum"] . $q . $q2;
		} else {
			$q = $points["idnum"] . $q;
		}
		$uncodedanswer = str_replace($q, '', $answer["answer"]);
		if ($answer["checked"] == 1) {
			$uncodedanswer = 'Answer Checked: ' . $uncodedanswer;
			$disabled[$i] = 'disabled';
		}
		$wager = $answer["wager"];
		$selected[$wager] = 'selected';

		print '<tr><td class="borders"></td><td class=answersheet_right>#$i Answer: </td><td class=answersheet><input type=text name=a$i value="' . $uncodedanswer . '" ' . $disabled[$i] . '></td>
		<td class=answersheet_left colspan="2">Wager: 
		<select name="w' . $i . '">
			<option ' . $selected[1] . ' value="1" ' . $disabled[$i] . '>1</option>
			<option ' . $selected[2] . ' value="2" ' . $disabled[$i] . '>2</option>
			<option ' . $selected[3] . ' value="3" ' . $disabled[$i] . '>3</option>
			<option ' . $selected[4] . ' value="4" ' . $disabled[$i] . '>4</option>
			<option ' . $selected[5] . ' value="5" ' . $disabled[$i] . '>5</option>
			<option ' . $selected[6] . ' value="6" ' . $disabled[$i] . '>6</option>
		</select></td><td class="borders"></td></tr>';
		$selected[$wager] = '';
	}
}
if ($round["picture"] == 1) {
	print "<input type='hidden' value='picture' name='currentround'>";
	$total = 15;
	for ($i = 1; $i <= $total; $i++) {
		$a = 'a' . $i;

		$answer = $database->select(
			$answerround,
			['answer', 'wager', 'checked'],
			['teamid' => $points["idnum"], 'round' => 'picture', 'question' => $i]
		);

		$uncodedanswer = str_replace($points["idnum"], '', $answer["answer"]);
		$q = 'q' . $i . 'q';
		$q2 = substr('$uncodedanswer', 2, 1);
		if (is_numeric($q2)) {
			$q = $points["idnum"] . $q . $q2;
		} else {
			$q = $points["idnum"] . $q;
		}
		$uncodedanswer = str_replace($q, '', $answer["answer"]);
		if ($answer["checked"] == 1) {
			$uncodedanswer = 'Answer Checked: ' . $uncodedanswer;
			$disabled[$i] = 'disabled';
		}
		print '<tr><td class="borders"></td><td class="answersheet_right" colspan="2"_right>#' . $i . ' Answer: </td><td class="answersheet"><input type="text" name=a' . $i . ' value="' . $uncodedanswer . '" ' . $disabled[$i] . '></td><td class="borders"></td></tr>';
	}
}
if ($round["secondhalf"] == 1) {
	print '<input type="hidden" value="secondhalf" name="currentround">';
	$total = 6;
	for ($i = 1; $i <= $total; $i++) {
		$a = 'a' . $i;
		$w = 'w' . $i;

		$answer = $database->select(
			$answerround,
			['answer', 'wager', 'checked'],
			['teamid' => $points["idnum"], 'round' => 'secondhalf', 'question' => $i]
		);
		$uncodedanswer = str_replace($points["idnum"], '', $answer["answer"]);
		$q = 'q' . $i . 'q';
		$q2 = substr('$uncodedanswer', 2, 1);
		if (is_numeric($q2)) {
			$q = $points["idnum"] . $q . $q2;
		} else {
			$q = $points["idnum"] . $q;
		}
		$uncodedanswer = str_replace($q, '', $answer["answer"]);
		if ($answer["checked"] == 1) {
			$uncodedanswer = 'Answer Checked: ' . $uncodedanswer;
			$disabled[$i] = 'disabled';
		}
		$wager = $answer["wager"];
		$selected[$wager] = 'selected';
		print '<tr><td class="borders"></td><td class=answersheet_right>#$i Answer: </td><td class=answersheet><input type=text name=a$i value=\"$uncodedanswer\" $disabled[$i]></td>
		<td class=answersheet_left colspan="2">Wager: 
		<select name="w' . $i . '">
			<option $selected[2] value="2" ' . $disabled[$i] . '>2</option>
			<option $selected[4] value="4" ' . $disabled[$i] . '>4</option>
			<option $selected[6] value="6" ' . $disabled[$i] . '>6</option>
			<option $selected[8] value="8" ' . $disabled[$i] . '>8</option>
			<option $selected[10] value="10" ' . $disabled[$i] . '>10</option>
			<option $selected[12] value="12" ' . $disabled[$i] . '>12</option>
		</select></td><td class="borders"></td></tr>';
		$selected[$wager] = '';
	}
}
if ($round["id"] == 1) {
	print "<input type='hidden' value='id' name='currentround'>";
	$total = 15;
	for ($i = 1; $i <= $total; $i++) {
		$a = 'a' . $i;
		$answer = $database->select(
			$answerround,
			['answer', 'wager', 'checked'],
			['teamid' => $points["idnum"], 'round' => 'id', 'question' => $i]
		);


		$uncodedanswer = str_replace($points["idnum"], '', $answer["answer"]);
		$q = 'q' . $i . 'q';
		$q2 = substr('$uncodedanswer', 2, 1);
		if (is_numeric($q2)) {
			$q = $points["idnum"] . $q . $q2;
		} else {
			$q = $points["idnum"] . $q;
		}
		$uncodedanswer = str_replace($q, '', $answer["answer"]);
		if ($answer["checked"] == 1) {
			$uncodedanswer = 'Answer Checked: ' . $uncodedanswer;
			$disabled[$i] = 'disabled';
		}
		print '<tr><td class="borders"></td><td class="answersheet_right" colspan="2">#' . $i . ' Answer: </td><td class="answersheet"><input type="text" name="a' . $i . '" value="' . $uncodedanswer . '" ' . $disabled[$i] . '></td><td class="borders"></td></tr>';
	}
}
if ($round["currentevents"] == 1) {
	print "<input type='hidden' value='currentevents' name='currentround'>";
	$total = 2;
	for ($i = 1; $i <= $total; $i++) {
		$a = 'a' . $i;
		$w = 'w' . $i;
		if ($i == 1) {
			$social = 'Facebook';
		}
		if ($i == 2) {
			$social = 'Instagram';
		}

		$answer = $database->select(
			$answerround,
			['answer', 'wager', 'checked'],
			['teamid' => $points["idnum"], 'round' => 'currentevents', 'question' => $i]
		);

		$uncodedanswer = str_replace($points["idnum"], '', $answer["answer"]);
		$q = 'q' . $i . 'q';
		$q2 = substr('$uncodedanswer', 2, 1);
		if (is_numeric($q2)) {
			$q = $points["idnum"] . $q . $q2;
		} else {
			$q = $points["idnum"] . $q;
		}
		$uncodedanswer = str_replace($q, '', $answer["answer"]);
		if ($answer["checked"] == 1) {
			$uncodedanswer = 'Answer Checked: ' . $uncodedanswer;
			$disabled[$i] = 'disabled';
		}
		print '<tr><td class="borders"></td><td class="answersheet_right" colspan="2">' . $social . ' Answer: </td><td class="answersheet"><input type="text" name=a' . $i . ' value="' . $uncodedanswer . '" ' . $disabled[$i] . '></td><td class="borders"></td></tr>';
	}
}
if ($round["wager"] == 1) {
	print "<input type='hidden' value='wager' name='currentround'>";
	$total = 2;
	for ($i = 1; $i <= $total; $i++) {
		$a = 'a' . $i;
		$w = 'w' . $i;
		$answer = $database->select(
			$answerround,
			['answer', 'wager', 'checked'],
			['teamid' => $points["idnum"], 'round' => 'wager', 'question' => $i]
		);

		$uncodedanswer = str_replace($points["idnum"], '', $answer["answer"]);
		$q = 'q' . $i . 'q';
		$q2 = substr('$uncodedanswer', 2, 1);
		if (is_numeric($q2)) {
			$q = $points["idnum"] . $q . $q2;
		} else {
			$q = $points["idnum"] . $q;
		}
		$uncodedanswer = str_replace($q, '', $answer["answer"]);
		if ($answer["checked"] == 1) {
			$uncodedanswer = 'Answer Checked: ' . $uncodedanswer;
			$disabled[$i] = 'disabled';
		}
		$wager = $answer["wager"];
		$selected[$wager] = 'selected';
		print '<tr><td class="borders"></td><td class=answersheet_right>#$i Answer: </td><td class=answersheet><input type=text name=a$i value=\"$uncodedanswer\" $disabled[$i]></td>
		<td class=answersheet_left colspan="2">Wager: 
		<select name="w' . $i . '">
			<option ' . $selected[1] . ' value="0">0</option>
			<option ' . $selected[1] . ' value="1" ' . $disabled[$i] . '>1</option>
			<option ' . $selected[2] . ' value="2" ' . $disabled[$i] . '>2</option>
			<option ' . $selected[3] . ' value="3" ' . $disabled[$i] . '>3</option>
			<option ' . $selected[4] . ' value="4" ' . $disabled[$i] . '>4</option>
			<option ' . $selected[5] . ' value="5" ' . $disabled[$i] . '>5</option>
			<option ' . $selected[6] . ' value="6" ' . $disabled[$i] . '>6</option>
			<option ' . $selected[7] . ' value="7" ' . $disabled[$i] . '>7</option>
			<option ' . $selected[8] . ' value="8" ' . $disabled[$i] . '>8</option>
			<option ' . $selected[9] . ' value="9" ' . $disabled[$i] . '>9</option>
			<option ' . $selected[10] . '  value="10" ' . $disabled[$i] . '>10</option>
			<option ' . $selected[11] . '  value="11" ' . $disabled[$i] . '>11</option>
			<option ' . $selected[12] . '  value="12" ' . $disabled[$i] . '>12</option>
		</select></td><td class="borders"></td></tr>';
	}
}
if ($round["scores"] == 1) {
	$points = $database->select(
		'points',
		'*',
		[
			'idnum' => $points["idnum"]
		]
	);

	if ($_GET["round"]) {
		print '<tr><td class="borders"></td><td class="scores_round" id="scores_header">Round</td><td class=scores_other id="scores_header">Question</td><td class="scores_answer" id="scores_header">Answer(Wager)</td><td class=scores_other id="scores_header">Status</td><td class="borders"></td></tr>';

		$team = $database->select(
			$_GET["round"], // table
			[
				'AND' =>
				['idnum', 'round', 'question', 'answer', 'wager', 'checked', 'correct'], // where
				["ORDER" => ["question" => "ASC"]], // order
			], // select
			['teamid' => $points["idnum"]]
		);

		foreach ($team as $myteam) {
			if ($alternate == 1) {
				$alternate = 2;
			} else {
				$alternate = 1;
			}
			if ($myteam["round"] == 'firsthalf') {
				$cround = 'First Half';
			}
			if ($myteam["round"] == 'picture') {
				$cround = 'Picture Round';
			}
			if ($myteam["round"] == 'secondhalf') {
				$cround = 'Second Half';
			}
			if ($myteam["round"] == 'id') {
				$cround = 'ID Round';
			}
			if ($myteam["round"] == 'currentevents') {
				$cround = 'Social Bonus';
			}
			if ($myteam["round"] == 'wager') {
				$cround = 'Final Questions';
			}
			if ($myteam["correct"] == '0' && $myteam["checked"] == '1') {
				$option = "<a href=/scorepage/?dispute=" . $myteam["idnum"] . "&dround=" . $_GET["round"] . "><img src=/img/incorrect.png></a>";
			} elseif ($myteam["correct"] > 0) {
				$option = '<img src=/img/correct.png>';
			} else {
				$option = 'Checking';
			}
			if ($myteam["wager"] > 0) {
				$wager = '(' . $myteam["wager"] . ')';
			} else {
				$wager = '';
			}
			$uncodedanswer = str_replace($points["idnum"], '', $myteam["answer"]);
			$q = 'q' . $myteam["question"] . 'q';
			$q2 = substr('$uncodedanswer', 2, 1);
			if (is_numeric($q2)) {
				$q = $points["idnum"] . $q . $q2;
			} else {
				$q = $points["idnum"] . $q;
			}
			$uncodedanswer = str_replace($q, '', $myteam["answer"]);
			print '<tr><td class="borders"></td><td class="scores_round" id="scores' . $alternate . '">' . $cround . '</td><td class="scores_other" id="scores' . $alternate . '">' . $myteam["question"] . '</td><td class="scores_answer" id="scores' . $alternate . '">' . $uncodedanswer . $wager . '</td><td class="scores_other" id="scores' . $alternate . '">' . $option . '</td><td class="borders"></td></tr>';
		}
	} else {
		print '<tr><td class="borders"></td><td class="rank_right" id="scores1" colspan="2">First Half:</td><td class="rank_left" id="scores1" colspan="2"> &nbsp; <a href="/scorepage/?round=answers_r1">' . $points["firsthalf"] . '</a></td><td class="borders"></td></tr>
		<tr><td class="borders"></td><td class="rank_right" id=scores2 colspan="2">Picture Round:</td><td class=rank_left  id=scores2 colspan="2"> &nbsp; <a href=/scorepage/?round=answers_r2>' . $points["picture"] . '</a></td><td class="borders"></td></tr>
		<tr><td class="borders"></td><td class="rank_right" id=scores1 colspan="2">Second Half:</td><td class=rank_left  id=scores1 colspan="2"> &nbsp; <a href=/scorepage/?round=answers_r3>' . $points["secondhalf"] . '</a></td><td class="borders"></td></tr>
		<tr><td class="borders"></td><td class="rank_right" id=scores2 colspan="2">ID Round:</td><td class=rank_left  id=scores2 colspan="2"> &nbsp; <a href=/scorepage/?round=answers_r4>' . $points["id"] . '</a></td><td class="borders"></td></tr>
		<tr><td class="borders"></td><td class="rank_right" id=scores1 colspan="2">Social Bonus:</td><td class=rank_left  id=scores1 colspan="2"> &nbsp; <a href=/scorepage/?round=answers_r5>' . $points["currentevents"] . '</a></td><td class="borders"></td></tr>
		<tr><td class="borders"></td><td class="rank_right" id=scores2 colspan="2">Final Questions:</td><td class=rank_left  id=scores2 colspan="2"> &nbsp; <a href=/scorepage/?round=answers_r6>' . $points["wager"] . '</a></td><td class="borders"></td></tr>';
	}
	print '<tr><td class="borders"></td><td colspan="2"><br><br></td><td colspan="2"><br><br></td><td class="borders"></td></tr>
	<tr><td class="borders"></td><td class="rank_header" id="scores_header">Rank</td><td class="rank_header" id="scores_header" colspan="2">Team Name</td><td class="rank_header" id="scores_header">Total</td><td class="borders"></td>';
	$rank = 0;

	
	foreach ($teams as $team) {
		$rank = $rank + 1;
		if ($alternate == 1) {
			$alternate = 2;
		} else {
			$alternate = 1;
		}
		print '<tr><td class="borders"></td><td class="rank" id="scores' . $alternate . '>' . $rank . '</td><td class="rank" id="scores' . $alternate . '" colspan="2">' . $team["teamname"] . '</td><td class="rank" id="scores' . $alternate . '">' . $team["total"] . '</td><td class="borders"></td></tr>';
	}
}

if ($round["scores"] != 1) {
	print "<tr><td class=answersheet colspan=6><br><b>Submitted answers can be changed at any time until the host moves on to the next round.</b></td></tr>
	<tr><td class=answersheet colspan=6><br><b>REMEMBER:</b> Only one set of answers per team will be used.  If you're playing with others on your team remotely, you can view what they've entered by refreshing the page after they submit their answers.  Refreshing the page will delete any answers you have not yet submitted though so be careful!</tr>";
}
if (isset($teamname)) {
	print '<tr><td class="borders"></td><td colspan=4><br><a href=/scorepage/ id=refresh  class=refresh>Refresh</a><input type=submit value="Submit" name="btn-submit" id="san-button" disabled="disabled"><td class="borders"></td>';
} else {
	print '<tr><td class="borders"></td><td colspan=4><p id="fadeout"><br><br>' . $tellpeople . '</p><a href="/scorepage/" id="refresh" class="refresh">Refresh</a> <input type="submit" value="Submit" name="btn-submit" id="san-button"><br></td><td class="borders"></td>';
}
print "</form></tr></table>";


if ($hostsset > 0) {
	print '<table><tr><td class="borders"></td><td colspan="4"><p id="fadeout">Your hosts for tonight are: ';

	foreach ($hostsset as $tonightsHost) {
		echo $tonightsHost['host'] . ',';
	}
	echo count($hostsset) . ' Whisper them on Twitch if you have any issues come up.</td><td class="borders"></td></tr></table>';
}
?>

<script src="http://code.jquery.com/jquery-1.10.2.js"></script>
<script>
	function validate() {

		var valid = true;
		valid = checkEmpty($("#name"));

		$("#san-button").attr("disabled", true);
		if (valid) {
			$("#san-button").attr("disabled", false);
		}
	}

	function checkEmpty(obj) {
		var name = $(obj).attr("name");
		$("." + name + "-validation").html("");
		$(obj).css("border", "");
		if ($(obj).val() == "") {
			$(obj).css("border", "#FF0000 1px solid");
			$("." + name + "-validation").html("Required");
			return false;
		}

		return true;
	}

	function checkEmail(obj) {
		var result = true;

		var name = $(obj).attr("name");
		$("." + name + "-validation").html("");
		$(obj).css("border", "");

		result = checkEmpty(obj);

		if (!result) {
			$(obj).css("border", "#FF0000 1px solid");
			$("." + name + "-validation").html("Required");
			return false;
		}

		return result;
	}

	window.onload = function() {
		window.setTimeout(fadeout, 5000); //5 seconds
	}

	function fadeout() {
		document.getElementById('fadeout').style.opacity = '0';
	}

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>
