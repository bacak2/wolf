<?php
/**
 * Moduł elementów zlecenia
 * 
 * @author		Michal Bugański <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2012
 * @package		Panelplus Ferroli
 * @version		1.0
 */

class ModulZleceniaItems extends ModulBazowy {
        public $PackageID = false;
        public $Komentarze;
        public $RequireCheckbox = array();
        public $WymaganePuste = array();
        public $SendRequireCheckbox = array();
        public $ReplacementParts = array();
        public $Interventions = array();
        public $DataOstatniejAkcji = false;
        public $DataDozwolonejAkcji = "Today";
        public $CzyMaBycJeszczePrzeglad = true;
        public $DomyslnaKwotaZaRozruch = 0;
        public $CheckError = false;
        public $WymaganyRozruch = true;
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->Tabela = 'items';
            $this->PoleID = 'id';
            $this->PoleSort = 'id';
            $this->SortHow = 'DESC';
            $this->PoleNazwy = 'title';
            $this->Nazwa = 'Zestawienie zleceń';
            $this->ModulyBezMenuBocznego[] = $this->Parametr;
            $this->ModulyBezKasowanie[] = $this->Parametr;
            $this->Komentarze = new Komentarze($this->Baza, $this->Uzytkownik);
            if(isset($_GET['id']) && is_numeric($_GET['id'])){
                $this->ZablokujDostep($_GET['id']);
            }
            if(isset($_GET['zlid']) && is_numeric($_GET['zlid'])){
                $this->ZablokujDostep(false, $_GET['zlid']);
            }

            $this->LinkPowrotu = "?modul=zlecenia&akcja=szczegoly&id=".(isset($_GET['zlid']) ? $_GET['zlid'] : $this->PackageID);
            $this->PrzyciskiFormularza['anuluj']['link'] = $this->LinkPowrotu;
	}

        function &GenerujFormularz() {
            $Formularz = parent::GenerujFormularz();
            $Formularz->DodajPole('client_dane', 'dane_klienta', 'Użytkownik*'.($this->WykonywanaAkcja == "dodaj_element" ? $this->TextInfo() : ""), array('tabelka' => Usefull::GetFormStandardRow(), 'pola' => $this->GetPolaDanychKlienta()));
            $Formularz->DodajPole('description', 'tekst_dlugi', 'Opis', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('amount', 'tekst', 'Kwota*', array('tabelka' => Usefull::GetFormStandardRow(), 'stan'=>($this->Uzytkownik->IsAdmin() ?  array() : array('readonly'=>'readonly')), 'atrybuty' => array('style' => 'width: 80px; '.($this->Uzytkownik->IsAdmin()? '' :  'background-color: #DFDFDF;' ), 'rozruch' => $this->DomyslnaKwotaZaRozruch), 'decimal' => true, 'id' => 'kwota_rozruchu'));
            $Formularz->DodajPole('happened_at', 'tekst_data', 'Data zdarzenia*', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 80px;', 'readonly' => 'readonly')));
            if($this->Uzytkownik->IsAdmin() == false){
                $Formularz->UstawOpcjePola("happened_at", "max_today", $this->DataDozwolonejAkcji, false);
                if($this->WymaganyRozruch == true){
                    $Formularz->UstawOpcjePola("happened_at", "min", "-2 months", false);
                }
            }
            $Formularz->DodajPole('required_checkbox', 'podzbiór_checkbox_1n', 'Parametry wymagane*', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('class' => 'require_checks'), 'elementy' => $this->RequireCheckbox));
            $Formularz->DodajPole('no_warranty', 'checkbox_bez_gwarancji', 'Rozruch bez gwarancji', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPoleWymagane("happened_at", false);
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}

        function TextInfo(){
            return "<br /><br /><small>Wymagane jest podanie nazwy Firmy lub imienia i nazwiska oraz wszystkie dane poza numerem lokalu.<br /></small>";
        }

        function &GenerujFormularzPrzeglad() {
            $Formularz = parent::GenerujFormularz();
            $Formularz->DodajPole('happened_at', 'tekst_data', 'Data przeglądu*', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 80px;', 'readonly' => 'readonly')));
            if($this->Uzytkownik->IsAdmin() == false){
                $Formularz->UstawOpcjePola("happened_at", "max_today", $this->DataDozwolonejAkcji, false);
                $Formularz->UstawOpcjePola("happened_at", "min", $this->DataOstatniejAkcji, false);
            }
            $Formularz->DodajPoleWymagane("happened_at", false);
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}

        function &GenerujFormularzNaprawa() {
            $Formularz = parent::GenerujFormularz();
            $Formularz->DodajPole('interventions', 'typ_interwencji', null, array('tabelka' => Usefull::GetFormButtonRow(), 'elementy' => $this->GetTypyInterwencji(), 'parts' => $this->ReplacementParts));
            $Formularz->DodajPole('finanse', 'stawki_za_naprawy', null, array('tabelka' => Usefull::GetFormButtonRow(), 'adm' => $this->Uzytkownik->IsAdmin()));
            $Formularz->DodajPole('description', 'tekst_dlugi', 'Opis zgłosznia*', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('happened_at', 'tekst_data', 'Data naprawy*', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 80px;', 'readonly' => 'readonly')));
            if($this->Uzytkownik->IsAdmin() == false){
                $Formularz->UstawOpcjePola("happened_at", "max_today", $this->DataDozwolonejAkcji, false);
                $Formularz->UstawOpcjePola("happened_at", "min", $this->DataOstatniejAkcji, false);
            }
            $Formularz->DodajPoleWymagane("happened_at", false);
            $Formularz->DodajPoleWymagane("description", false);
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}

        function  WyswietlAkcje($ID = null) {
            $this->OperacjePoPrzeladowaniu($ID);
            parent::WyswietlAkcje($ID);
        }

        function  AkcjeNiestandardowe($ID) {
            switch($this->WykonywanaAkcja){
                case "dodaj_element": $this->AkcjaDodajElement($_GET['zlid']); break;
                default: $this->AkcjaLista();
            }
        }

        /**
         * Ustawia domyślny warunek w zapytaniu do listy elementów
         * @return string
         */
	function DomyslnyWarunek(){
            if($this->Uzytkownik->IsAdmin()){
                $domyslny = '';
            }else{
		$domyslny = ($this->Uzytkownik->CzyUprawniony() == false ? "p.user_id = '{$this->Uzytkownik->ZwrocIdUzytkownika()}'" : "p.company_id = '{$this->Uzytkownik->ZwrocInfo('company_id')}'");
            }
            return $domyslny;
        }

        function ZablokujDostep($ID = false, $PackageID = false) {
            if($PackageID == false){
                $PackageID = $this->Baza->GetValue("SELECT package_id FROM items WHERE id = '$ID'");
            }
            $this->PackageID = $PackageID;
            $Status = $this->Baza->GetValue("SELECT status FROM packages WHERE id = '$this->PackageID'");
            if($Status > 3 && $this->Uzytkownik->IsAdmin() == false){
                $this->ZablokowaneElementyIDs['edycja'][] = $_GET['id'];
                $this->ZablokowaneElementyIDs['kasowanie'][] = $_GET['id'];
                $this->ZablokowaneElementyIDs['dodawanie'][] = $_GET['id'];
                $this->ZablokowaneElementyIDs['dodaj_element'][] = $_GET['id'];
            }
            if($this->Uzytkownik->IsAdmin()){
                return true;
            }
            $CheckAccess = $this->Baza->GetValue("SELECT count(p.$this->PoleID) FROM packages p WHERE p.$this->PoleID = '$this->PackageID' AND {$this->DomyslnyWarunek()}");
            if($CheckAccess > 0){
                return true;
            }
            $this->ZablokowaneElementyIDs['szczegoly'][] = $_GET['id'];
            return false;
        }

        function GetCompanies(){
            if($this->Companies == false){
                $ModulFirmy = new ModulFirmy($this->Baza, $this->Uzytkownik, "test123", $this->Sciezka);
                $this->Companies = $ModulFirmy->GetList();
            }
            return $this->Companies;
        }

        function GetActions($ID){
            if($this->Uzytkownik->IsAdmin()){
                $SerialNumberID = $this->Baza->GetValue("SELECT serial_number_id FROM $this->Tabela WHERE id = '$ID'");
                $Akcje['szczegoly'][] = array('link' => "?modul=numery_seryjne&akcja=szczegoly&id=$SerialNumberID&back=items&bid=$ID", 'img' => 'information.gif', 'desc' => 'Numer seryjny info');
            }
            return $Akcje;
        }

        function PobierzDaneElementu($ID){
            $Dane = parent::PobierzDaneElementu($ID);
            $Pola = $this->GetPolaDanychKlienta();
            foreach($Pola as $Key => $Value){
                $Dane['client_dane'][$Key] = $Dane[$Key];
            }
            $Dane['client_dane']['details'] = $Dane['details'];
            $Dane['dane_klienta'] = ($Dane['details'] != "" ? nl2br($Dane['details']) : $this->DaneKlienta($Dane['client_dane']));
            $Dane['happened_at'] = date("Y-m-d", strtotime($Dane['happened_at']));
            $NoWarranty = $Dane['no_warranty'];
            unset($Dane['no_warranty']);
            $Dane['no_warranty']['check'] = $NoWarranty;
            $Dane['no_warranty']['description'] = $Dane['no_warranty_description'];
            if($Dane['type'] == "WarrantyRepairItem"){
                $Dane['interventions'] = $this->Baza->GetData("SELECT * FROM interventions WHERE warranty_repair_item_id = '$ID'");
                $Dane['interventions']['used_parts'] = $this->Baza->GetRows("SELECT * FROM used_parts WHERE item_id = '$ID'");
                foreach($Dane['interventions']['used_parts'] as $Idx => $UsedParts){
                    $NewPart = $this->Baza->GetData("SELECT * FROM parts WHERE id = '{$UsedParts['part_id']}'");
                    $Dane['interventions']['used_parts'][$Idx]['kzc'] = $NewPart['kzc'];
                    $Dane['interventions']['used_parts'][$Idx]['title'] = $NewPart['title'];
                    $Dane['interventions']['used_parts'][$Idx]['doc-type'] = $UsedParts['reinvoice'];
                    $Dane['interventions']['used_parts'][$Idx]['doc-number'] = $UsedParts['reinvoice_number'];
                }
                $_SESSION['interventions']['used_parts'] = $Dane['interventions']['used_parts'];
            }
            $Dane['finanse']['amount'] = $Dane['amount'];
            $Dane['finanse']['distance'] = $Dane['distance'];
            $Dane['finanse']['rbh_amount'] = $Dane['rbh_amount'];
            $Dane['finanse']['stawka_za_dojazd'] = $this->GetDefaultStakeForDrive();
            $Dane['finanse']['custom'] = $Dane['custom'];
            $DeviceID = $this->Baza->GetValue("SELECT device_id FROM serial_numbers WHERE id = '{$Dane['serial_number_id']}'");
            $Dane['finanse']['default_amount'] = $this->GetDefaultAmountForDevice($DeviceID);
            $Dane['required_checkbox'] = $this->Baza->GetValues("SELECT required_checkbox_id FROM items_package_required_checkbox WHERE item_id='$ID' AND check_status = '1'");
            return $Dane;
        }

        function OperacjePrzedZapisem($Wartosci){
            if($_GET['act'] == "setup" || $this->Type == "SetupItem"){
                $Wartosci = $this->OperacjePrzedZapisemRozruch($Wartosci);
            }
            if($_GET['act'] == "service"){
                $Wartosci['serial_number_id'] = $this->Baza->GetValue("SELECT id FROM serial_numbers WHERE snu = '{$_SESSION['SerialNumberCheck']}'");
            }
            if($_GET['act'] == "repair" || $this->Type == "WarrantyRepairItem"){
                $Wartosci = $this->OperacjePrzedZapisemNaprawa($Wartosci);
                if($_GET['act'] == "repair"){
                    $Wartosci['serial_number_id'] = $this->Baza->GetValue("SELECT id FROM serial_numbers WHERE snu = '{$_SESSION['SerialNumberCheck']}'");
                }
            }
            $Now = date("Y-m-d H:i:s");
            if($this->WykonywanaAkcja == "dodaj_element"){
                $Wartosci['package_id'] = $_GET['zlid'];
                $Wartosci['created_at'] = $Now;
                $Wartosci['updated_at'] = $Now;
            }else{
                $Wartosci['updated_at'] = $Now;
            }
            return $Wartosci;
        }

        function OperacjePrzedZapisemRozruch($Wartosci){
            foreach($Wartosci['client_dane'] as $Key => $Value){
                $Wartosci[$Key] = $Value;
            }
            unset($Wartosci['client_dane']);
            $stawka_za_km = $this->Baza->GetValue("SELECT value FROM settings WHERE id = '11'");
            $distance = $this->Baza->GetValue("SELECT distance FROM $this->Tabela WHERE id = '{$_GET['id']}'");
            $Wartosci['total_amount'] = number_format($Wartosci['amount'] + ($distance * $stawka_za_km),2,".","");
            $Wartosci['costs_per_km'] = str_replace(",", ".", $stawka_za_km);
            $NoWarranty = $Wartosci['no_warranty'];
            unset($Wartosci['no_warranty']);
            $Wartosci['no_warranty'] = (isset($NoWarranty['check']) ? 1 : 0);
            $Wartosci['no_warranty_description'] = ($Wartosci['no_warranty'] == 1 ? $NoWarranty['description'] : "");
            unset($Wartosci['required_checkbox']);
            $Now = date("Y-m-d H:i:s");
            if($this->WykonywanaAkcja == "dodaj_element"){
                $SaveSerial['snu'] = $_SESSION['SerialNumberCheck'];
                $SaveSerial['device_id'] = $_SESSION['new-serial-device-id'];
                $SaveSerial['created_at'] = $Now;
                $SaveSerial['updated_at'] = $Now;
                $ZapiszSerial = $this->Baza->PrepareInsert("serial_numbers", $SaveSerial);
                if($this->Baza->Query($ZapiszSerial)){
                    $Wartosci['serial_number_id'] = $this->Baza->GetLastInsertID();
                }
            }
            return $Wartosci;
        }

        function OperacjePrzedZapisemNaprawa($Wartosci){
            foreach($Wartosci['finanse'] as $Key => $Value){
                $Wartosci[$Key] = str_replace(",", ".", $Value);
            }
            if(!isset($Wartosci['custom'])){
                $Wartosci['custom'] = 0;
            }
            $this->Interventions = $Wartosci['interventions'];
            unset($Wartosci['finanse']);
            unset($Wartosci['interventions']);
            $Dojazd = $this->Baza->GetValue("SELECT value FROM settings WHERE id = '11'");
            $Wartosci['total_amount'] = ($Wartosci['amount']*$Wartosci['rbh_amount']) + ($Wartosci['distance'] * $Dojazd);
            return $Wartosci;
        }

        function AkcjaSzczegoly($ID){
            $Dane = $this->PobierzDaneElementu($ID);
            $Dane['package'] = $this->Baza->GetData("SELECT * FROM packages WHERE id = '{$Dane['package_id']}'");
            $SerialNumber = $this->Baza->GetData("SELECT snu, device_id FROM serial_numbers WHERE id = '{$Dane['serial_number_id']}'");
            $Dane['numer_seryjny'] = $SerialNumber['snu'];
            $Dane['urzadzenie'] = $this->Baza->GetValue("SELECT CONCAT(dt.devices_title_title,' (',d.kzu,')') as device_name, dt.devices_title_title
                                                            FROM devices d LEFT JOIN devices_title dt ON(dt.devices_title_id = d.devices_title_devices_title_id)
                                                            WHERE d.id = '{$SerialNumber['device_id']}'");
            $Dane['client_name'] = $this->Baza->GetValue("SELECT CONCAT(firstname,' ',surname) FROM users WHERE id = '{$Dane['package']['user_id']}'");
            $Dane['simplename'] = $this->Baza->GetValue("SELECT simplename FROM companies WHERE id = '{$Dane['package']['company_id']}'");
            $Dane['paczka'] = $Dane['package']['id']." ".$Dane['package']['title'];
            $Dane['total_amount'] .= " zł";
            $Dane['details'] = $Dane['dane_klienta'];
            
            $Typy = Usefull::GetItemTypes();
            $Name_multi[] = array( 'key' => 'Urządzenie: ', 'value' => '<span style="font-size: 18px; color:#000">'.$Dane['numer_seryjny'].'</span> - '. $Dane['urzadzenie']);
            $Name_multi[] = array( 'key' => 'Typ: ', 'value' => $Typy[$Dane['type']].($Dane['type'] == "SetupItem" && $Dane['no_warranty']['check'] == 1 ? " bez gwarancji - " : " - ").$Dane['total_amount'] );
            $Name_multi[] = array( 'key' => 'Data: ', 'value' => $Dane['happened_at'] );
//            $Dane['type_name'] = $Typy[$Dane['type']].($Dane['type'] == "SetupItem" && $Dane['no_warranty']['check'] == 1 ? " bez gwarancji" : "");
//            $PolaJeden['id'] = array("naglowek" => 'ID', 'style' => 'width: 60px;');
//            $PolaJeden['type_name'] = array("naglowek" => 'Typ');
//            $PolaJeden['numer_seryjny'] = array("naglowek" => 'Numer seryjny');
            if($Dane['type'] == "WarrantyRepairItem"){
                $PolaJeden['interventions_data'] = array("naglowek" => 'Interwencje', 'style' => "width: 40%;");
                unset($Dane['interventions']['used_parts']);
                $Dane['interventions_data'] = '';
                $interventions = $this->GetTypyInterwencji();
                foreach( $Dane['interventions'] as $key=>$val){
                    if( array_key_exists($key, $interventions) && $val == '1' ){
                        $Dane['interventions_data'] .= ' - '.$interventions[$key].'<br />';
                    }
                }
                $Dane['total_amount'] .= "<div style='font-weight: bold;'>w tym:</div>".
                'Stawka zł - '.$Dane['amount'].'<br/>'.
                'Ilość roboczogodzin - '.$Dane['rbh_amount'].'h<br/>'.
                'Dojazd (ilość km) - '.$Dane['distance'].'km';
                $PolaJeden['total_amount'] = array("naglowek" => 'Kwota', 'style' => 'text-align: right;');
            }elseif($Dane['type'] == "WarrantyServiceItem"){
                $PolaJeden['happened_at'] = array("naglowek" => 'Data przeglądu');
            }elseif($Dane['type'] == "SetupItem"){
                $PolaJeden['details'] = array("naglowek" => 'Użytkownik', 'style' => 'width: 20%;');
            }
            if($Dane['type'] == "SetupItem"){
                $Urzadzenia = new ModulUrzadzenia($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
                $Wymogi = $Urzadzenia->GetListOfParametersRequired();
                $PolaJeden['required_checkbox_list'] = array("naglowek" => 'Zaznaczone wymagane parametry', 'style' => "width: 40%;");
                $Dane['required_checkbox_list'] = '<ol>';
                foreach($Dane['required_checkbox'] as $RC ){
                    $Dane['required_checkbox_list'] .=  '<li class="'.($RC).'">'.$Wymogi[$RC["required_checkbox_id"]].'</li>';
                }
                $Dane['required_checkbox_list'] .= '</ol>';
            }
            $PolaJeden['description'] = array("naglowek" => 'Opis', 'style' => "padding: 0;width: 40%;");
            $ModulParts = new ModulCzesci($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
            $Dane['description'] = "<p style='padding: 3px 3px 3px 3px; margin-bottom: 3px;'>{$Dane['description']}</p>";
            if($Dane['no_warranty']['description'] != ""){
                $Dane['description'] .= ($Dane['description'] != "" ? "<hr class='opis-przedzielacz' />" : "")."<p style='padding: 3px 3px 3px 3px; margin-top: 3px;'>{$Dane['no_warranty']['description']}</p>";
            }
            $UsedParts = $this->Baza->GetRows("SELECT part_id, amount FROM used_parts WHERE item_id = '$ID'");
            if($UsedParts){
                foreach($UsedParts as $Kid=>$Uid){
                    $Dane['used_parts'][$Kid] = $ModulParts->PobierzDaneElementu($Uid['part_id']);
                    $Dane['used_parts'][$Kid]['amount'] = $Uid['amount'];
                }
                $Description = $Dane['description'];
                $Dane['description'] = "<p style='padding: 3px 3px 5px 3px;'>$Description</p>";
                $Dane['description'] .= "<table class='no-border history' cellpadding='7' cellspacing='0' style='width: 100%; margin-top: 15px; border-collapse: collapse;'>";
//                $Dane['description'] .= "<tr><th>KZC</th><th>Nazwa</th><th>Cena</th></tr>";
                $Dane['description'] .= "<tr><th>KZC</th><th>Nazwa</th></tr>";
                foreach($Dane['used_parts'] as $Part){
//                    $Dane['description'] .= "<tr><td>{$Part['kzc']}</td><td class='bordered'>{$Part['title']}</td><td class='bordered'>{$Part['amount']}</td></tr>";
                    $Dane['description'] .= "<tr><td>{$Part['kzc']}</td><td class='bordered'>{$Part['title']}</td></tr>";
                }
                $Dane['description'] .= "</table>";
            }
            $CommentsMultipleDivs = true;
            $PolaComments['zlecenie']['paczka'] = 'Zestawienie zleceń';
            $PolaComments['zlecenie']['urzadzenie'] = 'Urządzenie';
            $PolaComments['user']['client_name'] = 'Użytkownik';
            $PolaComments['user']['simplename'] = 'Firma';
            $PolaComments['date']['created_at'] = 'Utworzono';
            $PolaComments['date']['updated_at'] = 'Ostatnia zmiana';
            $PolaComments['date2']['sent_at'] = 'Wysłano';
            $Dane['created_at'] = UsefullTime::PolskiDzien(date("D, d.m.Y H:i:s", strtotime($Dane['package']['created_at'])));
            $Dane['updated_at'] = UsefullTime::PolskiDzien(date("D, d.m.Y H:i:s", strtotime($Dane['package']['updated_at'])));
            $Dane['sent_at'] = (is_null($Dane['package']['sent_at']) ? "brak" : UsefullTime::PolskiDzien(date("D, d.m.Y H:i:s", strtotime($Dane['package']['sent_at']))));
            $this->include_tpls = "komentarze.tpl.php";
            $this->include_tpls_data = array('PolaDwa' => array('komentarze' => $this->Komentarze->GetComments($ID), 'last_user_comment' => $this->Komentarze->GetLastComment($this->UserID, $ID), 'package_status' => $Dane['package']['status']));
            include(SCIEZKA_SZABLONOW."show.tpl.php");
        }

        function OperacjePoPrzeladowaniu($ID){
            if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['NewComment'])){
                if($this->Komentarze->SaveComment($_POST, $ID, $this->UserID)){
                    Usefull::ShowKomunikatOk("Komentarz został zapisany", true);
                }else{
                    Usefull::ShowKomunikatError("Komentarz nie został zapisany", true);
                }
            }
            if(isset($_GET['upost']) && is_numeric($_GET['upost'])){
                if($this->Komentarze->DeleteComment($_GET['upost'], $ID, $this->UserID)){
                    Usefull::ShowKomunikatOk("Komentarz został usunięty", true);
                }else{
                    Usefull::ShowKomunikatError("Komentarz nie został usunięty", true);
                }
            }
        }

        function GetPolaDanychKlienta(){
            $Pola['client_companyname'] = "Firma/Instytucja";
            $Pola['client_firstname'] = "Imię";
            $Pola['client_surname'] = "Nazwisko";
            $Pola['client_street'] = "Ulica";
            $Pola['client_address_number'] = "Nr";
            $Pola['client_local_number'] = "Lokal";
            $Pola['client_postcode'] = "Kod pocztowy";
            $Pola['client_city'] = "Miejscowosc";
            $Pola['client_phone'] = "Tel. kontaktowy";
            return $Pola;
        }

        function DeleteItem($ItemID, $ID, $PackageStatus){
            if($this->Uzytkownik->IsAdmin() || $PackageStatus < 3){
                $TypeSerial = $this->Baza->GetValue("SELECT concat(type, '|', serial_number_id) FROM $this->Tabela WHERE id = '$ItemID' AND package_id = '$ID'");
                list($Type, $SerialID) = explode('|', $TypeSerial);
                if($Type == 'SetupItem'){
                    $TypeSerial2 = $this->Baza->GetValue("SELECT type FROM $this->Tabela WHERE serial_number_id = '$SerialID' AND package_id != '$ID'");
                    if($TypeSerial2 === FALSE)
                        $this->Baza->Query("DELETE FROM serial_numbers WHERE id = '$SerialID'");
                }
                return $this->Baza->Query("DELETE FROM $this->Tabela WHERE id = '$ItemID' AND package_id = '$ID'");
            }
        }

        function AcceptItem($ItemID, $ID, $PackageStatus){
            if($this->Uzytkownik->IsAdmin()){
                if($this->Baza->Query("UPDATE $this->Tabela SET approved = '1' WHERE id = '$ItemID' AND package_id = '$ID'")){
                    if($PackageStatus == 5){
                        $SerialNumberID = $this->Baza->GetValue("SELECT serial_number_id FROM $this->Tabela WHERE id = '$ItemID' AND package_id = '$ID'");
                        $this->Baza->Query("UPDATE serial_numbers SET approved = '1' WHERE id = '$SerialNumberID'");
                    }
                    return true;
                }
            }
        }

        function CancelItem($ItemID, $ID, $PackageStatus){
            if($this->Uzytkownik->IsAdmin()){
                if($this->Baza->Query("UPDATE $this->Tabela SET approved = '0' WHERE id = '$ItemID' AND package_id = '$ID'")){
                    if($PackageStatus == 5){
                        $SerialNumberID = $this->Baza->GetValue("SELECT serial_number_id FROM $this->Tabela WHERE id = '$ItemID' AND package_id = '$ID'");
                        $this->Baza->Query("UPDATE serial_numbers SET approved = '0' WHERE id = '$SerialNumberID'");
                    }
                    return true;
                }
            }
        }

        function AkcjaDodajElement($PackageID){
            $ActionSerialNumber = 0;
            $BlockSerialNumber = false;
            if( is_null($PackageID) || !is_numeric($PackageID) ){
                $zlid = $this->GetPackages();
                Usefull::RedirectLocation($this->LinkPodstawowy.'&zlid='.$zlid);
            }
            if(isset($_POST['SerialNumberCheck'])){
                unset($_SESSION['interventions']['used_parts']);
                unset($_SESSION['invoice']);

                if($_POST['SerialNumberCheck'] == ""){
                    $this->Error = "Musisz wprowadzić numer seryjny!";
                }else{
                    if(isset($_POST['change-serial-number'])){
                        $ActionSerialNumber = 0;
                    }else{
                        $ActionSerialNumber = $this->CheckSerialNumber($_POST['SerialNumberCheck']);
                        if($ActionSerialNumber == -2){
                            $SerialNumber = $this->Baza->GetData("SELECT * FROM serial_numbers WHERE snu = '{$_POST['SerialNumberCheck']}'");
                            $_SESSION['new-serial-device-id'] = $SerialNumber['device_id'];
                            $Urzadzenia = new ModulUrzadzenia($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
                            $DaneUrzadzenia = $Urzadzenia->PobierzDaneElementu($_SESSION['new-serial-device-id']);
                        }
                        if($ActionSerialNumber == 1){
                            $Urzadzenia = new ModulUrzadzenia($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
                            $UrzadzeniaLista = $Urzadzenia->GetList(($this->Uzytkownik->ZwrocInfo('role') == 'instalator' ? true : false));
                            $BlockSerialNumber = true;
                        }
                        if($ActionSerialNumber == 2){
                            $SerialNumber = $this->Baza->GetData("SELECT * FROM serial_numbers WHERE snu = '{$_POST['SerialNumberCheck']}'");
                            $_SESSION['new-serial-device-id'] = $SerialNumber['device_id'];
                            $Urzadzenia = new ModulUrzadzenia($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
                            $DaneUrzadzenia = $Urzadzenia->PobierzDaneElementu($_SESSION['new-serial-device-id']);
                            $Items = $this->Baza->GetRows("SELECT * FROM items WHERE serial_number_id = '{$SerialNumber['id']}'");
                            $BlockSerialNumber = true;
                        }
                    }
                    $Values['SerialNumberCheck'] = $_POST['SerialNumberCheck'];
                    $_SESSION['SerialNumberCheck'] = $_POST['SerialNumberCheck'];
                }
            }
            if(isset($_POST['make-setup'])){
                if($_POST['new-serial-device-id'] == -1){
                    $ActionSerialNumber = 1;
                    $Urzadzenia = new ModulUrzadzenia($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
                     $UrzadzeniaLista = $Urzadzenia->GetList(  ($this->Uzytkownik->ZwrocInfo('role') == 'instalator' ? true : false) );
                     $BlockSerialNumber = true;
                     $Values['SerialNumberCheck'] = $_SESSION['SerialNumberCheck'];
                }else{
                    $ActionSerialNumber = 3;
                    $BlockSerialNumber = true;
                    $Values['SerialNumberCheck'] = $_SESSION['SerialNumberCheck'];
                    $_SESSION['new-serial-device-id'] = $_POST['new-serial-device-id'];
                }
            }
            if($_GET['act'] == "setup" && $this->CheckSerialNumber($_SESSION['SerialNumberCheck']) == 1){
                $ActionSerialNumber = 4;
                $BlockSerialNumber = true;
                $Values['SerialNumberCheck'] = $_SESSION['SerialNumberCheck'];
                $Values['new-serial-device-id'] = $_SESSION['new-serial-device-id'];
                $Urzadzenia = new ModulUrzadzenia($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
                $DaneUrzadzenia = $Urzadzenia->PobierzDaneElementu($Values['new-serial-device-id']);
                $Wymogi = $Urzadzenia->GetListOfParametersRequired();
                $this->WymaganyRozruch = ($DaneUrzadzenia['warranty_service'] == 1 ? true : false);
                $WymogiToDevice = $Urzadzenia->GetListOfDevicesParametersRequired($Values['new-serial-device-id']);
                foreach($WymogiToDevice as $WID){
                    $this->RequireCheckbox[$WID] = $Wymogi[$WID];
                }
            } 
            if(($_GET['act'] == "service" || $_GET['act'] == "repair") && $this->CheckSerialNumber($_SESSION['SerialNumberCheck']) == 2){
                $ActionSerialNumber = ($_GET['act'] == "repair" ? 6 : 5);
                $BlockSerialNumber = true;
                $Values['SerialNumberCheck'] = $_SESSION['SerialNumberCheck'];
                $Values['new-serial-device-id'] = $_SESSION['new-serial-device-id'];
                $Urzadzenia = new ModulUrzadzenia($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
                $DaneUrzadzenia = $Urzadzenia->PobierzDaneElementu($_SESSION['new-serial-device-id']);
                $Czesci = $Urzadzenia->GetParts($_SESSION['new-serial-device-id']);
                $this->SetReplacementParts($Czesci);
            }
            ### Zapis rozruchu ###
            if(isset($_POST['OpcjaFormularza']) && $this->Parametr != "zlecenia"){
                if($_GET['act'] == "service"){
                    $Formularz = $this->GenerujFormularzPrzeglad($_POST, true);
                }else if($_GET['act'] == "repair"){
                    $Formularz = $this->GenerujFormularzNaprawa($_POST, true);
                }else{
                    $Formularz = $this->GenerujFormularz($_POST, true);
                }
                $PolaWymagane = $Formularz->ZwrocPolaWymagane();
                $PolaZdublowane = $Formularz->ZwrocPolaNieDublujace();
                $Wartosci = $Formularz->ZwrocWartosciPol($_POST);
                if(isset($_POST['add-replacement-part'])){
                    $_POST['OpcjaFormularza'] = "only_used_parts";
                    $PartID = $_POST['selected-replacement-part'];
                    $NewPart = $this->Baza->GetData("SELECT * FROM parts WHERE id = '$PartID'");
                    $NewPart['part_id'] = $PartID;
                    $NewPart['doc-type'] = $_POST['selected-replacement-part-document'];
                    $NewPart['doc-number'] = $_POST['selected-replacement-part-document-number'];
                    $_SESSION['interventions']['used_parts'][] = $NewPart;
                    $Wartosci['interventions']['used_parts'] = $_SESSION['interventions']['used_parts'];
                    unset($_POST['selected-replacement-part-document']);
                    unset($_POST['selected-replacement-part-document-number']);
                        if($NewPart['doc-type'] == 1){
                            $aNotIn = ( isset( $_SESSION['invoice']['NotIn'] ) ? $_SESSION['invoice']['NotIn'] : array() );
                            if( !empty($aNotIn) ){
                                $s_NotIn = "AND purchase_id NOT IN ('".implode("', '",$aNotIn)."')";
                            }
                            $this->Baza->Query("SELECT * FROM line_items li
                                WHERE
                                invoice = '{$NewPart['doc-number']}'
                                AND part_id = '{$NewPart['part_id']}'
                                $s_NotIn
                                LIMIT 1
                                ");
                            $line_item = $this->Baza->GetRow();
                            if( !empty($line_item['purchase_id'])){
                                if( $_SESSION['invoice']['count'][$line_item['id']]+1 >= $i_quantity_approved )
                                $_SESSION['invoice']['NotIn'][$NewPart['doc-number']] = $line_item['purchase_id'];
                            }
                            if( isset($_SESSION['invoice']['count'][$line_item['id']]) ){
                                $_SESSION['invoice']['count'][$line_item['id']] += 1;
                            }else{
                                $_SESSION['invoice']['count'][$line_item['id']] = 1;
                            }
                        }
                    }
                    if(isset($_POST['remove-replacement-part'])){
                        $OpcjaFormularza = "only_used_parts";
                            foreach($_POST['Usun'] as $Key){
                                $this->Baza->Query("SELECT * FROM line_items li
                                        WHERE
                                        invoice = '{$_SESSION['interventions']['used_parts'][$Key]['doc-number']}'
                                        AND part_id = '{$_SESSION['interventions']['used_parts'][$Key]['part_id']}'
                                        LIMIT 1
                                        ");
                                    $line_item = $this->Baza->GetRow();        
                                if( $_SESSION['interventions']['used_parts'][$Key]['doc-type'] == 1 && isset($_SESSION['invoice']['count'][$line_item['id']]) ){

                                    if($_SESSION['invoice']['count'][$line_item['id']] > 1)
                                        $_SESSION['invoice']['count'][$line_item['id']]--;
                                    else
                                        unset($_SESSION['invoice']['count'][$line_item['id']]);
                                }
                                unset($_SESSION['invoice']['NotIn'][$_SESSION['interventions']['used_parts'][$Key]['doc-number']]);
                                unset($_SESSION['interventions']['used_parts'][$Key]);
                            }
                        $Wartosci['interventions']['used_parts'] = $_SESSION['interventions']['used_parts'];
                    }
                if ($_POST['OpcjaFormularza'] == 'zapisz'){
                    echo "<div style='clear: both;'></div>\n";
                    if($this->SprawdzDane($Formularz, $Wartosci) && $this->SprawdzPliki($Formularz->ZwrocDanePrzeslanychPlikow()) && $this->SprawdzPolaWymagane($PolaWymagane, $Wartosci) && $this->SprawdzPolaNieDublujace($PolaZdublowane, $Wartosci)){
                        if($_GET['act'] == "service"){
                            $Wartosci['type'] = 'WarrantyServiceItem';
                        }else if($_GET['act'] == "repair"){
                            $Wartosci['type'] = 'WarrantyRepairItem';
                        }else{
                            $Wartosci['type'] = 'SetupItem';
                            $this->SendRequireCheckbox = $Wartosci['required_checkbox'];
                        }
                        if ($this->ZapiszDaneElementu($Formularz, $Wartosci, $Formularz->ZwrocDanePrzeslanychPlikow())) {
                                $this->ShowOK();
                                return;
                        }
                        else {
                            $this->Error = '<b>Wystąpił problem. Dane nie zostały zapisane.</b><br/>'.$this->Baza->GetLastErrorDescription();
                        }
                    }
                }
                $Wartosci['interventions']['used_parts'] = $_SESSION['interventions']['used_parts'];
                $Wartosci = $this->AkcjaPrzeladowanie($Wartosci, $Formularz);
                $Formularz = $this->AkcjaPrzeladowanieFormularz($Wartosci, $Formularz);
                foreach($this->PolaWymaganeNiewypelnione as $NazwaPola){
                        $Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
                        if($NazwaPola == "client_dane"){
                            $Formularz->UstawOpcjePola($NazwaPola, 'wymagane-puste', $this->WymaganePuste, false);
                        }
                }
                foreach($this->PolaZdublowane as $NazwaPola){
                        $Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
                }
            }
            include(SCIEZKA_SZABLONOW."forms/zlecenie-numer-seryjny-form.tpl.php");
        }

        function CheckSerialNumber($SerialNumber, $Type = false){
            if($this->Baza->GetValue("SELECT count(*) FROM serial_numbers WHERE snu = '$SerialNumber'") == 0){
                return 1;
            }else{
                $SerialNumberDane = $this->Baza->GetData("SELECT * FROM serial_numbers WHERE snu = '$SerialNumber'");
                $DeviceDane = $this->Baza->GetData("SELECT * FROM devices WHERE id = '{$SerialNumberDane['device_id']}'");
                if($DeviceDane['warranty_service'] == 0){
                    $Rozruch = $this->Baza->GetValue("SELECT happened_at FROM items WHERE serial_number_id = '{$SerialNumberDane['id']}' AND type='SetupItem'");
                }else{
                    $Rozruch = $this->Baza->GetValue("SELECT happened_at FROM items WHERE serial_number_id = '{$SerialNumberDane['id']}' AND type='SetupItem'");
                    $Gwarancja = $this->Baza->GetValue("SELECT no_warranty FROM items WHERE serial_number_id = '{$SerialNumberDane['id']}' AND type='SetupItem' AND approved = '1'");
                    if($Gwarancja == 1){
                        $this->CheckError = "Urządzenie nie ma gwarancji";
                        return -1;
                    }
                }

                $IsWarranty = true;
                $this->DataOstatniejAkcji = $this->Baza->GetValue("SELECT happened_at FROM items WHERE serial_number_id = '{$SerialNumberDane['id']}' AND (type='SetupItem' OR type='WarrantyServiceItem')  ORDER BY happened_at DESC LIMIT 1");
                $IlePrzegladowWykonano = $this->Baza->GetValue("SELECT count(*) FROM items WHERE serial_number_id = '{$SerialNumberDane['id']}' AND (type='WarrantyServiceItem') AND approved = '1'");
                if($Rozruch){
                        $WarrantyMonths = $str = preg_replace("/[^0-9\,]+/","",$DeviceDane['warranty_service_months']);
                        if($WarrantyMonths != ""){
                            $Okresy = explode(",", $WarrantyMonths);
                        }else{
                            $Okresy = array();
                        }
                        $IlePrzegladow = count($Okresy);
                        $Okresy[] = $DeviceDane['warranty'];
                        $Ostatni = $Rozruch;
                        $Odejmij = 0;
                         $Dzis = date("Y-m-d", strtotime($this->Dzis."-1 months"));
                        foreach($Okresy as $Days){
                           $Days = intval($Days);
                           $CheckDays = $Days - $Odejmij;
                           $CheckDate = date("Y-m-d", strtotime($Ostatni."+$CheckDays months"));
                           if(strtotime( $Dzis ) > strtotime( $CheckDate )){
                                $Ostatni = $this->Baza->GetValue("SELECT happened_at FROM items WHERE serial_number_id = '{$SerialNumberDane['id']}' AND approved = '1' AND type='WarrantyServiceItem' AND happened_at <= '$CheckDate' AND happened_at > '$Ostatni' ORDER BY happened_at ASC LIMIT 1");
                                $Odejmij = $Days;
                                if($Ostatni == false){
                                    $IsWarranty = false;
                                    break;
                                }
                                $Ostatni = $CheckDate;
                            }else{
                                break;
                            }
                        }
                        if( strtotime($this->Dzis) > strtotime($CheckDate)){
                            $this->DataDozwolonejAkcji = $CheckDate;
                        }
                }else{
                    $IsWarranty = false;
                }
                $type = $this->Baza->GetValue("SELECT type FROM items 
                    LEFT JOIN packages ON (items.package_id = packages.id)
                    WHERE serial_number_id = '{$SerialNumberDane['id']}'
                        AND packages.status IN ( 0,1,2,3 )
                        AND items.approved = 0
                    ORDER BY happened_at DESC LIMIT 1");
                if( ($SerialNumberDane['approved'] == 1 || $DeviceDane['warranty_service'] == 0) && ($IsWarranty || $this->Uzytkownik->IsAdmin()) && $this->Uzytkownik->IsWarrantyService()){
                    if($IlePrzegladow <= $IlePrzegladowWykonano){
                        $this->CzyMaBycJeszczePrzeglad = false;
                    }
                    if($_GET['act'] == "repair" || $Type == "WarrantyRepairItem"){
                        $this->DataOstatniejAkcji = $Rozruch;
                    }
                    return 2;
                }elseif($DeviceDane['warranty_service'] == 1 && !($IsWarranty && $Rozruch) && $type == 'SetupItem' ){
                    $this->CheckError = "Niezatwierdzony rozruch";
                    return 2;
                }else{
                    if($this->Uzytkownik->IsAdmin()){
                        return 2;    
                    }elseif($SerialNumberDane['approved'] == 0 && $DeviceDane['warranty_service'] == 1){
                        $this->CheckError = "Dla tego urządzenia okres gwarancyjny zakończył się!";
                    }else if( $this->Uzytkownik->ZwrocInfo('role') === 'ai'){
                        $this->CheckError = "Nie masz uprawnień do wykonywania przeglądów oraz napraw gwarancyjnych";
                    }else{
                        $this->CheckError = "Urządzenie nie ma gwarancji";
                        return -2;
                    }
                    return -1;
                }
            }
            return 0;
        }

        function SprawdzDane($Formularz, $Wartosci){
            if($_GET['act'] == "setup" || $this->Type == "SetupItem"){
                return $this->SprawdzDaneRozruch($Formularz, $Wartosci);
            }
            if($_GET['act'] == "service" || $this->Type == "WarrantyServiceItem"){
                if($Wartosci['happened_at'] > $this->Dzis){
                    $this->Error = "Nie możesz wybrać daty przyszłej";
                    $this->PolaWymaganeNiewypelnione[] = "happened_at";
                    return false;
                }
                if($Wartosci['happened_at'] < $this->DataOstatniejAkcji && !$this->Uzytkownik->IsAdmin()){
                    $this->Error = "Niewłaściwa data zdarzenia";
                    $this->PolaWymaganeNiewypelnione[] = "happened_at";
                    return false;
                }
            }
            if($_GET['act'] == 'repair' || $this->Type == "WarrantyRepairItem"){
                if(count($Wartosci['interventions']) == 0){
                    $this->Error = "Musisz wybrać typ interwencji";
                    return false;
                }
            }
            return true;
        }

        function SprawdzDaneRozruch($Formularz, $Wartosci){
            if(isset($Wartosci['client_dane']['details']) && $Wartosci['client_dane']['details'] == ""){
                $this->Error = "Proszę wypełnić wymagane pola";
                $this->PolaWymaganeNiewypelnione[] = "client_dane";
                return false;
            }else if(!isset($Wartosci['client_dane']['details'])){
                if($Wartosci['client_dane']['client_companyname'] == "" && ($Wartosci['client_dane']['client_firstname'] == "" || $Wartosci['client_dane']['client_surname'] == "")){
                    $this->Error = "Proszę wypełnić wymagane pola";
                    $this->PolaWymaganeNiewypelnione[] = "client_dane";
                    $this->WymaganePuste[] = "client_companyname";
                    $this->WymaganePuste[] = "client_firstname";
                    $this->WymaganePuste[] = "client_surname";
                    return false;
                }
                if($Wartosci['client_dane']['client_street'] == "" || $Wartosci['client_dane']['client_address_number'] == ""
                        || $Wartosci['client_dane']['client_postcode'] == "" || $Wartosci['client_dane']['client_city'] == "" || $Wartosci['client_dane']['client_phone'] == ""){
                        $this->Error = "Proszę wypełnić wymagane pola";
                        $this->PolaWymaganeNiewypelnione[] = "client_dane";
                        if($Wartosci['client_dane']['client_street'] == ""){
                            $this->WymaganePuste[] = "client_street";
                        }
                        if($Wartosci['client_dane']['client_address_number'] == ""){
                            $this->WymaganePuste[] = "client_address_number";
                        }
                        if($Wartosci['client_dane']['client_postcode'] == ""){
                            $this->WymaganePuste[] = "client_postcode";
                        }
                        if($Wartosci['client_dane']['client_city'] == ""){
                            $this->WymaganePuste[] = "client_city";
                        }
                        if($Wartosci['client_dane']['client_phone'] == ""){
                            $this->WymaganePuste[] = "client_phone";
                        }
                        return false;
                }
            }
            if($this->Uzytkownik->IsAdmin() == false && $this->Dzis > date("Y-m-d", strtotime($Wartosci['happened_at']."+2 months" )) && $this->WymaganyRozruch == true){
                $this->Error = "Nie możesz wprowadzić zdarzenia sprzed 2 miesięcy";
                $this->PolaWymaganeNiewypelnione[] = "happened_at";
                return false;
            }
            $Required = array_keys($this->RequireCheckbox);
            $Unchecked = false;
            foreach($Required as $ReqID){
                if((!isset($Wartosci['required_checkbox']) || !in_array($ReqID, $Wartosci['required_checkbox']))){
                    $Unchecked = true;
                    break;
                }
            }
            if($Unchecked){
                if(!isset($Wartosci['no_warranty']['check'])){
                    $this->Error = "Musisz zaznaczyć wszystkie wymagane parametry lub zaznaczyć \"Rozruch bez gwarancji\"";
                    return false;
                }else if($Wartosci['no_warranty']['description'] == ""){
                    $this->Error = "Proszę wpisać powód rozruchu bez gwarancji";
                    return false;
                }
            }
            return true;
        }

        function WykonajOperacjePoZapisie($Wartosci, $ID){
            if($_GET['act'] == "setup" || $this->Type == "SetupItem"){
                $this->Baza->Query("DELETE FROM items_package_required_checkbox WHERE item_id = '$ID'");
                $Required = array_keys($this->RequireCheckbox);
                foreach($Required as $ReqID){
                    $Save['check_status'] = (in_array($ReqID, $this->SendRequireCheckbox) ? 1 : 0);
                    $Save['item_id'] = $ID;
                    $Save['required_checkbox_id'] = $ReqID;
                    $Zap = $this->Baza->PrepareInsert("items_package_required_checkbox", $Save);
                    $this->Baza->Query($Zap);
                }
            }
            if($_GET['act'] == "repair" || $this->Type == "WarrantyRepairItem"){
                $Typy = $this->GetTypyInterwencji();
                foreach($Typy as $Key => $Value){
                    $Save[$Key] = (isset($this->Interventions[$Key]) ? 1 : 0);
                }
                $Save['updated_at'] = date("Y-m-d H:i:s");
                if($this->Baza->GetValue("SELECT count(*) FROM interventions WHERE warranty_repair_item_id = '$ID'") == 1){
                    $Zap = $this->Baza->PrepareUpdate("interventions", $Save, array("warranty_repair_item_id" => $ID));
                }else{
                    $Save['warranty_repair_item_id'] = $ID;
                    $Save['created_at'] = date("Y-m-d H:i:s");
                    $Zap = $this->Baza->PrepareInsert("interventions", $Save);
                }
                $this->Baza->Query($Zap);
                $this->Baza->Query("DELETE FROM used_parts WHERE item_id = '$ID'");
                if($Save['part_replacement'] == 1){
                    foreach($_SESSION['interventions']['used_parts'] as $UsedPart){
                        $UsPartSave['item_id'] = $ID;
                        $UsPartSave['part_id'] = $UsedPart['part_id'];
                        $UsPartSave['amount'] = $UsedPart['amount'];
                        $UsPartSave['reinvoice'] = $UsedPart['doc-type'];
                        $UsPartSave['reinvoice_number'] = $UsedPart['doc-number'];
                        $UsPartSave['created_at'] = date("Y-m-d H:i:s");
                        $UsPartSave['updated_at'] = date("Y-m-d H:i:s");
                        $ZapNew = $this->Baza->PrepareInsert("used_parts", $UsPartSave);
                        $this->Baza->Query($ZapNew);
                    }
                }
                //CountValue = 
                if(isset($_SESSION['invoice']['count'])){
                    foreach($_SESSION['invoice']['count'] as $CountKey => $CountValue){
                        $ZapCount = "UPDATE line_items SET were_used = were_used + {$CountValue} WHERE id = {$CountKey}";
                        $this->Baza->Query($ZapCount);
                    }
                }
            }
        }

        function DaneKlienta($Item){
            $Dane['details'] = ($Item['client_companyname'] != "" ? $Item['client_companyname']."<br />" : "");
            $Dane['details'] .= ($Item['client_firstname'] != "" || $Item['client_surname'] ? $Item['client_firstname']." ".$Item['client_surname']."<br />" : "");
            $Dane['details'] .= $Item['client_street']." ".$Item['client_address_number'].($Item['client_local_number'] != "" ? "/{$Item['client_local_number']}" : "")."<br />";
            $Dane['details'] .= $Item['client_postcode']." ".$Item['client_city']."<br />";
            $Dane['details'] .= "tel. {$Item['client_phone']}";
            return $Dane['details'];
        }

        function AkcjaEdycja($ID) {
            $Type = $this->Baza->GetValue("SELECT type FROM $this->Tabela WHERE id = '$ID'");
            $SerialNumber = $this->Baza->GetValue("SELECT serial_number_id FROM $this->Tabela WHERE id = '$ID'");
            $SNU = $this->Baza->GetValue("SELECT snu FROM serial_numbers WHERE id = '$SerialNumber'");
            $DeviceID = $this->Baza->GetValue("SELECT device_id FROM serial_numbers WHERE id = '$SerialNumber'");
            $Urzadzenia = new ModulUrzadzenia($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
            $DaneUrzadzenia = $Urzadzenia->PobierzDaneElementu($DeviceID);
            $this->DomyslnaKwotaZaRozruch = $DaneUrzadzenia['setup_amount'];
            $this->WymaganyRozruch = ($DaneUrzadzenia['warranty_service'] == 1 ? true : false);
            if($Type == "SetupItem"){
                $Wymogi = $Urzadzenia->GetListOfParametersRequired();
                $WymogiToDevice = $Urzadzenia->GetListOfDevicesParametersRequired($DeviceID);
                foreach($WymogiToDevice as $WID){
                    $this->RequireCheckbox[$WID] = $Wymogi[$WID];
                }
            }
            $this->CheckSerialNumber($SNU, $Type);
            if($Type == "WarrantyRepairItem"){
                $Czesci = $Urzadzenia->GetParts($DeviceID);
                $this->SetReplacementParts($Czesci);
            }
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    if($Type == "WarrantyServiceItem"){
                        $Formularz = $this->GenerujFormularzPrzeglad($_POST, true);
                    }else if($Type == "WarrantyRepairItem"){
                        $Formularz = $this->GenerujFormularzNaprawa($_POST, true);
                    }else if($Type == "SetupItem"){
                        $Formularz = $this->GenerujFormularz($_POST, true);
                    }
                    $PolaWymagane = $Formularz->ZwrocPolaWymagane();
                    $PolaZdublowane = $Formularz->ZwrocPolaNieDublujace();
                    $OpcjaFormularza = (isset($_POST['OpcjaFormularza']) ? $_POST['OpcjaFormularza'] : 'zapisz');
                    $Wartosci = $Formularz->ZwrocWartosciPol($_POST);
                    if(isset($_POST['add-replacement-part'])){
                        $OpcjaFormularza = "only_used_parts";
                        $PartID = $_POST['selected-replacement-part'];
                        $NewPart = $this->Baza->GetData("SELECT * FROM parts WHERE id = '$PartID'");
                        $NewPart['doc-type'] = $_POST['selected-replacement-part-document'];
                        $NewPart['doc-number'] = $_POST['selected-replacement-part-document-number'];
                        $NewPart['part_id'] = $PartID;
                        $_SESSION['interventions']['used_parts'][] = $NewPart;
                        $Wartosci['interventions']['used_parts'] = $_SESSION['interventions']['used_parts'];
                        unset($_POST['selected-replacement-part-document']);
                        unset($_POST['selected-replacement-part-document-number']);
                        if($NewPart['doc-type'] == 1){
                            $aNotIn = ( isset( $_SESSION['invoice']['NotIn'] ) ? $_SESSION['invoice']['NotIn'] : array() );
                            if( !empty($aNotIn) ){
                                $s_NotIn = "AND purchase_id NOT IN ('".implode("', '",$aNotIn)."')";
                            }
                            $this->Baza->Query("SELECT * FROM line_items li
                                WHERE
                                invoice = '{$NewPart['doc-number']}'
                                $s_NotIn
                                LIMIT 1
                                ");
                            $line_item = $this->Baza->GetRow();
                            if( !empty($line_item['purchase_id'])){
                                if( $_SESSION['invoice']['count'][$line_item['id']]+1 >= $i_quantity_approved )
                                $_SESSION['invoice']['NotIn'][$NewPart['doc-number']] = $line_item['purchase_id'];
                            }
                            if( isset($_SESSION['invoice']['count'][$line_item['id']]) ){
                                $_SESSION['invoice']['count'][$line_item['id']] += 1;
                            }else{
                                $_SESSION['invoice']['count'][$line_item['id']] = 1;
                            }
                        }
                    }
                    if(isset($_POST['remove-replacement-part'])){
                        $OpcjaFormularza = "only_used_parts";
                            foreach($_POST['Usun'] as $Key){
                                $this->Baza->Query("SELECT * FROM line_items li
                                        WHERE
                                        invoice = '{$_SESSION['interventions']['used_parts'][$Key]['doc-number']}'
                                        AND part_id = '{$_SESSION['interventions']['used_parts'][$Key]['part_id']}'
                                        LIMIT 1
                                        ");
                                    $line_item = $this->Baza->GetRow();        
                                if( $_SESSION['interventions']['used_parts'][$Key]['doc-type'] == 1 && isset($_SESSION['invoice']['count'][$line_item['id']]) ){

                                    if($_SESSION['invoice']['count'][$line_item['id']] > 1)
                                        $_SESSION['invoice']['count'][$line_item['id']]--;
                                    else
                                        unset($_SESSION['invoice']['count'][$line_item['id']]);
                                }
                                unset($_SESSION['invoice']['NotIn'][$_SESSION['interventions']['used_parts'][$Key]['doc-number']]);
                                unset($_SESSION['interventions']['used_parts'][$Key]);
                            }
                        $Wartosci['interventions']['used_parts'] = $_SESSION['interventions']['used_parts'];
                    }
                    if ($OpcjaFormularza == 'zapisz') {
                            echo "<div style='clear: both;'></div>\n";
                            $this->Type = $Type; 
                            if($this->SprawdzDane($Formularz, $Wartosci, $ID) && $this->SprawdzPolaWymagane($PolaWymagane, $Wartosci) && $this->SprawdzPolaNieDublujace($PolaZdublowane, $Wartosci, $ID)){
                                    $this->SendRequireCheckbox = $Wartosci['required_checkbox'];
                                    if ($this->ZapiszDaneElementu($Formularz, $Wartosci, $Formularz->ZwrocDanePrzeslanychPlikow(), $ID)) {
                                            $this->ShowOK();
                                            return;
                                    }
                                    else {
                                            Usefull::ShowKomunikatError('<b>Wystąpił problem! Dane nie zostały zapisane.</b><br/>'.$this->Baza->GetLastErrorDescription());
                                    }
                            }else{
                                    Usefull::ShowKomunikatError('<b>'.$this->Error.'</b>');
                            }
                    }
                    if($Type == "WarrantyRepairItem"){
                        $Wartosci['finanse']['stawka_za_dojazd'] = $this->GetDefaultStakeForDrive();
                        $Wartosci['finanse']['default_amount'] = $this->GetDefaultAmountForDevice($DeviceID);
                    }

                    $Wartosci = $this->AkcjaPrzeladowanie($Wartosci, $Formularz, $ID);
                    $Formularz = $this->AkcjaPrzeladowanieFormularz($Wartosci, $Formularz, $ID);
                    foreach($this->PolaWymaganeNiewypelnione as $NazwaPola){
                            $Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
                    }
                    foreach($this->PolaZdublowane as $NazwaPola){
                            $Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
                    }
                    echo "<div style='width: 70%; margin: 0 auto;'>";
                        $Urzadzenia->AkcjaSzczegolyProste($DaneUrzadzenia);
                    echo "</div>";
                    $Formularz->Wyswietl($Wartosci, false);
            }
            else {
                    $Dane = $this->PobierzDaneElementu($ID);
                    if($Type == "WarrantyServiceItem"){
                        $Formularz = $this->GenerujFormularzPrzeglad($Dane, false);
                    }else if($Type == "WarrantyRepairItem"){
                        $Formularz = $this->GenerujFormularzNaprawa($Dane, false);
                    }else if($Type == "SetupItem"){
                        $Formularz = $this->GenerujFormularz($Dane, false);
                    }
                    echo "<div style='width: 70%; margin: 0 auto;'>";
                        $Urzadzenia->AkcjaSzczegolyProste($DaneUrzadzenia);
                    echo "</div>";
                    $Formularz->Wyswietl($Dane, false);
            }
	}

        function GetTypyInterwencji(){
            return array('tuning' => 'Regulacja (T - Tuning & Adjustment)', 'mechanical_repair' => 'Mechaniczna naprawa (M – Mechanical Repair)',
                             'electrical_repair' => 'Elektryczna naprawa (E – Electrical Repair)',  'part_replacement' => 'Wymiana elementów (P - Part Replacement)', 'anything_else' => 'Cokolwiek innego (A – Anything Else)');
        }

        function GetDefaultStakeForDrive(){
            return $this->Baza->GetValue("SELECT value FROM settings WHERE id = '11'");
        }

        function GetDefaultAmountForDevice($DeviceID){
            return $this->Baza->GetValue("SELECT devices_cost_of_repair FROM devices WHERE id = '$DeviceID'");
        }

        function SetReplacementParts($Czesci){
            $this->ReplacementParts = array();
            foreach($Czesci as $PartID => $Part){
                $this->ReplacementParts[$PartID] = $Part['kzc']." | ".$Part['title'];
            }
        }

        function WyswietlAJAX($Akcja){
            if(isset($_POST['part_id']) && !empty($_POST['part_id']) && is_numeric($_POST['part_id']) && isset($_POST['z_id']) && !empty($_POST['z_id']) && is_numeric($_POST['z_id']) ){
                $userID = $this->Baza->GetValue("SELECT user_id FROM packages WHERE id = '".$_POST['z_id']."'");
                $aNotIn = ( isset( $_SESSION['invoice']['NotIn'] ) ? $_SESSION['invoice']['NotIn'] : array() );
                if($this->Uzytkownik->IsAdmin()){
                    $InvoiceNumber = $this->GetInvoiceNumber($userID, $_POST['part_id'], $aNotIn);
                }elseif($this->Uzytkownik->CzyUprawniony()){
                    $InvoiceNumber = $this->GetInvoiceNumber($userID, $_POST['part_id'], $aNotIn);
                }else{
                    if( $this->Uzytkownik->ZwrocIdUzytkownika() == $userID)
                        $InvoiceNumber = $this->GetInvoiceNumber($this->Uzytkownik->ZwrocIdUzytkownika(), $_POST['part_id'], $aNotIn);
                    else
                        echo 'error';
                }
                echo $InvoiceNumber;
            }else{
                echo 'brak';
            }
        }
        
        function GetInvoiceNumber( $userID, $PartID, $NotIn = array() ){
            if( !empty($NotIn) ){
                $s_NotIn = "AND purchase_id NOT IN ('".implode("', '",$NotIn)."')";
            }
            $this->Baza->Query("SELECT li.* FROM line_items li
                LEFT JOIN purchases p ON ( p.user_id = '{$userID}' )
                WHERE were_used < quantity_approved
                AND part_id = '{$PartID}'
                $s_NotIn
                LIMIT 1
                ");
            $invoice = $this->Baza->GetRow();
            if( $invoice != false ){
                foreach($_SESSION['interventions']['used_parts'] as $Part ){
                    if( $Part['doc-type'] == 1 && $Part['part_id'] == $PartID && $Part['doc-number'] == $invoice['invoice']){
                        if( $invoice['quantity_approved'] > intval($invoice['were_used'])+1+(isset($_SESSION['invoice']['count'][$invoice['invoice']]) ? $_SESSION['invoice']['count'][$invoice['invoice']] : 0) ){
                               $ReturnInvoice = $invoice['invoice'];
                           }elseif( $invoice['quantity_approved'] == intval($invoice['were_used'])+1+(isset($_SESSION['invoice']['count'][$invoice['invoice']]) ? $_SESSION['invoice']['count'][$invoice['invoice']] : 0) ){
                               $NotIn[$invoice['invoice']] = $invoice['purchase_id'];
                               $ReturnInvoice = $invoice['invoice'];
                           }else{
                               $NotIn[$invoice['invoice']] = $invoice['purchase_id'];
                               $ReturnInvoice = $this->GetInvoiceNumber($userID, $PartID, $NotIn);
                           }
                           break;
                    }
                }
                if( empty($ReturnInvoice) ){
                    if( $invoice['quantity_approved'] > intval($invoice['were_used'])+1+(isset($_SESSION['invoice']['count'][$invoice['invoice']]) ? $_SESSION['invoice']['count'][$invoice['invoice']] : 0) ){
                        $ReturnInvoice = $invoice['invoice'];
                    }elseif( $invoice['quantity_approved'] == intval($invoice['were_used'])+1+(isset($_SESSION['invoice']['count'][$invoice['invoice']]) ? $_SESSION['invoice']['count'][$invoice['invoice']] : 0) ){
                        $NotIn[$invoice['invoice']] = $invoice['purchase_id'];
                        $ReturnInvoice = $invoice['invoice'];
                    }else{
                        $NotIn[$invoice['invoice']] = $invoice['purchase_id'];
                        $ReturnInvoice = $this->GetInvoiceNumber($userID, $PartID, $NotIn);
                    }
                }
            }else{
                $ReturnInvoice = '';
            }
            return $ReturnInvoice;
        }
        
        
        public function GetPackages(){
            $user_id = $this->UserID;
            $company_id = $this->Uzytkownik->ZwrocInfo("company_id");
            $IsPackageSQL = "SELECT id FROM packages WHERE status=0 AND user_id = '$user_id' AND company_id = '$company_id'";
            $PackageID = $this->Baza->GetValue($IsPackageSQL);
            if( $PackageID )
                return $PackageID;
            else
                return $this->ZrobNoweZlecenie ();
        }
        
        public function ZrobNoweZlecenie(){
            $Save['user_id'] = $this->UserID;
            $Save['company_id'] = $this->Uzytkownik->ZwrocInfo("company_id");
            $Save['status'] = '0';
            $Save['title'] = "";
            $Save['created_at'] = date("Y-m-d H:i:s");
            $Query = $this->Baza->PrepareInsert("packages", $Save);
            $this->Baza->Query($Query);
            return $this->Baza->GetLastInsertID();
        }
}
?>
