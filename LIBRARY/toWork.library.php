<?php

if((isset($_POST['user'])) && ($_POST['time'] >= 1 && $_POST['time'] <= 8)) { //walidacja przesłanych danych

    $um = new GameManager;

    $id = ltrim($_POST['user'], 'u_');
    $time = intval($_POST['time']);

    $res = $um->sendToWork($id, $time); //przesłanie tablicy do metody CreateUser w klasie UserManager

    if($res) {
        echo "success";
        exit;
    } else {
        return 0;
    }


} else {
    return false;
}

?>