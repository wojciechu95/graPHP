<?php

if(isset($_POST['user']) && isset($_POST['name']) && isset($_POST['action']) && isset($_POST['id'])) {

    $um = new GameManager;

    $id = ltrim($_POST['user'], 'u_');


    if($_POST['action'] == 'equip') {
        $res = $um->equipItem($id, $_POST['id'], $_POST['name']);
    }
    elseif($_POST['action'] == 'takeoff') {
        $res = $um->takeOffItem($id, $_POST['id'], $_POST['name']);
    }


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