<?php

class UserManager {
    
    //utworzenie zmiennych
    protected $login;
    protected $password;
	protected $mail;
    protected $id;
    
    public function LogIn($LOGIN, $PASSWORD) { //przyjmujemy w formularza login i hasło
        
        $this->login = htmlentities($LOGIN, ENT_QUOTES, "UTF-8"); //przypisanie loginu
        $this->password = $PASSWORD; //przypisanie hasła

        if(self::isExist() && count(self::isExist()) > 0) { //sprawdzenie czy metoda isExist wyszukała użytkownika - wyszukała, logujemy
             $id = self::getIdByUsername(); //pobranie id użytkownika
             $this->id = $id; //przypisanie id

            self::log_in(); //ustawienie sesji
            return $this->login;
            
        } else {
            
            return false; //nie znaleziono takiego użytkownika
            
        }
    }
    
    protected function isExist() { //sprawdzenie czy użytkownik o podanej kombinacji nazwy i hasła istnieje
        
        $arr = DatabaseManager::selectBySQL("SELECT * FROM users WHERE username='".$this->login."' LIMIT 1");
        
        if($arr) {
            foreach($arr as $row) {
                if(password_verify($this->password, $row['password'])){
                   return $arr; //zwrócenie tablicy 
                } else {
                    return false;
                }
            }
        }    
    }
    
    protected function getIdByUsername() { //pobieranie id użytkownika mając jego nick
            
        $array = DatabaseManager::selectBySQL("SELECT * FROM users WHERE username='".$this->login."' LIMIT 1");
        foreach($array as $key) {
            $id = $key['id'];
        }
        return $id; //zwracamy id
            
    }

    protected function log_in() { //utworzenie sesji
        
        $_SESSION['uid'] = $this->id;
        $_SESSION['logged'] = true;
        
    }
    
    public function LogOut() { //wylogowanie
        
        $_SESSION['uid'] = false; //ustwienie sesji na false
        $_SESSION['logged'] = false; //ustwienie sesji na false
        
        session_destroy(); // zniszczenie sesji
        
    }

    static function validation(){//validacja danych
        $wszystko_OK = true;

        $imie = $_POST['imie'];
        if ((strlen($imie) < 2) || (strlen($imie) > 45)) {
            $wszystko_OK = false;
            $_SESSION['e_imie'] = "Imie musi posiadać od 2 do 45 znaków!";
        }

        $user = $_POST['username'];
        if ((strlen($user) < 5) || (strlen($user) > 25)) {
            $wszystko_OK = false;
            $_SESSION['e_nick'] = "Nazwa użytkownika musi posiadać od 5 do 25 znaków!";
        }

        //Sprawdź poprawność hasła
        $haslo1 = $_POST['password'];
        $haslo2 = $_POST['password1'];

        if ((strlen($haslo1)<5) || (strlen($haslo1)>25)){
            $wszystko_OK=false;
            $_SESSION['e_haslo']="Hasło musi posiadać od 5 do 25 znaków!";
        }

        if ($haslo1!=$haslo2){
            $wszystko_OK=false;
            $_SESSION['e_haslo']="Podane hasła nie są identyczne!";
        }

        //Sprawdzanie adresu e-mail
        $email = $_POST['email'];
        $emailB = filter_var($email, FILTER_SANITIZE_EMAIL);//sanityzacja adresu e-mail

        if ((filter_var($emailB, FILTER_VALIDATE_EMAIL) == false) || ($emailB != $email)) {
            $wszystko_OK = false;
            $_SESSION['e_email'] = "Podaj poprawny adres E-Mail";
        }

        $secret_key = "6Leq5isUAAAAAL9ieLr8r73EmKfgy0mFEVzKXGwo";
        $sprawdz = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret_key.'&response='.$_POST['g-recaptcha-response']);

        $odpowiedz = json_decode($sprawdz);

        if ($odpowiedz->success==false){
            $wszystko_OK=false;
            $_SESSION['e_bot']="Potwierdź, że nie jesteś botem!";
        }

        //Zapamiętaj wprowadzone dane
        $_SESSION['fr_nick'] = $user;
        $_SESSION['fr_imie'] = $imie;
        $_SESSION['fr_email'] = $email;
        $_SESSION['fr_haslo1'] = $haslo1;
        $_SESSION['fr_haslo2'] = $haslo2;

        if ($wszystko_OK) return true;
        else return false;
    }

    public function CreateUser($POST) { //rejestracja użytkownika
        
        if(isset($POST) && is_array($POST)) { //sprawdzenie czy została przesłana tablica i czy jest tablicą

            $this->login = htmlentities($POST['username'], ENT_QUOTES, "UTF-8");
            $this->password = $_POST['password'];

            if(self::isExist() && count(self::isExist()) >= 1) { //sprawdzenie czy metoda isExist wyszukała użytkownika - wyszukała
                return false;
            }else {
                $res = DatabaseManager::insertInto("users", array(
                        "imie" => htmlentities($POST['imie'], ENT_QUOTES, "UTF-8"),
                        "username" => htmlentities($POST['username'], ENT_QUOTES, "UTF-8"),
                        "password" => password_hash($POST['password'], PASSWORD_BCRYPT),
                        "email" => addslashes($POST['email']),
                        "data_rejestracji" => date('Y-m-d_H-i-s'),
                        "data_zmiany_hasla" => date('Y-m-d_H-i-s'),
                        "klucz_odzyskiwania" => hash('md5', date('Y-m-d_H-i-s'))));//dodanie użytkownika do bazy danych

                $id = DatabaseManager::selectBySQL("SELECT id FROM users WHERE username='" . addslashes($POST['username']) . "'");
                $res2 = DatabaseManager::insertInto("stats", array(
                        "uid" => $id[0]['id'],
                        "hp" => 100,
                        "attack" => 20,
                        "defense" => 10,
                        "gold" => 200,
                        "points" => 0)); //dodanie użytkownika do bazy danych
            }

            if($res && $res2) { 
                return true; //powodzenie, zwracamy true
            } else {
                return false; //niepowoedzenie, zwracamy false
            }
        } else {
            return false; // zwracamy false
        }       
    }  

    public function getUsernameById($ID) { //pobieranie nazwy użytkownika mając jego id
            
            $array = DatabaseManager::selectBySQL("SELECT username FROM users WHERE id='".$ID."'");
            foreach($array as $user) {
                $username = $user['username'];
            }
            return $username; //zwracamy id
            
    }
}
?>