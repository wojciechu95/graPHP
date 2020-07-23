<?php

class GameManager {
    
    public function updateStats($UID, $STAT) {
        
        if(isset($UID) && isset($STAT)) { 
            
                $select = DatabaseManager::selectBySQL("SELECT * FROM stats WHERE uid={$UID}");
                foreach($select as $arr) {
                    $stats = $arr;
                }
                $gold_needed = $stats[$STAT] * 2; //potrzebne złoto do podniesienia statystyki
                
                if($gold_needed > $stats['gold']) { //niewystarczająca ilość złota
                    return false;
                    exit;
                }
                
                else {
                    $stats_inc = $stats[$STAT] + 1;
                    $gold_aft = $stats['gold'] - ($stats[$STAT] * 2);
                    $res = DatabaseManager::updateTable("stats", Array(''.$STAT.'' => ''.$stats_inc.'',
                                                                        'gold' => ''.$gold_aft.''),
                                                              Array('uid' => ''.$UID.'')); //aktualizacja bazy danych
                    if($res) { 
                        return true;
                    } else {
                        return false; //niepowodzenie, zwracamy false
                    }
                }                                                               
      
        } else {      
            return false; // zwracamy false           
        }       
    }  
    
   public function sendToWork($UID, $TIME) {
        
        if(isset($UID) && isset($TIME)) { 
            
                $select = DatabaseManager::selectBySQL("SELECT * FROM work WHERE uid={$UID}"); 
                
                
                
                if($select) { //znaleziono już pracę
                    return false;
                    exit;
                }
                
                else {
                    
                    $seconds = time() + ($TIME * 3600);
                    $reward = ($seconds / 1000000) * $TIME;
                    
                    $res = DatabaseManager::insertInto("work", Array('uid' => ''.$UID.'',
                                                                      'finish_date' => ''.$seconds.'', 
                                                                      'reward' => ''.$reward.''));
                    if($res) { 
                        return true;
                    } else {
                        return false; //niepowodzenie, zwracamy false
                    }
                }                                                               
      
        } else {      
            return false; // zwracamy false           
        }       
    } 
    
    public function checkWorkStatus($UID) {
        
     
                $akt = time();
                $select = DatabaseManager::selectBySQL("SELECT * FROM work WHERE uid={$UID} AND finish_date<{$akt}"); 
                
                
                
                if($select) { 
                    $select2 = DatabaseManager::selectBySQL("SELECT * FROM stats WHERE uid={$UID}");
                    
                    foreach($select2 as $arr) {
                        $stats = $arr;
                    }
                    
                    foreach($select as $arr){
                        $reward = $arr;
                    }    
                    $update_gold = $stats['gold'] + $reward['reward'];
                    DatabaseManager::updateTable("stats", Array('gold' => ''.$update_gold.''), Array('uid' => ''.$UID.''));
                    DatabaseManager::deleteFrom("work", array('uid' => ''.$UID.''));
                }
                
                else {
                    return false;
                    exit;
                }                                                                       
    }
    
    public function buyItem($UID, $NAME, $PRICE, $DEFENSE, $ATTACK) {
        
     
                $select = DatabaseManager::selectBySQL("SELECT * FROM stats WHERE uid={$UID} AND gold>{$PRICE}"); //sprawdzenie czy użytkownik ma wystarczająco złota
                
                
                
                if($select) { //ma wystarczająco
                       
                    foreach($select as $arr) {
                        $stats = $arr;
                    }
                        
                    $update_gold = $stats['gold'] - $PRICE; //obliczenie złota po kupieniu przedmiotu
                     
                    DatabaseManager::updateTable("stats", Array('gold' => ''.$update_gold.''), Array('uid' => ''.$UID.'')); //odjęcie złota z konta gracza
                        
                    $res = DatabaseManager::insertInto("items", Array('uid' => ''.$UID.'',
                                                                        'name' => ''.$NAME.'',
                                                                        'defense' => ''.$DEFENSE.'',
                                                                        'attack' => ''.$ATTACK.'',
                                                                        'is_equipped' => '0'      
                                                                        )); //dodanie przedmiotu do tabeli items
                    if($res) {
                        return true;                                                  
                    }
                        
                    else {
                        return false;
                    }
                  
                } //brakuje złota
                
                else {
                    return false;
                    exit;
                }                                                                       
    }   
    
    public function equipItem($UID, $ITEM_ID, $NAME) {
        
     
                $select = DatabaseManager::selectBySQL("SELECT * FROM items WHERE is_equipped=1 AND name='".$NAME."' AND uid={$UID}"); //sprawdzenie czy przedmiot tego typu nie jest już założony
                
                
                
                if(!$select) { //można założyć
                 
                    
                         
                  $res = DatabaseManager::updateTable("items", Array('is_equipped' => '1'), Array('id' => ''.$ITEM_ID.'')); //założenie przedmiotu
                        
   
                    if($res) {
                        return true;                                                  
                    }
                        
                    else {
                        return false;
                    }
                  
                }
                
                else {
                    return false;
                    exit;
                }                                                                       
    }
    
    public function takeOffItem($UID, $ITEM_ID, $NAME) {
        
     
                $select = DatabaseManager::selectBySQL("SELECT * FROM items WHERE is_equipped=1 AND name='".$NAME."' AND uid={$UID}"); //sprawdzenie czy przedmiot jest założony
                
                
                
                if($select) { //można zdjąć
                 
                    
                         
                  $res = DatabaseManager::updateTable("items", Array('is_equipped' => '0'), Array('id' => ''.$ITEM_ID.''));
                        
   
                    if($res) {
                        return true;                                                  
                    }
                        
                    else {
                        return false;
                    }
                  
                }
                
                else {
                    return false;
                    exit;
                }                                                                       
    }
    
    public function duel($attacker_id, $defender_id) {
                
                if($attacker_id == $defender_id) {
                    return false;
                    exit;
                }
     
                $select_report = DatabaseManager::selectBySQL("SELECT * FROM report WHERE attacker='".$attacker_id."' AND defender='".$defender_id."' AND time>'".time()."'"); //pobranie statystyk atakującego
     
                $select_attacker = DatabaseManager::selectBySQL("SELECT * FROM stats WHERE uid='".$attacker_id."'"); //pobranie statystyk atakującego
                $select_defender = DatabaseManager::selectBySQL("SELECT * FROM stats WHERE uid='".$defender_id."'"); //pobranie statystyk obrońcy
                
                $select_attacker_eq = DatabaseManager::selectBySQL("SELECT * FROM items WHERE uid='".$attacker_id."' AND is_equipped=1"); //pobranie ekwipunku atakującego
                $select_defender_eq = DatabaseManager::selectBySQL("SELECT * FROM items WHERE uid='".$defender_id."' AND is_equipped=1"); //pobranie ekwipunku obrońcy
                
                
                
                if(!$select_report){ //można zaatakować gracza
                
                if($select_attacker && $select_defender) { //znaleziono graczy w bazie
                 
                    
                    foreach($select_attacker as $arr) {
                        $attacker = $arr;
                    }     
                    foreach($select_defender as $arr2) {
                        $defender = $arr2;
                    }   
                    
                    
                    if($select_attacker_eq) { //dodanie statystyk z założonych przedmiotów atakującemu
                       foreach($select_attacker_eq as $attacker_eq) {
                            $attacker['attack'] += $attacker_eq['attack'];
                            $attacker['defense'] += $attacker_eq['defense'];
                        }   
                    }
                    
                    if($select_defender_eq) { //dodanie statystyk z założonych przedmiotów obrońcy
                       foreach($select_defender_eq as $defender_eq) {
                            $defender['attack'] += $defender_eq['attack'];
                            $defender['defense'] += $defender_eq['defense'];
                        }   
                    }
                                
                  
                    while($attacker['hp'] > 0 && $defender['hp'] > 0) { //pętla wykonująca się póki hp jednego z gracza nie spadnie poniżej 0
                        $defender['hp'] -= ($attacker['attack'] / ($defender['defense'] / 10)); //obliczenie obrażeń na obrońcy
                        $attacker['hp'] -= ($defender['attack'] / ($attacker['defense'] / 10)); //obliczenie obrażeń na atakującym           
                    }
                    
                    if($attacker['hp'] <= 0 && $defender['hp'] <= 0) { //remis
                        $time = time() + 3600;
                        $res = DatabaseManager::insertInto("report", Array('attacker' => ''.$attacker['id'].'', //dodanie raportu do bazy
                                                                        'defender' => ''.$defender['id'].'',
                                                                        'time' => ''.$time.''    
                                                                        ));                      
                        return 'remis';
                    }
                    
                    elseif($attacker['hp'] > 0 && $defender['hp'] <= 0) { //atakujący zwycięża
                        $update_points = $attacker['points'] + 10;             
                        DatabaseManager::updateTable("stats", Array('points' => ''.$update_points.''), Array('uid' => ''.$attacker['id'].'')); //dodanie punktów
                        $time = time() + 3600;
                        $res = DatabaseManager::insertInto("report", Array('attacker' => ''.$attacker['id'].'', //dodanie raportu do bazy
                                                                        'defender' => ''.$defender['id'].'',
                                                                        'time' => ''.$time.''    
                                                                        ));     
                        return 'zwyciestwo';
                    }
                    
                    elseif($defender['hp'] > 0 && $attacker['hp'] <= 0) { //obrońca zwycięża
                        $update_points = $attacker['points'] - 5;             
                        DatabaseManager::updateTable("stats", Array('points' => ''.$update_points.''), Array('uid' => ''.$attacker['id'].'')); //odjęcie punktów
                        $time = time() + 3600;
                        $res = DatabaseManager::insertInto("report", Array('attacker' => ''.$attacker['id'].'', //dodanie raportu do bazy
                                                                        'defender' => ''.$defender['id'].'',
                                                                        'time' => ''.$time.''    
                                                                        ));      
                        return 'porazka';
                    }
                  
                } //nie znaleziono graczy/gracza w bazie
                
                else {
                    return false;
                    exit;
                } 
                
                } else { //nie można zaatakować gracza
                    return false;
                }                                                                      
    }
}

?>