<?php
/**
 * Moduł numery seryjne
 * 
 * @author		Michal Bugański <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2012
 * @package		Panelplus 
 * @version		1.0
 */

class ModulNumerySeryjne extends ModulBazowy {

    private $Categories;
    private $Head;
    private $Title;
    private $Query;
    private $Functions;
    private $FunctionOnElementsBeforeMakeRow;
    protected $allowedImportTypes;
    protected $errors;
    protected $columnsFormat;
    protected $importTitle;

	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->ModulyBezMenuBocznego[] = $this->Parametr;
            if(!$this->Uzytkownik->IsAdmin()){
                $this->ModulyBezEdycji[] = $this->Parametr;
                $this->ModulyBezDodawania[] = $this->Parametr;
            }
            $this->Tabela = 'serial_numbers';
            $this->PoleID = 'id';
            $this->PoleNazwy = "snu";
            $this->PoleSort = "id";
            $this->SortHow = "DESC";
            $this->Nazwa = 'Numery seryjne';
            if($this->Uzytkownik->IsAdmin()){
//                Urządzenie, Rola, firma, użytkownik oraz zakres dat rozruchu urządzenia (Od-Do), kategorie polskie, kategorie włoskie, czynność
                $Urzadzenia = new ModulUrzadzenia($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
                $UrzadzeniaLista = $Urzadzenia->GetList();
                $ModulFirmy = new ModulFirmy($this->Baza, $this->Uzytkownik, "test123", $this->Sciezka);
                $Companies = $ModulFirmy->GetList();
                $ModulUzytkownicy = new ModulUzytkownicy($this->Baza, $this->Uzytkownik, "test123", $this->Sciezka);
                $Users = $ModulUzytkownicy->GetListWithCompanies();
                
                $this->Filtry[] = array('nazwa' => 'category_id', 'opis' => 'Kategorie', 'opcje' => $this->GetCategories(), "typ" => "lista", "domyslna" => "kategoria", "class"=>'polskie');
                $this->Filtry[] = array('nazwa' => 'user_id', 'opis' => 'Użytkownik', 'opcje' => $Users, "typ" => "lista", "domyslna" => "Użytkownik");
                $this->Filtry[] = array('nazwa' => 'p.company_id', 'opis' => 'Firma', 'opcje' => $Companies, "typ" => "lista", "domyslna" => "firma");
                $this->Filtry[] = array('nazwa' => 'happened_at', 'opis' => 'Data zdarzenia', "typ" => "data_oddo", 'clear'=>true );
/*                
                $this->Filtry[] = array('nazwa' => 'role', 'opis' => 'Rola', 'opcje' => Role::GetRoles($this->Baza), "typ" => "lista", "domyslna" => "rola");
                $this->Filtry[] = array('nazwa' => 'user_id', 'opis' => 'Użytkownik', 'opcje' => $Users, "typ" => "lista", "domyslna" => "Użytkownik");
                $this->Filtry[] = array('nazwa' => 'type', 'opis' => 'czynność', 'opcje' => $this->GetItemTypes(), "typ" => "lista", "domyslna" => "czynność");
                $this->Filtry[] = array('nazwa' => 'italiancategory_id', 'opis' => 'Kategorie włoskie', 'opcje' => $this->GetCategories('it'), "typ" => "lista", "domyslna" => "kategoria", "class"=>'wloskie', 'clear'=>true);
                $this->Filtry[] = array('nazwa' => 'device_id', 'opis' => 'Urządzenie', 'opcje' => $UrzadzeniaLista, "typ" => "lista", "domyslna" => "Urządzenie");*/
              }
            $this->Filtry[] = array('nazwa' => 'snu', 'opis' => false, "typ" => "tekst", "placeholder" => "Numer seryjny", 'search' => true);
            if(isset($_GET['id']) && is_numeric($_GET['id'])){
                if($this->ZablokujDostep($_GET['id']) == false){
                    $this->ZablokowaneElementyIDs['szczegoly'][] = $_GET['id'];
                    $this->ZablokowaneElementyIDs['kasowanie'][] = $_GET['id'];
                }
            }
            if($this->Uzytkownik->IsAdmin() == false){
                $this->ModulyBezKasowanie[] = $this->Parametr;
            }
            $this->CzySaOpcjeWarunkowe = true;
            if(isset($_GET['back']) && $_GET['back'] == "items"){
                $this->LinkPowrotu = "?modul=items&akcja=szczegoly&id={$_GET['bid']}";
                $this->PrzyciskiFormularza['anuluj']['link'] = $this->LinkPowrotu;
            }

            $this->allowedImportTypes = array('xls','xlsx' ,'ods');
            $this->columnsFormat = '
                <tr><td rowspan="2">Lp.</td><td colspan="2">Dane zainstalowanych urządzeń</td></tr>
                <tr><td>Urządzenie</td><td>Numer seryjny</td></tr>
                ';
            $this->importTitle = 'numery seryjne';

            /* uzupełnienie nowych kolumn z gwarancją w serial_numbers */
//            if($_GET['uzupelnij'] == 1){
//                $all_devices = $this->Baza->GetValues("SELECT DISTINCT(device_id) FROM $this->Tabela");
//                foreach($all_devices as $dev){
//                    $device = $this->Baza->GetData("SELECT * FROM devices WHERE id = '$dev'");
//                    $save['warranty_service'] = $device['warranty_service'];
//                    $save['warranty_service_months'] = $device['warranty_service_months'];
//                    $save['warranty'] = $device['warranty'];
//                    $update = $this->Baza->PrepareUpdate($this->Tabela, $save, array('device_id' => $dev));
//                    $this->Baza->Query($update);
//                }
//            }
            
	}

        function PobierzListeElementow($Filtry = array()) {
            $this->lp = true;
            $this->NoSort[] = "elementy_count";
            $this->NoSort[] = "zatwierdzony";
            $this->NoSort[] = "happened_at";
            $Wyniki = array(
                'id' => "ID",
                'snu' => "Numer seryjny",
                'zatwierdzony' => array('naglowek' => 'Zatwierdzony', 'elementy' => Usefull::GetCheckImg(), 'td_class' => 'active-check', 'type' => 'checked-many'),
                'device_name' => 'Urządzenie',
                'elementy_count' => array('naglowek' => 'Elementy', 'td_styl' => 'text-align: center;'),
                'happened_at' => 'Zerowe Uruchomienie',
                'updated_at' => 'Zmieniono',
                'tr_class' =>  array( 'class' => array('0' => 'normal', '1' => 'no_warranty_list'), 'element_name' => 'no_warranty'),
                'tr_color' => false
            );
            $Where = $this->GenerujWarunki();
            $Sort = $this->GenerujSortowanie();
            $this->Baza->Query($this->QueryPagination("SELECT DISTINCT(f.$this->PoleID) as $this->PoleID, f.snu, f.updated_at, f.approved, CONCAT(dt.devices_title_title,' (',d.kzu,')') as device_name, dt.devices_title_title
                        FROM $this->Tabela f
                        LEFT JOIN devices d ON(d.id = f.device_id)
                        LEFT JOIN categories ct ON (d.category_id = ct.id)
                        LEFT JOIN devices_title dt ON(dt.devices_title_id = d.devices_title_devices_title_id)
                        LEFT JOIN items i ON(i.serial_number_id = f.id)
                        LEFT JOIN users u ON(u.id = i.user_id )
                        $Where $Sort", $this->ParametrPaginacji, 30));
            return $Wyniki;
	}

        /**
         * Ustawia domyślny warunek w zapytaniu do listy elementów
         * @return string
         */
	function DomyslnyWarunek(){
            $Where= array();
            /*if(isset($_GET['type'])){
                if($_GET['type'] == 'warranty'){
                    $Where[] = "type = 'SetupItem'";
                }elseif($_GET['type'] == 'intervention'){
                    $Where[] = "type = 'WarrantyRepairItem'";
                }
            }*/
            if($this->Uzytkownik->IsAdmin() == false){
                $Where[] = "p.company_id = '{$this->Uzytkownik->ZwrocInfo("company_id")}'";
            }
            return implode(' AND ', $Where);
	}

        function ObrobkaDanychLista($Elementy){
            $Rozruchy = $this->Baza->GetResultAsArray("SELECT serial_number_id, happened_at, approved, no_warranty FROM items WHERE serial_number_id IN(".implode(",",$this->ListIDs).") AND type = 'SetupItem'", "serial_number_id");
            $County = $this->Baza->GetOptions("SELECT serial_number_id, count(*) as ile FROM items WHERE serial_number_id IN(".implode(",",$this->ListIDs).") GROUP BY serial_number_id");
            foreach($Elementy as $Idx => $Element){
                $Elementy[$Idx]['elementy_count'] = intval($County[$Element[$this->PoleID]]);
                if($Elementy[$Idx]['elementy_count'] > 0){
                    $this->ZablokowaneElementyIDs['kasowanie'][] = $Element['id'];
                }
                $Elementy[$Idx]['zatwierdzony'] = ($Element['approved'] == 1 && $Rozruchy[$Element[$this->PoleID]]['approved'] == 1 ? 1 : 0);
                $Elementy[$Idx]['happened_at'] = (!isset($Rozruchy[$Element[$this->PoleID]]['happened_at']) ? "brak" : UsefullTime::PolskiDzien(date("D, d.m.Y", strtotime($Rozruchy[$Element[$this->PoleID]]['happened_at']))));
                $Elementy[$Idx]['updated_at'] = date("d.m.Y H:i", strtotime($Element['updated_at']));
//                $Elementy[$Idx]['no_warranty'] = isset($Rozruchy) && !empty($Rozruchy) ? $Rozruchy[$Element[$this->PoleID]]['no_warranty'] : '';
            }
            return $Elementy;
        }

        function PobierzDaneElementu($ID, $Typ = null) {
            $Dane = $this->Baza->GetData("SELECT f.*, CONCAT(dt.devices_title_title,' (',d.kzu,')') as device_name, dt.devices_title_title
                                    FROM $this->Tabela f
                                    LEFT JOIN devices d ON(d.id = f.device_id)
                                    LEFT JOIN devices_title dt ON(dt.devices_title_id = d.devices_title_devices_title_id)
                                    WHERE f.$this->PoleID = '$ID'");
	    $Dane['actions_history'] = $this->Baza->GetRows("SELECT * FROM items WHERE serial_number_id = '{$ID}'");
            return $Dane;
        }

	function &GenerujFormularzSzczegoly(&$Wartosc= array(), $Mapuj = false) {
	    $Zlecenia = new ModulZestawienieZlecen($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
        
            $Formularz = parent::GenerujFormularz($Wartosci, $Mapuj);
            $Formularz->DodajPole('snu', 'tekst', 'Numer Seryjny', array('show_label'=>true,'div_class'=>'super-form-span4',));
            $Formularz->DodajPole('kzu', 'tekst', 'Numer Katalogowy', array('show_label'=>true,'div_class'=>'super-form-span4',));
            $Formularz->DodajPole('devices_title_devices_title_id', 'lista-chosen-single', 'Urządzenie', array('show_label'=>true,'div_class'=>'super-form-span4', 'elementy' => $this->GetSelect('UrzadzeniaNazwa')));
            $Formularz->DodajPole('category_id', 'lista-chosen-single', 'Kategoria', array('show_label'=>true,'div_class'=>'super-form-span4', 'elementy' => $this->GetSelect('Kategorie')));
            $Formularz->DodajPole('clear', 'clear', '', array());

            $Formularz->DodajPole('client_companyname', 'tekst', 'Firma/Instytucja', array('show_label'=>true,'div_class'=>'super-form-span4',));
//            $Formularz->DodajPole('client_firstname_surname', 'tekst', 'Użytkownik/Właściciel', array('show_label'=>true,'div_class'=>'super-form-span4',));
            $Formularz->DodajPole('clear', 'clear', '', array());
            $Formularz->DodajPole('client_street', 'lista-chosen-single', 'Adres', array('show_label'=>true,'div_class'=>'super-form-span4', 'elementy' => $this->GetSelect('Urzadzenia')));
            $Formularz->DodajPole('client_city', 'tekst', 'Miejscowość', array('show_label'=>true,'div_class'=>'super-form-span4',));
            $Formularz->DodajPole('client_postcode', 'tekst', 'Kod pocztowy', array('show_label'=>true,'div_class'=>'super-form-span4'));
            $Formularz->DodajPole('client_phone', 'tekst', 'Telefon', array('show_label'=>true,'div_class'=>'super-form-span4',));            
            $Formularz->DodajPole('clear', 'clear', '', array());
            
            $Formularz->DodajPole('actions_history', 'historia_czynnosci', 'Historia czynności', array('show_label'=>true,'div_class'=>'super-form-span1', 'elementy'=>$Zlecenia->GetTypeActions()));
            $Formularz->DodajPole('clear', 'clear', '', array());
            return $Formularz;
	}
        
        
        function AkcjaSzczegoly($ID){
            $Dane = $this->PobierzDaneElementu($ID);
            $ModulDevices = new ModulUrzadzenia($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);    
            $Devices = $ModulDevices->PobierzDaneElementu($Dane['device_id']);
            $Dane += $Devices;
            
            //$ModulParts = new ModulCzesci($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
            $ModulItems = new ModulZestawienieZlecen($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
            $Items = $this->Baza->GetRows("SELECT * FROM items WHERE serial_number_id = '$ID'");
            if($Items){
                foreach($Items as $Idx => $Item){
                    if($Item['type'] == "SetupItem"){
 //                       $Dane['client_companyname'] = $Item['client_companyname'];
 //                       $Dane['client_firstname_surname'] = $Item['client_firstname'].' '.$Item['client_surname'];
                        $Dane['client_street'] = $Item['client_street'];
                        $Dane['client_city'] = $Item['client_city'];
                        $Dane['client_postcode'] = $Item['client_postcode'];
                        $Dane['client_phone'] = $Item['client_phone'];
                    }
                    $Items[$Idx]['paczka'] = "<a href='?modul=zlecenia&akcja=szczegoly&id={$Item['id']}' class='pokaz textowy' style='color: #000'><nobr>".$Item['id']." ".$Item['title']."</nobr>".(!empty($Item['invoice_number']) ? "<br />({$Item['invoice_number']})" : "")."</a>";
                }
            }
            $Formularz = $this->GenerujFormularzSzczegoly($Dane);
            $Formularz->WyswietlDane($Dane);
            echo "<div class='clear'></div>";
//            $this->VAR_DUMP($Dane);
        }

        function GetItemTypes(){
            return array('ZeroSetupItem' => 'zerowe uruchomienie', 'SetupItem' => 'serwisowe uruchomienie', 'WarrantyRepairItem' => 'naprawa gwarancyjna', 'WarrantyServiceItem' => 'przegląd gwarancyjny');
        }

        function DostepDoDanychHistorii($CompanyID){
            if(!$this->Uzytkownik->IsAdmin() && $this->Uzytkownik->ZwrocInfo("company_id") != $CompanyID){
                return false;
            }
            return true;
        }

        function ZablokujDostep($ID) {
            if($_GET['akcja'] == "kasowanie"){
                if($this->Baza->GetValue("SELECT count(*) as ile FROM items WHERE serial_number_id = '$ID' ") > 0){
                    return false;
                }
            }
            if($this->Uzytkownik->IsAdmin()){
                return true;
            }
//            return true;
            $CheckAccess = $this->Baza->GetValue("SELECT count(f.$this->PoleID) FROM $this->Tabela f
                                                    LEFT JOIN items i ON(i.serial_number_id = f.id)
                                                    LEFT JOIN packages p ON(p.id = i.package_id)
                                                    {$this->GenerujWarunki('f')} AND f.$this->PoleID = '$ID'");
            if($CheckAccess > 0){
                return true;
            }
        }

	function GenerujWarunki($AliasTabeli = null) {
		$Where = $this->DomyslnyWarunek();
		$SNUWhere = '';
		if (isset($_SESSION['Filtry']) && count($_SESSION['Filtry'])) {
			for ($i = 0; $i < count($this->Filtry); $i++) {
				$Pole = $this->Filtry[$i]['nazwa'];
				if (isset($_SESSION['Filtry'][$Pole])) {
                                    $Wartosc = $_SESSION['Filtry'][$Pole];
                                    $ExpPole = explode("|", $Pole);
                                    $WhereArray = "";
                                    foreach($ExpPole as $NewPole){
                                        if($this->Filtry[$i]['typ'] == "lista" || $this->Filtry[$i]['typ'] == "checkbox"){
                                            if(is_array($Wartosc)){
                                                $WhereArraySecond = "";
                                                foreach($Wartosc as $Value){
                                                    if($NewPole == 'italiancategory_id')
                                                        $WhereArraySecond .= ($WhereArraySecond != '' ? ' OR ' : '').($AliasTabeli ? "$AliasTabeli." : '')."($NewPole = '{$Value}' OR ic.ancestry LIKE '%{$Value}%')";
                                                    elseif($NewPole == 'category_id')
                                                        $WhereArraySecond .= ($WhereArraySecond != '' ? ' OR ' : '').($AliasTabeli ? "$AliasTabeli." : '')."($NewPole = '{$Value}' OR ct.ancestry LIKE '%{$Value}%')";
                                                    else
                                                        $WhereArraySecond .= ($WhereArraySecond != '' ? ' OR ' : '').($AliasTabeli ? "$AliasTabeli." : '')."$NewPole = '$Value'";
                                                }
                                                if($WhereArraySecond != ""){
                                                    $WhereArray .= ($WhereArray != '' ? ' OR ' : '')."($WhereArraySecond)";
                                                }
                                            }else{
                                                $WhereArray .= ($WhereArray != '' ? ' OR ' : '').($AliasTabeli ? "$AliasTabeli." : '')."$NewPole = '$Wartosc'";
                                            }
                                        }elseif($this->Filtry[$i]['typ'] == "data_oddo" ){
                                            if( !empty($Wartosc['od']) && UsefullTime::CheckDate($Wartosc['od']) && !empty($Wartosc['do']) && UsefullTime::CheckDate($Wartosc['do']) ){
                                                $WhereArray = "DATE(".$this->Filtry[$i]['nazwa'].") BETWEEN '".$Wartosc['od']."' AND '".$Wartosc['do']."'";
                                            }elseif(!empty($Wartosc['od']) && UsefullTime::CheckDate($Wartosc['od'])){
                                                $WhereArray = "DATE(".$this->Filtry[$i]['nazwa'].") > '".$Wartosc['od']."'";
                                            }elseif(!empty($Wartosc['do']) && UsefullTime::CheckDate($Wartosc['do'])){
                                                $WhereArray = "DATE(".$this->Filtry[$i]['nazwa'].") < '".$Wartosc['do']."'";
                                            }
//                                            $WhereArray .= " AND type='SetupItem'";
                                        }else{
                                            $WhereArray .= ($WhereArray != '' ? ' OR ' : '').($AliasTabeli ? "$AliasTabeli." : '')."$NewPole REGEXP '".preg_replace( '/([*?])/', '.\1', $Wartosc)."'";
                                            if($NewPole == 'snu' && !$this->Uzytkownik->IsAdmin()){
                                                $SNUWhere .= ' OR '.($AliasTabeli ? "$AliasTabeli." : '')."$NewPole LIKE '$Wartosc'";
                                            }
                                        }
                                    }
                                    $Where .= ($Where != '' ? ' AND ' : '')."($WhereArray)" . $SNUWhere;
				}
			}
		}
		return ($Where != '' ? "WHERE $Where" : '');
	}

        function GetCategories(){
            if($this->Categories != false){
                return $this->Categories;
            }
            $this->Categories = $this->Baza->GetOptions("SELECT 
                    id, CONCAT( title, ' [', code ,']')
                    FROM categories ic
                    WHERE status = 2
                    ORDER BY ancestry");
            return $this->Categories;
        }
/*
        public function Pobierz($Akcja) {

            switch ($_GET['type']){
                case 'warranty':
                    $this->Warranty();
                    break;
                case 'intervention':
                    $this->Intervention();
                    break;
                case 'serialnumber':
                    $this->SerialNumber();
                    break;
                case 'all':
                    $this->Warranty();
                    $this->Intervention();
                    $this->SerialNumber();
                    break;
                default:
                    break;
            }
            
            $ExportToExcel = new ModulGenerateExcel($this->Baza);
            $ExportToExcel->SetSheetsTitles( $this->Title );
            $ExportToExcel->SetHeaders( $this->Head );
            $ExportToExcel->SetQuerys( $this->Query );
            $ExportToExcel->SetAutoColumnDimensions( true );
            if(isset($this->Functions) && !empty($this->Functions))
                $ExportToExcel->SetFunctions($this->Functions);
            if(isset($this->FunctionOnElementsBeforeMakeRow) && !empty($this->FunctionOnElementsBeforeMakeRow))
                $ExportToExcel->SetFunctionOnElementsBeforeMakeRow($this->FunctionOnElementsBeforeMakeRow);
            $ExportToExcel->GenerateExcel();
            $ExportToExcel->seve();
        }

        function Intervention(){
            $this->Head[] = 
                array(
                    'cmpy_code' => 'cmpy_code', //Kod kraju - będziemy sami go wpisywać	
                    'id' => 'intervention_number', //Numer interwencji - ID opis w emailu	
                    'snu' => 'serial_number', 
                    'kzu' => 'item_code',
                    'happened_at' => 'intervention_date', //Data interwencji (ta sama tabela co ID numer interwencji)
                    'setapitemdate' => 'warranty_starting_date', //Data pierwszego uruchomienia
                    'faulty_component_code_1' => 'faulty_component_code_1', //Kod części zamiennej użytej przy naprawie gwarancyjnej, jeśli jest więcej części to  pozycja kod 2 i pozycja kod 3
                    'action_code_1' => 'action_code_1', //Symbol kodu z tabeli w formularzu wpisywania naprawy gwarancyjnej (Typ interwencji)
                    'faulty_component_code_2' => 'faulty_component_code_2', //Jeżeli jest wymiana części musi być kod P, jeśli nie pierwsze trzy zaznaczony z tabeli Typ interwencji. 
                    'action_code_2' => 'action_code_2',
                    'faulty_component_code_3' => 'faulty_component_code_3',
                    'action_code_3' => 'action_code_3',
                    'spare_parts_cost' => 'spare_parts_cost', //Kolumnę wypełniamy sami
                    'total_amount' => 'intervention_cost', //Suma dojazdu i robocizny - pobieramy z danej interwencji
            );
            
            $this->Title[] = 'Intervention';
            $Where = $this->GenerujWarunki();
            $Sort = $this->GenerujSortowanie('f');

            $this->Query[] = "SELECT 
                NULL as cmpy_code, i.id, f.snu, i.total_amount, d.kzu,
                tuning, mechanical_repair, electrical_repair, part_replacement,
                DATE_FORMAT( i.happened_at, '%d.%m.%Y') AS happened_at,
                ( SELECT DATE_FORMAT( i2.happened_at, '%d.%m.%Y') FROM $this->Tabela sn2 LEFT JOIN items i2 ON (i2.serial_number_id = sn2.id AND i2.type = 'SetupItem') WHERE sn2.id = f.id) AS setapitemdate,
                IF( i.no_warranty = 0, d.warranty, NULL) AS warranty,
                GROUP_CONCAT(pt.kzc SEPARATOR '|') AS parts
                FROM $this->Tabela f
                LEFT JOIN devices d ON (d.id = f.device_id)
                LEFT JOIN categories ct ON (d.category_id = ct.id)
                LEFT JOIN italiancategories ic ON (d.italiancategory_id = ic.id)
                LEFT JOIN devices_title dt ON (dt.devices_title_id = d.devices_title_devices_title_id)
                LEFT JOIN items i ON (i.serial_number_id = f.id)
                LEFT JOIN interventions iv ON (iv.warranty_repair_item_id = i.id)
                LEFT JOIN used_parts up ON (up.item_id = i.id)
                LEFT JOIN parts pt ON (up.part_id = pt.id)
                LEFT JOIN packages p ON (p.id = i.package_id)
                LEFT JOIN companies c ON (p.company_id = c.id)
                LEFT JOIN users u ON(u.id = p.user_id )
                $Where
                GROUP BY i.id
                $Sort"
            ;
            $this->Functions[] = 'ModulNumerySeryjne::InterventionRowModyfication';
        }
        
        static function InterventionRowModyfication($row){
             $TypyInterwencji = array('tuning' => 'T', 'mechanical_repair' => 'M',
                'electrical_repair' => 'E',  'part_replacement' => 'P');
            $RowTypI = array();
            $Parts = explode('|', $row['parts']);
            $PartsCount = count($Parts);
            $i=1;
            if( $row['part_replacement'] === '1'){
                if($PartsCount > 3){
                    for($i; $i<=3; $i++){
                        $row['faulty_component_code_'.$i] = $Parts[$i-1];
                        $row['action_code_'.$i] = $TypyInterwencji['part_replacement'];
                    }
                }else{
                    for($i; $i<=$PartsCount; $i++){
                        $row['faulty_component_code_'.$i] = $Parts[$i-1];
                        $row['action_code_'.$i] = 'P';
                    }
                }
            }
            if($PartsCount < 3){
                foreach($TypyInterwencji as $TypI=>$SkrotI){
                    if( isset($row[$TypI]) )
                        if($row[$TypI] === '1' && !in_array($TypI, array('part_replacement')) ){
                            $RowTypI[] = $SkrotI;
                        }
                }
                $i = $PartsCount+1;
                if(is_array($RowTypI))
                    $RowTypI = array_reverse($RowTypI);
                
                for($i; $i <= 3; $i++ ){
                    $row['faulty_component_code_'.$i] = '';
                    if(is_array($RowTypI) && !empty($RowTypI))
                        $row['action_code_'.$i] = array_pop($RowTypI);
                }
            }
            
            return $row;
        }
        
        function Warranty(){
            $this->Head[] = 
                array(
                    'cmpy_code' => 'cmpy_code',
                    'id' => 'registration_number', //Numer interwencji - ID opis w emailu
                    'snu' => 'serial_number', 
                    'kzu' => 'item_code', 
                    'created_at' => 'registration_date', //Data wpisania pierwszego uruchomienia do bazy przez serwisanta
                    'happened_at' => 'warranty_starting_date', //Data pierwszego uruchomienia
                    'warranty' => 'warranty_expiring_date', //Data zakończenia gwarancji
                    'total_amount' => 'registration_cost', //Kwota pierwszego uruchomienia
            );
            $this->Title[] = 'Warranty';
            $Where = $this->GenerujWarunki();
            $Sort = $this->GenerujSortowanie('f');

            $this->Query[] = "SELECT 
                NULL as cmpy_code, i.id, 
                f.snu, total_amount, 
                d.kzu,
                DATE_FORMAT( i.happened_at, '%d.%m.%Y') AS happened_at,
                DATE_FORMAT( i.created_at, '%d.%m.%Y') AS created_at,
                IF( i.no_warranty = 0,  DATE_FORMAT( DATE_ADD(i.happened_at, INTERVAL d.warranty MONTH) , '%d.%m.%Y'), NULL) AS warranty
                FROM $this->Tabela f
                LEFT JOIN devices d ON (d.id = f.device_id)
                LEFT JOIN categories ct ON (d.category_id = ct.id)
                LEFT JOIN italiancategories ic ON (d.italiancategory_id = ic.id)
                LEFT JOIN devices_title dt ON (dt.devices_title_id = d.devices_title_devices_title_id)
                LEFT JOIN items i ON (i.serial_number_id = f.id)
                LEFT JOIN interventions iv ON (iv.warranty_repair_item_id = i.id)
                LEFT JOIN used_parts up ON (up.item_id = i.id)
                LEFT JOIN parts pt ON (up.part_id = pt.id)
                LEFT JOIN packages p ON (p.id = i.package_id)
                LEFT JOIN companies c ON (p.company_id = c.id)
                LEFT JOIN users u ON(u.id = p.user_id )
                $Where
                GROUP BY i.id
                $Sort";
            
        }
            
            
        function SerialNumber(){
            
            $this->PoleSort = "i.id";
            $this->SortHow = "ASC";
            
            $TypyInterwencji = array('tuning' => 'Regulacja (T - Tuning & Adjustment)', 'mechanical_repair' => 'Mechaniczna naprawa (M – Mechanical Repair)',
                'electrical_repair' => 'Elektryczna naprawa (E – Electrical Repair)',  'part_replacement' => 'Wymiana elementów (P - Part Replacement)', 'anything_else' => 'Cokolwiek innego (A – Anything Else)');
            $RodzajeInterwencji = array( 'SetupItem' => 'Rozruch',
                'WarrantyRepairItem' => 'Naprawa gwarancyjna',
                'WarrantyServiceItem' => 'Przegląd gwarancyjny',
            );

            $this->Title[] = 'NumerSeryjny' ;
            
            $this->Head[] =
                array(
                    'id' => 'ID zdarzenia',
                    'snu' => "Numer seryjny",
                    'kzu' => 'KZU',
                    'device_name' => 'Urządzenie',
                    'code'=>'Kategoria włoska',
                    'month' => "Miesiąc",
                    'year' => "Rok",
                    'happened_at' => "Data zdarzenia",
                    'simplename' => "Firma",
                    'user_name' => "Użytkownik",
                    'type' => array( 'naglowek'=> "Typ interwencji", 'elementy'=>$RodzajeInterwencji),
                    'total_amount' => "Całkowita kwota",
                    'amount' => 'Stawka za godzinę',
                    'distance' => 'Dojazd (ilość km)',
                    'costs_per_km' => 'Stawka za km',
                    'rbh_amount'=>'Ilość roboczogodzin',
                    'tuning' => array( 'naglowek'=> $TypyInterwencji['tuning'], 'elementy' => Usefull::GetTakNie()),
                    'mechanical_repair' => array( 'naglowek'=> $TypyInterwencji['mechanical_repair'], 'elementy' => Usefull::GetTakNie()),
                    'electrical_repair' => array( 'naglowek'=> $TypyInterwencji['electrical_repair'], 'elementy' => Usefull::GetTakNie()),
                    'part_replacement' => array( 'naglowek'=> $TypyInterwencji['part_replacement'], 'elementy' => Usefull::GetTakNie()),
                    'anything_else' => array( 'naglowek'=> $TypyInterwencji['anything_else'], 'elementy' => Usefull::GetTakNie()),
                    'part_title'=>'Nazwa Części',
                    'kzc'=>'KZC',
            );
            $Where = $this->GenerujWarunki();
            $Sort = $this->GenerujSortowanie();

            $this->Query[] = "SELECT i.id, f.snu, d.kzu, happened_at, c.simplename, type, total_amount,tuning,
                mechanical_repair, electrical_repair, part_replacement, anything_else,
                dt.devices_title_title as device_name, i.amount,i.distance, i.rbh_amount,costs_per_km,
                c.simplename, DATE_FORMAT( happened_at, '%m') AS month, DATE_FORMAT( happened_at, '%Y') AS year,
                pt.title AS part_title, pt.kzc,
                ic.code, concat(firstname, ' ', surname) as user_name
                FROM $this->Tabela f
                LEFT JOIN devices d ON (d.id = f.device_id)
                LEFT JOIN categories ct ON (d.category_id = ct.id)
                LEFT JOIN italiancategories ic ON (d.italiancategory_id = ic.id)
                LEFT JOIN devices_title dt ON (dt.devices_title_id = d.devices_title_devices_title_id)
                LEFT JOIN items i ON (i.serial_number_id = f.id)
                LEFT JOIN interventions iv ON (iv.warranty_repair_item_id = i.id)
                LEFT JOIN used_parts up ON (up.item_id = i.id)
                LEFT JOIN parts pt ON (up.part_id = pt.id)
                LEFT JOIN packages p ON (p.id = i.package_id)
                LEFT JOIN companies c ON (p.company_id = c.id)
                LEFT JOIN users u ON(u.id = p.user_id )
                $Where
                $Sort";
             $this->FunctionOnElementsBeforeMakeRow[] = 'ModulNumerySeryjne::SerialNumberRowModyfication';
        }
        
        static function SerialNumberRowModyfication(){
            $args = func_get_args();
            $Dane = $args[0];
            $ItemID = 0;
            foreach($Dane as $Key=>$Val){
                if($ItemID != 0){
                    if($ItemID == $Val['id']){
                        $Dane[$Key]['total_amount'] = 0.00;
                        $Dane[$Key]['amount'] = 0.00;
                        $Dane[$Key]['distance'] = 0.00;
                        $Dane[$Key]['costs_per_km'] = 0.00;
                        $Dane[$Key]['rbh_amount'] = 0.00;
                    }
                }
                $ItemID = $Val['id'];
            }
            return $Dane;
        }
*/        
        function GetActions($ID = null){
            $Akcje = array();
            if($this->Uzytkownik->IsAdmin()){
/*                $SerialNumber = array('link' => "?modul=$this->Parametr&akcja=pobierz&type=serialnumber\" target=\"_blank\"", 'class' => 'lista', 'img' => 'excel.gif', 'desc' => 'NumerySeryjne');
                $Intervention = array('link' => "?modul=$this->Parametr&akcja=pobierz&type=intervention\" target=\"_blank\"", 'class' => 'lista', 'img' => 'excel.gif', 'desc' => 'Interwencje');
                $Warranty = array('link' => "?modul=$this->Parametr&akcja=pobierz&type=warranty\" target=\"_blank\"", 'class' => 'lista', 'img' => 'excel.gif', 'desc' => 'Gwarancje');
                $Akcje['lista'] = array( $SerialNumber, $Intervention, $Warranty );*/
            }
            $Dodawanie = array('link' => "?modul=$this->Parametr&akcja=dodawanie", 'class' => 'dodawanie', 'img' => 'add.gif', 'desc' => 'Nowy nr seryjny');
            $Lista = array('link' => "?modul=$this->Parametr&akcja=lista", 'class' => 'lista', 'img' => 'list.gif', 'desc' => 'Lista numerów');
            if(!in_array($this->Parametr, $this->ModulyBezDodawania)){
                $Akcje['akcja'][] = $Dodawanie;
            }
            $Akcje['akcja'][] = $Lista;
            return $Akcje;
        }

        function &GenerujFormularz(&$Wartosci = array(), $Mapuj = false){
            $Formularz = parent::GenerujFormularz($Wartosci, $Mapuj);
            $Formularz->DodajPole('snu', 'tekst', 'Numer seryjny*', array('show_label'=>true,'div_class'=>'super-form-span3',));
            $Formularz->DodajPole('clear', 'clear', '', array());            
            $Formularz->DodajPole('device_id', 'lista-chosen-single', 'Urządzenie*', array('show_label'=>true,'div_class'=>'super-form-span3', 'elementy' => $this->GetSelect('Urzadzenia')));
//            $Formularz->DodajPole('approved', 'lista-chosen-single', 'Zatwierdzony', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => Usefull::GetTakNie(), 'atrybuty' => array('style' => 'width: 80px;') ));
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array('show_label'=>false, "elementy" => $this->PrzyciskiFormularza));
            }
            
            $Formularz->DodajPoleNieDublujace('snu', false);
            
            return $Formularz;
	}
        
        function GetDevices(){
            if($this->Devices != false){
                return $this->Devices;
            }
            $ModulUrzadzenia = new ModulUrzadzenia($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
            $this->Devices = $ModulUrzadzenia->GetList();
            return $this->Devices;
        }
        
          function OperacjePrzedZapisem($Wartosci, $ID){
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
/**
 * funkcja pobierająca nazwy urządzeń
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @return array zwraca listę nazw urządzeń
 */           
        
        function GetListaNazw(){
            return $this->Baza->GetOptions('SELECT devices_title_id, devices_title_title FROM devices_title');
        }     
        
    /**
 * funkcja pobierająca listę ścieżek kategorii
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @param string $tabela Nazwa tabeli z której należy pobrać listę ścieżek kategorii
 * @return array $category
 */          
        
        protected function GetListPathKategorii( $tabela ){
            $path_category = $this->Baza->GetOptions( "SELECT id, concat( path_to,' [', code, ']') FROM $tabela " );
            return $path_category;
        }

        function putImportedElements($objPHPExcel, $rowNumber){

            $rowNumber++;
            $deviceTitle = $objPHPExcel->getActiveSheet(0)->getCellByColumnAndRow(1, $rowNumber)->getValue();
            $snu = $objPHPExcel->getActiveSheet(0)->getCellByColumnAndRow(2, $rowNumber)->getValue();

            //check if snu is already exists in DB
            $snuInDB = $this->Baza->GetValue("SELECT id FROM $this->Tabela WHERE snu = '{$snu}'");
            if($snuInDB){
                echo Usefull::ShowKomunikatOstrzezenie("nie zaimporowano wiersza nr $rowNumber - taki numer seryjny znajduję się już w bazie<br>");
                return false;
            }

            //check if in DB is device of this name
            $deviceId = $this->Baza->GetValue("SELECT id FROM devices WHERE title = '{$deviceTitle}'");
            if(!$deviceId){
                echo Usefull::ShowKomunikatOstrzezenie("nie zaimporowano wiersza nr $rowNumber - nie znaleziono urządzenia o nazwie $deviceTitle<br>");
                return false;
            }

            //prepare and insert serial number
            $data = array(
                'snu' => $snu,
                'device_id' => $deviceId,
                'approved' => 1,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            );

            $insert = $this->Baza->PrepareInsert('serial_numbers', $data);
            if(!$this->Baza->Query($insert)){
               echo Usefull::ShowKomunikatOstrzezenie("nie zaimporowano wiersza nr $rowNumber - błąd przy wprowadzaniu numeru seryjnego<br>");
               return false;
            }

        }
        
}
?>
