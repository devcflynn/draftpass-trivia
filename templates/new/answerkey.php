<?php include("config.php");

$total1=6;
$round1='First Half';
$total2=15;
$round2='Picture Round';
$total3=6;
$round3='Second Half';
$total4=15;
$round4='ID Round';
$total5=2;
$round5='Social';
$total6=2;
$round6='Bonus Round';

print "<div align=center><table align=center width=98% class=answersheet>
<tr><td class=borders></td><td class=answersheet colspan=4>$round1<br><br></td><td class=borders></td></tr>
<form method=post action=answerkey.php>";

if ($_GET[delete]) {
	mysql_query("delete from answerkey");
}

if ($_POST){
	for($i=1; $i<=$total1;$i++) {
		$current='firsthalf';
		$answer = mysql_fetch_array(mysql_query("select * from answerkey where round='$current' and question='$i'"));
		$a='a'.$i;
		$ac='ac'.$i;
		$removechar[$a] = str_replace  ("'", "", $_POST[$a]);
		$removechar[$a]= preg_replace ("/[^a-zA-Z0-9\s]/", '', $removechar[$a]);
			mysql_query("delete from answerkey where round='$current' and question='$i'");
			mysql_query("insert into answerkey (round, question, answer, shortcut) values('$current', '$i', '$removechar[$a]', '$_POST[$ac]')") or die("Could not submit $current $i.");
	}
	for($i=1; $i<=$total2;$i++) {
		$current='picture';
		$answer = mysql_fetch_array(mysql_query("select * from answerkey where round='$current' and question='$i'"));
		$a='b'.$i;
		$ac='bc'.$i;
		$removechar[$a] = str_replace  ("'", "", $_POST[$a]);
		$removechar[$a]= preg_replace ("/[^a-zA-Z0-9\s]/", '', $removechar[$a]);
			mysql_query("delete from answerkey where round='$current' and question='$i'");
			mysql_query("insert into answerkey (round, question, answer, shortcut) values('$current', '$i', '$removechar[$a]', '$_POST[$ac]')") or die("Could not submit $current $i.");
	}
	for($i=1; $i<=$total3;$i++) {
		$current='secondhalf';
		$answer = mysql_fetch_array(mysql_query("select * from answerkey where round='$current' and question='$i'"));
		$a='c'.$i;
		$ac='cc'.$i;
		$removechar[$a] = str_replace  ("'", "", $_POST[$a]);
		$removechar[$a]= preg_replace ("/[^a-zA-Z0-9\s]/", '', $removechar[$a]);
			mysql_query("delete from answerkey where round='$current' and question='$i'");
			mysql_query("insert into answerkey (round, question, answer, shortcut) values('$current', '$i', '$removechar[$a]', '$_POST[$ac]')") or die("Could not submit $current $i.");
	}
	for($i=1; $i<=$total4;$i++) {
		$current='id';
		$answer = mysql_fetch_array(mysql_query("select * from answerkey where round='$current' and question='$i'"));
		$a='d'.$i;
		$ac='dc'.$i;
		$removechar[$a] = str_replace  ("'", "", $_POST[$a]);
		$removechar[$a]= preg_replace ("/[^a-zA-Z0-9\s]/", '', $removechar[$a]);
			mysql_query("delete from answerkey where round='$current' and question='$i'");
			mysql_query("insert into answerkey (round, question, answer, shortcut) values('$current', '$i', '$removechar[$a]', '$_POST[$ac]')") or die("Could not submit $current $i.");
	}
	for($i=1; $i<=$total5;$i++) {
		$current='currentevents';
		$answer = mysql_fetch_array(mysql_query("select * from answerkey where round='$current' and question='$i'"));
		$a='e'.$i;
		$ac='ec'.$i;
		$removechar[$a] = str_replace  ("'", "", $_POST[$a]);
		$removechar[$a]= preg_replace ("/[^a-zA-Z0-9\s]/", '', $removechar[$a]);
			mysql_query("delete from answerkey where round='$current' and question='$i'");
			mysql_query("insert into answerkey (round, question, answer, shortcut) values('$current', '$i', '$removechar[$a]', '$_POST[$ac]')") or die("Could not submit $current $i.");
	}
	for($i=1; $i<=$total6;$i++) {
		$current='wager';
		$answer = mysql_fetch_array(mysql_query("select * from answerkey where round='$current' and question='$i'"));
		$a='f'.$i;
		$ac='fc'.$i;
		$removechar[$a] = str_replace  ("'", "", $_POST[$a]);
		$removechar[$a]= preg_replace ("/[^a-zA-Z0-9\s]/", '', $removechar[$a]);
			mysql_query("delete from answerkey where round='$current' and question='$i'");
			mysql_query("insert into answerkey (round, question, answer, shortcut) values('$current', '$i', '$removechar[$a]', '$_POST[$ac]')") or die("Could not submit $current $i.");
	}
}

for($i=1; $i<=$total1;$i++) {
	$answer = mysql_fetch_array(mysql_query("select * from answerkey where round='firsthalf' and question='$i'"));
	if ($answer[shortcut] ==1) { $checked='checked'; } else { $checked=''; }
	print "<tr><td class=borders></td><td class=answersheet_right >#$i Answer: </td><td class=answersheet ><input type=text name=a$i value=\"$answer[answer]\"></td><td class=answersheet_left ><input type='checkbox' name='ac$i' value='1' $checked></td><td class=borders></td></tr>";
}
print "<tr><td class=borders></td><td class=answersheet colspan=4><br><br>$round2<br><br></td><td class=borders></td></tr>";

for($i=1; $i<=$total2;$i++) {
	$answer = mysql_fetch_array(mysql_query("select * from answerkey where round='picture' and question='$i'"));
	if ($answer[shortcut] ==1) { $checked='checked'; } else { $checked=''; }
	print "<tr><td class=borders></td><td class=answersheet_right >#$i Answer: </td><td class=answersheet ><input type=text name=b$i value=\"$answer[answer]\"></td><td class=answersheet_left ><input type='checkbox' name='bc$i' value='1' $checked></td><td class=borders></td></tr>";
}
print "<tr><td class=borders></td><td class=answersheet colspan=4><br><br>$round3<br><br></td><td class=borders></td></tr>";

for($i=1; $i<=$total3;$i++) {
	$answer = mysql_fetch_array(mysql_query("select * from answerkey where round='secondhalf' and question='$i'"));
	if ($answer[shortcut] ==1) { $checked='checked'; } else { $checked=''; }
	print "<tr><td class=borders></td><td class=answersheet_right >#$i Answer: </td><td class=answersheet ><input type=text name=c$i value=\"$answer[answer]\"></td><td class=answersheet_left ><input type='checkbox' name='cc$i' value='1' $checked></td><td class=borders></td></tr>";
}
print "<tr><td class=borders></td><td class=answersheet colspan=4><br><br>$round4<br><br></td><td class=borders></td></tr>";

for($i=1; $i<=$total4;$i++) {
	$answer = mysql_fetch_array(mysql_query("select * from answerkey where round='id' and question='$i'"));
	if ($answer[shortcut] ==1) { $checked='checked'; } else { $checked=''; }
	print "<tr><td class=borders></td><td class=answersheet_right >#$i Answer: </td><td class=answersheet ><input type=text name=d$i value=\"$answer[answer]\"></td><td class=answersheet_left ><input type='checkbox' name='dc$i' value='1' $checked></td><td class=borders></td></tr>";
}
print "<tr><td class=borders></td><td class=answersheet colspan=4><br><br>$round5<br><br></td><td class=borders></td></tr>";

for($i=1; $i<=$total5;$i++) {
	$answer = mysql_fetch_array(mysql_query("select * from answerkey where round='currentevents' and question='$i'"));
	if ($answer[shortcut] ==1) { $checked='checked'; } else { $checked=''; }
	print "<tr><td class=borders></td><td class=answersheet_right >#$i Answer: </td><td class=answersheet ><input type=text name=e$i value=\"$answer[answer]\"></td><td class=answersheet_left ><input type='checkbox' name='ec$i' value='1' $checked></td><td class=borders></td></tr>";
}
print "<tr><td class=borders></td><td class=answersheet colspan=4><br><br>$round6<br><br></td><td class=borders></td></tr>";

for($i=1; $i<=$total6;$i++) {
	$answer = mysql_fetch_array(mysql_query("select * from answerkey where round='wager' and question='$i'"));
	if ($answer[shortcut] ==1) { $checked='checked'; } else { $checked=''; }
	print "<tr><td class=borders></td><td class=answersheet_right >#$i Answer: </td><td class=answersheet ><input type=text name=f$i value=\"$answer[answer]\"></td><td class=answersheet_left ><input type='checkbox' name='fc$i' value='1' $checked></td><td class=borders></td></tr>";
}

print "<tr><td class=borders></td><td colspan=4><br><input type=submit value='Submit' name='btn-submit' id='san-button'><br><br>
<a href=answerkey.php?delete=1>Delete Answers</a></td><td class=borders></td></tr></form></table>";