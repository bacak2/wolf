<?php
/**
 * Moduł profilu użytkownika
 * 
 * @author		Michal Bugański <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2012
 * @package		Panelplus 
 * @version		1.0
 */

class ModulProfilUzytkownika extends ModulUzytkownicy {

	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->only_profile = true;
            $this->FiltersAndActionsWidth = true;
	}

        function GetActions($ID){
//            $Akcje['edycja'][] = array('link' => "?modul=$this->Parametr", 'class' => 'edycja', 'img' => 'arrow_left.gif', 'desc' => 'Powrót');
//            $Akcje['szczegoly'][] = array('link' => "?modul=$this->Parametr&akcja=edycja", 'class' => 'edycja', 'img' => 'pencil.gif', 'desc' => 'Edycja');
            $Akcje['szczegoly'][] = array('link' => "?modul=$this->Parametr&akcja=edycja", 'class' => 'edycja', 'img' => 'pencil.gif', 'desc' => 'Edycja');
//            $Akcje['szczegoly'][] = array('link' => "?modul=lista_szkolen", 'class' => 'lista', 'img' => 'pencil.gif', 'desc' => 'Lista aktualnych szkoleń');
            return $Akcje;
        }

        function  Wyswietl($Akcja) {
            $this->WykonywanaAkcja = ($Akcja == "edycja" ? "edycja" : "szczegoly");
            $_GET['id'] = $this->UserID;
            parent::Wyswietl($this->WykonywanaAkcja);
        }

}
?>