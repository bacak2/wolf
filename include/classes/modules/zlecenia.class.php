<?php
/**
 * Moduł elementów zlecenia
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012
 * @package		Panelplus
 * @version		1.0
 */

define('ZeroSetupItem', '8');
define('SetupItem', '16');
define('WarrantyRepairItem', '32');
define('WarrantyServiceItem', '64');
define('RepairItem', '128');
define('ServiceItem', '256');

class ModulZestawienieZlecen extends ModulBazowy {

	
	
	public $OKs = false;
        public $Status = false;
        public $Companies = false;
        public $Users = false;
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
        public $Type = false;
        
        
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->Tabela = 'items';
            $this->PoleID = 'id';
            $this->PoleSort = 'id';
            $this->SortHow = 'DESC';
            $this->PoleNazwy = 'title';
            $this->Nazwa = 'Zestawienie zleceń';
            $this->ModulyBezMenuBocznego[] = $this->Parametr;
            //$this->ModulyBezEdycji[] = $this->Parametr;
            $this->Filtry[] = array('nazwa' => 'status', 'opis' => 'Status zlecenia', 'opcje' => Usefull::GetPackageStatuses($this->Uzytkownik->IsAdmin()), "typ" => "checkbox", 'col'=>'2', 'class'=>Usefull::GetTRClass('order'));
            $this->Filtry[] = array('opis' => true);
            $this->Filtry[] = array('nazwa' => 'happened_at', 'opis' => 'Data czynności', "typ" => "data_oddo" );
            $this->Filtry[] = array('nazwa' => 'sent_at', 'opis' => 'Data wysłania do WOLF', "typ" => "data_oddo" );
            $this->Filtry[] = array('nazwa' => 'p.company_id', 'opis' => 'Firma', 'opcje' => $this->GetCompanies(), "typ" => "lista", "domyslna" => "firma");
            $this->Filtry[] = array('nazwa' => 'type', 'opis' => 'Czynność', 'opcje' => $this->GetTypeActions(), "typ" => "lista", "domyslna" => "czynność", 'clear'=>true);
            $this->Filtry[] = array('nazwa' => "title|CONCAT(u.firstname,' ',u.surname)|p.id", 'opis' => false, "typ" => "tekst", "domyslna" => "", 'placeholder'=>'Szukaj zlecenia', 'search' => true);
            if($this->Uzytkownik->IsAdmin()){
                $this->DefaultFilters = array('status'=>array( 4,3 ) );
            }
            $this->CzySaOpcjeWarunkowe = true;
            if(isset($_GET['id']) && is_numeric($_GET['id'])){
                if($this->ZablokujDostep($_GET['id']) == false){
                    $this->ZablokowaneElementyIDs['szczegoly'][] = $_GET['id'];
                    $this->ZablokowaneElementyIDs['edycja'][] = $_GET['id'];
                    $this->ZablokowaneElementyIDs['kasowanie'][] = $_GET['id'];
                }
            }
	    if(isset($_GET['akcja']) && $_GET['akcja'] == "dodawanie"){
                $this->FiltersAndActionsWidth = true;
            }
//            $this->Baza->EnableLog();
	}
        function PobierzListeElementow($Filtry = array()) {
            $Wyniki = array(
                    $this->PoleID => array('naglowek' => 'Id', 'td_styl' => 'width: 40px;'),
                    'type' => array('naglowek' => 'Czynność',  'td_styl' => 'width: 150px;', 'elementy'=>$this->GetTypeActions()),
 //                   'invoice' =>  array('naglowek' => 'FV', 'elementy' => Usefull::GetCheckImg(), 'td_class' => 'active-check'),
 //                   'invoice_number' => array('naglowek' => 'Nr faktury'),
                    'status' => array('naglowek' => 'Status' ,'td_styl' => 'width: 100px;text-align: left;', "elementy" => Usefull::GetPackageStatuses($this->Uzytkownik->IsAdmin()), "class" => Usefull::GetPackageStatusesColor()),
                    'happened_at' => array('naglowek' => 'Data czynności',  'td_styl' => 'width: 80px;'),
                    'sent_at' => array('naglowek' => 'Data wysłania do WOLF',  'td_styl' => 'width: 80px;'),
                    'finalized_at' => array('naglowek' => 'Zakończono',  'td_styl' => 'width: 80px;'),
                    'tr_class' =>  array( 'class' => Usefull::GetTRClass('order'), 'element_name' => 'expired'),
                    'tr_color' => (isset($_GET['selectid']) && is_numeric($_GET['selectid']) ? array( 'color' => array( $_GET['selectid'] => 'style="border: 2px solid #FFAAAA"'), 'element_name' => $this->PoleID ) : false),                );
                $Where = $this->GenerujWarunki();
                $Sort = $this->GenerujSortowanie();
                $max_opoznienie = intval($this->Baza->GetValue("SELECT value FROM settings WHERE var LIKE 'purchases_latest_limit'")) - 1;
                $this->Baza->Query($this->QueryPagination("SELECT p.*, 
				IF(DATEDIFF( NOW(), sent_at) > $max_opoznienie AND status IN(3,4), status, NULL ) as expired,
				CONCAT(u.firstname,' ',u.surname) as client_name, c.simplename
                                FROM $this->Tabela p
                                LEFT JOIN users u ON (u.id = p.user_id) 
                                LEFT JOIN companies c ON(c.id = p.company_id)
                                $Where $Sort", $this->ParametrPaginacji, 30));
                return $Wyniki;
	}
	
	function Wyswietl($Akcja) {
	    if(isset($_GET['id']))
	      $this->Status = $this->Baza->GetValue("SELECT status FROM {$this->Tabela} WHERE id = '{$_GET['id']}'");
            $this->OperacjePoPrzeladowaniu( isset($_GET['id']) ? $_GET['id'] : false);
            parent::Wyswietl($Akcja);
        }

        function  WyswietlAkcje($ID = null) {
            if($this->OKs != false){
                Usefull::ShowKomunikatOk($this->OKs, true);
            }
            if($this->Error != false){
                Usefull::ShowKomunikatError($this->Error, true);
            }
            parent::WyswietlAkcje($ID);
        }

        function DomyslnyWarunek(){
            if($this->Uzytkownik->IsAdmin()){
                $domyslny = '';
            }else{
				$domyslny = ($this->Uzytkownik->CzyUprawniony() == false ? "p.user_id = '{$this->Uzytkownik->ZwrocIdUzytkownika()}'" : "p.company_id = '{$this->Uzytkownik->ZwrocInfo('company_id')}'");
            }
			if($this->WykonywanaAkcja == 'edycja'){
				$domyslny .=  (($domyslny == '') ? "" : " AND ") ."p.status = 0";
			}
				$domyslny .=  (($domyslny == '') ? "" : " AND ") . "is_delete = 0";
            return $domyslny;
        }

        function ZablokujDostep($ID) {
            if($this->Uzytkownik->IsAdmin()){
                return true;
            }
            $CheckAccess = $this->Baza->GetValue("SELECT count(p.$this->PoleID) FROM $this->Tabela p WHERE p.$this->PoleID = '$ID' AND {$this->DomyslnyWarunek()}");
            if($CheckAccess > 0){
                return true;
            }
            return false;
        }

        function GetCompanies(){
            if($this->Companies == false){
                $ModulFirmy = new ModulFirmy($this->Baza, $this->Uzytkownik, "test123", $this->Sciezka);
                $this->Companies = $ModulFirmy->GetList();
            }
            return $this->Companies;
        }

        function GetUsers(){
            if($this->Users == false){
                $ModulUzytkownicy = new ModulUzytkownicy($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
                $this->Users = $ModulUzytkownicy->GetListWithCompanies();
            }
            return $this->Users;
        }

        function AkcjaLista($Filtry = array()) {
            ?><script type="text/javascript" src="js/plugins/jquery.qtip.js"></script><?php
            parent::AkcjaLista($Filtry);
        }
       
        function ObrobkaDanychLista($Elementy){
            foreach($Elementy as $ElementyKey => $Element){
                ob_start();
                include(SCIEZKA_SZABLONOW."tooltip-zlecenia.tpl.php");
                $tooltip = ob_get_contents();
                ob_clean();
                $Elementy[$ElementyKey]['tooltip_content'] = $tooltip;
            }
            return $Elementy;
        }        

        function GetActions($ID){
            $Dodawanie = array('link' => "?modul=$this->Parametr&akcja=dodawanie", 'class' => 'dodawanie', 'img' => 'add.gif', 'desc' => 'Dodaj zlecenie');
            $Lista = array('link' => "?modul=$this->Parametr&akcja=lista", 'class' => 'lista', 'img' => 'list.gif', 'desc' => 'Lista zleceń');
            if($this->Uzytkownik->IsAdmin()){
                if($this->Status == 3){
                    if($this->WykonywanaAkcja == "szczegoly"){
                        $Akcje['szczegoly'][] = array('link' => "javascript:AcceptPackage($ID)", 'class' => 'dodawanie', 'img' => 'accept.gif', 'desc' => 'Akceptuj');
                    }
                    $Akcje['szczegoly'][] = array('link' => "?modul=zlecenia&akcja=szczegoly&id=$ID&act=cancel", 'class' => 'usun', 'img' => 'cancel.gif', 'desc' => 'Zamknij');
                }
                if($this->Status == 4){
                    $Akcje['szczegoly'][] = array('link' => "?modul=zlecenia&akcja=szczegoly&id=$ID&act=revive", 'class' => 'usun', 'img' => 'accept_red.gif', 'desc' => 'Przywróć');
                }
            }
/*            if($this->Status < 3 || $this->Uzytkownik->IsAdmin()){
                $Akcje['szczegoly'][] = array('link' => "?modul=$this->Parametr&akcja=edycja&id=$ID&back=details", 'class' => 'drukowanie', 'img' => 'pencil.gif', 'desc' => 'Edycja');
            }else{
                 $this->ZablokowaneElementyIDs['edycja'][] = $_GET['id'];
            }
            if(!in_array($this->Parametr, $this->ModulyBezDodawania)){
                $Dodawanie = array('link' => "?modul=$this->Parametr&akcja=dodawanie", 'class' => 'dodawanie', 'img' => 'add.gif', 'desc' => 'Nowe zestawienie');
                $Akcje['lista'][] = $Dodawanie;
            }
            $IleItems = $this->Baza->GetValue("SELECT count(*) FROM items WHERE package_id = '$ID'");
            if($this->Status <= 3 && $IleItems == 0){
                $Akcje['szczegoly'][] = array('link' => "?modul=$this->Parametr&akcja=kasowanie&id=$ID", 'class' => 'usun', 'img' => 'bin_empty.gif', 'desc' => 'Usuń');
            }else{
                $this->ZablokowaneElementyIDs['kasowanie'][] = $_GET['id'];
            }
            if(($this->Status == 0 || $this->Status == 1) && ($this->Uzytkownik->CzyUprawniony() && $IleItems > 0)){
                $Akcje['szczegoly'][] = array('link' => "javascript:SendPackage($ID)", 'class' => 'wyslij', 'img' => 'email.gif', 'desc' => 'Wyślij');
            }else if($this->Status == 0  && $IleItems > 0){
                $Akcje['szczegoly'][] = array('link' => "javascript:ReadyPackage($ID)", 'class' => 'gotowe', 'img' => 'tick_blue.gif', 'desc' => 'Gotowe');
            }
            $Akcje['szczegoly'][] = array('link' => "?modul=$this->Parametr&akcja=szczegoly&id=$ID&typ=drukuj\" target=\"_blank\"", 'class' => 'drukowanie', 'img' => 'printer.gif', 'desc' => 'Zestawienie');
            if($this->Status < 3 || $this->Uzytkownik->IsAdmin()){
                $Akcje['szczegoly'][] = array('link' => "?modul=items&akcja=dodaj_element&zlid=$ID", 'class' => 'dodawanie', 'img' => 'add.gif', 'desc' => 'Nowe zlecenie');
            }
*/			
	    if(($this->Status == 0 || $this->Status == 1) && $this->Uzytkownik->CzyUprawniony()){
	      $Akcje['szczegoly'][] = array('link' => "javascript:SendPackage($ID)", 'class' => 'wyslij', 'img' => 'email.gif', 'desc' => 'Wyślij');
	    }
	    $Akcje['akcja'] = array($Dodawanie, $Lista);
           
            return $Akcje;
        }        
        
    function GetTypeActions(){
        return array('ZeroSetupItem'=>'zerowe uruchomienie', 'SetupItem' => 'serwisowe uruchomienie',
		     'WarrantyRepairItem' => 'naprawa gwarancyjna', 'WarrantyServiceItem' => 'przegląd gwarancyjny',
		     //'RepairItem'=>'naprawa pogwarancyjna', 'ServiceItem'=>'przegląd pogwaranycjny'
		     );
    }
    
    function AkcjaDodawanie(){
	    $typ = array('SetupItem'=>'zero_guided_start',
			  'ZeroSetupItem'=>'service_guided_start');
	    $ActionSerialNumber = 0;
            $BlockSerialNumber = false;
            $Urzadzenia = new ModulUrzadzenia($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
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
                            $DaneUrzadzenia = $Urzadzenia->PobierzDaneElementu($_SESSION['new-serial-device-id']);
                        }
                        if($ActionSerialNumber == 1){
                            $UrzadzeniaLista = $Urzadzenia->GetList(($this->Uzytkownik->ZwrocInfo('role')));
                            $BlockSerialNumber = true;
                        }
                        if($ActionSerialNumber == 2){
                            $SerialNumber = $this->Baza->GetData("SELECT * FROM serial_numbers WHERE snu = '{$_POST['SerialNumberCheck']}'");
                            $_SESSION['new-serial-device-id'] = $SerialNumber['device_id'];
			    $DaneUrzadzenia = $Urzadzenia->PobierzDaneElementu($_SESSION['new-serial-device-id']);
			    $Items = $this->Baza->GetRows("SELECT * FROM items WHERE serial_number_id = '{$SerialNumber['id']}'");
                            $BlockSerialNumber = true;
                        }
			if($ActionSerialNumber == 8){
                            $SerialNumber = $this->Baza->GetData("SELECT * FROM serial_numbers WHERE snu = '{$_POST['SerialNumberCheck']}'");
                            $_SESSION['new-serial-device-id'] = $SerialNumber['device_id'];
			    $DaneUrzadzenia = $Urzadzenia->PobierzDaneElementu($_SESSION['new-serial-device-id']);
			    $Items = $this->Baza->GetData("SELECT * FROM items WHERE serial_number_id = '{$SerialNumber['id']}'");
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
		    $UrzadzeniaLista = $Urzadzenia->GetList(  $this->Uzytkownik->ZwrocInfo('role') );
		    $BlockSerialNumber = true;
		    $Values['SerialNumberCheck'] = $_SESSION['SerialNumberCheck'];
                }else{
                    $ActionSerialNumber = 3;
                    $BlockSerialNumber = true;
                    $Values['SerialNumberCheck'] = $_SESSION['SerialNumberCheck'];
                    $_SESSION['new-serial-device-id'] = $_POST['new-serial-device-id'];
                }
            }
            if( isset($_GET['act']) && $_GET['act'] == "setup" ){
	      $CheckSerialNumber = $this->CheckSerialNumber($_SESSION['SerialNumberCheck']);
	      $ActionSerialNumber = 4;
	      $BlockSerialNumber = true;
	      $Values['SerialNumberCheck'] = $_SESSION['SerialNumberCheck'];
	      $Values['new-serial-device-id'] = $_SESSION['new-serial-device-id'];
	      $DaneUrzadzenia = $Urzadzenia->PobierzDaneElementu($Values['new-serial-device-id']);
	      $this->WymaganyRozruch = ($DaneUrzadzenia['warranty_service'] == 1 ? true : false);
	      $QueryWymagania = "SELECT concat(category_required_id, '_', id) as id, required_name FROM required ORDER BY category_required_id";
	      $KategorieWymagan = $this->GetSelect("KategorieWymagan");
	      $Wymogi = $this->GetIDName(false, $QueryWymagania);
	      if($CheckSerialNumber == 1 ){
		$_SESSION['NoSerial'] = true;
		if($DaneUrzadzenia['setup_zero'] == 1){
		  foreach($Wymogi as $WK=>$WV){
		    list($KatID, $WymID) = explode('_', $WK);
		    if(in_array($WymID, $DaneUrzadzenia['devices_params']['zero_guided_start'])){
		      $this->RequireCheckbox[$KategorieWymagan[$KatID]][$WymID] = $WV;
		    }
		  }
		  $this->Type = "ZeroSetupItem";
                }elseif($DaneUrzadzenia['setup'] == 1){
		  foreach($Wymogi as $WK=>$WV){
		    list($KatID, $WymID) = explode('_', $WK);
		    if(in_array($WymID, $DaneUrzadzenia['devices_params']['service_guided_start_parameters'])){
		      $this->RequireCheckbox[$KategorieWymagan[$KatID]][$WymID] = $WV;  
		    }
		  }
		  $this->Type = 'SetupItem';
                }else{
		  $this->RequireCheckbox = array();
                  $this->Type = 'SetupItem';
                }
	      }elseif($CheckSerialNumber == ZeroSetupItem){
		$_SESSION['NoSerial'] = false;
		foreach($Wymogi as $WK=>$WV){
		    list($KatID, $WymID) = explode('_', $WK);
		    if(in_array($WymID, $DaneUrzadzenia['devices_params']['zero_guided_start'])){
		      $this->RequireCheckbox[$KategorieWymagan[$KatID]][$WymID] = $WV;
		    }
		  }
		  $this->Type = "ZeroSetupItem";
	      }elseif($CheckSerialNumber == SetupItem){
		$_SESSION['NoSerial'] = false;
		if($DaneUrzadzenia['setup_zero'] == 1){
		  $SerialNumber = $this->Baza->GetData("SELECT * FROM serial_numbers WHERE snu = '{$Values['SerialNumberCheck']}'");
		  $Items = $this->Baza->GetData("SELECT * FROM items WHERE serial_number_id = '{$SerialNumber['id']}'");
		}
		$this->Type = 'SetupItem';
		foreach($Wymogi as $WK=>$WV){
		  list($KatID, $WymID) = explode('_', $WK);
		  if(in_array($WymID, $DaneUrzadzenia['devices_params']['service_guided_start_parameters'])){
		    $this->RequireCheckbox[$KategorieWymagan[$KatID]][$WymID] = $WV;  
		  }
		}
	      }
            } 
            if((isset($_GET['act']) && ($_GET['act'] == "service" || $_GET['act'] == "repair")) && $this->CheckSerialNumber($_SESSION['SerialNumberCheck']) == 2){
                $ActionSerialNumber = ($_GET['act'] == "repair" ? 6 : 5);
                $BlockSerialNumber = true;
                $DaneUrzadzenia = $Urzadzenia->PobierzDaneElementu($_SESSION['new-serial-device-id']);
                $SerialNumber = $this->Baza->GetData("SELECT * FROM serial_numbers WHERE snu = '{$_SESSION['SerialNumberCheck']}'");
                $Items = $this->Baza->GetData("SELECT * FROM items WHERE serial_number_id = '{$SerialNumber['id']}' AND type='SetupItem'");
                $Items ['serial_numbers'] = $Values['SerialNumberCheck'] = $_SESSION['SerialNumberCheck'];
                $Items ['device_id'] = $Values['new-serial-device-id'] = $_SESSION['new-serial-device-id'];
		
                $Czesci = $Urzadzenia->GetParts($_SESSION['new-serial-device-id']);
		if( $_GET['act'] == "service"  ){
		  $this->Type = WarrantyServiceItem;
		  $DaneUrzadzenia = $Urzadzenia->PobierzDaneElementu($Values['new-serial-device-id']);
		  $QueryWymagania = "SELECT concat(category_required_id, '_', id) as id, required_name FROM required ORDER BY category_required_id";
		  $KategorieWymagan = $this->GetSelect("KategorieWymagan");
		  $Wymogi = $this->GetIDName(false, $QueryWymagania);
		  foreach($Wymogi as $WK=>$WV){
		    list($KatID, $WymID) = explode('_', $WK);
		    if(in_array($WymID, $DaneUrzadzenia['devices_params']['service_guided_start_parameters'])){
		      $this->RequireCheckbox[$KategorieWymagan[$KatID]][$WymID] = $WV;
		    }
		  }
                }else{
		  $this->Type = WarrantyRepairItem;
                }
                
            }

            ### Zapis rozruchu ###
            if(isset($_POST['OpcjaFormularza'])){
                if($_GET['act'] == "service"){
                    $Formularz = $this->GenerujFormularz($_POST, true);
                }else if($_GET['act'] == "repair"){
                    $Formularz = $this->GenerujFormularzNaprawa($_POST, true);
                }else{
                    $Formularz = $this->GenerujFormularz($_POST, true);
                }
                $PolaWymagane = $Formularz->ZwrocPolaWymagane();
                $PolaZdublowane = $Formularz->ZwrocPolaNieDublujace();
                $Wartosci = $Formularz->ZwrocWartosciPol($_POST);
/*                if(isset($_POST['add-replacement-part'])){
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
                    */
                if ($_POST['OpcjaFormularza'] == 'zapisz'){
		    echo "<div style='clear: both;'></div>\n";
                    if($this->SprawdzDane($Formularz, $Wartosci) && $this->SprawdzPliki($Formularz->ZwrocDanePrzeslanychPlikow()) && $this->SprawdzPolaWymagane($PolaWymagane, $Wartosci) && $this->SprawdzPolaNieDublujace($PolaZdublowane, $Wartosci)){
                        if($_GET['act'] == "service"){
                            $Wartosci['type'] = 'WarrantyServiceItem';
                            $this->SendRequireCheckbox = $Wartosci['required_checkbox'];
                        }else if($_GET['act'] == "repair"){
                            $Wartosci['type'] = 'WarrantyRepairItem';
                        }else{
                            $Wartosci['type'] = $this->Type;
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
                $Wartosci = $this->AkcjaPrzeladowanie($Wartosci, $Formularz);
                $Formularz = $this->AkcjaPrzeladowanieFormularz($Wartosci, $Formularz);
                foreach($this->PolaWymaganeNiewypelnione as $NazwaPola){
                        $Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
                        if($NazwaPola == "client_dane"){
			  $Formularz->UstawOpcjePola($NazwaPola, 'wymagane-puste', $this->WymaganePuste, false);
                        }elseif($NazwaPola == "required_checkbox"){
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
	    $CountSerialNumbers = $this->Baza->GetValue("SELECT count(*) FROM serial_numbers WHERE snu = '$SerialNumber'");
            if($CountSerialNumbers == 0){
                return 1;
            }else{
		$SerialNumberDane = $this->Baza->GetData("SELECT * FROM serial_numbers WHERE snu = '$SerialNumber'");
                $DeviceDane = $this->Baza->GetData("SELECT * FROM devices WHERE id = '{$SerialNumberDane['device_id']}'");
                if($SerialNumberDane['warranty_service'] == '0'){
		    $Rozruch = $this->Baza->GetValue("SELECT happened_at FROM items WHERE serial_number_id = '{$SerialNumberDane['id']}' AND type='SetupItem'");
                }else{
		    if($DeviceDane['setup_zero'] == '1'){
			$RozruchZero = $this->Baza->GetValue("SELECT happened_at FROM items WHERE serial_number_id = '{$SerialNumberDane['id']}' AND type='ZeroSetupItem'");
		    }
		    if($DeviceDane['setup'] == '1' ){
			$Rozruch = $this->Baza->GetValue("SELECT happened_at FROM items WHERE serial_number_id = '{$SerialNumberDane['id']}' AND type='SetupItem'");
		    }
                    $Rozruch = $this->Baza->GetValue("SELECT happened_at FROM items WHERE serial_number_id = '{$SerialNumberDane['id']}' AND type='SetupItem'");
                    //$this->VAR_DUMP($DeviceDane);
		    if( $DeviceDane['setup_zero'] == '1' &&  $RozruchZero === false ){
		      $this->Type = "ZeroSetupItem";
		    }elseif($DeviceDane['setup'] == '1' && $Rozruch === false){
		      $this->Type = 'SetupItem';
                    }elseif($DeviceDane['setup'] == '0' && $Rozruch === false){
                        $this->Type = 'SetupItem';
                    }
                    
		    if($this->Type){;
		      return(constant("$this->Type"));
		    }
                }

                $IsWarranty = true;
                $this->DataOstatniejAkcji = $this->Baza->GetValue("SELECT happened_at FROM items WHERE serial_number_id = '{$SerialNumberDane['id']}' AND (type='SetupItem' OR type='WarrantyServiceItem')  ORDER BY happened_at DESC LIMIT 1");
                $IlePrzegladowWykonano = $this->Baza->GetValue("SELECT count(*) FROM items WHERE serial_number_id = '{$SerialNumberDane['id']}' AND (type='WarrantyServiceItem') AND approved = '1'");
                if($Rozruch){
                        $WarrantyMonths = $str = preg_replace("/[^0-9\,]+/","",$SerialNumberDane['warranty_service_months']);
                        if($WarrantyMonths != ""){
                            $Okresy = explode(",", $WarrantyMonths);
                        }else{
                            $Okresy = array();
                        }
                        $IlePrzegladow = count($Okresy);
                        $Okresy[] = $SerialNumberDane['warranty'];
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
		WHERE serial_number_id = '{$SerialNumberDane['id']}'
		    AND status IN ( 0,1,2,3 )
		    AND items.approved = 0
		ORDER BY happened_at DESC LIMIT 1");
                if( ($SerialNumberDane['approved'] == 1 || $SerialNumberDane['warranty_service'] == 0) && ($IsWarranty || $this->Uzytkownik->IsAdmin())){
                    if($IlePrzegladow <= $IlePrzegladowWykonano){
                        $this->CzyMaBycJeszczePrzeglad = false;
                    }
                    if($_GET['act'] == "repair" || $Type == "WarrantyRepairItem"){
                        $this->DataOstatniejAkcji = $Rozruch;
                    }
                    return 2;
                }elseif($SerialNumberDane['warranty_service'] == 1 && !($IsWarranty && $Rozruch) && $type == 'SetupItem' ){
                    $this->CheckError = "Niezatwierdzony rozruch";
                    return 2;
                }else{
                    if($this->Uzytkownik->IsAdmin()){
                        return 2;    
                    }elseif($SerialNumberDane['approved'] == 0 && $SerialNumberDane['warranty_service'] == 1){
                        $this->CheckError = "Dla tego urządzenia okres gwarancyjny zakończył się!";
                    }else if( $this->Uzytkownik->ZwrocInfo('role') !== 'ws' ){
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

        function SprawdzDane($Formularz, $Wartosci, $ID = NULL){
            if($_GET['act'] == "setup" || $this->Type == "SetupItem" || $this->Type == "ZeroSetupItem"){
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
                return $this->SprawdzDaneRozruch($Formularz, $Wartosci);
            }
            if($_GET['act'] == 'repair' || $this->Type == "WarrantyRepairItem"){
            }
            return true;
        }

        function SprawdzDaneRozruch($Formularz, $Wartosci){
	  $ret = true;
            if(	$Wartosci['client_dane']['client_street'] == "" ||
		$Wartosci['client_dane']['client_postcode'] == "" ||
		$Wartosci['client_dane']['client_city'] == "" ||
		$Wartosci['client_dane']['client_email'] == "" ||
		$Wartosci['client_dane']['client_phone'] == ""                
               ){
               
		$this->Error = "Proszę wypełnić wymagane pola";
		$this->PolaWymaganeNiewypelnione[] = "client_dane";
		if($Wartosci['client_dane']['client_street'] == ""){
		    $this->WymaganePuste[] = "client_street";
		}
		if($Wartosci['client_dane']['client_city'] == ""){
		    $this->WymaganePuste[] = "client_city";
		}
		if($Wartosci['client_dane']['client_postcode'] == ""){
		    $this->WymaganePuste[] = "client_postcode";
		}
		if($Wartosci['client_dane']['client_email'] == ""){
		    $this->WymaganePuste[] = "client_email";
		}
		if($Wartosci['client_dane']['client_phone'] == ""){
		    $this->WymaganePuste[] = "client_phone";
		}
	      $ret &= false;
            }
            if($this->Uzytkownik->IsAdmin() == false && $this->Dzis > date("Y-m-d", strtotime($Wartosci['happened_at']."+2 months" )) && $this->WymaganyRozruch == true){
                $this->Error = "Nie możesz wprowadzić zdarzenia sprzed 2 miesięcy";
                $this->PolaWymaganeNiewypelnione[] = "happened_at";
                $ret &= false;
            }
            foreach($Wartosci['required_checkbox'] as $ReqID=>$ReqVal){
		$val = trim($ReqVal);
                if( $val == ''){
		  if(!in_array("required_checkbox", $this->PolaWymaganeNiewypelnione))
		    $this->PolaWymaganeNiewypelnione[] = "required_checkbox";
		  $this->WymaganePuste[] = $ReqID; 
                }
            }
            return $ret;
        }

	function OperacjePrzedZapisem($Wartosci, $ID){
            if($_GET['act'] == "setup" || $this->Type == "SetupItem" || $this->Type == "ZeroSetupItem" ){
                $Wartosci = $this->OperacjePrzedZapisemRozruch($Wartosci);
            }
            if($_GET['act'] == "service" || $this->Type == "WarrantyServiceItem"){
		$Wartosci = $this->OperacjePrzedZapisemRozruch($Wartosci);
                $Wartosci['serial_number_id'] = $this->Baza->GetValue("SELECT id FROM serial_numbers WHERE snu = '{$_SESSION['SerialNumberCheck']}'");
            }
            if($_GET['act'] == "repair" || $this->Type == "WarrantyRepairItem"){
                $Wartosci = $this->OperacjePrzedZapisemNaprawa($Wartosci);
                $Wartosci['serial_number_id'] = $this->Baza->GetValue("SELECT id FROM serial_numbers WHERE snu = '{$_SESSION['SerialNumberCheck']}'");
            }
            $Now = date("Y-m-d H:i:s");
            if($this->WykonywanaAkcja == "dodawanie"){
                $Wartosci['created_at'] = $Now;
                $Wartosci['updated_at'] = $Now;
                $Wartosci['user_id'] = $this->UserID;
                $Wartosci['status'] = 0;
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
            unset($Wartosci['firm_dane']);
            $stawka_za_km = $this->Baza->GetValue("SELECT value FROM settings WHERE var LIKE 'costs_per_km'");
            $Wartosci['total_amount'] = number_format($Wartosci['amount'],2,".","");
            $Wartosci['costs_per_km'] = str_replace(",", ".", $stawka_za_km);
            $Wartosci['no_warranty'] = 0;
            $this->SaveRequireCheckbox = $Wartosci['required_checkbox'];
            unset($Wartosci['required_checkbox']);
            unset($Wartosci['serial_numbers']);
            unset($Wartosci['devices_name_id']);
            $Now = date("Y-m-d H:i:s");
            if(isset($_SESSION['NoSerial']) && $_SESSION['NoSerial']){
                $SaveSerial['snu'] = $_SESSION['SerialNumberCheck'];
                $SaveSerial['device_id'] = $_SESSION['new-serial-device-id'];
                $SaveSerial['created_at'] = $Now;
                $SaveSerial['updated_at'] = $Now;
                /** zapisujemy też aktualne warunki gwarancji, przeglądów itp **/
                $device = $this->Baza->GetData("SELECT * FROM devices WHERE id = '{$SaveSerial['device_id']}'");
                $SaveSerial['warranty_service'] = $device['warranty_service'];
                $SaveSerial['warranty_service_months'] = $device['warranty_service_months'];
                $SaveSerial['warranty'] = $device['warranty'];
	      $ZapiszSerial = $this->Baza->PrepareInsert("serial_numbers", $SaveSerial);
	      if($this->Baza->Query($ZapiszSerial)){
		  $Wartosci['serial_number_id'] = $this->Baza->GetLastInsertID();
	      }
	    }else{
	      $Wartosci['serial_number_id'] = $this->Baza->GetValue("SELECT id FROM serial_numbers WHERE snu = '{$_SESSION['SerialNumberCheck']}'");
	    }
	    
            return $Wartosci;
        }

        
        
        function OperacjePrzedZapisemNaprawa($Wartosci){
            foreach($Wartosci['client_dane'] as $Key => $Value){
                $Wartosci[$Key] = $Value;
            }
	    unset($Wartosci['client_dane']);
            unset($Wartosci['firm_dane']);
            unset($Wartosci['device_dane']);
            unset($Wartosci['serial_numbers']);
	    $this->ReplacementParts = $Wartosci['parts_dane'];
	    unset($Wartosci['parts_dane']);
	    $Dojazd = $this->Baza->GetValue("SELECT value FROM settings WHERE var LIKE 'costs_per_km'");
	    $Wartosci['costs_per_km'] = str_replace(",", ".", $Dojazd);
            $Wartosci['total_amount'] = ($Wartosci['amount']*$Wartosci['rbh_amount']) + ($Wartosci['distance'] * $Dojazd);
            return $Wartosci;
        }
        
        
        function WykonajOperacjePoZapisie($Wartosci, $ID){
            if($_GET['act'] == "setup" || $this->Type == "SetupItem" || $this->Type == "ZeroSetupItem"){
                $this->Baza->Query("DELETE FROM items_package_required_checkbox WHERE item_id = '$ID'");
                foreach($this->SaveRequireCheckbox as $ReqID=>$ReqVal){
                    $Save['check_status'] = $ReqVal;
                    $Save['item_id'] = $ID;
                    $Save['required_checkbox_id'] = $ReqID;
                    $Zap = $this->Baza->PrepareInsert("items_package_required_checkbox", $Save);
                    $this->Baza->Query($Zap);
                }
            }
            if($_GET['act'] == "repair" || $this->Type == "WarrantyRepairItem"){
                $this->Baza->Query("DELETE FROM used_parts WHERE item_id = '$ID'");
		$rep = $this->ReplacementParts;
		$part_no_error = $rep['part_no_error'];
		unset($rep['part_no_error']);
		foreach($rep as $UsedPartKey=>$UsedPart){
		    $No = preg_replace('/[^0-9]/', '', $UsedPartKey);
		    $Name = preg_replace('/_[0-9]+/', '', $UsedPartKey);
		    $Parts[$No][$Name] = $UsedPart;
		}
		foreach($Parts as $PartsNameKey=>$PartsValue){
		    $UsPartSave['item_id'] = $ID;
		    $UsPartSave['part_id'] = $PartsValue['part_id'];
		    $UsPartSave['part_serial_number'] = $PartsValue['part_serial_number'];
		    $UsPartSave['part_kat_number'] = $PartsValue['part_kat_number'];
		    $UsPartSave['part_no_error'] = $part_no_error;
		    $UsPartSave['created_at'] = date("Y-m-d H:i:s");
		    $UsPartSave['updated_at'] = date("Y-m-d H:i:s");
		    $ZapNew = $this->Baza->PrepareInsert("used_parts", $UsPartSave);
		    $this->Baza->Query($ZapNew);
		}
            }
        }

	function GetPolaDanychKlienta(){
//            $Pola['client_companyname'] = "Firma/Instytucja";
//            $Pola['client_firstname'] = "Imię";
//            $Pola['client_surname'] = "Nazwisko";
            $Pola['client_street'] = "Ulica";
  //          $Pola['client_address_number'] = "Nr";
  //          $Pola['client_local_number'] = "Lokal";
            $Pola['client_postcode'] = "Kod pocztowy";
            $Pola['client_city'] = "Miejscowosc";
            $Pola['client_email'] = "e-mail";
            $Pola['client_phone'] = "Tel. kontaktowy";
            $Pola['client_fax'] = "fax";
	    return $Pola;
        }
        
        function GetPolaFirmy(){
            $Pola['firm_name'] = "Nazwa firmy:";
            $Pola['firm_1'] = "&nbsp;";
            $Pola['firm_2'] = "&nbsp;";
            $Pola['firm_email'] = "e-mail";
            $Pola['firm_phone'] = "Tel. kontaktowy";
            $Pola['firm_fax'] = "fax";
	    return $Pola;
        }
	
	function GetPolaFirmyNaprawa(){
            $Pola['firm_name'] = "Nazwa firmy:";
            $Pola['firm_nip'] = "NIP:";
            $Pola['firm_user'] = "Użytkownik";
            $Pola['firm_email'] = "e-mail";
            $Pola['firm_phone'] = "Tel. kontaktowy";
	    return $Pola;
        }
	function GetPolaDanychKlientaNaprawa(){
//            $Pola['client_companyname'] = "Firma/Instytucja";
//            $Pola['client_firstname'] = "Imię";
//            $Pola['client_surname'] = "Nazwisko";
            $Pola['client_street'] = "Ulica";
  //          $Pola['client_address_number'] = "Nr";
  //          $Pola['client_local_number'] = "Lokal";
            $Pola['client_postcode'] = "Kod pocztowy";
            $Pola['client_city'] = "Miejscowosc";
            $Pola['client_email'] = "e-mail";
            $Pola['client_phone'] = "Tel. kontaktowy";
	    return $Pola;
        }
        
        function GetPolaUrzadzenie(){
            $Pola['device_name'] = "Nazwa urządzenia";
            $Pola['device_setup'] = "Data uruchomienia";
            $Pola['device_serial_number'] = "Numer seryjny";
            $Pola['device_firm'] = "Firma uruchamiająca";
            $Pola['device_email'] = "E-Mail";
            $Pola['device_tel'] = "Tel.";
            $Pola['device_puste'] = "&nbsp;";
	    return $Pola;
        }
        
        function GetPolaCzesci(){
            $parts[''] = '-- WYBIERZ --';
            $Query = "SELECT id, concat( title, ' [', kzc, ']') FROM parts WHERE visible = '1'";
            $parts += $this->Baza->GetOptions($Query);
            $Pola['part_id_1'] = array('title'=>"Uszkodzona cześć 1", 'elementy'=>$parts);
            $Pola['part_kat_number_1'] = "Nr kat. części 1";
            $Pola['part_serial_number_1'] = "Numer seryjny części 1";
            $Pola['part_id_2'] = array('title'=>"Uszkodzona cześć 2", 'elementy'=>$parts);
            $Pola['part_kat_number_2'] = "Nr kat. części 2";
            $Pola['part_serial_number_2'] = "Numer seryjny części 2";
            $Pola['part_no_error'] = "Kod błędu";
	    return $Pola;
        }
        
        
	function &GenerujFormularz(&$Wartosci = Array(), $Mapuj = false) {
		$Wartosci['info'] = '<small>Dołączyć wydruk analizy spalin dla mocy minimalnej i maksymalnej (dotyczy kotłów gazowych kondensacyjnych, wszystkich olejowych oraz z palnikiem nadmuchowym). W przypadku braku możliwości wykonania wydruku dołączyć zdjęcie z pomiarami analizatora na tle tabliczki znamionowej</small>';
		if(empty($Wartosci) || (!isset($Wartosci['user_id']) && !isset($Wartosci['company_id']))){
			$UserCompanyID = $this->Uzytkownik->ZwrocInfo('company_id');
			$Wartosci['company_id'] = $UserCompanyID;
			$Wartosci['user_id'] = $this->Uzytkownik->ZwrocIdUzytkownika();
		}
		elseif( isset($Wartosci['user_id']) && !empty($Wartosci['user_id']) && (!isset($Wartosci['company_id']) || empty($Wartosci['company_id'])) ) {
			$UserCompanyID = $this->Baza->GetValue("SELECT company_id FROM users WHERE id = {$Wartosci['user_id']}");
			$Wartosci['company_id'] = $UserCompanyID;
		}
		
		$Firma = new ModulFirmy($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
		$DaneFirmy = $Firma->PobierzDaneElementu($Wartosci['company_id']);
		$Wartosci['firm_dane'] = array(
				  'firm_name' => stripslashes($DaneFirmy['name']),
				  'firm_email' => $this->Baza->GetValue("SELECT email FROM users WHERE id = '{$Wartosci['user_id']}'"),
				  'firm_phone' => $DaneFirmy['phone_number'],
				  'firm_fax' => $DaneFirmy['fax'],
		);

	  $Formularz = parent::GenerujFormularz($Wartosci, $Mapuj);
	  if( (isset($_GET['act']) && $_GET['act'] === 'service') || $Wartosci['type'] == 'WarrantyServiceItem' ){
              $DataUruchomienia = 'Data przeglądu';
          }else{
              $DataUruchomienia = 'Data uruchomienia kotłowni';
          }
	  $Formularz->DodajPole('happened_at', 'tekst_data', $DataUruchomienia, array('atrybuty' => array('readonly' => 'readonly' ), 'show_label'=>true,'div_class'=>'super-form-span4 f_right'));
            if($this->Uzytkownik->IsAdmin() == false){
                $Formularz->UstawOpcjePola("happened_at", "max_today", $this->DataDozwolonejAkcji, false);
                if($this->WymaganyRozruch == true){
                    $Formularz->UstawOpcjePola("happened_at", "min", "-2 months", false);
                }
            }
	  $Formularz->DodajPole('clear', 'clear', false );
	  $Formularz->DodajPole('devices_name', 'tekst', 'Urządzenie' , array('atrybuty' => array('disabled' => 'disabled' ), 'show_label'=>true, 'div_class'=>'super-form-span3' ));
	  $Formularz->DodajPole('serial_numbers', 'tekst', 'Numer fabryczny', array('atrybuty' => array('readonly' => 'readonly' ), 'show_label'=>true,'div_class'=>'super-form-span3'));
	  $Formularz->DodajPole('devices_name_id', 'hidden', false, array());
	  $Formularz->DodajPole('devices_id', 'hidden', false, array());
	  $Formularz->DodajPole('amount', 'hidden', false, array());
          $Formularz->DodajPole('clear', 'clear', false );
	  $Formularz->DodajDoPolBezRamki('devices_name_id', true);
	  $Formularz->DodajPole('required_checkbox', 'required_checkbox', false , array('show_label'=>false, 'div_class'=>'super-form-span1', 'elementy' => $this->RequireCheckbox ));
          $Formularz->DodajPole('clear', 'clear', false );
	  $Formularz->DodajPole('info', 'tekstowo', 'Dane analizy spalin moc MIN/MAX', array('show_label'=>true, 'div_class'=>'super-form-span1')); 
	  $Formularz->DodajDoPolBezRamki('info', true);
	  $Formularz->DodajPole('clear', 'clear', false );
	  
	  $Formularz->DodajPole('client_dane', 'dane_klienta', 'Dane użytkownika', array('show_label'=>true, 'div_class'=>'super-form-span2', 'pola' => $this->GetPolaDanychKlienta()));
	  $Formularz->DodajPole('firm_dane', 'dane_klienta', 'Dane firmy', array('stan'=>array('readonly'=>'readonly'), 'show_label'=>true, 'div_class'=>'super-form-span2', 'pola' => $this->GetPolaFirmy()));
	  $Formularz->DodajPole('company_id', 'hidden', '', array('show_label'=>false));
	  $Formularz->DodajPole('clear', 'clear', false );
	  $Formularz->DodajPole('description', 'tekst_dlugi', 'Uwagi', array('show_label'=>true, 'div_class'=>'super-form-span1')); 
	  $Formularz->DodajPole('clear', 'clear', false );
	  $Formularz->DodajDoPolBezRamki('client_dane', true);
	  $Formularz->DodajDoPolBezRamki('firm_dane', true);
	  $Formularz->DodajDoSamePola('devices_name_id');
	  $Formularz->DodajDoSamePola('devices_id');
	  $Formularz->DodajDoSamePola('amount');
	  $Formularz->DodajDoSamePola('company_id');
	  
	  
	  $Formularz->DodajPoleWymagane('serial_numbers', false);
	  //$Formularz->DodajPoleWymagane('required_checkbox', false);
	  $Formularz->DodajPoleWymagane('client_dane', false);
	  $Formularz->DodajPoleWymagane('firm_dane', false);

	  if($this->Uzytkownik->IsAdmin() == false && $this->Type == WarrantyServiceItem){
	      $Formularz->UstawOpcjePola("happened_at", "max_today", $this->DataDozwolonejAkcji, false);
	      $Formularz->UstawOpcjePola("happened_at", "min", $this->DataOstatniejAkcji, false);
	  }
	  
	  if($this->WykonywanaAkcja != "szczegoly"){
	      $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
	  }
	  return $Formularz;
	}
	
	function PobierzDaneElementu($ID, $Typ = NULL){
		$Dane = parent::PobierzDaneElementu($ID);
                $this->Type = $Dane['type'];
		$Pola = $this->GetPolaDanychKlienta();
		$Dane['happened_at'] = date("Y-m-d", strtotime($Dane['happened_at']));
		foreach($Pola as $Key => $Value){
			$Dane['client_dane'][$Key] = $Dane[$Key];
		}
		$Firma = new ModulFirmy($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
		$DaneFirmy = $Firma->PobierzDaneElementu($Dane['company_id']);
		$Dane['firm_dane'] = array(
				  'firm_name' => stripcslashes($DaneFirmy['name']),
				  'firm_email' => $this->Baza->GetValue("SELECT email FROM users WHERE id = '{$Dane['user_id']}'"),
				  'firm_phone' => $DaneFirmy['phone_number'],
				  'firm_fax' => $DaneFirmy['fax'],
		);
		
		$DeviceID = $this->Baza->GetValue("SELECT device_id FROM serial_numbers WHERE id = '{$Dane['serial_number_id']}'");
		$Dane['serial_numbers'] = $this->Baza->GetValue("SELECT snu FROM serial_numbers WHERE id = '{$Dane['serial_number_id']}'");
		$Dane['devices_name'] =  $this->Baza->GetValue("SELECT title FROM devices WHERE id = '{$DeviceID}'");
		return $Dane;
	}
        

	function CzyMozeWykonacNaprawe($UserID){
	  return true;
	}
          function OperacjePoPrzeladowaniu($ID){
            if($this->Status < 3 || $this->Uzytkownik->IsAdmin()){
                if(isset($_GET['act'])){
                    if($_GET['act'] == "accept" && $this->Uzytkownik->IsAdmin()){
                        if($this->AcceptPackage($ID)){
                            //$this->OKs = "Zlecenie zostało zaakceptowane";
                        }else{
                            $this->Error = "Zlecenie nie zostało zaakceptowane";
                        }
                    }
                    if($_GET['act'] == "cancel" && $this->Uzytkownik->IsAdmin()){
                        if($this->CancelPackage($ID)){
                            //$this->OKs = "Zlecenie zostało zamknięte";
                        }else{
                            $this->Error = "Zlecenie nie zostało zamknięte";
                        }
                    }
                    if($_GET['act'] == "send" && $this->Uzytkownik->CzyUprawniony()){
                        if($this->SendPackage($ID)){
                            //$this->OKs = "Zlecenie zostało wysłane";
                        }else{
                            $this->Error = "Zlecenie nie zostało wysłane";
                        }
                    }
                }
            }
	    if($this->Status == 4){
		if($_GET['act'] == "revive" && $this->Uzytkownik->IsAdmin()){
		    if($this->RevivePackage($ID)){
			//$this->OKs = "Zlecenie zostało przywrócone";
		    }else{
			$this->Error = "Zlecenie nie zostało przywrócone";
		    }
		}
	    }
        }

        function AcceptPackage($ID){
            if($this->Baza->Query("UPDATE $this->Tabela SET approved = '1', status = '5', finalized_at = now() WHERE $this->PoleID = '$ID' AND status <= '3'")){
                $this->Status = 5;
                // wszystkie itemy ustawić w serial number approved
                $Items = $this->Baza->GetRows("SELECT * FROM items WHERE $this->PoleID = '$ID'");
				foreach($Items as $Item){
					if(($Item['approved'] == 1 || $Item['happened_at'] > date("Y-m-d H:i:s", strtotime("-1 months")) && $Item['no_warranty'] == 0)){
						$this->Baza->Query("UPDATE serial_numbers SET approved = '1' WHERE id = '{$Item['serial_number_id']}'");
					}
				}
                return true;
            }
            return false;
        }

        function CancelPackage($ID){
            $this->Status = 4;
            if($this->Baza->Query("UPDATE $this->Tabela SET status = '4', finalized_at = now() WHERE $this->PoleID = '$ID' AND status <= '3'")){
                $Items = $this->Baza->GetValue("SELECT serial_number_id FROM items WHERE id = '$ID'");
		$this->Baza->Query("UPDATE serial_numbers SET approved = '0' WHERE id = '{$Item['serial_number_id']}'");
                return true;
            }
            return false;
        }

        function RevivePackage($ID){
            $this->Status = 3;
            return $this->Baza->Query("UPDATE $this->Tabela SET status = '3', updated_at = now() WHERE $this->PoleID = '$ID' AND status = '4'");
        }


        function SendPackage($ID){
            $this->Status = 3;
            return $this->Baza->Query("UPDATE $this->Tabela SET status = '3', updated_at = now(), sent_at = now() WHERE $this->PoleID = '$ID' AND status < '3'");
        }
  
        function &GenerujFormularzNaprawa(&$Wartosci = Array(), $Mapuj = false) {
			$Admin = $this->Uzytkownik->IsAdmin();
			if(empty($Wartosci) || (!isset($Wartosci['user_id']) && !isset($Wartosci['company_id']))){
			$UserCompanyID = $this->Uzytkownik->ZwrocInfo('company_id');
			$Wartosci['company_id'] = $UserCompanyID;
			$Wartosci['user_id'] = $this->Uzytkownik->ZwrocIdUzytkownika();
		}
		elseif( isset($Wartosci['user_id']) && !empty($Wartosci['user_id']) && (!isset($Wartosci['company_id']) || empty($Wartosci['company_id'])) ) {
			$UserCompanyID = $this->Baza->GetValue("SELECT company_id FROM users WHERE id = {$Wartosci['user_id']}");
			$Wartosci['company_id'] = $UserCompanyID;
		}
		
		$Firma = new ModulFirmy($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
		$DaneFirmy = $Firma->PobierzDaneElementu($Wartosci['company_id']);
		$Wartosci['firm_dane'] = array(
				  'firm_name' => stripslashes($DaneFirmy['name']),
				  'firm_email' => $this->Baza->GetValue("SELECT email FROM users WHERE id = '{$Wartosci['user_id']}'"),
				  'firm_phone' => $DaneFirmy['phone_number'],
				  'firm_fax' => $DaneFirmy['fax'],
		);


	  $Formularz = parent::GenerujFormularz($Wartosci, $Mapuj);
	  $Formularz->DodajPole('company_id', 'hidden', false, array());
	  $Formularz->DodajPole('devices_id', 'hidden', false, array());
	  $Formularz->DodajPole('firm_dane', 'dane_klienta', 'Wolf Technika Grzewcza', array('stan'=>array('readonly'=>'readonly'), 'show_label'=>true, 'div_class'=>'super-form-span2', 'pola' => $this->GetPolaFirmyNaprawa(), 'admin'=>$Admin ));
	  $Formularz->DodajPole('client_dane', 'dane_klienta', 'Miejsce montarzu', array('stan'=>array('readonly'=>'readonly'), 'show_label'=>true, 'div_class'=>'super-form-span2', 'pola' => $this->GetPolaDanychKlientaNaprawa(), 'admin'=>$Admin));
	  $Formularz->DodajPole('clear', 'clear', false );
	  $Formularz->DodajPole('device_dane', 'dane_klienta', 'Dane Urządzenia', array('stan'=>array('readonly'=>'readonly'), 'show_label'=>true, 'div_class'=>'super-form-span2', 'pola' => $this->GetPolaUrzadzenie(), 'admin'=>$Admin ));
	  $Formularz->DodajPole('parts_dane', 'dane_klienta', 'Dane uszkodzonej części', array( 'show_label'=>true, 'div_class'=>'super-form-span2', 'pola' => $this->GetPolaCzesci(), 'script'=>array('plugins/chosen.jquery.js', 'zlecenia.js')));
	  $Formularz->DodajPole('clear', 'clear', false );
	  $Formularz->DodajPole('details', 'tekst_dlugi', 'Szczegółowy opis usterki(jakiej częście dotyczy, jakie są objawyusterek, co było przyczyną, kod awarii)', array('show_label'=>true, 'div_class'=>'super-form-span1', 'atrybuty'=>array('style'=>'height: 120px;')));
	  $Formularz->DodajPole('clear', 'clear', false );
	  
	  $Formularz->DodajPole('puste', 'sam_tekst', false, array('show_label'=>false, 'div_class'=>'super-form-span2'));
	  $Formularz->DodajPole('holidays', 'lista-chosen-single', '21:00-7:00/święta', array('elementy'=>Usefull::GetTakNie(),  'show_label'=>true, 'div_class'=>'super-form-span4'));
	  $Formularz->DodajPole('happened_at', 'tekst_data', 'Data naprawy', array('show_label'=>true, 'div_class'=>'super-form-span4', 'atrybuty' => array('readonly' => 'readonly')));
	  $Formularz->DodajPole('clear', 'clear', false );
	  
	  $Formularz->DodajPole('distance', 'tekst', 'Ilość kilometrów dojazdu do i od urządzenia', array('show_label'=>true, 'div_class'=>'super-form-span3'));
	  $Formularz->DodajPole('number_of_people', 'tekst', 'ilość osób', array('show_label'=>true, 'div_class'=>'super-form-span3'));
	  $Formularz->DodajPole('rbh', 'tekst', 'Ilość godzin pracy', array('show_label'=>true, 'div_class'=>'super-form-span3'));
	  $Formularz->DodajPole('clear', 'clear', false );
	  $Formularz->DodajPole('costs_per_km', 'hidden', false, array());
	  $Formularz->DodajPole('rbh_amount', 'hidden', false, array());
	  $Formularz->DodajPole('devices_id', 'hidden', false, array());
	  $Formularz->DodajPole('holidays_amount', 'hidden', false, array());
	  $Formularz->DodajPole('next_rbh_amount', 'hidden', false, array());
	  $Formularz->DodajPole('kmrbh_amount', 'hidden', false, array());
	  
	  if($this->Uzytkownik->IsAdmin() == false){
	      $Formularz->UstawOpcjePola("happened_at", "max_today", $this->DataDozwolonejAkcji, false);
	      $Formularz->UstawOpcjePola("happened_at", "min", $this->DataOstatniejAkcji, false);
	  }
	  $Formularz->DodajPoleWymagane("happened_at", false);
	  $Formularz->DodajPoleWymagane("details", false);
	  
	  
	  $Formularz->DodajDoPolBezRamki('holidays');
	  $Formularz->DodajDoPolBezRamki('puste');
	  $Formularz->DodajDoSamePola('holidays_amount');
	  $Formularz->DodajDoSamePola('next_rbh_amount');
	  $Formularz->DodajDoSamePola('rbh_amount');
	  $Formularz->DodajDoSamePola('costs_per_km');
	  $Formularz->DodajDoSamePola('kmrbh_amount');
	  $Formularz->DodajDoSamePola('company_id');
	  $Formularz->DodajDoSamePola('devices_id');
	  
	  if($this->WykonywanaAkcja != "szczegoly"){
	      $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
	  }
	  return $Formularz;
	}
	
	
	function AkcjaSzczegoly($ID) {
            $Dane = $this->PobierzDaneElementu($ID);

            $RequiredParams = $this->Baza->GetRows("SELECT * FROM items_package_required_checkbox WHERE item_id = '$ID'");
            if( is_array($RequiredParams)){
                foreach($RequiredParams  as $RP){
                    $Dane['required_checkbox'][$RP['required_checkbox_id']] = $RP['check_status'];
                }
            }else{
                $Dane['required_checkbox'] = array();
            }
            $Urzadzenia = new ModulUrzadzenia($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
            $DaneUrzadzenia = $Urzadzenia->PobierzDaneElementu($Dane['devices_id']);
            
		if(in_array($Dane['type'], array('SetupItem', 'ZeroSetupItem', 'WarrantyServiceItem'))){
			$QueryWymagania = "SELECT concat(category_required_id, '_', id) as id, required_name FROM required ORDER BY category_required_id";
			$KategorieWymagan = $this->GetSelect("KategorieWymagan");
			$Wymogi = $this->GetIDName(false, $QueryWymagania);
			if($Dane['type'] == 'ZeroSetupItem'){
			  foreach($Wymogi as $WK=>$WV){
				list($KatID, $WymID) = explode('_', $WK);
				if(in_array($WymID, $DaneUrzadzenia['devices_params']['zero_guided_start'])){
				  $this->RequireCheckbox[$KategorieWymagan[$KatID]][$WymID] = $WV;
				}
			  }
			}elseif(in_array($Dane['type'], array('SetupItem', 'WarrantyServiceItem'))){
			  foreach($Wymogi as $WK=>$WV){
				list($KatID, $WymID) = explode('_', $WK);
				if(in_array($WymID, $DaneUrzadzenia['devices_params']['service_guided_start_parameters'])){
				  $this->RequireCheckbox[$KategorieWymagan[$KatID]][$WymID] = $WV;  
				}
			  }
			}else{
				$this->RequireCheckbox = array();
			}
			$Formularz = $this->GenerujFormularz($Dane);
		}elseif(in_array($Dane['type'], array('WarrantyRepairItem'))){
//                $PolaFirmy = $this->GetPolaFirmyNaprawa();
                $PolaCzesci = $this->GetPolaCzesci();
//                foreach($PolaFirmy as $Key => $Value){
//                    $Dane['firm_dane'][$Key] = $Dane[$Key];
//                }
                $Parts = $this->Baza->GetRows("SELECT * FROM used_parts WHERE item_id = '$ID'");
                foreach($Parts as $UsedPartKey=>$UsedPart){
                    foreach($UsedPart as $Key => $Value){
                        $Dane['parts_dane'][$Key.'_'.(intval($UsedPartKey)+1)] = $Value;
                    }
                }
                $Dane['parts_dane']['part_no_error'] = $Dane['parts_dane']['part_no_error_1'];
                $Dane['device_dane']['device_name'] = $this->Baza->GetValue("SELECT devices_title_title FROM devices_title dt LEFT JOIN devices d ON (d.devices_title_devices_title_id = dt.devices_title_id) WHERE d.id = '{$Dane['company_id']}'");
                $Dane['device_dane']['device_setup'] = $this->Baza->GetValue("SELECT happened_at FROM $this->Tabela WHERE serial_number_id = '{$Dane['serial_number_id']}' AND type LIKE 'SetupItem'");
                $Dane['device_dane']['device_serial_number'] = $this->Baza->GetValue("SELECT snu FROM serial_numbers WHERE id = '{$Dane['serial_number_id']}'");
                $Dane['device_dane']['device_firm'] = $this->Baza->GetValue("SELECT name FROM companies WHERE id = '{$Dane['company_id']}'");
                $Dane['device_dane']['device_email'] = $this->Baza->GetValue("SELECT email FROM users WHERE id = '{$Dane['user_id']}'");;
                $Dane['device_dane']['device_tel'] = $this->Baza->GetValue("SELECT phone_number FROM companies WHERE id = '{$Dane['company_id']}'");
                $Firma = new ModulFirmy($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
                $DaneFirmy = $Firma->PobierzDaneElementu($Dane['company_id']);
                $Dane['firm_dane'] = array(
                      'firm_name' => stripslashes($DaneFirmy['name']),
                      'firm_nip' => $DaneFirmy['nip'],
                      'firm_email' => $this->Baza->GetValue("SELECT email FROM users WHERE id = '{$Dane['user_id']}' LIMIT 1"),
                      'firm_user' => $this->Baza->GetValue("SELECT concat(firstname, ' ', surname) FROM users WHERE id = '{$Dane['user_id']}' LIMIT 1"),
                      'firm_phone' => $DaneFirmy['phone_number'],
                      'firm_fax' => $DaneFirmy['fax'],
            );				  
                $Formularz = $this->GenerujFormularzNaprawa($Dane);
            }
            

            $Formularz->WyswietlDane($Dane);
	}
  
}
