<?php $this->layout('template', ['title' => 'Score Page']); 
//Leave Team
if (isset($_GET["leave"])) {
	if ($_POST["teamname"] == null) {
		setcookie("teamname", null, time() - 3600, '/');
	}
}

?>
<form method=post action=scorepage.php>
<?php  

echo isset($teamname) && strlen($teamname) > 0 ? '<a href=# class=wtf>'.$teamname.' $</a>
<div align=center><table align=center width=98% class=answersheet>' : ''; 

if (! isset($points["idnum"]))  {
	$points = $database->insert("points", [
		"teamname" => $teamname
	]);
}

//Submit answers
if (isset($_POST["currentround"])) {
	$tellpeople='Answers Submitted!';
	
	$sorry='<tr><td class=borders></td><td class=answersheet colspan=4><br><b>Sorry! The next round has begun, answers from the previous round can no longer be submitted.</b><br></td><td class=borders></td></tr>';
	if ($_POST["currentround"] == 'firsthalf' && $round["firsthalf"] != 1) { print "$sorry";	
	} elseif ($_POST["currentround"] == 'picture' && $round["picture"] != 1) { print "$sorry";
	} elseif ($_POST["currentround"] == 'secondhalf' && $round["secondhalf"] != 1) { print "$sorry";
	} elseif ($_POST["currentround"] == 'id' && $round["id"] != 1) { print "$sorry";
	} elseif ($_POST["currentround"] == 'currentevents' && $round["currentevents"] != 1) { print "$sorry";
	} else {
		$_POST["currentround"]=mysqli_real_escape_string($con, $_POST["currentround"]);
	
		for($i=1; $i<=15;$i++) {
			$a='a'.$i;
			$w='w'.$i;
			$q='q'.$i.'q';
			if ($_POST[$a]) {
				$entry = array(" THE ", "THE ", " IN ", " OF ", " TOO ", " TO ", " IS ", " AND ", " AN ", " A ", " MY ");
				$nohtml = array("<",">",'"');
				$unique=str_replace ($nohtml, '', $_POST[$a]);
				$unique=$points["idnum"].$q.$unique; //used to make answer a unique scorepage
				$uploadanswer=mysqli_real_escape_string($con, $unique);
				$special=preg_replace ("/[^a-zA-Z0-9\s]/", '', $_POST[$a]);
				$special2=strtoupper($special);
				$special2 = str_replace($entry, " ", $special2);
				$special2 = str_replace(" ", "", $special2);
				$a_special2 = str_split($special2);
				$answerkey = $database->select("answerkey", 
					['answer', 'shortcut'],
					['question' => $i, 'round' => $_POST["currentround"]]
				);
				$answerkeyupper=strtoupper($answerkey["answer"]);
				$answerkeyupper=str_replace($entry, " ", $answerkeyupper);
				$answerkeyupper = str_replace(" ", "", $answerkeyupper);
				$a_answerkeyupper = str_split($answerkeyupper);
				sort($a_special2);
				sort($a_answerkeyupper);
				$special2=implode('',$a_special2);
				$answerkeyupper=implode('',$a_answerkeyupper);
				if ($answerkey["shortcut"] == 0) {
					if ($special2 == $answerkeyupper && !empty($answerkeyupper)) { $checked=2; } else { $checked=0; }
				} else {
					if(strpos($special2, $answerkeyupper) !== false && !empty($answerkeyupper)) { $checked=2; } else { $checked=0; }
				}

				$database->delete($answerround, [
					'round' => $_POST["currentround"],
					'question'=> $i,
					'teamid' => $points['idnum'],
					'answer' => $unique
				]);

				$database->insert( $answerround , [
					'teamid' => $points["idnum"],
					'round' => $_POST["currentround"],
					'question' => $i,
					'answer' => $uploadanswer,
					'wager' => $_POST[$w],
					'checked' => $checked

				]);
			}
		}
	}
}



//Set Round and team info
if ($round["firsthalf"] == 1) {
	$instructions= "Wager 1, 2, 3, 4, 5, or 6 points on each answer.<br>
	You can wager each value only ONCE<br><br>";
} elseif ($round["secondhalf"] == 1) {
	$instructions= "Wager 2, 4, 6, 8, 10, or 12 points on each answer.<br>
	You can wager each value only ONCE<br><br>";
} elseif ($round["wager"] == 1) {
	$instructions= "Wager between 0-12 points.  <b>THIS ROUND ONLY</b>: If you wager points and get the answer wrong, you lose those points.<br><br>";
} elseif ($round["scores"] == 1) {
	$instructions= "<br><br>You can check the overall scores below.  Click on the points for each round to see your answers.<br><br>
	Did we mark one of your correct answers wrong? Press <img src=/img/incorrect.png> next to the answer and we'll recheck it.<br>";
}


if (isset($_GET["dispute"])) {
	$checkifdisputed = $database->count($_GET["dround"], ['idnum' => $_GET["dispute"], 'checked' => 1]);
	if ($checkifdisputed == 1) {
		$newdispute=$points["dispute"]+1;

		$database->update('points', 
			['dispute' => $newdispute], 
			['idnum' => $points["idnum"]]);

		if ($points["dispute"] >= 3 && $round["scores"] == 1) {
			$message="You've disputed too many answers tonight.";
		} elseif($points["dispute"] < 3 && $round["scores"] == 1) {
			$message="You're answer has been sent to the host to recheck!";

			$database->update($_GET["dround"], 
			['checked' => 0], 
			['idnum' => $_GET["dispute"], 'correct' => 0]);
		}
		print "<tr><td class=borders></td><td class=answersheet colspan=4><br><br>$message</td><td class=borders></td></tr>";
	}
}

//Answersheet main section
if ((isset($points) && ! is_array($points) && $points->rowCount() > 0) && isset($teamname) && $teamname !== null) {  
	$points = $database->get('points', ['idnum', 'teamname', 'total', 'dispute'], ['teamname' => $teamname]);
}

if(isset($instructions)) {
	print "<tr><td class=borders></td><td class=answersheet colspan=4>$instructions<br></td><td class=borders></td></tr>";
}

print "<td class=answersheet colspan=6>Team: ";
if (isset($_GET["leave"]) && $_GET["leave"] ==1) { //stops from showing old team name
	print "<input type=text name=teamname id='name' value='' onblur=\"validate()\" maxlength='25'>";	
} else {
	print "<input type=text name=teamname id='name' value=\"$teamname\" onblur=\"validate()\" maxlength='25'>";
}

if (isset($round["scores"], $points['total']) && $round["scores"] != 1 && $points['total'] == 0) {
	print "<br><a href=scorepage.php?leave=1>(Leave Team)</a> &nbsp;";
	sprintf('Current Points: %s <br />', $points['total']);
}

$prevround=substr($answerround, -1);
$prevround= (int)$prevround - 1;
if ($prevround > 0) {
	$prevround='answers_r'.$prevround;
	$anyleft = $database->count($prevround, ['checked' => 0]);
	if ($anyleft > 0) { 
		print "<b>Checking scores still in progress.</b><br><br>"; }
}
print "</td></tr>";
if(isset($points["idnum"])) {
	
	if ($round["firsthalf"] == 1) {
		print "<input type='hidden' value='firsthalf' name='currentround'>";
		$total=6;
		for($i=1; $i<=$total;$i++) {
			$a='a'.$i;
			$w='w'.$i;
			$disabled = [$i];
			
			$answer = $database->get('firsthalf',
				['answer', 'wager', 'checked'],
				['teamid' => $points['idnum'], 'round' => 'firsthalf', 'question' => $i]
			);
			$uncodedanswer=str_replace($points["idnum"],'',$answer["answer"]);
			$q='q'.$i.'q';
			$q2=substr('$uncodedanswer', 2, 1);
			if (is_numeric($q2)) { $q=$points["idnum"].$q.$q2; } else { $q=$points["idnum"].$q; }
			$uncodedanswer=str_replace($q,'',$answer["answer"]);
			if ($answer["checked"] == 1) { 
				$uncodedanswer='Answer Checked: '.$uncodedanswer; 
				
			}
			$disabled[$i] = 'disabled'; 
			$wager=$answer['wager'];
			$selected[$wager] ='selected';
			?>
			
			<tr>
				<td class=borders></td><td class=answersheet_right>#<?php echo $i; ?> Answer: </td>
				<td class=answersheet><input type=text name=a<?php echo $i; ?> value="<?php echo $uncodedanswer; ?>"></td>
			<td class=answersheet_left colspan=2>Wager: 
			<select name='w<?php echo $i; ?>'>
				<option <?php echo isset($selected[1]) ? $selected[1] : ''; ?> value='1' <?php echo isset($disabled[$i]) ? $disabled[$i] : ''; ?>>1</option>
				<option <?php echo isset($selected[2]) ? $selected[2] : ''; ?> value='2' <?php echo isset($disabled[$i]) ? $disabled[$i] : ''; ?>>2</option>
				<option <?php echo isset($selected[3]) ? $selected[3] : ''; ?> value='3' <?php echo isset($disabled[$i]) ? $disabled[$i] : ''; ?>>3</option>
				<option <?php echo isset($selected[4]) ? $selected[4] : ''; ?> value='4' <?php echo isset($disabled[$i]) ? $disabled[$i] : ''; ?>>4</option>
				<option <?php echo isset($selected[5]) ? $selected[5] : ''; ?> value='5' <?php echo isset($disabled[$i]) ? $disabled[$i] : ''; ?>>5</option>
				<option <?php echo isset($selected[6]) ? $selected[6] : ''; ?> value='6' <?php echo isset($disabled[$i]) ? $disabled[$i] : ''; ?>>6</option>
			</select></td><td class=borders></td></tr>
			<?php
			$selected[$wager] = '';
		}
	}
	if ($round["picture"] == 1) {
		print "<input type='hidden' value='picture' name='currentround'>";
		$total=15;
		for($i=1; $i<=$total;$i++) {
			$a='a'.$i;

			$answer = $database->get($answerround, //Table
				['answer', 'wager', 'checked'], // Select cols
				[ // Where
					'teamid' => $points['idnum'], 
					'round' => 'picture', 
					'question' => $i
				]
			);

			$uncodedanswer=str_replace($points["idnum"],'',$answer["answer"]);
			$q='q'.$i.'q';
			$q2=substr('$uncodedanswer', 2, 1);
			if (is_numeric($q2)) { $q=$points["idnum"].$q.$q2; } else { $q=$points["idnum"].$q; }
			$uncodedanswer=str_replace($q,'',$answer["answer"]);
			if ($answer["checked"] == 1) { $uncodedanswer='Answer Checked: '.$uncodedanswer; $disabled[$i]='disabled'; }
			print "<tr><td class=borders></td><td class=answersheet_right colspan=2_right>#$i Answer: </td><td class=answersheet><input type=text name=a$i value=\"$uncodedanswer\" $disabled[$i]></td><td class=borders></td></tr>";
		}
	}
	if ($round["secondhalf"] == 1) {
		print "<input type='hidden' value='secondhalf' name='currentround'>";
		$total=6;
		for($i=1; $i<=$total;$i++) {
			$a='a'.$i;
			$w='w'.$i;

			$answer = $database->get($answerround, //Table
				['answer', 'wager', 'checked'], // Select cols
				[ // Where
					'teamid' => $points['idnum'], 
					'round' => 'secondhalf', 
					'question' => $i
				]
			);

			$uncodedanswer=str_replace($points["idnum"],'',$answer["answer"]);
			$q='q'.$i.'q';
			$q2=substr('$uncodedanswer', 2, 1);
			if (is_numeric($q2)) { $q=$points["idnum"].$q.$q2; } else { $q=$points["idnum"].$q; }
			$uncodedanswer=str_replace($q,'',$answer["answer"]);
			if ($answer["checked"] == 1) { $uncodedanswer='Answer Checked: '.$uncodedanswer; $disabled[$i]='disabled'; }
			$wager=$answer['wager'];
			$selected[$wager] ='selected';
			print "<tr><td class=borders></td><td class=answersheet_right>#$i Answer: </td><td class=answersheet><input type=text name=a$i value=\"$uncodedanswer\" $disabled[$i]></td>
			<td class=answersheet_left colspan=2>Wager: 
			<select name='w$i'>
				<option $selected[2] value='2' $disabled[$i]>2</option>
				<option $selected[4] value='4' $disabled[$i]>4</option>
				<option $selected[6] value='6' $disabled[$i]>6</option>
				<option $selected[8] value='8' $disabled[$i]>8</option>
				<option $selected[10] value='10' $disabled[$i]>10</option>
				<option $selected[12] value='12' $disabled[$i]>12</option>
			</select></td><td class=borders></td></tr>";
			$selected[$wager] = '';	}
	}
	if ($round["id"] == 1) {
		print "<input type='hidden' value='id' name='currentround'>";
		$total=15;
		for($i=1; $i<=$total;$i++) {
			$a='a'.$i;
			$answer = $database->get($answerround, //Table
				['answer', 'wager', 'checked'], // Select cols
				[ // Where
					'teamid' => $points['idnum'], 
					'round' => 'id', 
					'question' => $i
				]
			);
		
			$uncodedanswer=str_replace($points["idnum"],'',$answer["answer"]);
			$q='q'.$i.'q';
			$q2=substr('$uncodedanswer', 2, 1);
			if (is_numeric($q2)) { $q=$points["idnum"].$q.$q2; } else { $q=$points["idnum"].$q; }
			$uncodedanswer=str_replace($q,'',$answer["answer"]);
			if ($answer["checked"] == 1) { $uncodedanswer='Answer Checked: '.$uncodedanswer; $disabled[$i]='disabled'; }
			print "<tr><td class=borders></td><td class=answersheet_right colspan=2>#$i Answer: </td><td class=answersheet><input type=text name=a$i value=\"$uncodedanswer\" $disabled[$i]></td><td class=borders></td></tr>";
		}
	}
	if ($round["currentevents"] == 1) {
		print "<input type='hidden' value='currentevents' name='currentround'>";
		$total=2;
		for($i=1; $i<=$total;$i++) {
			$a='a'.$i;
			$w='w'.$i;
			if ($i==1) {$social='Facebook'; }
			if ($i==2) {$social='Instagram'; }

			$answer = $database->get($answerround, //Table
				['answer', 'wager', 'checked'], // Select cols
				[ // Where
					'teamid' => $points['idnum'], 
					'round' => 'currentevents', 
					'question' => $i
				]
			);

			$uncodedanswer=str_replace($points["idnum"],'',$answer["answer"]);
			$q='q'.$i.'q';
			$q2=substr('$uncodedanswer', 2, 1);
			if (is_numeric($q2)) { $q=$points["idnum"].$q.$q2; } else { $q=$points["idnum"].$q; }
			$uncodedanswer=str_replace($q,'',$answer["answer"]);
			if ($answer["checked"] == 1) { $uncodedanswer='Answer Checked: '.$uncodedanswer; $disabled[$i]='disabled'; }
			print "<tr><td class=borders></td><td class=answersheet_right colspan=2>$social Answer: </td><td class=answersheet><input type=text name=a$i value=\"$uncodedanswer\" $disabled[$i]></td><td class=borders></td></tr>";
		}
	}
	if ($round["wager"] == 1) {
		print "<input type='hidden' value='wager' name='currentround'>";
		$total=2;
		for($i=1; $i<=$total;$i++) {
			$a='a'.$i;
			$w='w'.$i;

			$answer = $database->get($answerround, //Table
				['answer', 'wager', 'checked'], // Select cols
				[ // Where
					'teamid' => $points['idnum'], 
					'round' => 'wager', 
					'question' => $i
				]
			);

			$uncodedanswer=str_replace($points["idnum"],'',$answer["answer"]);
			$q='q'.$i.'q';
			$q2=substr('$uncodedanswer', 2, 1);
			if (is_numeric($q2)) { $q=$points["idnum"].$q.$q2; } else { $q=$points["idnum"].$q; }
			$uncodedanswer=str_replace($q,'',$answer["answer"]);
			if ($answer["checked"] == 1) { $uncodedanswer='Answer Checked: '.$uncodedanswer; $disabled[$i]='disabled'; }
			$wager=$answer['wager'];
			$selected[$wager] ='selected';
			print "<tr><td class=borders></td><td class=answersheet_right>#$i Answer: </td><td class=answersheet><input type=text name=a$i value=\"$uncodedanswer\" $disabled[$i]></td>
			<td class=answersheet_left colspan=2>Wager: 
			<select name='w$i'>
				<option $selected[1] value='0'>0</option>
				<option $selected[1] value='1' $disabled[$i]>1</option>
				<option $selected[2] value='2' $disabled[$i]>2</option>
				<option $selected[3] value='3' $disabled[$i]>3</option>
				<option $selected[4] value='4' $disabled[$i]>4</option>
				<option $selected[5] value='5' $disabled[$i]>5</option>
				<option $selected[6] value='6' $disabled[$i]>6</option>
				<option $selected[7] value='7' $disabled[$i]>7</option>
				<option $selected[8] value='8' $disabled[$i]>8</option>
				<option $selected[9] value='9' $disabled[$i]>9</option>
				<option $selected[10] value='10' $disabled[$i]>10</option>
				<option $selected[11] value='11' $disabled[$i]>11</option>
				<option $selected[12] value='12' $disabled[$i]>12</option>
			</select></td><td class=borders></td></tr>";
			$selected[$wager] = '';	}
	}
	if ($round["scores"] == 1) {
		$points = $database->select('points', '*', ['idnum' => $points['idnum']]);
		if ($_GET["round"]) {
			print "<tr><td class=borders></td><td class=scores_round id=scores_header>Round</td><td class=scores_other id=scores_header>Question</td><td class=scores_answer id=scores_header>Answer(Wager)</td><td class=scores_other id=scores_header>Status</td><td class=borders></td></tr>";
		
			$team = $database->select($_GET["round"], //Table
				['idnum', 'round', 'question','answer', 'wager', 'checked'], // Select cols
				[ // Where
					'teamid' => $points['idnum'], 
				],
				['ORDER' => ['question' => 'ASC']] 
			);
			foreach($team AS $myteam) {
				if ($alternate == 1) { $alternate=2; } else { $alternate=1; }
				if ($myteam["round"] == 'firsthalf') { $cround='First Half'; }
				if ($myteam["round"] == 'picture') { $cround='Picture Round'; }
				if ($myteam["round"] == 'secondhalf') { $cround='Second Half'; }
				if ($myteam["round"] == 'id') { $cround='ID Round'; }
				if ($myteam["round"] == 'currentevents') { $cround='Social Bonus'; }
				if ($myteam["round"] == 'wager') { $cround='Final Questions'; }
				if ($myteam["correct"] == '0' && $myteam["checked"] =='1') {
					$option="<a href=scorepage.php?dispute=".$myteam["idnum"]."&dround=".$_GET["round"]."><img src=/img/incorrect.png></a>";
				} elseif ($myteam["correct"] > 0) {
					$option='<img src=/img/correct.png>'; 
				} else {
					$option='Checking';
				}
				if ($myteam['wager'] > 0) { $wager='('.$myteam['wager'].')'; } else { $wager=''; }
				$uncodedanswer=str_replace($points["idnum"],'',$myteam['answer']);
				$q='q'.$myteam['question'].'q';
				$q2=substr('$uncodedanswer', 2, 1);
				if (is_numeric($q2)) { $q=$points["idnum"].$q.$q2; } else { $q=$points["idnum"].$q; }
				$uncodedanswer=str_replace($q,'',$myteam['answer']);
				print '<tr><td class=borders></td><td class=scores_round id=scores'.$alternate.'>'.$cround.'</td><td class=scores_other id=scores'.$alternate.'>'.$myteam['question'].'</td><td class=scores_answer id=scores'.$alternate.'>'.$uncodedanswer.' '.$wager.'</td><td class=scores_other id=scores'.$alternate.'>'.$option.'</td><td class=borders></td></tr>';
			}
		} else {
			print "<tr><td class=borders></td><td class=rank_right id=scores1 colspan=2>First Half:</td><td class=rank_left  id=scores1 colspan=2> &nbsp; <a href=scorepage.php?round=answers_r1>$points[firsthalf]</a></td><td class=borders></td></tr>
			<tr><td class=borders></td><td class=rank_right id=scores2 colspan=2>Picture Round:</td><td class=rank_left  id=scores2 colspan=2> &nbsp; <a href=scorepage.php?round=answers_r2>$points[picture]</a></td><td class=borders></td></tr>
			<tr><td class=borders></td><td class=rank_right id=scores1 colspan=2>Second Half:</td><td class=rank_left  id=scores1 colspan=2> &nbsp; <a href=scorepage.php?round=answers_r3>$points[secondhalf]</a></td><td class=borders></td></tr>
			<tr><td class=borders></td><td class=rank_right id=scores2 colspan=2>ID Round:</td><td class=rank_left  id=scores2 colspan=2> &nbsp; <a href=scorepage.php?round=answers_r4>$points[id]</a></td><td class=borders></td></tr>
			<tr><td class=borders></td><td class=rank_right id=scores1 colspan=2>Social Bonus:</td><td class=rank_left  id=scores1 colspan=2> &nbsp; <a href=scorepage.php?round=answers_r5>$points[currentevents]</a></td><td class=borders></td></tr>
			<tr><td class=borders></td><td class=rank_right id=scores2 colspan=2>Final Questions:</td><td class=rank_left  id=scores2 colspan=2> &nbsp; <a href=scorepage.php?round=answers_r6>$points[wager]</a></td><td class=borders></td></tr>";
		}
		print "<tr><td class=borders></td><td colspan=2><br><br></td><td colspan=2><br><br></td><td class=borders></td></tr>
		<tr><td class=borders></td><td class=rank_header id=scores_header>Rank</td><td class=rank_header id=scores_header colspan=2>Team Name</td><td class=rank_header id=scores_header>Total</td><td class=borders></td>";
		$rank=0;

		$allteams = $database->select('points', ['idnum', 'teamname', 'total'],
		['ORDER' => ['total' => 'DESC']] );
		foreach ($allteams AS $team) {
			$rank=$rank+1;
			if ($alternate == 1) { $alternate=2; } else { $alternate=1; }
			print "<tr><td class=borders></td><td class=rank id=scores$alternate>$rank</td><td class=rank id=scores$alternate colspan=2>".$team['teamname']."</td><td class=rank id=scores$alternate>".$team['total']."</td><td class=borders></td></tr>";
		}
	}

	if ($round["scores"] != 1) {
		print "<tr><td class=answersheet colspan=6><br><b>Submitted answers can be changed at any time until the host moves on to the next round.</b></td></tr>
		<tr><td class=answersheet colspan=6><br><b>REMEMBER:</b> Only one set of answers per team will be used.  If you're playing with others on your team remotely, you can view what they've entered by refreshing the page after they submit their answers.  Refreshing the page will delete any answers you have not yet submitted though so be careful!</tr>";
	}
} else {
	echo 'No Team has been created or has any points yet!';
}

if ($teamname == null) {
	print "<tr><td class=borders></td><td colspan=4><br><a href=scorepage.php id=refresh  class=refresh>Refresh</a><input type=submit value='Submit' name='btn-submit' id='san-button' disabled='disabled'><td class=borders></td>";
} elseif(isset($tellpeople)) {
	print "<tr><td class=borders></td><td colspan=4><p id='fadeout'><br><br>".$tellpeople."</p>
	<a href=scorepage.php id=refresh class=refresh>Refresh</a> <input type=submit value='Submit' name='btn-submit' id='san-button'><br></td><td class=borders></td>";
}

$hostsset= $database->count('hosts', ['*']);

if ($hostsset > 0) {
	print "<tr><td class=borders></td><td colspan=4><p id='fadeout'>Your hosts for tonight are: ";
	$hosts = $database->select('hosts', '*');
	$totalhosts = '';
	foreach($hosts AS $gamehost) {
		$totalhosts .= $gamehost['host'].',';
	}
	$totalhosts=substr_replace($totalhosts ,"",-1);
	print "$totalhosts. Whisper them on Twitch if you have any issues come up.</td><td class=borders></td></tr>";
}
?>
		</tr>
	</table>
</form>
<script>
window.onload = function() {
  window.setTimeout(fadeout, 5000); //5 seconds
}

function fadeout() {
  document.getElementById('fadeout').style.opacity = '0';
}
</script>

<script>
if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}
</script>


<script src="http://code.jquery.com/jquery-1.10.2.js"></script>
<script>
function validate() {

var valid = true;
valid = checkEmpty($("#name"));

$("#san-button").attr("disabled",true);
if(valid) {
$("#san-button").attr("disabled",false);
} 
}
function checkEmpty(obj) {
var name = $(obj).attr("name");
$("."+name+"-validation").html(""); 
$(obj).css("border","");
if($(obj).val() == "") {
$(obj).css("border","#FF0000 1px solid");
$("."+name+"-validation").html("Required");
return false;
}

return true; 
}
function checkEmail(obj) {
var result = true;

var name = $(obj).attr("name");
$("."+name+"-validation").html(""); 
$(obj).css("border","");

result = checkEmpty(obj);

if(!result) {
$(obj).css("border","#FF0000 1px solid");
$("."+name+"-validation").html("Required");
return false;
}

return result; 
}
</script>  
