<?php
// login_ajax.php
header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json
//Because you are posting the data via fetch(), php has to retrieve it elsewhere.
//$json_str = file_get_contents('php://input');
//This will store the data into an associative array
//$json_obj = json_decode($json_str, true);
ini_set("session.cookie_httponly", 1);
session_start();
//Variables can be accessed as such:
$username = $_SESSION['username'];
$user_id = $_SESSION['userid'];
$created = true;
    require 'user_database.php';
    $eventsArr = array();
	$stmt = $mysqli->prepare(
                "SELECT title, date, description, start, end, recurring_days, recurring_weeks FROM events WHERE user_id=?"
            );
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        $created = false;
        exit;
    }
            
	$stmt->bind_param('s', $user_id);
	$stmt->execute();
	
	$stmt->bind_result($title, $date, $desc, $start, $end, $recurrDays, $recurrWeeks);
	while($stmt->fetch()){
        if($recurrDays == NULL){
            $recurrDays = "";
        }
        if($recurrWeeks == NULL){
            $recurrWeeks = "";
        }
        $temparr = [$title, $date, $desc, $start, $end, $recurrDays, $recurrWeeks];
        array_push($eventsArr, $temparr);
        }

if(count($eventsArr)>0 && $created){ 
	echo json_encode(array(
        "success" => true,
		"dataset"=>$eventsArr,
        "userid"=>$user_id
	));
	exit;
}else{
	echo json_encode(array(
        "success" => false,
        "message" => "No events found"
	));
	exit;
}

?>