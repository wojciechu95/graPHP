<?php

class MainPage {
    
    private $active_page;
    
    public function __construct($ACTIVE_PAGE) {
        
        $this->active_page = $ACTIVE_PAGE;
        
        switch($this->active_page) {
            
            case 'home':
            require_once $this->active_page.".library.php";
            break;   
            
            case 'statystyki':
            require_once $this->active_page.".library.php";
            break;   
            
            case 'praca':
            require_once $this->active_page.".library.php";
            break;
            
            case 'sklep':
            require_once $this->active_page.".library.php";
            break;
            
            case 'walka':
            require_once $this->active_page.".library.php";
            break;
            
            case 'ranking':
            require_once $this->active_page.".library.php";
            break;
            
            case 'login':
            require_once $this->active_page.".library.php";
            break;
            
            case 'logout':
            require_once $this->active_page.".library.php";
            break;
            
            case 'register':
            require_once $this->active_page.".library.php";
            break;
            
            case 'updateStats':
            require_once $this->active_page.".library.php";
            break;
            
            case 'toWork':
            require_once $this->active_page.".library.php";
            break;
            
            case 'buyItem':
            require_once $this->active_page.".library.php";
            break;
            
            case 'ekwipunek':
            require_once $this->active_page.".library.php";
            break;
            
            case 'battle':
            require_once $this->active_page.".library.php";
            break;

            case 'zapomnialem':
            require_once $this->active_page.".library.php";
            break;
            
          
        }
        
    }
    
}

?>