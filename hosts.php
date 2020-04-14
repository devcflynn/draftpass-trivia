<?php include("config.php");

$total=5; // total number of possible hosts

if ($_POST) {
    	// Clean out our table
    $database->delete("hosts", [
        "AND" => [
            "id[>]" => 0
        ]
    ]);
	// Initialize an empty array so we can run 1 query instead of many
    $insert = [];

    // Iterate over our hosts we just posted
    foreach($_POST['hosts'] AS $id => $existingHost) {
        if ($existingHost != null) {
                $newHost= preg_replace ("/[^a-zA-Z0-9\s]/", '', $existingHost);
                $insert[] = ['id' => $id, 'host' => $newHost];
        }
    }
    // Insert what we got with a single query
    $database->insert("hosts", $insert);
    $alerts[] = 'Hosts added!';
}
echo $templates->render('hosts', compact(
	'database',
	'alerts',
	'existingHosts'
));
?>