<?php

/**
 * Moduł Zamówienia Użytkownicy - moduł do zarządzanie zamówieniami
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 */

/*
 *  begin class ModulZamowieniaUzytkownicy
 */

class ModulZamowieniaUzytkownicy extends ModulZamowienia {

    
/**
 * zmienne klasy
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright       Copyright (c) Maciej Kura
 * @package		PanelPlus 
 */    
    
    
/**
 * konstruktor klasy Zamówienia Uzytkownicy
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @param resource &$Baza - zasób bazy danych
 * @param object $Uzytkownik - obiekt class Uzytkownik
 * @param string $Parametr - nazwa modułu
 * @param string $sciezka - tutaj czekam na opis :)
 * 
 */

	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->PoleSort = 'sent_at';
            $this->SortHow = 'DESC';
            $this->Filtry[] = array('nazwa' => 'status', 'opis' => 'Status zlecenia', 'opcje' => Usefull::GetPurchasesStatuses($this->Uzytkownik->IsAdmin(), $this->Uzytkownik->FirmaRMC($this->UserID)), "typ" => "checkbox", 'col'=>'2', 'class'=>Usefull::GetTRClass('order'));
            $this->Filtry[] = array('typ'=>'unknown', 'opis' => true);
            $this->Filtry[] = array('nazwa' => 'finalized_at', 'opis' => 'Data zakończenia', "typ" => "data_oddo" );
            $this->Filtry[] = array('nazwa' => 'sent_at', 'opis' => 'Data wysłania', "typ" => "data_oddo" , 'clear'=>true);
            $this->Filtry[] = array('nazwa' => 'p.company_id', 'opis' => 'Firma', 'opcje' => $this->GetCompanies(), "typ" => "lista", "domyslna" => "firma");
            $this->Filtry[] = array('nazwa' => 'title|p.id|c.simplename', 'opis' => false, "typ" => "tekst", "placeholder" => "Szukaj zamówień", 'search' => true);
            if($this->Uzytkownik->IsAdmin() || $this->Uzytkownik->FirmaRMC($this->Uzytkownik->ZwrocIdUzytkownika()) ){
                $status = ( $this->Uzytkownik->FirmaRMC($this->Uzytkownik->ZwrocIdUzytkownika()) ?  array(  1, 2, 3, 5 ) : array(  2, 3 ) );
                $this->DefaultFilters = array( 'status'=>$status );
//                $this->LinkMenu = array( 'Lista zamówien'=>'?modul=zamowieniauzytkownicy&akcja=lista',  'Lista firm'=>'?modul=zamowieniafirmy&akcja=lista',);
            }
            $this->ShortNameList = array(
                $this->PoleNazwy => array( 'IsTitle'=>true, 'lenght'=>'10'),
                'simplename' => array( 'IsTitle'=>true, 'lenght'=>'20'),
            );
            $this->NotDraft = $this->GetPurchasesDrafts(false);
            if($this->NotDraft)
                foreach($this->NotDraft as $blocked_id){
                    if(!in_array($blocked_id , $this->ZablokowaneElementyIDs['kasowanie']))
                        $this->ZablokowaneElementyIDs['kasowanie'][] = $blocked_id ;
                    if(!$this->Uzytkownik->IsAdmin() && !in_array($blocked_id , $this->ZablokowaneElementyIDs['edycja']) ){
                        $this->ZablokowaneElementyIDs['edycja'][] = $blocked_id ;
                    }
                }
            if(isset($_GET['id']) && is_numeric($_GET['id'])){
                if($this->ZablokujDostep($_GET['id']) == false){
                    $this->ZablokowaneElementyIDs['szczegoly'][] = $_GET['id'];
                }
            }
            if( isset($_GET['akcja']) && $_GET['akcja'] == 'gotowe'){
                $this->FiltersAndActionsWidth = true;
            }
            if( isset($_GET['act']) && $_GET['act'] == 'cancel' && $this->Uzytkownik->IsAdmin() && is_numeric($_GET['id']) && $this->CzyZamowienieZrealizowane($_GET['id'])){
                $this->Baza->Query( "UPDATE $this->Tabela SET status = '4' WHERE id = '{$_GET['id']}' AND status = '3'" );
            }
            
            if(isset($_GET['gotowe'])){
                if($_GET['gotowe']=='wyslij'){
                    $this->Komunikat = array(
                        'type' => 'ShowKomunikatOstrzezenie',
                        'text' => 'Twoje zamówienie jest już gotowe, aby dopełnić formalności należy je wysłać',
                        'bold' => true,
                    );
                }elseif($_GET['gotowe']=='powiadom'){
                    $this->Komunikat = array(
                        'type' => 'ShowKomunikatOstrzezenie',
                        'text' => 'Poinformuj swojego przełożonego o utworzeniu zamówienia. Zamówienia ze statusem Gotowe nie są realizowane.<br />Poproś przełożonego o wysłanie do realizacji.',
                        'bold' => true,
                    );
                }
            }
            
	}

/**
 * funkcja pobierająca oraz ustawiające listę wyświetlanych elementów
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @return array $Wynik - nagłówki tabeli
 */
        function PobierzListeElementow($Filtry = array()) {
            $Wyniki = array(
                    'id' => array('naglowek' => 'Nr zamówienia',  'td_styl' => 'width: 75px; text-align:right;'),
                    'invoice' => array('naglowek' => 'Nr faktury',  'td_styl' => 'width: 100px;'),
                    $this->PoleNazwy => array('naglowek' => 'Nazwa',  'td_styl' => 'width: 100px;'),
                    'warranty_mode' =>  array('naglowek' => 'Tryb gwarancyjny', 'elementy' => Usefull::GetCheckImg(), 'td_styl' => 'width: 80px;', 'td_class' => 'active-check'),
                    'status' => array('naglowek' => 'Status' ,'td_styl' => 'width: 100px;text-align: left;', "elementy" => Usefull::GetPurchasesStatuses($this->Uzytkownik->IsAdmin(), $this->Uzytkownik->FirmaRMC($this->UserID)), "class" => Usefull::GetPackageStatusesColor(), 'type'=> 'rmc' ),
                    'sent_at' => array('naglowek' => 'Wysłano',  'td_styl' => 'width: 80px;'),
                    'finalized_at' => array('naglowek' => 'Zakończono',  'td_styl' => 'width: 80px;'),
                    'simplename' => array('naglowek' => 'Zamawia',  'td_styl' => 'width: 80px;'),
                    'idrmc' => array('naglowek' => 'Realizuje',  'td_styl' => 'width: 80px;', "elementy" =>  $this->GetCompanies() ),
                    'tr_class' =>  array( 'class' => Usefull::GetTRClass('order'), 'element_name' => 'expired'),
                    'tr_color' => array( 'color' => Usefull::GetTRColor('role'), 'element_name' => 'role'),
                );
            $Where = $this->GenerujWarunki();
            $Sort = $this->GenerujSortowanie();
            $max_opoznienie = intval($this->Baza->GetValue("SELECT value FROM settings WHERE id = 8")) - 1;            
            $this->Baza->Query($this->QueryPagination("SELECT p.*, IF(DATEDIFF( NOW(), sent_at) > $max_opoznienie AND status  IN (3,4), status, NULL ) as expired,
                    c.simplename, IF(u.role LIKE  'rmc' AND status = 3, 1, 0) as role, CONCAT(u.firstname, ' ', u.surname) as client_name, role_name
                FROM $this->Tabela p 
                LEFT JOIN companies c ON (c.id = p.company_id)
                LEFT JOIN users u ON (p.user_id = u.id)
                LEFT JOIN user_roles ur ON (u.role = ur.role_id)
                    $Where $Sort", $this->ParametrPaginacji, 30));
            return $Wyniki;
	}
        /**
         * Ustawia domyślny warunek w zapytaniu do listy elementów
         * @return string
         */
	function DomyslnyWarunek($ISRMC = false){
            if($this->Uzytkownik->IsAdmin()){
                $domyslny = '';
            }elseif($this->Uzytkownik->FirmaRMC($this->UserID)){
                $domyslny = "( (p.idrmc = '{$this->Uzytkownik->ZwrocInfo('company_id')}' AND p.status IN (3, 4, 5)) OR p.company_id = '{$this->Uzytkownik->ZwrocInfo('company_id')}')";
            }else{
		$domyslny = ($this->Uzytkownik->CzyUprawniony() == false ? "p.user_id = '{$this->Uzytkownik->ZwrocIdUzytkownika()}'" : "p.company_id = '{$this->Uzytkownik->ZwrocInfo('company_id')}'");
            }
            return $domyslny;
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
            $Dodawanie = array('link' => "?modul=$this->Parametr&akcja=dodawanie", 'class' => 'dodawanie', 'img' => 'add.gif', 'desc' => 'Dodaj zamówienie');
            $Lista = array('link' => "?modul=$this->Parametr&akcja=lista", 'class' => 'lista', 'img' => 'list.gif', 'desc' => 'Lista zamówień');
 /*           
            if(!in_array($this->Parametr, $this->ModulyBezDodawania)){
                if( $this->IsZamowienie() == null ){
                    $Akcje['lista'][] = $Dodawanie;
                    $Akcje['szczegoly'][] = $Dodawanie;
                }
            }
*/
            if(!in_array($this->Parametr, $this->ModulyBezEdycji)){
                if( !in_array($ID, $this->ZablokowaneElementyIDs['edycja']) || $this->Uzytkownik->IsAdmin() )
                    $Akcje['szczegoly'][] = array('link' => "?modul=$this->Parametr&akcja=edycja&id=$ID&back=details", 'class' => 'edycja', 'img' => 'pencil.gif', 'desc' => 'Edycja');
                if( ($this->Uzytkownik->IsAdmin() ||  $this->Uzytkownik->FirmaRMC($this->UserID)) && $this->CzyZamowienieZrealizowane($ID) && isset($ID) ){
                    $ID_FIRM = $this->Baza->GetValue("SELECT company_id FROM $this->Tabela WHERE id = '$ID' LIMIT 1");
                    $Akcje['szczegoly'][] = array('link' => "?modul=zamowieniafirmy&akcja=szczegoly&id=$ID_FIRM", 'class' => 'zamowienia', 'img' => 'money_dollar.gif', 'desc' => 'Realizuj');
                    $Akcje['szczegoly'][] = array('link' => "?modul=zamowieniauzytkownicy&akcja=szczegoly&id=$ID&act=cancel", 'class' => 'usun', 'img' => 'cancel.gif', 'desc' => 'Odrzuć');
                }
            }
  /*          $Akcje['szczegoly'][] = array('link' => "?modul=$this->Parametr&akcja=szczegoly&id=$ID&typ=drukuj\" target=\"_blank\"", 'class' => 'drukowanie', 'img' => 'printer.gif', 'desc' => 'Drukuj zamówienie');
    */
            $IleItems = $this->Baza->GetValue("SELECT count(*) FROM line_items WHERE purchase_id = '$ID'");
            if($this->Status == 0  && $IleItems > 0){
//                $Akcje['szczegoly'][] = array('link' => "?modul=$this->Parametr&akcja=gotowe&id=$ID", 'class' => 'gotowe', 'img' => 'tick_blue.gif', 'desc' => 'Gotowe');
//            }else if($this->Status == 1 && $this->Uzytkownik->CzyUprawniony()){
                $Akcje['szczegoly'][] = array('link' => "?modul=$this->Parametr&akcja=zamow&id=$ID", 'img' => 'email.gif','class' => 'search-button', 'desc' => 'Zamów');
            }else if($this->Status == 2 && $this->Uzytkownik->IsAdmin()){
                $Akcje['szczegoly'][] = array('link' => "?modul=$this->Parametr&akcja=szczegoly&act=oczekujace&id=$ID", 'img' => 'email.gif','class' => 'search-button', 'desc' => 'Oczekujące');
            }
/*            else if($this->Status == 3 && $this->Uzytkownik->IsAdmin()){
                $Akcje['szczegoly'][] = array('link' => "?modul=$this->Parametr&akcja=szczegoly&act=wdrodze&id=$ID", 'img' => 'door_in.gif','class' => 'w-drodze', 'desc' => 'W drodze');
            }
*/
            $Akcje['akcja'] = array($Dodawanie, $Lista);
            if( ($this->Uzytkownik->IsAdmin() ||  $this->Uzytkownik->FirmaRMC($this->UserID)) /*&& $this->CzyZamowienieZrealizowane($ID) && isset($ID) */){
                //$ID_FIRM = $this->Baza->GetValue("SELECT company_id FROM $this->Tabela WHERE id = '$ID' LIMIT 1");
                $ListaFirm = array('link' => "?modul=zamowieniafirmy&akcja=lista", 'class' => 'lista', 'img' => 'list.gif', 'desc' => 'Lista Firm (reaizacja zamówień)');
                $Akcje['akcja'][] = $ListaFirm;
            }
            return $Akcje;
        }

        /**
 * funkcja generująca formularz
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @return object $Formularz - obiekt klasy Formularz
 */         
        function &GenerujFormularz(&$Wartosci = Array(), $Mapuj = false) {
            $Formularz = parent::GenerujFormularz($Wartosci, $Mapuj);
            
            if( $this->ShowWarrantyMode() ){
                $Formularz->DodajPole('warranty_mode', 'checkbox', 'Tryb gwarancyjny', array('atrybuty'=>array('class', 'warranty_mode'), 'show_label'=>true, 'div_class'=>'super-form-span4' ));
            }
            $Formularz->DodajPole('title', 'tekst', 'Nazwa', array( 'show_label'=>true, 'div_class'=>'super-form-span4'));            
            if($this->Uzytkownik->IsAdmin()){
                $Formularz->DodajPole('user_id', 'lista-chosen-single', 'Użytkownik*', array('show_label'=>true, 'div_class'=>'super-form-span4', 'elementy' => $this->GetUsersList(), 'domyslny' => '-- Użytkownik --' ) );
                $Formularz->DodajPoleWymagane('user_id', false);
/*                if($this->WykonywanaAkcja == 'edycja' ){
                    $ObecnyStatus = $this->Baza->GetValue("SELECT status FROM purchases WHERE id = '".intval($_GET['id'])."'");
                    if($ObecnyStatus == 3){*/
                        $Formularz->DodajPole('idrmc', 'lista-chosen-single', 'RMC*', array('show_label'=>true, 'div_class'=>'super-form-span4', 'elementy' => $this->GetRMCList(), 'domyslny' => '-- firma --'));
/*                         $Formularz->DodajPoleWymagane('idrmc', false);
                   }
                }*/
            }            
           $Formularz->DodajDoPolBezRamki('warranty_mode');
            $Formularz->DodajPole('clear', 'clear', false);
            $Formularz->DodajPole('description', 'tekst_dlugi', 'Opis', array( 'show_label'=>true, 'div_class'=>'super-form-span1', 'arybuty' => array('class'=>'opis')));
            $Formularz->DodajPole('clear', 'clear', false);
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}
                 /**
         * Wykonuje operacje na wartościach przed zapisem do bazy
         * @param array $Wartosci
         * @param integer $ID
         * @return array
         */       
        function OperacjePrzedZapisem($Wartosci, $ID){
            if(!$this->Uzytkownik->IsAdmin()){
                $Wartosci['user_id'] = $this->Uzytkownik->ZwrocIdUzytkownika();
                $Wartosci['company_id'] = $this->Uzytkownik->ZwrocInfo('company_id');
                $Wartosci['idrmc'] = $this->ZwrocRMC($this->Uzytkownik->ZwrocIdUzytkownika(), isset($Wartosci['warranty_mode']) ? $Wartosci['warranty_mode'] : '0');
            }else{
                $Wartosci['company_id'] = $this->Baza->GetValue("SELECT company_id FROM users WHERE id = '{$Wartosci['user_id']}'");
                if($this->WykonywanaAkcja == 'dodawanie'){
                    $Wartosci['idrmc'] = $this->ZwrocRMC($Wartosci['user_id'], isset($Wartosci['warranty_mode']) ? $Wartosci['warranty_mode'] : '0');
                }
            }
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

        function AkcjaSzczegoly($ID, $zmien = true){
           
            $drquantity_approved = $dquantity = $drSuma = $dSuma = 0.00;
            $Dane = $this->PobierzDaneElementu($ID);
            $Name['key'] = 'Zamówienie nr: ';
            $Name['value'] = $ID;
            $StatusesColor = Usefull::GetPackageStatusesColor();
            $PackageStatuses = Usefull::GetPurchasesStatuses($this->Uzytkownik->IsAdmin());
            $Dane['status_color'] = '<span class="'.$StatusesColor[$Dane['status']].'">'.$PackageStatuses[$Dane['status']].'</span>';
            $Dane['parts'] = $this->PobierzItems($ID);

            if( (in_array($Dane['status'], array(0)) || ($Dane['status'] == 3 && $this->Uzytkownik->IsAdmin())) && $zmien){
                $PolaDwa['mod'] = array('naglowek' => 'Zmień', 'type' => 'text', 'style' => 'text-align: center; width: 75px;');
                $_SESSION['basket-picked-order'] = $ID;
                foreach( $Dane['parts'] as $key=>$Value ){
                    $Dane['parts'][$key]['mod'] = "<a href='?modul=koszyk' class='like-button' style='margin-top: 0'>Zmień</a>";
                }
            }
            foreach( $Dane['parts'] as $key=>$Value ){
                $dSuma += $Value['amount']*$Value['quantity_approved'];
                $drSuma += $Value['amount_approved']*$Value['quantity_approved'];
                $drquantity_approved += $Value['quantity_approved'];
                $dquantity += $Value['quantity'];
                $Dane['parts'][$key]['amount'] = number_format($Value['amount'],2, ',', '');
                $Dane['parts'][$key]['amount_approved'] = number_format($Value['amount_approved'],2, ',', '');
                $Dane['parts'][$key]['quantity_approved'] = $Dane['status'] < 5 ? 0 : $Dane['parts'][$key]['quantity_approved'];
                $Dane['parts'][$key]['value'] = number_format($Dane['status'] < 5 ? $Value['amount']*$Value['quantity_approved'] : $Value['amount_approved']*$Value['quantity_approved'],2, ',', '');
            }
            $Dane['parts'][] = array(
                'title' => '<div class="f_right" style="margin-right: 8px;">Razem</div>',
                'amount' => '',
                'value' => '<div style="font-weight: bold;">'.number_format( ($Dane['status'] < 5 ? $dSuma :$drSuma ),2, ',', '')."</div>",
                'amount_approved' => '',// '<div style="font-weight: bold;">'.number_format($drSuma,2, ',', '')." zł</div>",
                'quantity_approved' => '<div style="font-weight: bold;">'.($Dane['status'] < 5 ? 0 : number_format($drquantity_approved,0, ',', ''))."</div>",
                'quantity' => '<div style="font-weight: bold;">'.number_format($dquantity,0, ',', '')."</div>",
                'discount' => '',
                'kzc' => '',
                'mod' => '',
            );
            $Dane['amount'] = number_format($dSuma,2, ',', '').'';
            $Dane['amount_discount'] = number_format($drSuma,2, ',', '').'';
            $Dane['created_at'] = UsefullTime::PolskiDzien(date("D, d.m.Y H:i:s", strtotime($Dane['created_at'])));
            $Dane['updated_at'] = UsefullTime::PolskiDzien(date("D, d.m.Y H:i:s", strtotime($Dane['updated_at'])));
            
            $Form = $this->GenerujFormularzSzczegoly($Dane);
            $Form->WyswietlDane($Dane);
            echo "<div class='clear'></div>";
            
        }

        
        function PobierzDaneElementu($ID, $Typ = NULL){
            $Dane = parent::PobierzDaneElementu($ID);
            $aUserFirm = $this->Baza->GetData("SELECT concat(firstname, ' ', surname) AS user_name, email, phone, company_id FROM users u
                    WHERE u.id = '{$Dane['user_id']}' LIMIT 1");
            $CompanyID = $aUserFirm['company_id'];
            unset($aUserFirm['company_id']);
            if( !empty($aUserFirm['email'])){
                $aUserFirm['email'] = '<a href="mailto: '.$aUserFirm['email'].'" class="pokaz textowy">'.$aUserFirm['email'].'</a>';
            }
            $Dane['firm_name'] = $this->PobierzDaneFirmy($CompanyID);
            
            $Dane['user_name'] = implode('<br />',$aUserFirm);
            $Dane['rmc_name'] = $this->PobierzDaneFirmy($Dane['idrmc']);
            if($this->Uzytkownik->FirmaRMC($this->UserID) || $this->Uzytkownik->IsAdmin()){
                $Dane['firm_name'] .= $this->GetFirmRole($CompanyID);
            }

            return $Dane;
        }

        function PobierzItems($ID){
             $rows = $this->Baza->GetRows( "SELECT il.*, p.kzc, p.title, lic.comments FROM line_items il
                                    LEFT JOIN parts p ON(p.id = il.part_id)
                                    LEFT JOIN line_items_comments lic ON( il.id = lic.line_items_id )
                                    WHERE il.purchase_id = '$ID'
                                    ORDER BY p.id ASC" );
        return $rows == false ? array() : $rows;
             
        }
        
        function IsZamowienie(){
            if(!$this->Uzytkownik->IsAdmin()){
                return $this->Baza->GetValue( "SELECT id from $this->Tabela WHERE user_id = '{$this->Uzytkownik->ZwrocIdUzytkownika()}' AND status = 0" );
            }else{
                return null;
            }
        }
        
        function AkcjaDodawanie(){
            if( $this->IsZamowienie() == null || $this->Uzytkownik->IsAdmin()){
                parent::AkcjaDodawanie();
            }else{
                echo "Możesz posiadać tylko jeden szkic zamówienia";
            }
        }
        
        function AkcjaDrukuj($ID, $Akcja = false) {
            $PackageStatuses = Usefull::GetPurchasesStatuses($this->Uzytkownik->IsAdmin());
            $Dane = $this->PobierzDaneElementu($ID);
            $Dane['array'] = $this->PobierzItems($ID);
            include(SCIEZKA_SZABLONOW."druki/zamowienia.tpl.php");
        }
        
        function CzyZamowienieZrealizowane($ID){
            $status = $this->Baza->GetValue( "SELECT status from $this->Tabela WHERE id = '$ID'" );
            if( $status == '3') {
                return true;
            }
            return false;
        }
        
        function AkcjaPotwierdzZamowienie($ID){
            $zamow = false;
            $IsUpdate = true;
            $ObecnyStatus = intval($this->Baza->GetValue("SELECT status FROM purchases WHERE id = '{$ID}'"));
            if($ObecnyStatus == 0){
                /*if( $_SESSION['UserStok']['active'] == 1 ){
                    $IsUpdate = $this->UaktualnijZamowieniaOStok($ID);
                }*/
                if($IsUpdate){
                    if( $this->Baza->GetValue("SELECT count(id) FROM users WHERE company_id = '{$this->Uzytkownik->ZwrocInfo('company_id')}'") < 2 ){
                        $status = '3';
                        $sent_at = ', sent_at = NOW()';
                    }else{
                        $status = '1';
                        $sent_at = '';
                    }
                    $zamow = $this->Baza->Query("UPDATE purchases SET status = '$status', updated_at = NOW() {$sent_at} WHERE id = '{$ID}'");
                }
            }
            if($zamow){
                if($status == '1' && $this->Uzytkownik->CzyUprawniony()){
                    $gotowe = "&gotowe=wyslij";
                    $this->LinkPowrotu = "?modul=$this->Parametr&akcja=szczegoly&id=$ID$gotowe";
                }elseif($status == '1' && !$this->Uzytkownik->CzyUprawniony()){
                    $gotowe = "&gotowe=powiadom";
                    $this->LinkPowrotu = "?modul=$this->Parametr&akcja=lista$gotowe";
                }else{
                    $gotowe = '&gotowe=nie';
                    $this->LinkPowrotu = "?modul=$this->Parametr&akcja=lista$gotowe";
                }
                unset($_SESSION['basket-picked-order']);
                
                $this->include_tpls = 'message-add.tpl.php';
                $this->ShowOK();
                return false;
            }else{
                Usefull::ShowKomunikatError('<b>Wystąpił problem. Dane nie zostały zapisane.</b><br/>'.$this->Baza->GetLastErrorDescription());
            }
         }
        
        function AkcjeNiestandardowe($ID){
            if(in_array($ID, $this->ZablokowaneElementyIDs['szczegoly'])){
                $this->AkcjaLista();
            }else{
		switch ($this->WykonywanaAkcja) {
                case 'gotowe':
                        $this->AkcjaZamowienieGotowe($ID);
                    break;
                case 'zamow':
                        $this->AkcjaZamowienie($ID);
                        break;
                case 'potwierdz-zamow':
                        $this->AkcjaPotwierdzZamowienie($ID);
                        break;
		default:
                    parent::AkcjeNiestandardowe($ID);
                    break;
                }
            }
	}
        
        function SprawdzZUsersStok($ID){
            $UsersID = $this->Baza->GetValue("SELECT user_id FROM purchases WHERE id = '{$ID}'");
            $PartsIDs = $this->Baza->GetValues("SELECT DISTINCT part_id FROM line_items WHERE purchase_id = '{$ID}' AND is_stok = '0'");
            $UserStok = $this->Baza->GetRows("SELECT users_stok_id, users_stok_parts_id, users_stok_amount, title, kzc
                FROM users_stok
                LEFT JOIN parts p ON (p.id = users_stok_parts_id)
                WHERE users_stok_users_id = {$UsersID}");
            
            foreach($UserStok as $UserStocKey=>$PartStok){
                if(!in_array( $PartStok['users_stok_parts_id'], $PartsIDs )){
                    unset($UserStok[$UserStocKey]);
                }
            }
            return $UserStok;
        }
        
        
        function AkcjaZamowienieGotowe($ID){
                $UserStok = array();
                $ObecnyStatus = $this->Baza->GetValue("SELECT status FROM purchases WHERE id = '{$ID}'");
                $ZamowienieGwarancyjne = intval($this->Baza->GetValue("SELECT warranty_mode FROM purchases WHERE id = '{$ID}'"));
                if($ZamowienieGwarancyjne == 1 && $ObecnyStatus < 2){
                    $UserStok = $this->SprawdzZUsersStok($ID);
                }
                if( !empty($UserStok) && count($UserStok) > 0 ){
                    $_SESSION['UserStok']['active'] = 1;
                    $_SESSION['UserStok']['values'] = $UserStok;
                    $this->include_tpls[] = 'forms/zamowienia-add-stok.tpl.php';
                    $compact = compact( "UserStok" );
                    $this->include_tpls_data = $compact;
                }else{
                    unset($_SESSION['UserStok']);
                }
                $this->AkcjaSzczegoly($ID, false);
        }
        
        
        function AkcjaZamowienie($ID){
            $ObecnyStatus = $this->Baza->GetValue("SELECT status FROM purchases WHERE id = '{$ID}'");
            if($this->Uzytkownik->CzyUprawniony() && $ObecnyStatus < 3){
//                $date = date("Y-m-d H:i:s");
                $zamow = $this->Baza->Query("UPDATE purchases p SET p.status = '3', updated_at = now(), sent_at = now() WHERE p.id = '{$ID}'");
            }else{
                Usefull::ShowKomunikatError('<b>Wystąpił problem. Dane nie zostały zapisane.</b><br/>'.$this->Baza->GetLastErrorDescription());
            }
            if($zamow){
                $this->include_tpls = 'message-add.tpl.php';
                $this->ShowOK();
                return false;
            }else{
                Usefull::ShowKomunikatError('<b>Wystąpił problem. Dane nie zostały zapisane.</b><br/>'.$this->Baza->GetLastErrorDescription());
            }
        }
        
        function UaktualnijZamowieniaOStok($ID){
            $UpdateStatus = true;
            if( !empty($_SESSION['UserStok']['values']) ){
                $Parts = $this->Baza->GetRows("SELECT id, part_id, quantity FROM line_items WHERE purchase_id = '{$ID}' AND is_stok = '0'");
                foreach( $Parts as $Part){
                    if( !empty($_SESSION['UserStok']['values']) ){
                        foreach($_SESSION['UserStok']['values'] as $StokKey => $StokValues ){
                            if($Part['part_id'] == $StokValues['users_stok_parts_id']){
                                $Part['quantity'] = $Part['quantity']+ $StokValues['users_stok_amount'];
                                $Part['quantity_approved'] = $Part['quantity'];
                                $LineItemID = $Part['id'];
                                $Part['is_stok'] = 1;
                                $Part['stok_amount'] = $StokValues['users_stok_amount'];
                                unset($Part['id']);
                                unset($Part['part_id']);
                                $QueryUpdate = $this->Baza->PrepareUpdate('line_items', $Part, array('id'=>$LineItemID));
                                $UpdateStatus &= $this->Baza->Query($QueryUpdate);
                                if( $UpdateStatus ){
                                    unset($_SESSION['UserStok']['values'][$StokKey]);
                                }else{
                                    return $UpdateStatus;
                                }
                            }
                        }
                    }else{
                        return $UpdateStatus;
                    }
                }
            }
            return $UpdateStatus;            
        }
        
        function GenerujSortowanie($AliasTabeli = null) {
            if(is_null($this->PoleSort)){
                    $this->PoleSort = $this->PoleNazwy;
            }
            $Sort = ($AliasTabeli ? "$AliasTabeli." : '').(isset($_GET['sort']) ? $_GET['sort'] : (isset($_SESSION['sort'][$this->Parametr]['sort']) ? $_SESSION['sort'][$this->Parametr]['sort'] : $this->PoleSort));
            $Jak = (isset($_GET['sort_how'])) ? $_GET['sort_how'] : (isset($_SESSION['sort'][$this->Parametr]['sort_how']) ? $_SESSION['sort'][$this->Parametr]['sort_how'] : $this->SortHow);
            return ($Sort != '' ? "ORDER BY role DESC, $Sort $Jak" : '');
        }
        
        
        function ObrobkaDanychLista($Elementy){
            $Elementy = parent::ObrobkaDanychLista($Elementy);
            /*foreach($Elementy as $ElementyKey => $Element){
                ob_start();
                include(SCIEZKA_SZABLONOW."tooltip-zamowienia.tpl.php");
                $tooltip = ob_get_contents();
                ob_clean();
                $Elementy[$ElementyKey]['tooltip_content'] = $tooltip;
            }*/
            return $Elementy;
        }
        
        function AkcjaLista($Filtry = array()) {
           /* ?>
            <script type="text/javascript" src="js/plugins/jquery.qtip.js"></script>
        <?php*/
            parent::AkcjaLista($Filtry);
        }
        
        function PobierzAkcjeNaLiscie($Dane = array(), $TH = false){
            $Akcje = parent::PobierzAkcjeNaLiscie();
            /*foreach ($Akcje as &$Akcja )
                $Akcja['tooltip'] = true;*/
            return $Akcje;
	}
        
        function OperacjePoPrzeladowaniu($ID){
            if(isset($_GET['act'])){
                if($_GET['act'] == 'wdrodze'){
                    $this->SetStatus($ID, 2);
                }elseif ($_GET['act'] == 'oczekujace'){
                    $this->SetStatus($ID, 3);
                }
            }
            if( is_numeric($this->ID) ){
                $this->Status = $this->Baza->GetValue("SELECT status FROM $this->Tabela WHERE id = '$this->ID'");
            }
        }
        
        function SetStatus($ID, $status){
            return $this->Baza->Query("UPDATE purchases p SET p.status = '{$status}', updated_at = now() WHERE p.id = '{$ID}'");
        }

        function &GenerujFormularzSzczegoly(&$Wartosci = Array(), $Mapuj = false) {
            $Formularz = parent::GenerujFormularz($Wartosci, $Mapuj);
            $Formularz->DodajPole('warranty_mode', 'checkbox', 'Tryb gwarancyjny', array('atrybuty'=>array('class', 'warranty_mode'), 'show_label'=>true, 'div_class'=>'super-form-span4' ));
            $Formularz->DodajPole('title', 'tekst', 'Nazwa zamówienia', array( 'show_label'=>true, 'div_class'=>'super-form-span4'));            
            $Formularz->DodajPole('idrmc', 'lista-chosen-single', 'RMC*', array('show_label'=>true, 'div_class'=>'super-form-span4', 'elementy' => $this->GetRMCList(), 'domyslny' => '-- firma --'));
            $Formularz->DodajPole('user_id', 'lista-chosen-single', 'Użytkownik*', array('show_label'=>true, 'div_class'=>'super-form-span4', 'elementy' => $this->GetUsersList(), 'domyslny' => '-- Użytkownik --' ) );
            $Formularz->DodajPole('clear', 'clear', false);
            $Formularz->DodajPole('description', 'tekst_dlugi', 'Opis', array( 'show_label'=>true, 'div_class'=>'super-form-span1', 'arybuty' => array('class'=>'opis')));
            $Formularz->DodajPole('clear', 'clear', false);
            $Formularz->DodajPole('status', 'lista-chosen-single', 'Status', array( 'show_label'=>true, 'div_class'=>'super-form-span1', "elementy" => Usefull::GetPurchasesStatuses($this->Uzytkownik->IsAdmin(), $this->Uzytkownik->FirmaRMC($this->UserID)), "class" => Usefull::GetPackageStatusesColor()));
            $Formularz->DodajPole('clear', 'clear', false);
            $Formularz->DodajPole('parts', 'zamowienia_czesci', 'Zamówione części', array('show_label'=>true,'div_class'=>'super-form-span1',));
            $Formularz->DodajPole('clear', 'clear', '', array());
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
        }
}

