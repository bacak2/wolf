<?php
/**
 * Moduł użytkownicy
 *
 * @author		Michal Bugański <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2012
 * @package		Panelplus 
 * @version		1.0
 */

class ModulUzytkownicy extends ModulBazowy {
        public $Provinces = false;
        public $Companies = false;
        public $Roles = false;
        private $devices_ids = array();
        private $users_certificate_category = array();
        /**
         * tablica z idkami użytkowników którzy mają jakieś wykonane zlecenie
         * @var array
         */
        private $users_with_orders = array();

        /**
         * Zmienna ustawiona czy dostęp do całego modułu czy tylko do własnych danych + jeżeli upoważniony do danych firmy
         * @var boolean
         */
        public $only_profile = false;

	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->Tabela = 'users';
            $this->PoleID = 'id';
            $this->PoleNazwy = "CONCAT(firstname,' ',surname) as imie_nazwisko";
            $this->PoleSort = "surname ASC, firstname";
            $this->Nazwa = 'Użytkownicy';
            $this->Filtry[] = array('nazwa' => 'province_id', 'opis' => 'Województwo', 'opcje' => $this->GetProvinces(), "typ" => "lista", "domyslna" => "wojewodztwo");
            $this->Filtry[] = array('nazwa' => 'role', 'opis' => 'Rola', 'opcje' => $this->GetRoles(), "typ" => "lista", "domyslna" => "rola");
            $this->Filtry[] = array('nazwa' => 'company_id', 'opis' => 'Firma', 'opcje' => $this->GetCompanies(), "typ" => "lista", "domyslna" => "firma");
            $this->Filtry[] = array('nazwa' => 'firstname|surname|email|phone', 'opis' => false, "typ" => "tekst", "placeholder" => "Znajdź użytkownika", 'search' => true);
            if(isset($_GET['sel_firm']) && is_numeric($_GET['sel_firm'])){
                $_SESSION['Filtry']['company_id'] = $_GET['sel_firm'];
            }
            $this->users_with_orders = $this->Baza->GetValues("SELECT DISTINCT(user_id) FROM packages WHERE status > 0");
            if(!empty($this->users_with_orders))
                foreach($this->users_with_orders as $blocked_id){
                    if(!in_array($blocked_id , $this->ZablokowaneElementyIDs['kasowanie']))
                        $this->ZablokowaneElementyIDs['kasowanie'][] = $blocked_id ;
                }
            $this->ErrorsDescriptions['unikalne']['email'] = "Użytkownik o tym adresie email istnieje już w bazie danych";
            $this->PrzyciskiFormularza['zapisz']['onclick'] = "";
            if($this->Uzytkownik->FirmaRMC($this->Uzytkownik->ZwrocIdUzytkownika())){
                $this->ModulyBezKasowanie[] = $this->Parametr;
                $this->ModulyBezDodawania[] = $this->Parametr;
//                $this->ModulyBezEdycji[] = $this->Parametr;
            }
                $this->ModulyBezMenuBocznego[] = $this->Parametr;            
            
	}

        function &GenerujFormularz(&$Wartosci = array(), $Mapuj = false){
            $Formularz = parent::GenerujFormularz($Wartosci, $Mapuj);
            $Formularz->DodajPole('firstname', 'tekst', 'Imię*', array('show_label'=>true,'div_class'=>'super-form-span4',));
            $Formularz->DodajPole('surname', 'tekst', 'Nazwisko*', array('show_label'=>true,'div_class'=>'super-form-span4',));
            $Formularz->DodajPole('email', 'tekst', 'E-mail*', array('show_label'=>true,'div_class'=>'super-form-span4',));
            $Formularz->DodajPole('phone', 'tekst', 'Telefon', array('show_label'=>true,'div_class'=>'super-form-span4',));
            $Formularz->DodajPole('clear', 'clear', '', array());
            if($this->only_profile == false){
                $Formularz->DodajPole('role', 'lista-chosen-single', 'Rola*', array( 'elementy' => $this->GetRoles(), 'id' => 'user_role', 'show_label'=>true,'div_class'=>'super-form-span4',));
                $Formularz->DodajPole('province_id', 'lista-chosen-single', 'Wojewodztwo', array( 'elementy' => $this->GetProvinces(), 'wybierz' => true, 'domyslna' => '-- województwo --', 'show_label'=>true,'div_class'=>'super-form-span4',));
                $Formularz->DodajPole('company_id', 'lista-chosen-single', 'Firma', array('id' => 'wybor-firmy', 'elementy' => $this->GetCompanies(), 'wybierz' => true, 'domyslna' => '-- firma --','show_label'=>true,'div_class'=>'super-form-span4',));
                $Formularz->DodajPole('devices_ids', 'lista-chosen', 'Grupa Urządzeń', array('id' => 'wybor-urzadzenia', 'elementy' => $this->GetDevices(), 'wybierz' => true, 'domyslna' => '-- Urządzenia --','show_label'=>true,'div_class'=>'super-form-span4',));
                $Formularz->DodajPole('clear', 'clear', '', array());
                
                $Formularz->DodajPole('entitled', 'checkbox', 'Upoważniony', array('show_label'=>true,'div_class'=>'super-form-span4',));
                $Formularz->DodajPole('active', 'checkbox', 'Aktywny', array('show_label'=>true,'div_class'=>'super-form-span4',));
                $Formularz->DodajPole('clear', 'clear', '', array());
                
                $Formularz->DodajPole('notes', 'tekst_dlugi', 'Opis', array('show_label'=>true,'div_class'=>'super-form-span1',));
                $Formularz->DodajPole('clear', 'clear', '', array());
                
                $Formularz->DodajPoleWymagane("role", false);
	    }
	    if(in_array($this->WykonywanaAkcja, array("edycja", "dodawanie")) && $this->only_profile == false){
                $Formularz->DodajPole('users_certificate_category', 'uprawnienia', 'Uprawnienia', array('show_label'=>true,'div_class'=>'super-form-span1', 'elementy'=>$this->GetSelect('Kategorie', false, "certificate_category = '1'")));
                $Formularz->DodajPole('clear', 'clear', '', array());                
            }elseif(in_array($this->WykonywanaAkcja, array("szczegoly")) && $this->only_profile == true){
                $Formularz->DodajPole('users_certificate_category', 'uprawnienia', 'Uprawnienia', array('show_label'=>true,'div_class'=>'super-form-span1', 'elementy'=>$this->GetSelect('Kategorie', false, "certificate_category = '1'")));
                $Formularz->DodajPole('clear', 'clear', '', array());                
	    }
            
            $Formularz->DodajPole('haslo', 'haslo', 'Hasło'.($this->WykonywanaAkcja == "dodawanie" ? "*" : " <small>Jeżeli nie chcesz zmienić pozostaw puste</small>"), array('div_class'=>'super-form-span1'));
            $Formularz->DodajPole('clear', 'clear', '', array());
            
            $Formularz->DodajPoleWymagane("surname", false);
            $Formularz->DodajPoleWymagane("firstname", false);
            $Formularz->DodajPoleWymagane("email", false);
            if($this->WykonywanaAkcja == "dodawanie"){
                $Formularz->DodajPoleWymagane("haslo", false);
            }
            $Formularz->DodajDoPolBezRamki('haslo');
            $Formularz->DodajDoPolBezRamki('users_certificate_category');
            $Formularz->DodajDoPolBezRamki('entitled');
            $Formularz->DodajDoPolBezRamki('active');
            $Formularz->DodajPoleNieDublujace("email", false);
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            
            
            return $Formularz;
	}

        function PobierzListeElementow($Filtry = array()) {
            $Wyniki = array(
                    $this->PoleID => "ID",
                    'surname' => 'Nazwisko',
                    'firstname' => 'Imię',
                    'email' => 'Email',
                    'role' => array('naglowek' => 'Rola', 'elementy' => $this->GetRoles()),
                    'active' => array('naglowek' => 'Aktywny', 'elementy' => Usefull::GetCheckImg(), 'td_class' => 'active-check'),
                    'entitled' => array('naglowek' => 'Upoważ.', 'elementy' => Usefull::GetCheckImg(), 'td_class' => 'active-check'),
            );
            $Where = $this->GenerujWarunki();
            $Sort = $this->GenerujSortowanie();
            $this->Baza->Query($this->QueryPagination("SELECT *, $this->PoleNazwy FROM $this->Tabela f $Where $Sort", $this->ParametrPaginacji, 30));
            return $Wyniki;
	}

        function GetActions($ID){
            $Akcje = array();
            $Dodawanie = array('link' => "?modul=$this->Parametr&akcja=dodawanie", 'class' => 'dodawanie', 'img' => 'add.gif', 'desc' => 'Nowy użytkownik');
            $Lista = array('link' => "?modul=$this->Parametr&akcja=lista", 'class' => 'lista', 'img' => 'text_list_bullets.gif', 'desc' => 'Lista Użytkowników');
            $Akcje['akcja'][] = $Dodawanie;
            $Akcje['akcja'][] = $Lista;          
//            $Akcje['szczegoly'][] = array('link' => "?modul=$this->Parametr&akcja=edycja&id=$ID&back=details", 'class' => 'edycja', 'img' => 'pencil.gif', 'desc' => 'Edycja');
               
            return $Akcje;
        }
        
        function GetProvinces(){
            if($this->Provinces == false){
                $this->Provinces = Wojewodztwa::GetProvinces($this->Baza);
            }
            return $this->Provinces;
        }

        function GetCompanies(){
            if($this->Companies == false){
                $ModulFirmy = new ModulFirmy($this->Baza, $this->Uzytkownik, "test123", $this->Sciezka);
                $this->Companies = $ModulFirmy->GetList();
            }
            return $this->Companies;
        }

        function GetRoles(){
            if($this->Roles == false){
                $this->Roles = Role::GetRoles($this->Baza);
            }
            return $this->Roles;
        }

        function PobierzDaneElementu($ID, $Typ = null) {
            $Dane = parent::PobierzDaneElementu($ID, $Typ);
            $Dane['expire_date'] = date("Y-m-d", strtotime($Dane['expire_date']));
            $Dane['devices_ids'] = $this->Baza->GetValues("SELECT users_devices_devices_id FROM users_devices WHERE users_devices_users_id = '$ID'");
            $Dane['users_certificate_category'] = $this->Baza->GetResultAsArray("SELECT users_certificate_category_certificate_category_id as id, users_certificate_category_date as date FROM users_certificate_category WHERE users_certificate_category_users_id = '$ID'", 'id');
            return $Dane;
        }

        function OperacjePrzedZapisem($Wartosci, $ID){
            if(isset($Wartosci['haslo']) && $Wartosci['haslo'] != ""){
                $Wartosci['encrypted_new_password'] = md5($Wartosci['haslo']);
            }
            unset($Wartosci['haslo']);
            $Wartosci['verification_hash'] = $this->Uzytkownik->WyznaczHash($Wartosci['email']);
            if(isset($Wartosci['devices_ids'])){
                $this->devices_ids = $Wartosci['devices_ids'];
                unset($Wartosci['devices_ids']);
            }else{
                $this->devices_ids = false;
            }
            if(isset($Wartosci['users_certificate_category'])){
                $this->users_certificate_category = $Wartosci['users_certificate_category'];
                unset($Wartosci['users_certificate_category']);
            }else{
                $this->users_certificate_category = false;
            }
            if($ID){
                $Values = $this->PobierzDaneElementu($ID);
                $users_certificate_category_to_del = $this->users_certificate_category;
                if( isset($users_certificate_category_to_del['add']) ) unset ($users_certificate_category_to_del['add']);
                foreach($Values['users_certificate_category'] as $UCCKey=>$UCCValue){
                    if(array_key_exists($UCCKey, $users_certificate_category_to_del)){
                    $Update = $Where = array();
                        if($UCCValue['date'] != $users_certificate_category_to_del[$UCCKey]['date'])
                        {
			    $Where['users_certificate_category_certificate_category_id'] = $users_certificate_category_to_del[$UCCKey]['id'];
			    $Where['users_certificate_category_users_id'] = $ID;
			    $Update['users_certificate_category_date'] = $users_certificate_category_to_del[$UCCKey]['date'];
                            $UpdateQueryUsersCertificate = $this->Baza->PrepareUpdate('users_certificate_category', $Update, $Where);
                            $this->Baza->Query($UpdateQueryUsersCertificate);
                        }
                    }else{
                        $this->Baza->Query("DELETE FROM users_certificate_category WHERE users_certificate_category_users_id='$ID' AND users_certificate_category_certificate_category_id='{$UCCKey}'");
                    }
                }
                foreach($Wartosci as $FieldID => $Value){
                    if($Value != $Values[$FieldID]){
                        $Wartosci['updated_at'] = date("Y-m-d H:i:s");
                        break;
                    }
                }
            }else{
                $Wartosci['created_at'] = date("Y-m-d H:i:s");
                $Wartosci['updated_at'] = date("Y-m-d H:i:s");
            }
            return $Wartosci;
        }

        function WykonajOperacjePoZapisie($Wartosci, $ID){ 
            $this->Baza->Query("DELETE FROM users_devices WHERE users_devices_users_id = '$ID'");
            if(is_array($this->devices_ids)){
                foreach($this->devices_ids as $DevID){
                    if(intval($DevID) > 0){
                        $Save['users_devices_devices_id'] = $DevID;
                        $Save['users_devices_users_id'] = $ID;
                        $Zapytanie = $this->Baza->PrepareInsert('users_devices', $Save);
                        $this->Baza->Query($Zapytanie);
                    }
                }
	    }
            if(is_array($this->users_certificate_category['add'])){
                $Save = array();
                foreach($this->users_certificate_category['add'] as $CertID=>$CertVal){
                    if(intval($CertVal['id']) > 0){
                        $Save['users_certificate_category_certificate_category_id'] = $CertVal['id'];
                        $Save['users_certificate_category_date'] = $CertVal['date'];
                        $Save['users_certificate_category_users_id'] = $ID;
                        $Zapytanie = $this->Baza->PrepareInsert('users_certificate_category', $Save);
                        $this->Baza->Query($Zapytanie);
                    }
                }
            }
        }

        function Wyswietl($Akcja) {
            if($Akcja == "kasowanie" && intval($_GET['id']) != 0 && in_array(intval($_GET['id']), $this->ZablokowaneElementyIDs['kasowanie'])){
                if(in_array($_GET['id'], $this->users_with_orders)){
                    $this->Error = "<b>Nie możesz usunąć użytkownika, który wykonał zlecenie</b>";
                }
            }
            if($Akcja == "dodawanie" || $Akcja == "edycja"){
                ?><script type='text/javascript' src='js/firm-choose.js'></script><?php
            }
            parent::Wyswietl($Akcja);
        }
        
        function GetListWithCompanies(){
            return $this->Baza->GetOptions("SELECT u.$this->PoleID, CONCAT(firstname,' ',surname,IF(company_id > 0,CONCAT(' [',c.simplename,']'),'')) FROM $this->Tabela u LEFT JOIN companies c ON(c.id = u.company_id) ORDER BY surname ASC, firstname ASC");
        }
        
        function GetDevices(){
            return $this->GetSelect('Urzadzenia', null, null);
            
        }
        
                /**
 * funkcja wywoływana podczas akcji "przeładowania" wykonująca operację na danych z formularza
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus Ferroli
 * @version		1.0
 * @param array $Wartosci Wartości formularza
 * @param object $Formularz  obiekt klasy Formularz
 * @param integer $ID ID elementu w bazie
 * @return array zwraca wartości formularza
 */
        
        public function AkcjaPrzeladowanie($Wartosci, $Formularz, $ID = null) {
            $OpcjaFormularza = $Formularz->ZwrocNazwePola($_POST['OpcjaFormularza']);
            if($OpcjaFormularza == 'users_certificate_category'){
                $params = $this->GetSelect('Kategorie');
            if( isset($Wartosci[$OpcjaFormularza]) && is_array($Wartosci[$OpcjaFormularza]) ){
                    if( !array_key_exists(  $_POST[$_POST['OpcjaFormularza'].'_add'] ,
                                            (isset($Wartosci[$OpcjaFormularza]['add']) ? $Wartosci[$OpcjaFormularza]['add'] : array() ) )
                            //&& !in_array(array('id'=>$_POST[$_POST['OpcjaFormularza'].'_add'], 'date'=>$_POST[$_POST['OpcjaFormularza'].'_date'] ) , $Wartosci[$OpcjaFormularza]) 
                            && array_key_exists($_POST[$_POST['OpcjaFormularza'].'_add'], $params)){
                        $Wartosci[$OpcjaFormularza]['add'][$_POST[$_POST['OpcjaFormularza'].'_add']]['id'] = $_POST[$_POST['OpcjaFormularza'].'_add'];
                        $Wartosci[$OpcjaFormularza]['add'][$_POST[$_POST['OpcjaFormularza'].'_add']]['date'] = $_POST[$_POST['OpcjaFormularza'].'_date'];
                    }
                }else{
                    if(array_key_exists($_POST[$_POST['OpcjaFormularza'].'_add'], $params)
                            && !array_key_exists($_POST[$_POST['OpcjaFormularza'].'_add'], (isset($Wartosci[$OpcjaFormularza]) ? $Wartosci[$OpcjaFormularza] : array() )) )
                        $Wartosci[$OpcjaFormularza]['add'][$_POST[$_POST['OpcjaFormularza'].'_add']]['id'] = $_POST[$_POST['OpcjaFormularza'].'_add'];
                        $Wartosci[$OpcjaFormularza]['add'][$_POST[$_POST['OpcjaFormularza'].'_add']]['date'] = $_POST[$_POST['OpcjaFormularza'].'_date'];
                }
                return $Wartosci; 
            }
        }
}

?>