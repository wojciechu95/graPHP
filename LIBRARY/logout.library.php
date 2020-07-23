<?php

if(isset($_SESSION['logged']) && isset($_SESSION['uid'])) {

    $um = new UserManager;
    $um->LogOut();

    header("Location: ".SERVER_ADDRESS."home");

} else {

    die("DOSTĘP DO TEJ STRONY ZOSTAŁ ZABLOKOWANY!");

}

?>