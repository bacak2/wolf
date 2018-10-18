<?php
/**
 * Moduł danych firmy zalogowanego użytkownika
 * 
 * @author		Michal Bugański <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2012
 * @package		Panelplus 
 * @version		1.0
 */

class ModulDaneFirmy extends ModulFirmy {

	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->only_profile = true;
            $this->FiltersAndActionsWidth = true;
	}

        function GetActions($ID){
                $Akcje['edycja'][] = array('link' => "?modul=$this->Parametr", 'class' => 'edycja', 'img' => 'arrow_left.gif', 'desc' => 'Powrót');

            if($this->Uzytkownik->CzyUprawniony()){
                $Akcje['szczegoly'][] = array('link' => "?modul=uzytkownik", 'class' => 'edycja', 'img' => 'arrow_left.gif', 'desc' => 'Powrót');
                if($ID == $this->Uzytkownik->ZwrocInfo('company_id')){
                    $Akcje['szczegoly'][] = array('link' => "?modul=$this->Parametr&akcja=edycja", 'class' => 'edycja', 'img' => 'pencil.gif', 'desc' => 'Edycja');
                }
            }
            return $Akcje;
        }

        function  Wyswietl($Akcja) {
            $this->WykonywanaAkcja = ($Akcja == "edycja" && $this->Uzytkownik->CzyUprawniony() ? "edycja" : "szczegoly");
            if($this->Uzytkownik->FirmaRMC($this->UserID) && isset($_GET['id'])){
                 $this->WykonywanaAkcja = "szczegoly";
            }else{
                $_GET['id'] = $this->Uzytkownik->ZwrocInfo("company_id");
            }
            parent::Wyswietl($this->WykonywanaAkcja);
        }
}
?>
