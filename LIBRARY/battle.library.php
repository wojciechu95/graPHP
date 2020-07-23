<?php

if(isset($_POST['attacker']) && isset($_POST['defender'])) {

    $um = new GameManager;

    $id = ltrim($_POST['attacker'], 'u_');

    $res = $um->duel($id, $_POST['defender']);

    if($res) {
        echo $res;
        exit;
    } else {
        return 0;
    }


} else {
    return false;
}

?>