<?php
// Configuration
require __DIR__ . '/vendor/autoload.php';
// Classes
require __DIR__ . '/../app/Database.php';
// Setup DB
$database =  TriviaDatabase::get();

// Setup Template Support
$templates = new League\Plates\Engine(dirname(__FILE__) . '/templates');

$channel = 'draftpassgames'; //Twitch.tv user channel;
function dd()
{
    echo '<pre>';
    array_map(function($x) { 
        var_dump($x); 
    }, func_get_args());
    echo '</pre>';
    die;
}
