<?php

session_start();
require_once("config.php");

if(!(isset($_GET['page']))) { // przekierowanie do strony głównej
    
    header("Location: ".SERVER_ADDRESS."home");
    
} else {
    
    $mp = new MainPage($_GET['page']); // przekierowanie do podanej strony
    
}

?>