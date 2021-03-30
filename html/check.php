<?php

session_start();

require_once './AccessManager.php';

if (isset($_POST["username"]) && isset($_POST["password"])) {
    $log = $_POST["username"];
    $pass = $_POST["password"];
    
    $accessmanager = new AccessManager();
    
    if ($accessmanager->checkLogged($log, $pass)) {
        $_SESSION["logged"] = "Y";
        header("Location:index.php");
    } else {
        header("Location:login.php?fail");
    }
    

}