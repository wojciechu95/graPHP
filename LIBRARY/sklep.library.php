<?php

if(!isset($_SESSION['logged'])){
    $_SESSION['e_log'] = "Musisz się zalogować!";
    $um = new GameManager;
    $res = $um->checkWorkStatus($_SESSION['uid']);
    header('Location: index.php');
    exit();
}

ModuleLoader::load('open');

echo '<div id="wrapper">'.PHP_EOL;

ModuleLoader::load('navbar');

ModuleLoader::load('sklep');

ModuleLoader::load('footer');

echo '</div>'.PHP_EOL;

ModuleLoader::load('js');

echo '</body>
</html>';

?>