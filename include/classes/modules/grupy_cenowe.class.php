<?php
/**
 * Moduł GrupyCenowe
 *
 * @author		Michal Bugański <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2012
 * @package		Panelplus 
 * @version		1.0
 */

class ModulGrupyCenowe extends ModulBazowy {

	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->ModulyBezMenuBocznego[] = $this->Parametr;
            $this->Tabela = 'group_pricing';
            $this->PoleID = 'id';
            $this->PoleNazwy = 'value';
            $this->Nazwa = 'GrupyCenowe';
            
	}

        function PobierzDaneElementu($ID, $Typ = NULL){
            $vars = $this->GetAllVars();
            $Dane = array();
            foreach($vars as $var){
                $Dane[$var['id']] = $var['value'];
            }
            return $Dane;
            
        }
        
        function GetAllVars(){
            return $this->Baza->GetResultAsArray("SELECT id, value FROM $this->Tabela ORDER BY $this->PoleID ASC", $this->PoleID);
        }

        function &GenerujFormularz(&$Wartosci = Array(), $Mapuj = false) {
            $Formularz = parent::GenerujFormularz($Wartosci, $Mapuj);
            $vars = $this->GetAllVars();
            foreach($vars as $var){
                $Formularz->DodajPole($var['id'], 'tekst', "Grupa ".$var['id'], array('show_label'=>true,'div_class'=>'super-form-span10', 'atrybuty'=>array('style'=>'text-align:right;') ));
                if($var['id']%10 == 0)
                    $Formularz->DodajPole('clear', 'clear', false);
            }
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
        }
        
        function ZapiszDaneElementu($Formularz, $Wartosci, &$PrzeslanePliki = null, $ID = null) {
            $this->LinkPowrotu = "?modul=ustawienia";
            $Vars = $this->GetAllVars();
            $NoError = true;
            foreach($Vars as $FieldID => $Value){
                $Save = array();
                ### Sprawdzamy czy nastąpiła zmiana w polu
                if($Wartosci[$Vars[$FieldID]['id']] != $Vars[$FieldID]['value']){
                    $Save['updated_at'] = date("Y-m-d H:i:s");
                    $Save['value'] = $Wartosci[$Vars[$FieldID]['id']];
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

        public function GetIDName($default, $Query = null, $Where = null ){
            $Query = "SELECT $this->PoleID, CONCAT( 'Grupa ',$this->PoleID, ' - [', $this->PoleNazwy, ']') FROM $this->Tabela ";
            return parent::GetIDName($default, $Query, $Where);
        }
        
        
        
}
?>
