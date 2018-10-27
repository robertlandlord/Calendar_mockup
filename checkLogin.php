<?php
// login_ajax.php
header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json
//Because you are posting the data via fetch(), php has to retrieve it elsewhere.
//This will store the data into an associative array
ini_set("session.cookie_httponly", 1);
session_start();
$cookie = null;
if(isset($_SESSION['token'])){
    $cookie = $_SESSION['token'];
    echo json_encode(array(
		"success" => true,
        "token" => $cookie
	));
    exit;
}
else{
    echo json_encode(array(
		"success" => false
	));
    exit;
}

?>