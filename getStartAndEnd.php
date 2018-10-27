<?php
// login_ajax.php
header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json
//Because you are posting the data via fetch(), php has to retrieve it elsewhere.
$json_str = file_get_contents('php://input');
//This will store the data into an associative array
$json_obj = json_decode($json_str, true);
ini_set("session.cookie_httponly", 1);
session_start();
//Variables can be accessed as such:
$username = $_SESSION['username'];
$user_id = $_SESSION['userid'];
$event_id = htmlentities($json_obj['selected-event-id']);
//This is equivalent to what you previously did with $_POST['username'] and $_POST['password']
$nofail = true;

    require 'user_database.php';

	$stmt = $mysqli->prepare(
                "SELECT start, end FROM events WHERE event_id=?"
            );
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        $nofail = false;
        exit;
    }
        
            
	$stmt->bind_param('s', $event_id);
	$stmt->execute();
	
	$stmt->bind_result($start, $end);
	$stmt->fetch();
    $stmt->close();

if($start!=null && $end!=null && $nofail){ 
	echo json_encode(array(
        "success" => true,
		"start"=>$start,
        "end"=>$end
	));
	exit;
}else{
	echo json_encode(array(
        "success" => false,
        "message" => "Failed to retrieve start and end"
	));
	exit;
}

?>