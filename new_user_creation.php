<?php
// login_ajax.php
header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json
//Because you are posting the data via fetch(), php has to retrieve it elsewhere.
$json_str = file_get_contents('php://input');
//This will store the data into an associative array
$json_obj = json_decode($json_str, true);

//Variables can be accessed as such:
$newuser = htmlentities($json_obj['newuser']);
$newpass = htmlentities($json_obj['newpass']);
require 'user_database.php';
	$created = true;
	$userfind = $mysqli->prepare("SELECT COUNT(*) FROM user_info WHERE username = ?");
    if(!$userfind){
        printf("Query Prep Failed: %s\n", $mysqli->error);
		$created = false;
        exit;
    }
            
	$userfind->bind_param('s', $newuser);
    $userfind->execute();
    $userfind->bind_result($usercount);
    $userfind->fetch();
    $userfind->close();
    //checking if user already exists, need to have distinct usernames
    if($usercount>0){
        echo json_encode(array(
							   "success"=> false,
							   "message"=>"User already exists."
							   ));
		exit;
    }
	
	else{ //otherwise make new user, and new password with given parameters
        $fullhash = password_hash((string)$newpass, PASSWORD_BCRYPT);
        $passarray = explode("$", $fullhash);
        $length = $passarray[2];
        $passhash = $passarray[3];
        
        $stmt = $mysqli->prepare(
            "insert into user_info (username, passhash, length) values (?, ?, ?)"
        );
       
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        
        $stmt->bind_param('sss', $newuser, $fullhash, $length);
    
        $stmt->execute();
    
        $stmt->close();
		
    }
	

if($created){
	echo json_encode(array(
		"success" => true
	));
	exit;
}else{
	echo json_encode(array(
		"success" => false,
		"message" => "Did not create new user."
	));
	exit;
}


?>