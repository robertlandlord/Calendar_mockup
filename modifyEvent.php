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

//json information
$newtitle = htmlentities($json_obj['mod-title']);
$newdesc = htmlentities($json_obj['mod-desc']);
$newstart = htmlentities($json_obj['mod-start']);
$newend = htmlentities($json_obj['mod-end']);
$newdate = htmlentities($json_obj['mod-date']);
$event_id = htmlentities($json_obj['event_id']);
$modified = true;
    require 'user_database.php';
    //$modEventsArr = array();
	$stmt = $mysqli->prepare(
                "UPDATE events SET title = ?, description = ?, start=?, end=?, date=? WHERE event_id = ?"
            );
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        $modified = false;
        exit;
    }
            
	$stmt->bind_param('ssssss', $newtitle, $newdesc, $newstart, $newend, $newdate, $event_id);
	//$username = (string)$_POST['username'];
	$stmt->execute();
	
	$stmt->close();
    
    //$hashed_pass = password_hash((string)$password, PASSWORD_BCRYPT);
    //$conditional = password_verify((string)$password, (string)$pwd_hash);

if($modified){ 
	echo json_encode(array(
        "success" => true,
		"start"=>$newstart,
        "end"=>$newend
	));
	exit;
}else{
	echo json_encode(array(
        "success" => false,
        "message" => "Failed to update"
	));
	exit;
}

?>