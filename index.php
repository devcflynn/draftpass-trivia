<?php // Start PHP session
session_start();

// Dependencies
use DI\Container;
use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\SetCookie;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

// Configuration
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config.php';

// Classes
require __DIR__ . '/../app/Database.php';
require __DIR__ . '/../app/Scorepage.php';

// Create Container using PHP-DI
$container = new Container();

// Set container to create App with on AppFactory
AppFactory::setContainer($container);

// Instantiate App
$app = AppFactory::create();

$database =  TriviaDatabase::get();

// Setup a DB instance
$container->set('database', function () use ($database) {    
    return $database;
});

// For Flash Messages
$container->set('flash', function () {
    return new \Slim\Flash\Messages();
});

// For Teamplates
$container->set('templates', function () {
    return new League\Plates\Engine(dirname(__FILE__) . '/templates');
});

/***** Routes *****/

// Add routes
$app->get('/', function (Request $request, Response $response)  {
    $response->getBody()->write('<a href="/hello/world">Try /hello/world</a>');
    return $response;
});

/* Answer Key */
$app->get('/answerkey/', function (Request $request, Response $response) use ($channel) {
    $templates = $this->get('templates');
    $response->getBody()->write(
        $templates->render('answerkey', compact('db'))
    );
    return $response;
});


/* Score Keeper */
$app->get('/scorekeeper/', function (Request $request, Response $response) {
    $templates = $this->get('templates');
    $database = $this->get('database');
    $response->getBody()->write(
        $templates->render('scorekeeper', compact('db'))
    );
    return $response;
});

/* Score Page */
$app->get('/scorepage/', function (Request $request, Response $response) {
    // App Utilities for Templates & DB Connections
    $templates = $this->get('templates');
    $database = $this->get('database');

    // Get Request Data, Server Params, Cookies
    $data = $request->getParsedBody();
    
    // Setup new Scorepage Class for Scorepage Logic
    $scorepage = new Scorepage($request);

    // Get our  data to send along
    $viewData = $scorepage->getViewData($request);

    $response->getBody()->write(
        $templates->render('scorepage', [
            'viewData' => $viewData, 
            'database' => $database, 
            'data' => $data
        ])
    );
    return $response;
});

/* Score Page */
$app->post('/scorepage/', function (Request $request, Response $response) {
    // App Utilities for Templates & DB Connections
    $templates = $this->get('templates');
    $database = $this->get('database');

    // Set empty array for alerts to user 
    $alerts = [];

    // Setup new Scorepage Class for Scorepage Logic
    $scorepage = new Scorepage($request);

    // Get our  data to send along
    $viewData = $scorepage->getViewData($request);
    
    $scorepage->handlePostData();
  
  //Submit answers
    
    if (isset($viewData["teamname"])) {
        if ($viewData["teamname"] == null || strlen($viewData["teamname"]) === 0) {
            $alerts[] ="No team name was entered.  Team names must have letters or numbers in them Please go back and enter a new name before submitting your answers.";
        }
    
        $alerts[] ='Answers Submitted!';
           
/*        
        $sorry='<tr><td class="borders"></td><td class=answersheet colspan=4><br><b>Sorry! The next round has begun, answers from the previous round can no longer be submitted.</b><br></td><td class="borders"></td></tr>';

        if ($data["teamname"]["currentround"] == 'firsthalf' && $round["firsthalf"] != 1) { print "$sorry";	
        } elseif ($data["teamname"]["currentround"] == 'picture' && $round["picture"] != 1) { print "$sorry";
        } elseif ($data["teamname"]["currentround"] == 'secondhalf' && $round["secondhalf"] != 1) { print "$sorry";
        } elseif ($data["teamname"]["currentround"] == 'id' && $round["id"] != 1) { print "$sorry";
        } elseif ($data["teamname"]["currentround"] == 'currentevents' && $round["currentevents"] != 1) { print "$sorry";
        } else {
        
            if ($points["idnum"] == null)  {
                $database->insert("points", [
                    "teamname" => $data["teamname"]
                ]);
                if($database->error()) {
                    die("Please leave your team using the link under the entry box and enter your team name again. Something went wrong.");
                }
                $points = $database->select("points", [
                    "idnum",
                    "teamname",
                    "total",
                    "dispute",

                ], [
                    "teamname" => $$data["teamname"]
                ]);
            }
            for($i=1; $i<=15;$i++) {
                $a='a'.$i;
                $w='w'.$i;
                $q='q'.$i.'q';
                if ($data["teamname"][$a]) {
                    $entry = array(" THE ", "THE ", " IN ", " OF ", " TOO ", " TO ", " IS ", " AND ", " AN ", " A ", " MY ");
                    $nohtml = array("<",">",'"');
                    $unique=str_replace ($nohtml, '', $data["teamname"][$a]);
                    $unique=$points["idnum"].$q.$unique; //used to make answer a unique scorepage
                    $special=preg_replace ("/[^a-zA-Z0-9\s]/", '', $data["teamname"][$a]);
                    $special2=strtoupper($special);
                    $special2 = str_replace($entry, " ", $special2);
                    $special2 = str_replace(" ", "", $special2);
                    $a_special2 = str_split($special2);
                    $answerkey = $database->select("answerkey",
                        ["answer", "shortcut"],
                        ["question" => $i, 'round' => $data["teamname"]['currentround']]
                    );
                    $answerkeyupper = strtoupper($answerkey["answer"]);
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
                        "AND" => [
                            "round" => $data["teamname"]["currentround"],
                            "question" => $i,
                            "teamid" => $points["idnum"],
                            "answer[!]" => $unique
                        ]
                    ]);

                    $database->insert($answerround, [
                        "teamid" => $points["idnum"],
                        "round" => $data["teamname"]["currentround"],
                        "question" =>$i,
                        "answer" => $unique,
                        "wager" => $data["teamname"][$w],
                        "wager" => $checked
                    ]);
                }
            }
        }*/
    }
  
    $response->getBody()->write(
        $templates->render('scorepage', [
            'database' => $database,
            'viewData' => $viewData
        ])
    );
    return $response;
});

/* Twitch */
$app->get('/twitch/', function (Request $request, Response $response) use ($channel) {
    $templates = $this->get('templates');
    $response->getBody()->write(
        $templates->render('twitch')
    );
    return $response;
});

/* Hosts */

$app->get('/hosts/', function (Request $request, Response $response) { 
    $templates = $this->get('templates');
    $database = $this->get('database');
    $flash = $this->get('flash');

    // Get flash messages from previous request
    $messages = $flash->getMessages();

    $total=5; // total number of possible hosts
    
    $existingHosts = $database->select('hosts', '*');

    $alerts = [
        'Test 1',
        'Test 2',
        'Test 3'
    ];

    $response->getBody()->write(
        $templates->render('hosts', compact(
            'database', 
            'existingHosts', 
            'alerts',
            'messages'))
    );
    return $response;
});

$app->post('/hosts/', function ($request, $response, $args) {
    // Get our dependencies
    $database = $this->get('database');
    $flash = $this->get('flash');

    // Grab the data we just $_POST'ed
    $data = $request->getParsedBody();
   
    // Clean out our table
    $database->delete("hosts", [
        "AND" => [
            "id[>]" => 0
        ]
    ]);

    // Initialize an empty array so we can run 1 query instead of many
    $insert = [];

    // Iterate over our hosts we just posted
    foreach($data['hosts'] AS $id => $host) {
        if ($host != null) {
                $newHost= preg_replace ("/[^a-zA-Z0-9\s]/", '', $host);
                $insert[] = ['id' => $id, 'host' => $newHost];
        }
    }
    // Insert what we got with a single query
    $database->insert("hosts", $insert);

    // Add message to be used in current request
    //$flash->addMessageNow('Test', 'This is another message');
    return $response->withHeader('Location', '/hosts/');
});

//$app->run();

 