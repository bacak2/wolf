<?php
/**
 * Moduł GrupyCenowe
 *
 * @author		Michal Bugański <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2012
 * @package		Panelplus 
 * @version		1.0
 */

class ModulWymagania extends ModulBazowy {

	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->ModulyBezMenuBocznego[] = $this->Parametr;
            $this->Tabela = 'required';
            $this->PoleID = 'id';
            $this->PoleNazwy = 'required_name';
            $this->Nazwa = 'Wymagania';
            $this->PoleVisible = 'visible';
	}

        function &GenerujFormularz(&$Wartosci = array(), $Mapuj = false) {
            $Formularz = parent::GenerujFormularz($Wartosci, $Mapuj);
            $Formularz->DodajPole($this->PoleNazwy, 'tekst', "Nazwa wymagania*", array('show_label'=>true,'div_class'=>'super-form-span1'));
            $Formularz->DodajPole('clear', 'clear', false);
            $Formularz->DodajPole('category_required_id', 'lista-chosen-single', "Kategoria wymagań*", array( 'elementy'=>$this->GetSelect('KategorieWymagan', true), 'show_label'=>true,'div_class'=>'super-form-span1'));
            $Formularz->DodajPole('clear', 'clear', false);
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            $Formularz->DodajPoleWymagane($this->PoleNazwy, false);
            $Formularz->DodajPoleWymagane('category_required_id', false);
            return $Formularz;
        }
        
        function OperacjePrzedZapisem($Wartosci, $ID) {
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
        
        
        function PobierzListeElementow($Filtry = array()) {
            $Wyniki = array(
                $this->PoleNazwy => 'Wymaganie',
                'category_required_id'=>array('naglowek'=>'Kategoria wymagań', 'elementy'=>$this->GetSelect('KategorieWymagan', true))
            );
            $Where = $this->GenerujWarunki();
            $Sort = $this->GenerujSortowanie();
            $this->Baza->Query($this->QueryPagination("SELECT f.*
                    FROM $this->Tabela f
                    $Where $Sort", $this->ParametrPaginacji, 30));
            return $Wyniki;
	}
        
        
        function BlockElements(){
        }
        
}
?>

