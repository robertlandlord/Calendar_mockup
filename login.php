<?php
// login_ajax.php
header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json
//Because you are posting the data via fetch(), php has to retrieve it elsewhere.
$json_str = file_get_contents('php://input');
//This will store the data into an associative array
$json_obj = json_decode($json_str, true);

//Variables can be accessed as such:
$username = htmlentities($json_obj['username']);
$password = htmlentities($json_obj['password']);
ini_set("session.cookie_httponly", 1);
//This is equivalent to what you previously did with $_POST['username'] and $_POST['password']
//debug_to_console($username);
// Check to see if the username and password are valid.  (You learned how to do this in Module 3.)
require 'user_database.php';
	$completed = true;
	$stmt = $mysqli->prepare(
                "SELECT username, COUNT(*), id, passhash FROM user_info WHERE username=?"
            );
	if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
		$completed = false;
        exit;
    }
            
	$stmt->bind_param('s', $username);
	$stmt->execute();
	
	$stmt->bind_result($user, $cnt, $user_id, $pwd_hash);
	$stmt->fetch();


if($cnt ==1 && password_verify($password, $pwd_hash) && $completed){ 
	session_start();
	$_SESSION['username'] = $username;
    $_SESSION['userid'] = $user_id;
	$_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32)); 
	echo json_encode(array(
		"success" => true,
		"token" => $_SESSION['token']
	));
	exit;
}else{
	echo json_encode(array(
		"success" => false,
		"message" => "Incorrect Username or Password"
	));
	exit;
}

?>