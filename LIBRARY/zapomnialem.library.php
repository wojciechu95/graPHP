<?php

if(isset($_SESSION['logged'])) {
    header("Location: statystyki");
}

ModuleLoader::load('open');

echo '<div id="wrapper">'.PHP_EOL;

ModuleLoader::load('navbar');

echo "\t".'<section class="content ranking">'.PHP_EOL;

ModuleLoader::load('zapomnialem');

echo "\t".'</section>'.PHP_EOL;

ModuleLoader::load('footer');

echo '</div>'.PHP_EOL;

ModuleLoader::load('js');

echo '</body>
</html>';

?>