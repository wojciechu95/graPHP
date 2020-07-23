<?php

if(isset($_POST['login']) && isset($_POST['password'])) {

    $um = new UserManager;

    if($um->LogIn(htmlentities($_POST['login'], ENT_QUOTES, "UTF-8"), $_POST['password'])) { //przekazanie do metody LogIn w klasie UserManager loginu i hasła

        header("Location: ".$_SERVER['HTTP_REFERER']); //przekierowanie do gry

    } else {

       $_SESSION['e_log'] = "Nieprawidłowa nazwa użytkownika lub hasło.";

        header('Location: ../index.php');
        exit();  //nieprawidłowe dane

    }

} else {
    die("DOSTĘP DO TEJ STRONY ZOSTAŁ ZABLOKOWANY!"); //wejście bez formularza
}

?>