<?php include("config.php");

echo $templates->render('scorekeeper', compact(
	'database'
));
