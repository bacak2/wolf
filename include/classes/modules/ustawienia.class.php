<?php
/**
 * Moduł ustawienia
 *
 * @author		Michal Bugański <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2012
 * @package		Panelplus 
 * @version		1.0
 */

class ModulUstawienia extends ModulBazowy {

	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->ModulyBezMenuBocznego[] = $this->Parametr;
            $this->Tabela = 'settings';
            $this->PoleID = 'id';
            $this->PoleNazwy = 'var';
            $this->Nazwa = 'Ustawienia';
            
	}

        function PobierzDaneElementu($ID, $Typ = NULL){
            $vars = $this->GetAllVars();
            $Dane = array();
            foreach($vars as $var){
                $Dane[$var['var']] = $var['value'];
            }
            return $Dane;
            
        }
        
        function GetAllVars(){
            return $this->Baza->GetResultAsArray("SELECT id, var, value, value_default, description FROM $this->Tabela ORDER BY $this->PoleID ASC", $this->PoleID);
        }

        function &GenerujFormularz(&$Wartosci = array(), $Mapuj = false) {
            $Formularz = parent::GenerujFormularz($Wartosci, $Mapuj);
            $vars = $this->GetAllVars();
            foreach($vars as $var){
                $exp = explode('_',$var['var']);
                if( isset($NameVar ) && $NameVar != $exp[0] ){
                    $Formularz->DodajPole('clear', 'clear', false);
                }
                $NameVar = $exp[0];
                $Formularz->DodajPole($var['var'], 'tekst', $var['description'].'<br/><small>defaults: '.$var['value_default'].'</small>', array('show_label'=>true,'div_class'=>'super-form-span5' ));
            }
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
        }
        
        function ZapiszDaneElementu($Formularz, $Wartosci, &$PrzeslanePliki = null, $ID = null) {
            $this->LinkPowrotu = "?modul=$this->Parametr";
            $Vars = $this->GetAllVars();
            $NoError = true;
            foreach($Vars as $FieldID => $Value){
                $Save = array();
                ### Sprawdzamy czy nastąpiła zmiana w polu
                if($Wartosci[$Vars[$FieldID]['var']] != $Vars[$FieldID]['value']){
                    $Save['updated_at'] = date("Y-m-d H:i:s");
                    $Save['value'] = $Wartosci[$Vars[$FieldID]['var']];
                    $Zap = $this->Baza->PrepareUpdate($this->Tabela, $Save, array($this->PoleID => $Value[$this->PoleID]));
                    if(!$this->Baza->Query($Zap)){
                        $NoError = false;
                    }
                }
            }
            if($NoError){
                Usefull::ShowKomunikatOK("Zmiany zostały zapisane");
                return true;
            }else{
                Usefull::ShowKomunikatError("Wystąpił błąd! Część zmian nie została zapisana");
                return false;
            }
        }
        /**
 * funkcja ustawiająca przyciski z dodatkowymi akcjami dla określonej akcji.
 *
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus
 * @version		1.0
 * @param integer $ID ID elementu w bazie
 */

        function GetActions($ID){
            $ListaWymagan = array('link' => "?modul=wymagania&akcja=lista&typ=wymagania", 'class' => 'lista', 'img' => 'list.gif', 'desc' => 'Lista wymagań');
            $DodajWymaganie = array('link' => "?modul=wymagania&akcja=dodawanie&typ=wymagania", 'class' => 'dodawanie', 'img' => 'add.gif', 'desc' => 'Dodaj wymaganie');
            $ListaKategoriiWymagan = array('link' => "?modul=kategorie_wymagan&akcja=lista&typ=kategoriawymagan", 'class' => 'lista', 'img' => 'list.gif', 'desc' => 'Lista kategorii wymagań');
            $DodajKategorieWymagan = array('link' => "?modul=kategorie_wymagan&akcja=dodawanie&typ=kategoriawymagan", 'class' => 'dodawanie', 'img' => 'add.gif', 'desc' => 'Dodaj kategorię wymagań');
            $ListaKategoriiCzesci = array('link' => "?modul=kategorie_czesci&akcja=lista&typ=kategoriaczesci", 'class' => 'lista', 'img' => 'list.gif', 'desc' => 'Lista kategorii części');
            $DodajKategorieCzesci = array('link' => "?modul=kategorie_czesci&akcja=dodawanie&typ=kategoriaczesci", 'class' => 'dodawanie', 'img' => 'add.gif', 'desc' => 'Dodaj kategorię części');
            $Konfiguracja = array('link' => "?modul=$this->Parametr&akcja=edycja", 'class' => 'edycja', 'img' => 'pencil.gif', 'desc' => 'Konfiguracja');
            $GrupyCenowe = array('link' => "?modul=grupy_cenowe&akcja=edycja", 'class' => 'edycja', 'img' => 'pencil.gif', 'desc' => 'Grupy cenowe');
            $ListaRMC = array('link' => "?modul=rmc&akcja=lista&typ=rmc", 'class' => 'lista', 'img' => 'list.gif', 'desc' => 'Lista RMC');
            $DodajRMC = array('link' => "?modul=rmc&akcja=dodawanie&typ=rmc", 'class' => 'dodawanie', 'img' => 'add.gif', 'desc' => 'Dodaj woj do RMC');
            $ImportujFirmy = array('link' => "?modul=firmy&akcja=import", 'class' => 'dodawanie', 'img' => 'add.gif', 'desc' => 'Import firm serwisowych');
            $ImportujUrzadzenia = array('link' => "?modul=urzadzenia&akcja=import", 'class' => 'dodawanie', 'img' => 'add.gif', 'desc' => 'Import urządzeń');
            $ImportujNumerySeryjne = array('link' => "?modul=numery_seryjne&akcja=import", 'class' => 'dodawanie', 'img' => 'add.gif', 'desc' => 'Import numerów seryjnych');
            $ImportujUruchomienia = array('link' => "?modul=zlecenia&akcja=import", 'class' => 'dodawanie', 'img' => 'add.gif', 'desc' => 'Import uruchomień');

            $Akcje['akcja'] = array( $Konfiguracja, $GrupyCenowe, $ListaKategoriiCzesci, $DodajKategorieCzesci, $ListaRMC, $DodajRMC,
                        $ListaWymagan, $DodajWymaganie, $ListaKategoriiWymagan, $DodajKategorieWymagan, $ImportujFirmy, $ImportujUrzadzenia, $ImportujNumerySeryjne, $ImportujUruchomienia);
            return $Akcje;
        }
        
        function AkcjaLista($Filtry = array()) {
            parent::AkcjaLista($Filtry);
        }

}
?>
