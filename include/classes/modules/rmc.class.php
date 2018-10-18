<?php
/**
 * Moduł ustawienia
 * 
 * @author		Michal Bugański <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2012
 * @package		Panelplus 
 * @version		1.0
 */

class ModulRMC extends ModulBazowy {

        private $CompanyID = null;
        private $Provinces;
        
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            
            $this->ModulyBezMenuBocznego[] = $this->Parametr;
            $this->ModulyBezKasowanie[] = $this->Parametr;
            $this->clickable_rows = false;
            $this->Tabela = 'rmc_provinces';
            $this->PoleID = 'company_id';
            $this->Nazwa = 'RMC';
	}

        function PobierzAkcjeNaLiscie($Dane = array(), $TH = false) {
            $Akcje[] = array('img' => "pencil.gif", 'img_grey' => "pencil_grey.gif", 'title' => "Edycja", "akcja" => "edycja");
            return $Akcje;
        }
        
        function GetActions($ID){
            $Akcje = array();
//            $Dodawanie = array('link' => "?modul=$this->Parametr&akcja=dodawanie", 'class' => 'dodawanie', 'img' => 'add.gif', 'desc' => 'Nowy RMC');
//            $Akcje['lista'][] = $Dodawanie;
//            $Akcje['szczegoly'][] = array('link' => "?modul=$this->Parametr&akcja=edycja&id=$ID&back=details", 'class' => 'edycja', 'img' => 'pencil.gif', 'desc' => 'Edycja');
            return $Akcje;
        }
        
        function PobierzListeElementow($Filtry = array()) {
            $Wyniki = array(
                'name' => 'Firma',
                'woj' => "Województwa",
            );
            $Where = $this->GenerujWarunki();
            $Sort = $this->GenerujSortowanie();
            $this->Baza->Query($this->QueryPagination("SELECT c.name, group_concat( p.name ) as woj, tp.company_id
                    FROM $this->Tabela tp 
                        LEFT JOIN companies c ON (c.id = tp.company_id)
                        LEFT JOIN provinces p ON (p.id = tp.province_id)
                    $Where
                    GROUP BY tp.company_id
                    $Sort"
                    , $this->ParametrPaginacji, 30));
            return $Wyniki;
	}

        function &GenerujFormularz(&$Wartosci = array(), $Mapuj = false) {
            $Formularz = parent::GenerujFormularz($Wartosci, $Mapuj);
            $Formularz->DodajPole('company_id', 'lista-chosen-single', 'RMC', array( 'elementy' => $this->GetRMC(), 'show_label'=>true,'div_class'=>'super-form-span2'));
            $Formularz->DodajPole('clear', 'clear', false);            
            $Formularz->DodajPole('province_id', 'lista-chosen', 'Województwa', array( 'elementy' => $this->GetProvinces(), 'show_label'=>true,'div_class'=>'super-form-span2'));
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}
        
        function AkcjaDodawanie() {
            if($this->CompanyID === null){
                return parent::AkcjaDodawanie();
            }else{
                Usefull::ShowKomunikatError('Brak wybranej firmy RMC');
            }
        }
        
        function AkcjaEdycja($ID) {
            if (isset($_GET['id']) && intval($_GET['id']) != 0) {
                $this->CompanyID = intval($_GET['id']);
            }else {
                $this->CompanyID = null;
            }
            if($this->CompanyID !== null){
                return parent::AkcjaEdycja($ID);
            }else{
                Usefull::ShowKomunikatError('Brak wybranej firmy RMC');
            }
        }
        
        function ZapiszDaneElementu($Formularz, $Wartosci, &$PrzeslanePliki = null, $ID = null) {
            $ReturnInsert = true;
            
            if($this->CompanyID !== null){
                $this->UsunElement($this->CompanyID);
            }else{
                $this->CompanyID = $Wartosci['company_id'] ;
            }
            foreach( $Wartosci['province_id'] as $PID ){
                $Fields = array( 'company_id' => $this->CompanyID, 'province_id' => $PID );
                $query = $this->Baza->PrepareInsert($this->Tabela, $Fields);
                $ReturnInsert &= $this->Baza->Query($query);
            }
            return $ReturnInsert;
        }

        function GetProvinces(){
            $this->Provinces = Wojewodztwa::GetProvinces($this->Baza);
            $Data = $this->Baza->GetValues("SELECT province_id FROM $this->Tabela WHERE $this->PoleID != '$this->CompanyID'");
            if(!empty($Data))
                foreach( $this->Provinces as $PKey=>$PValue ){
                    if( in_array($PKey, $Data) ){
                        unset($this->Provinces[$PKey]);
                    }
                }
            return $this->Provinces;
        }
        
        function PobierzDaneElementu($ID, $Typ = null) {
            $ReturnWoj = array();
            $Dane = $this->Baza->GetRows("SELECT * FROM $this->Tabela WHERE $this->PoleID = '$ID'");
            foreach($Dane as $woj){
                $ReturnWoj['province_id'][] = $woj['province_id'];
            }
            return $ReturnWoj;
        }
        
        function GetRMC(){
            return $this->Baza->GetOptions("SELECT c.id, c.name FROM companies AS c
                LEFT JOIN users u ON (c.id = u.company_id)
                WHERE u.role LIKE 'rmc'
                group by c.id
                ORDER BY name ASC");
        }
}
?>
