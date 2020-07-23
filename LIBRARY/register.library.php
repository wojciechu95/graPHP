<?php

$um = new UserManager;

if($um->validation()) {

    $res = $um->CreateUser($_POST); //przesłanie tablicy do metody CreateUser w klasie UserManager

    if($res) {
        $um->LogIn($_POST['username'], $_POST['password']); //zarejestrowano, logujemy użytkownika
        header("Location: ".$_SERVER['HTTP_REFERER']); //przekierowanie
    } else {

            $_SESSION['e_rej'] = "Utworzenie użytkownika nie było możliwe! Spróbuj wpisać inną nazwę użytkownika i/lub spróbujponownie później. ";
            header('Location: ../index.php');
            exit();
    }

} else {
    $_SESSION['e_rej'] = "Utworzenie użytkownika nie było możliwe! Spróbuj wpisać inną nazwę użytkownika i/lub spróbuj ponownie później. ";
    header('Location: ../index.php');
    exit();
}

?>