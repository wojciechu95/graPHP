<?php

if((isset($_POST['user']) && isset($_POST['type']))
    && ($_POST['type'] == 'hp' ||
        $_POST['type'] == 'attack' ||
        $_POST['type'] == 'defense')
) {

    $um = new GameManager;

    $id = ltrim($_POST['user'], 'u_'); //uzyskanie numeru id
    $res = $um->updateStats($id, $_POST['type']); //przesłanie argumentów do metody updateStats w klasie GameManager

    if($res) { //powodzenie
        echo "success";
        exit;
    } else {
        return false;
    }


} else {
    die("DOSTĘP DO TEJ STRONY ZOSTAŁ ZABLOKOWANY!"); //wejście poza formularzem
}

?>