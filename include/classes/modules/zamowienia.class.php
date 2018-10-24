<?php
/**
 * Moduł Zamówienia - moduł do zarządzanie zamówieniami
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 */

/*
 *  begin class ModulZamowienia
 */

class ModulZamowienia extends ModulBazowy {

    protected $Status = null;
    protected $Companies;
/**
 * zmienne klasy
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright       Copyright (c) Maciej Kura
 * @package		PanelPlus 
 */    
    
    
/**
 * konstruktor klasy Zamówienia
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
            $this->Tabela = 'purchases';
            $this->PoleID = 'id';
            $this->PoleSort = 'id';
            $this->PoleNazwy = 'title';
            $this->Nazwa = 'Zamówienia';
            $this->ModulyBezMenuBocznego[] = $this->Parametr;
	}
         
        function GetCompanies(){
            if($this->Companies == false){
                $ModulFirmy = new ModulFirmy($this->Baza, $this->Uzytkownik, "test123", $this->Sciezka);
                $this->Companies = $ModulFirmy->GetList();
            }
            return $this->Companies;
        }
        
        
         function ZablokujDostep($ID) {
            if($this->Uzytkownik->IsAdmin()){
                return true;
            }
            $CheckAccess = $this->Baza->GetValue("SELECT count(p.$this->PoleID) FROM $this->Tabela p
                  WHERE p.$this->PoleID = '$ID' AND {$this->DomyslnyWarunek( $this->Uzytkownik->FirmaRMC($this->UserID))}");
            if($CheckAccess > 0){
                return true;
            }
            return false;
        }
        
        public function GetPurchasesDrafts($YesNo = true ){
            if($YesNo){
                $compare_type = '=';
            }else{
                $compare_type = '!=';
            }
            return $this->Baza->GetValues("SELECT id FROM $this->Tabela WHERE status $compare_type 0 ");
        }

        public function GetUsersList(){
            return $this->Baza->GetOptions("SELECT u.id, concat(firstname, ' ', surname, ' - [', IFNULL(simplename, ''), ']') FROM users u
                LEFT JOIN companies c ON (c.id = u.company_id )
                ORDER BY concat(firstname, surname) ASC");
        }
        
        public function GetRMCList(){
                $RMC = $this->Baza->GetOptions("SELECT c.id, concat(firstname, ' ', surname, ' - [', IFNULL(simplename, ''), ']') FROM users u
                LEFT JOIN companies c ON (c.id = u.company_id )
                WHERE u.role LIKE 'rmc'
                ORDER BY concat(firstname, surname) ASC");
                $RMC['0'] = '-- wybierz --';
                ksort($RMC);
                return $RMC;
        }
     
        public function ZwrocRMC($UserID, $warranty_mode){
            $RMC = $this->Baza->GetValue("SELECT IF(role LIKE  'rms', 1, 0) as RMC FROM users u WHERE u.id = '$UserID'");
            if( $warranty_mode == '1' || $RMC === '1'){
                $RMC = 2;
            }else{
                $Query = "SELECT tp.company_id FROM rmc_provinces tp 
                                LEFT JOIN companies c ON (tp.province_id = c.province_id)
                                LEFT JOIN users u ON (u.company_id = c.id)
                                WHERE u.id = '{$UserID}'";
                $RMC = $this->Baza->GetValue($Query);
            }
            return is_numeric($RMC) ? $RMC : 2;
        }
        
        function GetZamowieniaByStatus($Status, $LimitID, $MaxOpoznienieID = false, $SortBy = "role DESC, ile_dni DESC", $ISRMC = false){
            $limit = intval($this->Baza->GetValue("SELECT value FROM settings WHERE id = '$LimitID'"));
            $Where = $this->DomyslnyWarunek($ISRMC);
            $Where .= ($Where != "" ? " AND " : "").("p.status = '$Status'");
            if($MaxOpoznienieID){
                $max_opoznienie = intval($this->Baza->GetValue("SELECT value FROM settings WHERE id = '{$MaxOpoznienieID['id']}'")) - 1;
                $Where .= ($Where != "" ? " AND " : "").("DATEDIFF( NOW(), sent_at) {$MaxOpoznienieID['znak']} $max_opoznienie");
            }
            $Where = ($Where != "" ? " WHERE $Where" : "");
            $zamowienia = $this->Baza->GetRows("SELECT p.*, DATEDIFF( NOW(), sent_at) as ile_dni, c.simplename, ct.simplename as top_name, IFNULL(phb.id_param, 'show') as pulpit_show,
                                                IF(u.role LIKE  'rms', 1, 0) as role
                                                FROM $this->Tabela p
                                                LEFT JOIN companies c ON(c.id = p.company_id)
                                                LEFT JOIN users u ON (p.user_id = u.id)
                                                LEFT JOIN pulpit_hide_box phb ON (p.id = phb.id_param AND p.status = phb.status AND type='zamowienia' AND phb.users_id = '{$this->Uzytkownik->ZwrocIdUzytkownika()}')
                                                LEFT JOIN companies ct ON(ct.id = p.idtop)
                                                $Where ORDER BY $SortBy LIMIT $limit");
            if($zamowienia)
                foreach($zamowienia as $ZamowieniaKey=>$ZamowieniaVal){
                    if($ZamowieniaVal['pulpit_show'] != 'show'){
                        unset($zamowienia[$ZamowieniaKey]);
                    }
                }
            return $zamowienia;            
        }
        
                /**
         * Ustawia domyślny warunek w zapytaniu do listy elementów
         * @return string
         */
	function DomyslnyWarunek($ISRMC = false){
            if($this->Uzytkownik->IsAdmin()){
                $domyslny = '';
            }elseif( $ISRMC ){
		$domyslny = "p.idrmc= '{$this->Uzytkownik->ZwrocInfo('company_id')}' ";
            }else{
		$domyslny = ($this->Uzytkownik->CzyUprawniony() == false ? "p.user_id = '{$this->Uzytkownik->ZwrocIdUzytkownika()}'" : "p.company_id = '{$this->Uzytkownik->ZwrocInfo('company_id')}'");
            }
            return $domyslny;
        }
        
        function ShowWarrantyMode(){
/*            $ShowWorentyMode = $this->Baza->GetValue("SELECT value FROM settings WHERE var = 'purchases.worenty_mode' LIMIT 1");
            if( ($ShowWorentyMode == '1') || $this->Uzytkownik->IsAdmin() || ($this->Uzytkownik->ZwrocInfo('role') == "serwisant_g") ){*/
                return true;
/*            }else{
                return false;
            }*/
        }
        
        function PobierzDaneFirmy($ID){
            $aTop = $this->Baza->GetData("SELECT name, nip, street, city, postcode, province_id, country, phone_number FROM companies c 
                WHERE id = '{$ID}' LIMIT 1");
            if($aTop['name'] != ""){
                $Adres[] = $aTop['name'];
            }
            if($aTop['street'] != ""){
                $Adres[] = $aTop['street'];
            }
            if($aTop['postcode'] != "" || $aTop['city'] != ''){
                $Adres[] = $aTop['postcode']." ".$aTop['city'];
            }
            if($aTop['country'] != ""){
                $Adres[] = $aTop['country'];
            }
            if($aTop['phone_number'] != ""){
                $Adres[] = $aTop['phone_number'];
            }
            return stripslashes(implode("<br />", $Adres));
        }
        
        function GetFirmRole($CompanyID){
                $Roles = array(
                        'administrator' => 5,
                        'rmc' => 4,
                        'serwisant_g' => 3,
                        'serwisant' => 2,
                        'instalator' => 1,
                        
                );
                $UsersRoles = $this->Baza->GetValues("SELECT role FROM users WHERE company_id = '{$CompanyID}'");
                $MaxRools = 1;
                foreach($UsersRoles as $Role){
                    $MaxRools = $MaxRools < $Roles[$Role] ? $Roles[$Role] : $MaxRools;
                }
                $Role = Role::GetRoles($this->Baza);
                $aaa = array_keys($Roles, $MaxRools);
                $ret = array_pop($aaa);
                return '<div style="font-weight: bold;">'.$Role[$ret].'</div>';
        }
             
        function  Wyswietl($Akcja) {
            $this->OperacjePoPrzeladowaniu($this->ID);
            parent::Wyswietl($Akcja);
        }
    
        function OperacjePoPrzeladowaniu($ID){
            return false;
        }
        
}
/*
 *  end class ModulZamowienia
 */