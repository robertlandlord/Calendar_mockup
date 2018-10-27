<?php
// login_ajax.php
header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json
//Because you are posting the data via fetch(), php has to retrieve it elsewhere.
$json_str = file_get_contents('php://input');
//This will store the data into an associative array
$json_obj = json_decode($json_str, true);
ini_set("session.cookie_httponly", 1);
session_start();
if(!array_key_exists("username", $_SESSION)||$_SESSION["username"]==""){
    echo json_encode(array(
        "success" => false,
        "message" => "Not logged in, please log in before trying to add events!!"
    ));
    exit;
}
//Variables can be accessed as such:
$title = htmlentities($json_obj['title']);
$description = htmlentities($json_obj['description']);
$start = htmlentities($json_obj['start']);
$end = htmlentities($json_obj['end']);
$date = htmlentities($json_obj['date']);
$recurrDays = htmlentities($json_obj['recurrDays']);
$username = $_SESSION['username'];

require 'user_database.php';

$added = true;
$dateArr = explode("-", $date);
$dayofmonth = $dateArr[2];
$month = (int)$dateArr[1];
$user_id = $_SESSION['userid'];
$multiple = false;
if($recurrDays == "yes" && (int)$dayofmonth<29){
    $multiple = true;
    $newYear = $dateArr[0];
    for($index = 0; $index <12; $index++){
        $newDateArr = explode("-", $date);
        $newMonth = ($month+$index)%12+1;
        
        if($month > 1 && $newMonth ==1){
            $newYear = (int)$newDateArr[0]+1;
        }
        
        $newDate = (string)$newYear."-" .(string)$newMonth."-".(string)$dayofmonth;
        
        $newquery = $mysqli->prepare(
            "INSERT INTO events(user_id, date, title, description, start, end, recurring_days) VALUES (?,?,?,?,?,?,?);"
        );
        if(!$newquery){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            $added = false;
            exit;
        }
        
        $newquery ->bind_param('sssssss', $user_id, $newDate, $title, $description, $start, $end,$recurrDays);
        $newquery->execute();
        $newquery->close();
        
    }
    
}
    $addevent = $mysqli->prepare(
        "INSERT INTO events (user_id, date, title, description, start, end, recurring_days) VALUES(?,?,?,?,?,?,?);"
    );

    if(!$addevent){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        $added = false;
        exit;
    }
    $addevent->bind_param('sssssss', $user_id, $date, $title, $description, $start, $end,$recurrDays);
    $addevent->execute();
    $addevent->close();

    
    
if($added){ 
	$_SESSION['username'] = $username;
	$_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
    if($multiple){
        echo json_encode(array(
		"success" => true,
        "message" => "multiple events added!"
	));
    }
    else{
        echo json_encode(array(
		"success" => true,
        "message" => "only 1 event added!"
	));
    }
	exit;
}else{
	echo json_encode(array(
		"success" => false,
		"message" => "Failed to add"
	));
	exit;
}

?>