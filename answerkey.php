<?php
include("config.php");

// Total Rounds
$rounds = [
	1 => [
		'round_name' => 'firsthalf',
		'round_title' => 'First Half',
		'total' => 6,
		'input_name_prefix' => 'a',
	],
	2 => [
		'round_name' => 'picture',
		'round_title' => 'Picture Round',
		'total' =>  15,
		'input_name_prefix' => 'b'
	],
	3 => [
		'round_name' => 'secondhalf',
		'round_title' => 'Second Half',
		'total' => 6,
		'input_name_prefix' => 'c'
	],
	4 => [
		'round_name' => 'id',
		'round_title' => 'ID Round',
		'total' => 15,
		'input_name_prefix' => 'd'
	],
	5 => [
		'round_name' => 'currentevents',
		'round_title' => 'Social',
		'total' => 2,
		'input_name_prefix' => 'e'
	],
	6 => [
		'round_name' => 'wager',
		'round_title' => 'Bonus Round',
		'total' => 2,
		'input_name_prefix' => 'f'
	]
];	
$alerts = [];
if (isset($_GET['delete'])) {
	if($database->delete("answerkey", [
		"AND" => [
			"question[>]" => 0
		]
	])) {
		array_push($alerts, 'Answers cleared!');
	}
	
}

if (isset($_POST) && count($_POST) > 0) {
	foreach($rounds AS $roundKey => $currentRound) {
		
		for ($i = 1; $i <= $currentRound['total']; $i++) {
			$current = $currentRound['round_name'];
			$answer = $database->select(
				'answerkey', //Table
				'*', // Select cols
				[ // Where
					'round' => $current,
					'question' => $i
				]
			);
			$a = $currentRound['input_name_prefix'] . $i;
			$ac = $currentRound['input_name_prefix'] .'c'. $i;
			
			if(isset($_POST[$a])) {
				$removechar[$a] = str_replace("'", "", $_POST[$a]);
				$removechar[$a] = preg_replace("/[^a-zA-Z0-9\s]/", '', $removechar[$a]);
			
				$database->delete("answerkey", [
					'AND' => [
						'round' => $current,
						'question' => $i
					]
				]);

				// Insert
				if(isset($removechar[$a]) && strlen($removechar[$a]) > 0) {
					$insert = [
						"round" => $current,
						"question" => $i,
						"answer" => (isset($removechar[$a])) ? $removechar[$a] : '',
						"shortcut" => (isset($_POST[$ac])) ? $_POST[$ac] : 0
					];
				
					$database->insert("answerkey", $insert);
				}
			}
		}
	}
	array_push($alerts, 'Rounds Updated!');
}
echo $templates->render('answerkey', compact(
	'database',
	'rounds',
	'round',
	'alerts'
));
?>
