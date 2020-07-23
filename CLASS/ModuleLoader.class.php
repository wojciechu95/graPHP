<?php

class ModuleLoader {

    static public function load($MODULE) {

        switch($MODULE) {

            case 'open':
            echo '<!DOCTYPE HTML>
<html lang="pl">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Osadnicy - osadnicy_projekt przeglądarkowa</title> 
               
    <link href="css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="css/css.css" rel="stylesheet" media="screen">
    <link href="css/style.css" rel="stylesheet" media="screen">
    <link href="css/responsive.css" rel="stylesheet" media="screen">
    <link href="css/smoke.min.css" rel="stylesheet" media="screen">
    <link href="css/input.css" rel="stylesheet" media="screen">
    <link href="css/fontello.css" rel="stylesheet" media="screen">
                        
    <link href="https://fonts.googleapis.com/css?family=Press+Start+2P&amp;subset=latin-ext" rel="stylesheet">
</head>
<body>'.PHP_EOL;
            break;

            case 'navbar':
                if(isset($_SESSION['uid'])){
                    $button = '<button type="button" class="navbar-toggle button_nav" data-toggle="collapse" data-target="#moje-menu">
                                <span class="sr-only">Nawigacja</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>';

                    $menu = '<ul class="nav navbar-nav navbar-right" id="ul_nawigacja">
                                <li class="active"><a href="statystyki">Statystyki</a></li>
                                <li><a href="praca">Praca</a></li>
                                <li><a href="sklep">Sklep</a></li>
                                <li><a href="walka">Walka</a></li>
                                <li><a href="ranking">Ranking</a></li>
                                <li><a href="logout">Wyloguj</a></li>
                            </ul>';
                } else {
                    $button = '';
                    $menu = '';
                }
                echo '<header id="menu" class="menu">
        <div class="container">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <nav class="navbar navbar-inverse" role="navigation" id="pasek_nawi">
                    <div class="container-fluid">
                        <div class="navbar-header">
                        '.$button.'
                            <div id="logo">
                                <h1 class="czcionka">OSADNICY</h1>
                            </div>
                        </div>
                        <div class="collapse navbar-collapse" id="moje-menu">
                        '.$menu.'
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </header>'.PHP_EOL;
                break;

            case 'home':
                echo '<div class="row">
            <h2 class="czcionka">"Tylko martwi ujrzeli koniec wojny" - Platon</h2>
        </div>'.PHP_EOL;
                break;

            case 'statystyki':
                $select = DatabaseManager::selectBySQL("SELECT * FROM stats WHERE uid={$_SESSION['uid']}");
                foreach($select as $arr) {
                    $stats = $arr;
                }

                $user = UserManager::getUsernameById($_SESSION['uid']);

                echo '<section class="content statystyki">  	
        <div class="container">        
            <h2>Statystyki użytkownika '.$user.':</h2>         
             <div class="row">
                <ul class="ul" id="u_'.$_SESSION['uid'].'">
                    <li><img src="images/punkty.png">Punkty: <span>'.$stats['points'].'</span></li>
                    <li><img src="images/serce.png">Życie: <span>'.$stats['hp'].'</span> <button class="add" name="hp" type="button"><img src="images/dodaj.png"></button></li>
                    <li><img src="images/miecz.png">Atak: <span>'.$stats['attack'].'</span> <button class="add" name="attack" type="button"><img src="images/dodaj.png"></button></li>
                    <li><img src="images/tarcza.png">Obrona: <span>'.$stats['defense'].'</span> <button class="add" name="defense" type="button"><img src="images/dodaj.png"></button></li>
                    <li><img src="images/zloto.png">Złoto: <span id="zloto">'.$stats['gold'].'</span> </li>
                </ul>
                <div id="result"></div>
             </div>
             <div class="row">
                <h2>Ekwipunek:</h2>'.PHP_EOL;
                $select2 = DatabaseManager::selectBySQL("SELECT * FROM items WHERE uid={$_SESSION['uid']}");

                if($select2) {
                    foreach($select2 as $item) {
                        ModuleLoader::loadItemsEquipment($item['name'], $item['defense'], $item['attack'], $item['is_equipped'], $item['id']);
                    }
                    ModuleLoader::loadhero();
                }
                else {
                    echo '<div style="display:table; width: 432px; height: 612px">
                    <h3>Nie masz żadnych przedmiotów.</h3>
                    <img src="images/ekwipunek.png" class="img-responsive">
                    </div>';
                }

                echo '</div>
        </div>   
    </section>'.PHP_EOL;
                break;

            case 'praca':
                $select = DatabaseManager::selectBySQL("SELECT * FROM work WHERE uid={$_SESSION['uid']}");

                if(!$select){
                    $form = '<form>
                    <input type="number" name="hours" class="hours" min="1" max="8" step="1" value="1"> <br />
                    <button type="button" id="u_'.$_SESSION['uid'].'">Pracuj</button>
                </form>'.PHP_EOL;
                }
                else {
                    $form = ''.PHP_EOL;
                }

                echo '<section class="content praca">  	
        <div class="container">        
            <h2>Praca:</h2>          
            <div class="row">'.PHP_EOL.$form;

                ModuleLoader::load('timer');

                echo '</div>                  
        </div>   
    </section>'.PHP_EOL;
                break;

            case 'timer':

                $select = DatabaseManager::selectBySQL("SELECT * FROM work WHERE uid={$_SESSION['uid']}");

                if($select) {

                    foreach($select as $arr) {
                       $work = $arr;
                    }

                    $akt = time();
                    $finish = $work['finish_date'];
                    $wynik = $finish - $akt;

                    echo '<script>

                            var seconds = '.$wynik.';
                            function timer() {
                                var days        = Math.floor(seconds/24/60/60);
                                var hoursLeft   = Math.floor((seconds) - (days*86400));
                                var hours       = Math.floor(hoursLeft/3600);
                                var minutesLeft = Math.floor((hoursLeft) - (hours*3600));
                                var minutes     = Math.floor(minutesLeft/60);
                                var remainingSeconds = seconds % 60;
                                if (remainingSeconds < 10) {
                                    remainingSeconds = "0" + remainingSeconds;
                                }

                                if (minutes < 10) {
                                    minutes = "0" + minutes;
                                }
                                document.getElementById("countdown").innerHTML = hours + ":" + minutes + ":" + remainingSeconds;
                                if (seconds == 0) {
                                    clearInterval(countdownTimer);
                                    document.getElementById("countdown").innerHTML = "Zakończono! Odśwież stronę.";
                                } else {
                                    seconds--;
                                }
                            }
                            var countdownTimer = setInterval("timer()", 1000);
                            </script>

                            	<span id="countdown" class="timer"></span>
                            ';
                        }

                        else {
                            echo '';
                        }
            break;

            case 'sklep':

                echo '<section class="content sklep" id="u_'.$_SESSION['uid'].'">  	
        <div class="container">        
            <h2>Sklep:</h2>         
            <div class="row">';

                ModuleLoader::loadItemsShop('miecz', 75);
                ModuleLoader::loadItemsShop('tarcza', 60);
                ModuleLoader::loadItemsShop('hełm', 50);
                ModuleLoader::loadItemsShop('zbroja', 150);
                ModuleLoader::loadItemsShop('trzewiki', 40);

                echo '</div>
        </div>
    </section>'.PHP_EOL;

                break;

            case 'walka':
                echo '<section class="content walka">  	
        <div class="container">        
            <h2>Walka:</h2>
            <div class="row">
                <div class="col-xs-8 col-sm-6 col-md-4 col-lg-4 col-lg-offset-4 col-md-offset-4 col-sm-offset-3 col-xs-offset-2">
                    <ul class="user_list" id="u_'.$_SESSION['uid'].'">'.PHP_EOL;

                $select_users = DatabaseManager::selectBySQL("SELECT * FROM stats WHERE uid!='".$_SESSION['uid']."'");

                if($select_users) {
                    foreach($select_users as $user) {
                        $username = UserManager::getUsernameById($user['uid']);
                        ModuleLoader::loadUserList($user['uid'], $username, $user['points'], true, false);
                    }
                }

                echo '</ul>
                </div>
            </div>
        </div>   
    </section>'.PHP_EOL;
                break;

            case 'ranking':
                echo '<section class="content ranking">
        <div class="container">
            <h2>Ranking:</h2>
            <div class="row">
                <div class="col-xs-10 col-sm-6 col-md-6 col-lg-4 col-lg-offset-4 col-md-offset-3 col-sm-offset-3 col-xs-offset-1">
                    <ul class="user_list">'.PHP_EOL;

                $select_users = DatabaseManager::selectBySQL("SELECT * FROM stats ORDER BY points desc");

                if($select_users) {
                    $i = 1;
                    foreach($select_users as $user) {
                        $username = UserManager::getUsernameById($user['uid']);
                        ModuleLoader::loadUserList($user['uid'], $username, $user['points'], false, $i);
                        $i++;
                    }
                }

                echo '</ul>
                </div>
            </div> 
        </div>
    </section>'.PHP_EOL;
                break;

            case 'rejestracja':

                $e_haslo = $e_imie = $e_email = $e_bot = $e_nick = '';
                if (isset($_SESSION['e_haslo'])) {
                    $e_haslo = '<p class="error">'.$_SESSION['e_haslo'].'</p>';
                    unset($_SESSION['e_haslo']);
                }
                if (isset($_SESSION['e_imie'])) {
                    $e_imie = '<p class="error">'.$_SESSION['e_imie'].'</p>';
                    unset($_SESSION['e_imie']);
                }
                if (isset($_SESSION['e_email'])) {
                    $e_email = '<p class="error">'.$_SESSION['e_email'].'</p>';
                    unset($_SESSION['e_email']);
                }
                if (isset($_SESSION['e_bot'])) {
                    $e_bot = '<p class="error">'.$_SESSION['e_bot'].'</p>';
                    unset($_SESSION['e_bot']);
                }
                if (isset($_SESSION['e_nick'])) {
                    $e_nick = '<p class="error">'.$_SESSION['e_nick'].'</p>';
                    unset($_SESSION['e_nick']);
                }

                $nick = $imie = $email = $haslo = $haslo2 = '';
                if (isset($_SESSION['fr_nick'])){
                    $nick = $_SESSION['fr_nick'];
                    unset($_SESSION['fr_nick']);
                }
                if (isset($_SESSION['fr_imie'])){
                    $imie = $_SESSION['fr_imie'];
                    unset($_SESSION['fr_imie']);
                }
                if (isset($_SESSION['fr_haslo1'])){
                    $haslo = $_SESSION['fr_haslo1'];
                    unset($_SESSION['fr_haslo1']);
                }
                if (isset($_SESSION['fr_haslo2'])){
                    $haslo2 = $_SESSION['fr_haslo2'];
                    unset($_SESSION['fr_haslo2']);
                }
                if (isset($_SESSION['fr_email'])){
                    $email = $_SESSION['fr_email'];
                    unset($_SESSION['fr_email']);
                }

            echo '<div id="popup-container" class="modal">
            <div class="formularz">
                <span class="close">&times;</span>
                <div class="rejestracja">
                    <h4 class="czcionka">Stwórz swoją postać osadnika:</h4>
                    <form action="register/" method="POST">
                            <div class="kol">
                            <div class="kol1">
                                <input id="imie" type="text" name="imie" value="'.$imie.'" placeholder="Imię" onfocus="this.placeholder = \'\'" onblur="this.placeholder = \'Imię\'">
                                <p class="komunikat"></p>
                                '.$e_imie.'  
                            </div>
                                                
                            <div class="kol2">
                                <input id="username" type="text" name="username" value="'.$nick.'" placeholder="Nick" onfocus="this.placeholder = \'\'" onblur="this.placeholder = \'Nick\'">
                                <p class="komunikat"></p>
                                '.$e_nick.'  
                            </div>
        
                            <div class="kol1">
                                <input id="password" type="password" name="password" value="'.$haslo.'" placeholder="Hasło" onfocus="this.placeholder = \'\'" onblur="this.placeholder = \'Hasło\'">
                                <p class="komunikat"></p> 
                                '.$e_haslo.'                
                            </div>
                                                
                            <div class="kol2">
                                <input id="password1" type="password" name="password1" value="'.$haslo2.'" placeholder="Powtórz Hasło" onfocus="this.placeholder = \'\'" onblur="this.placeholder = \'Powtórz Hasło\'">
                                <p class="komunikat"></p> 
                            </div>
        
                            <div class="kol">
                                <input id="email" type="email" name="email" value="'.$email.'" placeholder="E-mail" onfocus="this.placeholder = \'\'" onblur="this.placeholder = \'E-mail\'">
                                <p class="komunikat"></p> 
                                '.$e_email.' 
                            </div>
                            
                            <div class="kol">
                                <div class="g-recaptcha" data-sitekey="6Leq5isUAAAAANtyY69c2t0nA5tMjIpklna9p5hZ"></div>  
                                '.$e_bot.'    
                            </div>
                            
                        <input id="zarejestruj" type="submit" name="register" value="Zarejestruj">
                        </div>
                    </form>
                </div>
            </div>
        </div>'.PHP_EOL;
            break;

            case 'logowanie':

                $erro = '';
                if (isset($_SESSION['e_log'])) {
                    $erro = '<span class="error">'.$_SESSION['e_log'].'</span>';
                    unset($_SESSION['e_log']);
                }
                if (isset($_SESSION['e_rej'])) {
                    $erro = '<p class="error">'.$_SESSION['e_rej'].'</p>';
                    unset($_SESSION['e_rej']);
                }

                echo '<div class="row logowanie">
            <div class="col-xs-12 col-sm-8 col-md-6 col-lg-6 col-lg-offset-3 col-md-offset-3 col-sm-offset-2">        
            <h4 class="czcionka">Zaloguj się i wejdź do świata strategi!</h4><br>'.PHP_EOL.$erro.PHP_EOL.'
                    <br><button id="myBtn">Zarejestruj Użytkownika</button><br><br>
                <form action="login/" method="POST" class="home_form">
                                    
                    <label for="login">Nick</label>
                    <input id="login" type="text" name="login" placeholder="Nick" onfocus="this.placeholder = \'\'" onblur="this.placeholder = \'Nick\'"> <br/>
                                    
                    <label for="haslo">Hasło</label>
                    <input id="haslo" type="password" name="password" placeholder="Hasło" onfocus="this.placeholder = \'\'" onblur="this.placeholder = \'Hasło\'"> <br/>
                                    
                    <input type="submit" name="zaloguj" value="Zaloguj">
                </form>                
                <p class="zapomnialem"><a href="zapomnialem"><i class="icon-hand"></i> Zapomniałeś hasła? </a></p>
            </div>
        </div>'.PHP_EOL;
                break;

            case 'zapomnialem':
                echo '<div class="row">
        <h2 class="czcionka">Zapomnialem hasla</h2>
    </div>'.PHP_EOL;
                break;

            case 'footer':
                echo '<footer>
        <p>Osadnicy gra przeglądarkowa</p>
    </footer>'.PHP_EOL;
                break;

            case 'js':
                echo '<script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/smoke.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/wlasny.js"></script>
    <script src="https://www.google.com/recaptcha/api.js"></script>'.PHP_EOL;
                break;

            default;
            break;
        }
    }

    static public function loadItemsShop($item, $cena) {
        echo '<div class="item row">                                      
                    <div class="col-xs-1 col-sm-2 col-md-3 col-lg-3">       
                    </div>                                                       

                    <div class="col-xs-5 col-sm-4 col-md-3 col-lg-3">
                        <img src="images/sklep_'.$item.'.png" class="img-responsive">
                    </div>                                

                    <div class="col-xs-5 col-sm-4 col-md-3 col-lg-3">
                        <button class="sklep_'.$item.'">Kup <i>'.$item.' ('.$cena.')</i></button>
                    </div>                                                                    

                    <div class="col-xs-1 col-sm-2 col-md-3 col-lg-3">                                
                    </div>                                
                </div>'.PHP_EOL;
    }

    static  public function loadhero(){
        $selecte = DatabaseManager::selectBySQL("SELECT name, is_equipped FROM items WHERE uid={$_SESSION['uid']}");

        $miecz = $tarcza = $zbroja = $helm = $trzewiki = false;

        $item_ = "";
        if($selecte) {
            foreach($selecte as $item) {
             $item_ .= $item['name']. "-" .$item['is_equipped']." ";
             if ($item['name']=='miecz' && $item['is_equipped']==1) $miecz = true;
             if ($item['name']=='tarcza' && $item['is_equipped']==1) $tarcza = true;
             if ($item['name']=='zbroja' && $item['is_equipped']==1) $zbroja = true;
             if ($item['name']=='hełm' && $item['is_equipped']==1) $helm = true;
             if ($item['name']=='trzewiki' && $item['is_equipped']==1) $trzewiki = true;
            }

            echo '<div class="item row" style="display:table; width: 432px; height: 612px">
                    <img src="images/ekwipunek.png" class="img-responsive" style="position: absolute; z-index: -2">'.PHP_EOL;
            if ($zbroja) echo '<img src="images/ekwipunek_zbroja.png" class="img-responsive" style=" z-index: -1">'.PHP_EOL;
            if ($miecz) echo '<img src="images/ekwipunek_miecz.png" class="img-responsive" style="position: absolute; z-index: -1">'.PHP_EOL;
            if ($tarcza) echo '<img src="images/ekwipunek_tarcza.png" class="img-responsive" style="position: absolute; z-index: -1">'.PHP_EOL;
            if ($helm) echo '<img src="images/ekwipunek_hełm.png" class="img-responsive" style="position: absolute; z-index: -1">'.PHP_EOL;
            if ($trzewiki) echo '<img src="images/ekwipunek_trzewiki.png" class="img-responsive" style="position: absolute; z-index: -1">'.PHP_EOL;
                echo '</div>'.PHP_EOL;
        }
    }

    static public function loadItemsEquipment($item, $defense, $attack, $is_equipped, $item_id) {
        echo '<div class="item row">
                    <div class="col-xs-1 col-sm-2 col-md-3 col-lg-3">                                
                    </div>                                           
                    
                    <div class="col-xs-5 col-sm-4 col-md-3 col-lg-3">
                        <img src="images/sklep_'.$item.'.png" class="img-responsive">
                    </div>
                    
                    <div class="col-xs-5 col-sm-4 col-md-3 col-lg-3">'.PHP_EOL;

        if($is_equipped == 0) {
            echo '<button name="'.$item_id.'" class="equip_'.$item.'">Załóż <i>'.$item.' ('.$attack.' AT, '.$defense.' OB)</i></button>'.PHP_EOL;
        }
        else {
            echo '<button name="'.$item_id.'" class="takeoff_'.$item.'">Zdejmij <i>'.$item.' ('.$attack.'AT, '.$defense.'OB)</i></button>'.PHP_EOL;
        }

        echo '</div>
                    
                    <div class="col-xs-1 col-sm-2 col-md-3 col-lg-3">
                    </div>                                        
                </div>'.PHP_EOL;
    }

    static public function loadUserList($uid, $username, $points, $button, $rank) {
            echo '<li>';
                if($rank) {
                    echo $rank.'. ';
                }
                echo $username.' ('.$points.' pkt.)';
                if($button) {
                  echo '<button type="button" class="attack" name="'.$uid.'">Zaatakuj</button>';
                }
                echo '</li>'.PHP_EOL;

    }
}
?>
