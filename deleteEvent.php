<?php
// login_ajax.php
header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json
//Because you are posting the data via fetch(), php has to retrieve it elsewhere.
$json_str = file_get_contents('php://input');
//This will store the data into an associative array
$json_obj = json_decode($json_str, true);
ini_set("session.cookie_httponly", 1);
$del_token = htmlentities($json_obj['token']);
session_start();
if(!hash_equals($_SESSION['token'], $del_token)){
	die("Request forgery detected");
}
$deleted = false;
//get session variables
$username = $_SESSION['username'];
$user_id = $_SESSION['userid'];
//event_id
$event_id = htmlentities($json_obj['event_id']);

    require 'user_database.php';
    $modEventsArr = array();
	$stmt = $mysqli->prepare(
                "DELETE FROM events WHERE event_id=?"
            );
    $deleted = true;
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        $created = false;
        exit;
    }
            
	$stmt->bind_param('s', $event_id);
	$stmt->execute();
	$stmt->close();

if($deleted){ 
	echo json_encode(array(
        "success" => true,
		"dataset"=>$modEventsArr
	));
	exit;
}else{
	echo json_encode(array(
        "success" => false,
        "message" => "No events for given user"
	));
	exit;
}

?>