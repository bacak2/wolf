<?php
/**
 * Moduł ustawienia
 * 
 * @author		Michal Bugański <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2012
 * @package		Panelplus 
 * @version		1.0
 */

class ModulFirmy extends ModulBazowy {
        public $Provinces = false;

        /**
         * tablica zawierająca idki firm mających użytkowników
         * @var array
         */
        private $companies_with_users = array();

         /**
         * Zmienna ustawiona czy dostęp do całego modułu czy tylko do danych swojej firmy
         * @var boolean
         */
        public $only_profile = false;

    protected $allowedImportTypes;
    protected $errors;
    protected $columnsFormat;
    protected $importTitle;

	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->Tabela = 'companies';
            $this->PoleID = 'id';
            $this->PoleNazwy = 'simplename';
            $this->Nazwa = 'Firmy';
            $this->ModulyBezMenuBocznego[] = $this->Parametr;
            $this->Filtry[] = array('nazwa' => 'province_id', 'opis' => 'Województwo', 'opcje' => $this->GetProvinces(), "typ" => "lista", "domyslna" => "wojewodztwo");
            $this->Filtry[] = array('nazwa' => 'simplename|name|city', 'opis' => false, "typ" => "tekst", "placeholder" => "Znajdź firmę", 'search' => true);
            $this->companies_with_users = $this->Baza->GetValues("SELECT DISTINCT(company_id) FROM users");
            foreach($this->companies_with_users as $blocked_id){
                if(!in_array($blocked_id , $this->ZablokowaneElementyIDs['kasowanie']))
                    $this->ZablokowaneElementyIDs['kasowanie'][] = $blocked_id ;
            }
            $this->ErrorsDescriptions['unikalne']['nip'] = "Firma o numerze NIP: %s już istnieje w bazie";
            $this->allowedImportTypes = array('xls','xlsx' ,'ods');
            $this->columnsFormat = '<tr><td>Firma</td><td>Adres</td><td>Telefon stacjonarny</td><td>Telefon komórkowy</td><td>Fax</td><td>Mail</td><td>www</td><td>Województwo</td><td>Autoryzacja</td></tr>';
            $this->importTitle = 'firmy serwisowe';
}

        function &GenerujFormularz(&$Wartosci = Array(), $Mapuj = false) {
            $Formularz = parent::GenerujFormularz($Wartosci, $Mapuj);
            $Formularz->DodajPole('name', 'tekst_dlugi', 'Nazwa*', array('show_label'=>true, 'tooltip'=>'Pełna nazwa firmy'));
            $Formularz->DodajPole('clear', 'clear', '', array());
            $Formularz->DodajPole('simplename', 'tekst', 'Skrót*', array('show_label'=>true,'div_class'=>'super-form-span4', 'tooltip'=>'Skrót nazwy uzywany w listach wyboru, ograniczony do 40 znaków'));
            $Formularz->DodajPole('phone_number', 'tekst', 'Telefon', array('show_label'=>true,'div_class'=>'super-form-span4', 'tooltip'=>'Telefon formy'));
            $Formularz->DodajPole('nip', 'tekst', 'NIP*', array('show_label'=>true,'div_class'=>'super-form-span4', 'tooltip'=>'Nip firmy, możliwość wprowadzania tylko liczb',));
            $Formularz->DodajPole('province_id', 'lista', 'Wojewodztwo', array('show_label'=>true,'elementy' => $this->GetProvinces(),'div_class'=>'super-form-span4',));
            $Formularz->DodajPole('clear', 'clear', '', array());
            $Formularz->DodajPole('street', 'tekst', 'Ulica', array('show_label'=>true,'div_class'=>'super-form-span4',));
            $Formularz->DodajPole('postcode', 'tekst', 'Kod pocztowy', array('show_label'=>true,'div_class'=>'super-form-span4',));
            $Formularz->DodajPole('city', 'tekst', 'Miasto', array('show_label'=>true,'div_class'=>'super-form-span4',));
            $Formularz->DodajPole('country', 'tekst', 'Kraj', array('show_label'=>true,'div_class'=>'super-form-span4',));
            $Formularz->DodajPole('clear', 'clear', '', array());
            
             if($this->only_profile == false){
                $Formularz->DodajPole('notes', 'tekst_dlugi', 'Opis', array('show_label'=>true,));
             }
                $Formularz->DodajPole('TerminUprawnienText', 'brak', 'Termin Uprawnien', array('show_label'=>true,));
                $Formularz->DodajPole('TerminUprawnien', 'sam_tekst', 'Pola teminu uprawnień pojwią się w momencie powstania modułu kategorii z którym jest połączony', array('show_label'=>false, 'atrybuty'=>array('style'=>'color: silver;')));
            if($this->only_profile == false){
                $Formularz->DodajPole('discount', 'tekst', 'Rabat (%)', array('show_label'=>true, 'decimal' => 'tak','div_class'=>'super-form-span8',));
             }

            $Formularz->DodajDoPolBezRamki('TerminUprawnien');
            $Formularz->DodajDoPolBezLabel('TerminUprawnien');
            $Formularz->DodajDoPolBezRamki('TerminUprawnienText');
            
            $Formularz->DodajPoleWymagane('name', false);
            $Formularz->DodajPoleWymagane('nip', false);
            $Formularz->DodajPoleNieDublujace('nip', false);
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}

        function PobierzListeElementow($Filtry = array()) {
            $Wyniki = array(
                    $this->PoleID => "ID",
                    'name' => array('naglowek' => 'Nazwa', 'td_styl' => 'width: 200px;'),
                    'simplename' => 'Skrót nazwy',
                    'phone_number' => 'Telefon',
                    'province_id' => array('naglowek' => 'Województwo', 'elementy' => $this->GetProvinces()),
                    'city' => 'Miasto',
                    'user_count' => array('naglowek' => '<img class="inlineimg" alt="Użytkownicy" src="images/user.png">', 'td_styl' => 'text-align: center;'),
                    'tr_attr' => array( 'element_name'=>  $this->PoleID, 'attr'=>array('hidden-class'=>'hidden')),
                    'tr_class' => 'unhidden',
            );
            $Where = $this->GenerujWarunki();
            $Sort = $this->GenerujSortowanie();
            $this->Baza->Query($this->QueryPagination("SELECT $this->PoleID, name, simplename, phone_number, province_id, city, (SELECT COUNT(*) FROM users WHERE company_id = f.$this->PoleID) as user_count FROM $this->Tabela f $Where $Sort", $this->ParametrPaginacji, 30));
            return $Wyniki;
	}

        function WyswietlPanel($ID = null) {
            $History = $this->Baza->GetRows("SELECT $this->PoleID, $this->PoleNazwy, updated_at FROM $this->Tabela ORDER BY updated_at DESC LIMIT 5");
            include(SCIEZKA_SZABLONOW."panel-boczny/firmy.tpl.php");
        }

        function GetActions($ID){
            $Akcje = array();
            if($this->Uzytkownik->IsAdmin()){
                $Dodawanie = array('link' => "?modul=$this->Parametr&akcja=dodawanie", 'class' => 'dodawanie', 'img' => 'add.gif', 'desc' => 'Utwórz firmę');
                $Lista = array('link' => "?modul=$this->Parametr&akcja=lista", 'class' => 'lista', 'img' => 'text_list_bullets.gif', 'desc' => 'Lista Firm');
                $Akcje['akcja'][] = $Dodawanie;
                $Akcje['akcja'][] = $Lista;
//                $Akcje['lista'][] = $Dodawanie;
//                $Akcje['edycja'][] = $Dodawanie;
//                $Akcje['szczegoly'][] = $Dodawanie;
//                $Akcje['szczegoly'][] = array('link' => "?modul=$this->Parametr&akcja=edycja&id=$ID&back=details", 'class' => 'edycja', 'img' => 'pencil.gif', 'desc' => 'Edycja');
                $Akcje['szczegoly'][] = array('link' => "?modul=uzytkownicy&akcja=lista&sel_firm=$ID", 'img' => 'user.png', 'desc' => 'Użytkownicy');
            }
            return $Akcje;
        }

        function GetProvinces(){
            if($this->Provinces == false){
                $this->Provinces = Wojewodztwa::GetProvinces($this->Baza);
            }
            return $this->Provinces;
        }

        function OperacjePrzedZapisem($Wartosci, $ID){
            $Wartosci['nip'] = Usefull::FormatNip($Wartosci['nip']);
            if($ID){
                $Values = $this->PobierzDaneElementu($ID);
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

        function Wyswietl($Akcja) {
            if($Akcja == "kasowanie" && intval($_GET['id']) != 0 && in_array(intval($_GET['id']), $this->ZablokowaneElementyIDs['kasowanie'])){
                if(in_array($_GET['id'], $this->companies_with_users)){
                    $this->Error = "<b>Nie możesz usunąć firmy, do której podpięty jest użytkownik</b>";
                }
            }
            parent::Wyswietl($Akcja);
        }

        function  SprawdzDane($Formularz, $Wartosci, $ID = null) {
            if(Usefull::CheckNIP($Wartosci['nip']) == false){
                $this->Error = "Wprowadzony numer NIP jest nieprawidłowy";
                $this->PolaWymaganeNiewypelnione[] = "nip";
                return false;
            }
            return true;
        }

        /**
         * Sprawdza czy nie zostały wprowadzone dane które powinny być unikalne a są zdublowane
         * @param array $TablicaPolNieDublujacych
         * @param array $TablicaWartosci
         * @param integer $ID - id elementu, który wyłączamy ze sprawdzenia
         * @return boolean
         */
	function SprawdzPolaNieDublujace($TablicaPolNieDublujacych, $TablicaWartosci, $ID = null){
		$WhereID = !is_null($ID) ? "AND $this->PoleID != '$ID'" : "";
		foreach($TablicaPolNieDublujacych as $NazwaPola){
                        if($NazwaPola == "nip"){
                            $TablicaWartosci[$NazwaPola] = Usefull::FormatNip($TablicaWartosci[$NazwaPola]);
                        }
			if($this->Baza->GetValue("SELECT COUNT(*) FROM $this->Tabela WHERE $NazwaPola = '{$TablicaWartosci[$NazwaPola]}' $WhereID") > 0){
				$this->PolaZdublowane[] = $NazwaPola;
                                if(isset($this->ErrorsDescriptions['unikalne'][$NazwaPola])){
                                    $this->Error = sprintf($this->ErrorsDescriptions['unikalne'][$NazwaPola], $TablicaWartosci[$NazwaPola]);
                                    return false;
                                }
			}
		}
		if(count($this->PolaZdublowane) > 0){
                        $this->Error = "Wprowadzona wartość już istnieje w bazie danych.";
			return false;
		}else{
			return true;
		}
	}

        function ObrobkaDanychLista($Elementy) {
//            $this->VAR_DUMP($Elementy, true);
            foreach($Elementy as $Idx => $Element){
                $Exp = explode(" ", $Element['name']);
                $Name = array();
                $Licz = 0;
                foreach($Exp as $String){
                    $Name[] = $String;
                    $Licz += strlen($String);
                    if($Licz > 20){
                        break;
                    }
                }
                $Elementy[$Idx]['name'] = implode(" ", $Name).(count($Name) != count($Exp) ? "..." : "");
                $Elementy[$Idx]['user_count'] = $Element['user_count'].'';
                $Elementy[$Idx]['hidden-tr'] = $this->Baza->GetRows("SELECT email, phone, role, firstname, surname, active FROM users WHERE company_id = '{$Element[$this->PoleID]}'");
            }
            return $Elementy;
        }
        
        
        function ShowTR($Pola, $Element, $Licznik, $AkcjeNaLiscie, $TR_APPS = null ){
            
            parent::ShowTR($Pola, $Element, $Licznik, $AkcjeNaLiscie, $TR_APPS);
            $Colspan = count($Pola) + count($AkcjeNaLiscie);
            $HideTRClassName = $this->PoleID;
            $PolaDwa['surname'] = 'Nazwisko';
            $PolaDwa['firstname'] = 'Imię';
            $PolaDwa['email'] = 'Email';
            $PolaDwa['phone'] = 'Telefon';
            $PolaDwa['role'] = array('naglowek' => 'Rola', 'elementy' => Role::GetRoles($this->Baza));
            $PolaDwa['active'] = array('naglowek' => 'Aktywny', 'elementy' => Usefull::GetTakNie());
            $Dane['array'] = $Element['hidden-tr'];
            $this->include_tpls = "show-table.tpl.php";
            include(SCIEZKA_SZABLONOW."hidden-tr.tpl.php");
        }
          function  AkcjaLista($Filtry = array()) {
            $this->clickable_rows = false;
            include(SCIEZKA_SZABLONOW."firmy-js-scripts.tpl.php");
            parent::AkcjaLista($Filtry);
        }


    function putImportedElements($objPHPExcel, $rowNumber){

        $data = array(
            'name' => $objPHPExcel->getActiveSheet(0)->getCellByColumnAndRow(0, $rowNumber)->getValue(),
            'street' => $objPHPExcel->getActiveSheet(0)->getCellByColumnAndRow(1, $rowNumber)->getValue(),
            'phone_number' => $objPHPExcel->getActiveSheet(0)->getCellByColumnAndRow(2, $rowNumber)->getValue() ? $objPHPExcel->getActiveSheet(0)->getCellByColumnAndRow(2, $rowNumber)->getValue() : $objPHPExcel->getActiveSheet(0)->getCellByColumnAndRow(3, $rowNumber)->getValue(),
            'discount' => 0,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            'isrmc' => 3,
            'idrmc' => 2, //nie wiem co to ale wszystkie w bazie mają 2
            );

        $insert = $this->Baza->PrepareInsert('companies', $data);
        $this->Baza->Query($insert);

        $lastInsertedCompany = $this->Baza->GetLastInsertID();

        //check in users if there is user who have this email
        $email = $objPHPExcel->getActiveSheet(0)->getCellByColumnAndRow(5, $rowNumber)->getValue();
        $userId = $this->Baza->GetValue("SELECT id FROM users WHERE email = '{$email}' AND CHAR_LENGTH(email) = CHAR_LENGTH('{$email}')");
        $autoryzacja = strtolower($objPHPExcel->getActiveSheet(0)->getCellByColumnAndRow(8, $rowNumber)->getValue());
        if($userId){
            $query = "UPDATE users SET company_id = $lastInsertedCompany, role = $autoryzacja WHERE id = $userId";
            $this->Baza->Query($insert);
        }
        else{
            $voievodeship = $objPHPExcel->getActiveSheet(0)->getCellByColumnAndRow(7, $rowNumber)->getValue();
            $province_id = $this->Baza->GetValue("SELECT id FROM provinces WHERE name = '{$voievodeship}'");
            if(!$province_id) $province_id = 1;
            $data = array(
                'email' => $email,
                'encrypted_new_password' => md5($email),
                'verification_hash' => md5($email.Uzytkownik::HASH),
                'created_at' => date("Y-m-d"),
                'updated_at' => date("Y-m-d"),
                'role' => $autoryzacja,
                'active' => 1,
                'entitled' => 1,
                'province_id' => $province_id,
                );
            $insert = $this->Baza->PrepareInsert('users', $data);
            $this->Baza->Query($insert);
        }
    }
        
        
}
?>
