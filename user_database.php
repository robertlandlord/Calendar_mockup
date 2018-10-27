<?php

    $mysqli = new mysqli('localhost', 'module5_admin', 'grahamrubin', 'module5_cal');
        
        if($mysqli->connect_errno) {
            printf("Connection Failed: %s\n", $mysqli->connect_error);
            exit;
        }
?>