<?php

if(isset($_POST['user']) && isset($_POST['name']) && isset($_POST['price']) && isset($_POST['defense']) && isset($_POST['attack'])) {

    $um = new GameManager;

    $id = ltrim($_POST['user'], 'u_');

    $res = $um->buyItem($id, $_POST['name'], $_POST['price'], $_POST['defense'], $_POST['attack']);

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