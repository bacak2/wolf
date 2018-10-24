<?php
/**
 * Moduł Kategorie - moduł do zarządzanie kategoriami urządzeń.
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus Ferroli
 * @version		1.0
 */

/*
 *  begin class ModulKategorie
 */

class ModulKategorie extends ModulBazowy {
	
    /**
     * zmienne klasy
     * 
     * @author		Maciej Kura <Maciej.Kura@gmail.com>
     * @copyright       Copyright (c) Maciej Kura
     * @package		PanelPlus Ferroli
     * @access  private
     * @var string $b_ancestry czy istnieje nadrzędna kategoria
     */    
    private $b_ancestry = false;

    /**
     * prefix użyty do ciasteczek trzymających rozwinięte kategorie
     * @var string
     */
    public $cookie_prefix = "categories";

    /**
     * tablica zawierająca kategorie mające urządzenia
     * @var array
     */
    private $categories_have_device = array();

    /**
     * tablica zawierająca kategorie które mają podkategorie
     * @var array
     */
    private $categories_have_undercategories = array();
                
    
    private $service_guided_start_parameters;
    private $zero_guided_start;

                        
/**
 * konstruktor klasy Kategorie
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus Ferroli
 * @version		1.0
 * @param resource &$Baza - zasób bazy danych
 * @param object $Uzytkownik - obiekt class Uzytkownik
 * @param string $Parametr - nazwa modułu
 * @param string $sciezka - tutaj czekam na opis :)
 * 
 */

	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->Tabela = 'categories';
            $this->PoleID = 'id';
            $this->PoleNazwy = 'title';
            $this->Nazwa = 'kategorie_polskie';
            $this->PoleSort = "code, path_to";
            $this->PoleVisible = "status";
            $this->PoleVisibleValue = 2; 
            $this->CzySaOpcjeWarunkowe = true;
            $this->ModulyBezMenuBocznego[] = $this->Parametr;
            $this->ErrorsDescriptions['unikalne']['code'] = "Kategoria o kodzie %s już istnieje";
            $this->NoSort[] = "nazwa_expand";
            if(isset($_GET['akcja'] ) && $_GET['akcja'] == "dodawanie" && isset($_GET['back']) && $_GET['back'] == "details"){
                $this->LinkPowrotu = "?modul=$this->Parametr&akcja=szczegoly&id={$_GET['podkategoria']}";
                $this->PrzyciskiFormularza['anuluj']['link'] = $this->LinkPowrotu;
            }
	}

 /**
 * funkcja generująca formularz
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus Ferroli
 * @version		1.0
 * @return object $Formularz - obiekt klasy Formularz
 */       
        
        function &GenerujFormularz(&$Wartosci = Array(), $Mapuj = false)  {
            if(isset($_GET['podkategoria']) && $_GET['podkategoria'] != '' ){
                $this->b_ancestry = true;
            }
            $Formularz = parent::GenerujFormularz($Wartosci, $Mapuj);
            $Formularz->DodajPole($this->PoleNazwy, 'tekst', 'Nazwa*', array('show_label'=>true,'div_class'=>'super-form-span3',));
            $Formularz->DodajPole('code', 'tekst', 'Kod*', array('show_label'=>true,'div_class'=>'super-form-span3',));
            $Formularz->DodajPole('status', 'lista-chosen-single', 'Status*', array('elementy' => $this->GetListStatus(),'show_label'=>true,'div_class'=>'super-form-span3'));
            $Formularz->DodajPole('clear', 'clear', null);
            if($this->b_ancestry){
                $Formularz->DodajPole('path_to', 'tekstowo', 'Kategoria nadrzędna', array('show_label'=>true,'div_class'=>'super-form-span1'));
                $Formularz->DodajPole('clear', 'clear', null);
            }
            $Formularz->DodajPole('description', 'tekst_dlugi', 'Opis', array('show_label'=>true,'div_class'=>'super-form-span1'));
            $Formularz->DodajPole('clear', 'clear', null);
            
            $Formularz->DodajPole('rbh_amount', 'tekst', 'Pierwsza roboczo-godzina', array('show_label'=>true,'div_class'=>'super-form-span3',));
            $Formularz->DodajPole('kmrbh_amount', 'tekst', 'km w pierwszej roboczo-godzinie', array('show_label'=>true,'div_class'=>'super-form-span3',));
            $Formularz->DodajPole('next_rbh_amount', 'tekst', 'kolejna roboczo-godzina', array('show_label'=>true,'div_class'=>'super-form-span3'));
            $Formularz->DodajPole('clear', 'clear', null);
            
            $Formularz->DodajPole('holidays_amount', 'tekst', 'W godzinach 21-7/święta stawka x%', array('show_label'=>true,'div_class'=>'super-form-span3',));
            $Formularz->DodajPole('zero_guided_start_amount', 'tekst', 'Zerowe uruchomienie', array('show_label'=>true,'div_class'=>'super-form-span3',));
            $Formularz->DodajPole('service_guided_start_amount', 'tekst', 'Serwisowe uruchomienie', array('show_label'=>true,'div_class'=>'super-form-span3'));
            $Formularz->DodajPole('clear', 'clear', null);
            $Formularz->DodajPole('certificate_category', 'checkbox', 'Kategoria certyfikatów', array('show_label'=>true,'div_class'=>'super-form-span1'));
            $Formularz->DodajPole('clear', 'clear', null);
            $Formularz->DodajPole('zero_guided_start', 'lista_parametrow_urzadzenia', 'Parametry zerowego uruchomienia', array('show_label'=>true,'div_class'=>'super-form-span2','elementy'=>$this->GetSelect('Wymagania'),));
            $Formularz->DodajPole('service_guided_start_parameters', 'lista_parametrow_urzadzenia', 'Parametry serwisowego uruchomienia', array('show_label'=>true,'div_class'=>'super-form-span2','elementy'=>$this->GetSelect('Wymagania'),));
            $Formularz->DodajPole('clear', 'clear', null);            
            
            $Formularz->DodajPoleWymagane($this->PoleNazwy, false);
            $Formularz->DodajPoleWymagane("code", false);
            $Formularz->DodajPoleNieDublujace('code', false);
            $Formularz->DodajDoPolBezRamki('zero_guided_start');
            $Formularz->DodajDoPolBezRamki('service_guided_start_parameters');
            $Formularz->DodajDoPolBezRamki('certificate_category');
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
        }
 /**
 * funkcja pobierająca oraz ustawiające listę wyświetlanych elementów
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus Ferroli
 * @version		1.0
 * @return array $Wynik - nagłówki tabeli
 */          
        function PobierzListeElementow($Filtry = array()) {
            $Wyniki = array(
                    'nazwa_expand' => 'Nazwa',
                    'code' => 'KOD',
                    'status' => array('naglowek' => 'Status', 'elementy' => $this->GetListStatus(), 'td_class' => 'active-check')
            );
            $Where = $this->GenerujWarunki();
            if($this->Parametr == "urzadzenia"){
                $Sort = "ORDER BY $this->PoleSort ASC";
            }else{
                $Sort = $this->GenerujSortowanie();
            }
            $this->Baza->Query("SELECT * FROM $this->Tabela ic $Where $Sort");
            return $Wyniki;
	}

 /**
 * funkcja pobierająca informacje elementu o ID
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus Ferroli
 * @version		1.0
 * @param integer $ID ID elementu w bazie
 * /             
        
        function AkcjaSzczegoly($ID){
            $Dane = $this->PobierzDaneElementu($ID);
            $PolaJeden[$this->PoleNazwy] = 'Nazwa';
            $PolaJeden['code'] = 'Kod';
            $PolaJeden['path_to'] = 'Ścieżka';
            $PolaJeden['status'] = array('naglowek' => 'Status', 'elementy' => $this->GetListStatus());
            $PolaDwa['description'] = array('naglowek' => 'Opis', 'long' => true);
            $Dane['created_at'] = UsefullTime::PolskiDzien(date("D, d.m.Y H:i:s", strtotime($Dane['created_at'])));
            $Dane['updated_at'] = UsefullTime::PolskiDzien(date("D, d.m.Y H:i:s", strtotime($Dane['updated_at'])));
            $PolaComments['created_at'] = 'Utworzono';
            $PolaComments['updated_at'] = 'Ostatnia zmiana';
            include(SCIEZKA_SZABLONOW."show.tpl.php");
        }
 /**
 * funkcja wyświetlająca panel boczny z oięcioma oatatnio modyfikowanymi elementami
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus Ferroli
 * @version		1.0
 * @param integer $ID id elementu
 */
        
        function WyswietlPanel($ID = null) {
            $History = $this->Baza->GetRows("SELECT $this->PoleID, $this->PoleNazwy, updated_at FROM $this->Tabela ORDER BY updated_at DESC LIMIT 5");
            include(SCIEZKA_SZABLONOW."panel-boczny/kategorie.tpl.php");
        }

/**
 * funkcja zwracająca statusy kategorii
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus Ferroli
 * @version		1.0
 * @return array lista statusów kategorii
 */           

        function GetListStatus(){
            return array(
                0=>'szkic',
                1=>'ukryte',
                2=>'opublikowane',
            );
        }
 /**
 * funkcja ustawiająca przyciski z dodatkowymi akcjami dla określonej akcji.
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus Ferroli
 * @version		1.0
 * @param integer $ID ID elementu w bazie
 */
        
        function GetActions($ID){
            $DodawanieKategoria = array('link' => "?modul=$this->Parametr&akcja=dodawanie", 'class' => 'dodawanie', 'img' => 'add.gif', 'desc' => 'Nowa kategoria');
            $EdytujKategoria = array('link' => "?modul=$this->Parametr&akcja=edycja&id=$ID&back=details", 'class' => 'edycja', 'img' => 'pencil.gif', 'desc' => 'Edytuj');
            $DodawaniePodkategoria = array('link' => "?modul=$this->Parametr&akcja=dodawanie&podkategoria=$ID&back=details", 'class' => 'dodawanie', 'img' => 'add.gif', 'desc' => 'Nowa podkategoria');
            $ListsKategorii = array('link' => "?modul=$this->Parametr&akcja=lista", 'class' => 'lista', 'img' => 'list.gif', 'desc' => 'Lista kategorii');
            $Akcje['dodawanie'] = array();
            $Akcja['kasowanie'] = array();
            $Akcje['lista'][] =  $DodawanieKategoria;
            $Akcje['edycja'] = array();
            $Akcje['szczegoly'] = array(
                    $DodawaniePodkategoria,
                    $EdytujKategoria
                );
            $Akcje['akcja'] = array(
                    $DodawanieKategoria,
                    $ListsKategorii
                );
            return $Akcje;
        }
        
/**
 * funkcja Ustawiająca dane domyślne
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus Ferroli
 * @version		1.0
 * @return array dane domyślne
 */
        
        function &PobierzDaneDomyslne() {
            parent::PobierzDaneDomyslne();
            $ret = array();
            if(isset($_GET['podkategoria']) && $_GET['podkategoria'] != '' && is_numeric($_GET['podkategoria']) ){
                $this->b_ancestry = true;
                $Dane = $this->PobierzDaneElementu($_GET['podkategoria']);
                $ret['path_to'] = $Dane['path_to'];
            }
            return $ret;
        }
        
 /**
 * funkcja ustawiająca dane przed zapisem
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus Ferroli
 * @version		1.0
 * @return array dane domyślne
 */
        
        function OperacjePrzedZapisem($Wartosci, $ID){
            if(isset($Wartosci['zero_guided_start'])){
                $this->zero_guided_start = $Wartosci['zero_guided_start'];
                unset($Wartosci['zero_guided_start']);
            }else{
                $this->zero_guided_start = false;
            }
            if(isset($Wartosci['service_guided_start_parameters'])){
                $this->service_guided_start_parameters = $Wartosci['service_guided_start_parameters'];
                unset($Wartosci['service_guided_start_parameters']);
            }else{
                $this->service_guided_start_parameters = false;
            }
            if($ID){
                /***********************/
                $Values = $this->PobierzDaneElementu($ID, null);
                $zero_guided_start_to_del = $this->zero_guided_start;
                $service_guided_start_parameters_to_del = $this->service_guided_start_parameters;
                if( isset($zero_guided_start_to_del['add']) ) unset ($zero_guided_start_to_del['add']);
                $zgs_del = array_diff($Values['zero_guided_start'], $zero_guided_start_to_del );
                if( is_array($zgs_del) ){
                    $this->Baza->Query("DELETE FROM categories_zero_guided_start WHERE categories_zero_guided_start_categories_id='$ID' AND categories_zero_guided_start_category_required_id IN (".implode(',',$zgs_del).") ");
                }
                if( isset($service_guided_start_parameters_to_del['add']) ) unset ($service_guided_start_parameters_to_del['add']);
                $sgsp_del = array_diff($Values['service_guided_start_parameters'], $service_guided_start_parameters_to_del );
                if( is_array($sgsp_del) ){
                    $this->Baza->Query("DELETE FROM categories_service_guided_start_parameters WHERE categories_service_guided_start_parameters_categories_id='$ID' AND categories_zero_guided_start_category_required_id IN (".implode(',',$sgsp_del).") ");
                }
                /************************************/
                
                
                $Values = $this->PobierzDaneElementu($ID);
                foreach($Wartosci as $FieldID => $Value){
                    if($Value != $Values[$FieldID]){
                        $Wartosci['updated_at'] = date("Y-m-d H:i:s");
                        break;
                    }
                }
            }elseif(isset($_GET['podkategoria']) && $_GET['podkategoria'] != '' && is_numeric($_GET['podkategoria']) ){
                $Dane = $this->PobierzDaneElementu($_GET['podkategoria']);
                $ancestry = explode('/', $Dane['ancestry']);
                $Wartosci['path_to'] = $this->GenerujPathTo( $Wartosci[$this->PoleNazwy], $ancestry );
                $Wartosci['ancestry'] = is_null($Dane['ancestry']) ? $_GET['podkategoria'] : $Dane['ancestry'].'/'.$_GET['podkategoria'];
                $Wartosci['created_at'] = date("Y-m-d H:i:s");
                $Wartosci['updated_at'] = date("Y-m-d H:i:s");
            }else{
                $Wartosci['path_to'] = $this->GenerujPathTo( $Wartosci[$this->PoleNazwy]);
                $Wartosci['created_at'] = date("Y-m-d H:i:s");
                $Wartosci['updated_at'] = date("Y-m-d H:i:s");
            }
            return $Wartosci;
        }
/**
 * funkcja wywoływana przed wywołaniem funkcji bazowej zapisującej dane fomrularza do bazy danych
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus Ferroli
 * @version		1.0
 * @param array $Wartosci Wartości formularza
 * @param integer $ID ID elementu w bazie
 * @return array zwraca wartości formularza
 */
        
        function WykonajOperacjePoZapisie($Wartosci, $ID){
            $Zapytanie = array();
            if( is_array($this->zero_guided_start['add'] )){
               
                foreach($this->zero_guided_start['add'] as $param ){
                    $SaveValues['categories_zero_guided_start_category_required_id'] = $param;
                    $SaveValues['categories_zero_guided_start_categories_id'] = $ID;
                    $Zapytanie[] = $this->Baza->PrepareInsert('categories_zero_guided_start', $SaveValues);
                }
            }
            if( is_array($this->service_guided_start_parameters['add'] )){
                foreach($this->service_guided_start_parameters['add'] as $param ){
                    $SaveValues['categories_service_guided_start_parameters_category_required_id'] = $param;
                    $SaveValues['categories_service_guided_start_parameters_categories_id'] = $ID;
                    $Zapytanie[] = $this->Baza->PrepareInsert('categories_service_guided_start_parameters', $SaveValues);
                }
            }
            
            foreach($Zapytanie as $zap)
                $this->Baza->Query($zap);
            return $Wartosci;
        }
        
 /**
 * funkcja generująca zmienną patch_to do zapisu w bazie danych
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus Ferroli
 * @version		1.0
 * @param string title nazwa wprowadzanej kategorii
 * @param array $ancestry id kategorii nadrzędnych
 */
        
        function GenerujPathTo( $title, $ancestry = null ){
            $pathto = '';
            if( $ancestry != null && is_array($ancestry) ){
                $results = $this->Baza->GetValues( "SELECT $this->PoleNazwy FROM $this->Tabela WHERE $this->PoleID IN (".implode(',', $ancestry).")" );
                if( $results )
                    foreach ($results as $value) {
                       $pathto .= $value.' &raquo; '; 
                    }
            }        
            $pathto .= '<strong>'.$title.'</strong>';
            return $pathto;
            
        }
/**
 * funkcja ustawiająca akcje na liście elementów
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus Ferroli
 * @version		1.0
 * @param array $Dane
 * @param bool $TH
 */
    
       	function PobierzAkcjeNaLiscie($Dane = array(), $TH = false){
		$Akcje = array();
                $Akcje[] = array('img' => "add.gif", 'img_grey' => "add_grey.gif", 'title' => "Dodaj podkategorie", "akcja_link" => "?modul=$this->Parametr&akcja=dodawanie".($TH ? "" : "&podkategoria={$Dane[$this->PoleID]}"));
                $Akcje[] = array('img' => "information.gif", 'img_grey' => "information_grey.gif", 'title' => "Pokaż", "akcja" => "szczegoly");
		$Akcje[] = array('img' => "pencil.gif", 'img_grey' => "pencil_grey.gif", 'title' => "Edycja", "akcja" => "edycja");
		if(!in_array($this->Parametr,$this->ModulyBezKasowanie)){
			$Akcje[] = array('img' => "bin_empty.gif", 'img_grey' => "bin_empty_grey.gif", 'title' => "Kasowanie", "akcja" => "kasowanie");
		}
		return $Akcje;
	}

        /**
         * Ustawia zablokowane elementy
         */
        function CheckBlockedElements(){
            $check_block = $this->Baza->GetOptions("SELECT id, ancestry FROM $this->Tabela ic");
            $this->categories_have_device = $this->Baza->GetValues("SELECT DISTINCT ". str_replace('ies','y',$this->Tabela)."_id FROM devices ");
            foreach($check_block as $key=>$value){
                $lista[$key] = $key;
                if( !empty($value) ){
                /* @var $temp_ancestry array */
                    $temp_ancestry = array_reverse(explode( '/', $value));
                    if( !in_array($temp_ancestry[0] , $this->ZablokowaneElementyIDs['kasowanie'] ) ){
                        $this->categories_have_undercategories[] = $temp_ancestry[0];
                        $this->ZablokowaneElementyIDs['kasowanie'][] = $temp_ancestry[0];
                        unset($lista[$temp_ancestry[0]]);
                    }
                }
            }
            foreach($this->categories_have_device as $blocked_id){
                    if( !in_array($blocked_id , $this->ZablokowaneElementyIDs['kasowanie'] ) )
                            $this->ZablokowaneElementyIDs['kasowanie'][] = $blocked_id ;
            }
            foreach($lista as $id_lista){
                $this->ZablokowaneElementyIDs['lista'][] = $id_lista;
            }
        }
/**
 * funkcja generująca listę elementów zablokowanych do akcji
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus Ferroli
 * @version		1.0
 * @param array $Elementy elementy listy
 */
        public function ObrobkaDanychLista($Elementy) {
            $Expanded = array();
            if(isset($_COOKIE[$this->cookie_prefix]))
                $Expanded = explode(",", $_COOKIE[$this->cookie_prefix]);
            
            $Sortowanie = array("--TOP--" => array());
            foreach($Elementy as $Idx => $Element){
                $CountParents = explode("/", $Element['ancestry']);
                $CountParentsRev = array_reverse($CountParents);
                $Wx = ($this->Parametr == "urzadzenia" ? 15 : 35);
                $ParentsAreExpanded = true;
                if(empty($Element['ancestry'])){
                    $Padding = $Wx;
                    $Sortowanie["--TOP--"][] = $Idx;
                }else{
                    $Padding = (count($CountParents)+1) * $Wx;
                    $Sortowanie[$CountParentsRev[0]][] = $Idx;
                    foreach($CountParents as $ParID){
                        if(!in_array($ParID, $Expanded)){
                            $ParentsAreExpanded = false;
                            break;
                        }
                    }
                }
                $Elementy[$Idx]['nazwa_expand'] = "<div style='padding-left: {$Padding}px'><span ".(!in_array($Element[$this->PoleID], $this->ZablokowaneElementyIDs['lista']) ? "class='expander' " : "")."data-id='{$Element[$this->PoleID]}'>&nbsp;</span>".($this->Parametr == "urzadzenia" ? "<a href='?modul=$this->Parametr&akcja=lista&kategoria={$Element[$this->PoleID]}'>" : "").$Element[$this->PoleNazwy].($this->Parametr == "urzadzenia" ? "</a>" : "")."</div>";
                $Node = implode("-", $CountParents);
                $Elementy[$Idx]['tr_attr']['this-id'] = $Element[$this->PoleID];
                $Elementy[$Idx]['tr_attr']['id'] = "row".($Node != "" ? "-$Node" : "")."-{$Element[$this->PoleID]}";
                $Elementy[$Idx]['tr_attr']['class'] = (empty($Element['ancestry']) ? "parent ".(in_array($Element[$this->PoleID], $Expanded)  ? "expanded" : "collapsed") : "child-of-row-$Node ".($ParentsAreExpanded ? (in_array($Element[$this->PoleID], $Expanded) ? "expanded" : "collapsed") : "hidden-class"));
            }
            $ElementyWynikowe = $this->PosortujElementy(array(), $Sortowanie, $Elementy, "--TOP--");
            return $ElementyWynikowe;
        }

        function  AkcjaLista($Filtry = array()) {
            include(SCIEZKA_SZABLONOW."category-js-scripts.tpl.php");
            include(SCIEZKA_SZABLONOW."category-all-options.tpl.php");
            parent::AkcjaLista($Filtry);
        }

        function ShowTR($Pola, $Element, $Licznik, $AkcjeNaLiscie, $TR_APPS = NULL){
            $ElementDef = $Element;
            $KolorWiersza = ($Licznik % 2 != 0 ? '#FFFFFF' : '#F0F0F0');
            $AtrybutyDodatkowe = "";
            if(isset($Element['tr_attr'])){
                foreach($Element['tr_attr'] as $Name => $Value){
                    $AtrybutyDodatkowe .= " $Name='$Value'";
                }
            }
            echo("<tr$AtrybutyDodatkowe>");
            foreach ($Pola as $Nazwa => $Opis) {
                    $Styl = "";
                    $Element_class = null;
                    $Title = "";
                    if( array_key_exists($Nazwa, $this->ShortNameList) ){
                        if($this->ShortNameList[$Nazwa]['IsTitle'] == true){
                            $Title = 'title="'.htmlspecialchars($Element[$Nazwa]['TitleListName']).'"';
                            $Element[$Nazwa] = $Element[$Nazwa][$Nazwa];
                        }
                    }                    
                    if(is_array($Opis)){
                        $Styl .= (isset($Opis['td_styl']) ? " style='{$Opis['td_styl']}'" : '');
                        $Styl .= (isset($Opis['td_class']) ? " class='{$Opis['td_class']}'" : "");
                        if(isset($Opis['class']) ){
                            if( is_array($Opis['class'])){
                                $Element_class = 'class="'.$Opis['class'][$Element[$Nazwa]].'"';
                            }elseif(is_string($Opis['class'])){
                                $Element_class = 'class="'.$Opis['class'].'"';
                            }
                        }
                        if(isset($Opis['elementy'])){
                                if(isset($Opis['type']) && $Opis['type'] == "checked-many"){
                                    $Element[$Nazwa] = $Element[$Nazwa] > 1 ? $Opis['elementy'][1] : $Opis['elementy'][$Element[$Nazwa]];
                                }else{
                                    $Element[$Nazwa] = $Opis['elementy'][$Element[$Nazwa]];
                                }
                        }
                        if(isset($Opis['type']) && $Opis['type'] == "date"){
                            $Element[$Nazwa] = ($Element[$Nazwa] == "0000-00-00" ? "&nbsp;" : $Element[$Nazwa]);
                        }
                        if(isset($Opis['type']) && $Opis['type'] == "pln"){
                            $Element[$Nazwa] = number_format($Element[$Nazwa], 2, ","," ")." zł";
                        }
                    }
                    $this->ShowRecord($Element, $Nazwa, $Styl, $Element_class,  $Title);
            }
            if($this->CzySaOpcjeWarunkowe){
                    $AkcjeNaLiscie = $this->PobierzAkcjeNaLiscie($ElementDef);
            }
            $this->ShowActionsList($AkcjeNaLiscie, $ElementDef);
            echo("</tr>");
        }

        /**
         * Sortuje tablice parentów i ich childów
         * @param array $Elementy - tablica do której dokładamy kolejne elementy
         * @param array $Sortowanie - tablica zawierająca odniesienie jaki key ma jakiego parenta
         * @param array $ElementySortuj - elementy które sortujemy
         * @param string $Key - klucz wg którego sortujemy
         * @return array
         */
        function PosortujElementy($Elementy, $Sortowanie, $ElementySortuj, $Key){
            if(isset($Sortowanie[$Key])){
                foreach($Sortowanie[$Key] as $Id){
                    $Elementy[] = $ElementySortuj[$Id];
                    $Elementy = $this->PosortujElementy($Elementy, $Sortowanie, $ElementySortuj, $ElementySortuj[$Id][$this->PoleID]);
                }
            }
            return $Elementy;
        }

        /**
         * Pobiera elementy do lewego menu
         */
        function GetElementsToLeftTree(){
            include(SCIEZKA_SZABLONOW."category-js-scripts.tpl.php");
            $Pola = $this->PobierzListeElementow($Filtry);
            $Elementy = array();
            while ($Element = $this->Baza->GetRow()) {
                $Elementy[] = $Element;
            }
            $Elementy = $this->ObrobkaDanychLista($Elementy);
            return $Elementy;
        }

        function Wyswietl($Akcja) {
            if($Akcja == "kasowanie" && intval($_GET['id']) != 0 && in_array(intval($_GET['id']), $this->ZablokowaneElementyIDs['kasowanie'])){
                if(in_array($_GET['id'], $this->categories_have_device)){
                    $this->Error = "<b>Nie możesz usunąć kategorii do której podpięte jest urządzenie</b>";
                }
                if(in_array($_GET['id'], $this->categories_have_undercategories)){
                    $this->Error = "<b>Nie możesz usunąć kategorii, która posiada podkategorię</b>";
                }
            }
            parent::Wyswietl($Akcja);
        }

        function ShowRecord($Element, $Nazwa, $Styl, $Element_class, $Title){
            echo("<td$Styl>".stripslashes($Element[$Nazwa])."</td>");
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
            if($OpcjaFormularza == 'zero_guided_start' || $OpcjaFormularza == 'service_guided_start_parameters'){
                $params = $this->GetSelect('Wymagania');
                if( isset($Wartosci[$OpcjaFormularza]) && is_array($Wartosci[$OpcjaFormularza]) ){
                    if( !in_array($_POST[$_POST['OpcjaFormularza'].'_add'], (isset($Wartosci[$OpcjaFormularza]['add']) ? $Wartosci[$OpcjaFormularza]['add'] : array() ))
                            && !in_array($_POST[$_POST['OpcjaFormularza'].'_add'], $Wartosci[$OpcjaFormularza]) 
                            && array_key_exists($_POST[$_POST['OpcjaFormularza'].'_add'], $params))
                        $Wartosci[$OpcjaFormularza]['add'][] = $_POST[$_POST['OpcjaFormularza'].'_add'];
                }else{
                    if(array_key_exists($_POST[$_POST['OpcjaFormularza'].'_add'], $params)
                            && !in_array($_POST[$_POST['OpcjaFormularza'].'_add'], (isset($Wartosci[$OpcjaFormularza]) ? $Wartosci[$OpcjaFormularza] : array() )) )
                        $Wartosci[$OpcjaFormularza]['add'][] = $_POST[$_POST['OpcjaFormularza'].'_add'];
                }
                return $Wartosci; 
            }
        }
        
        
/**
 * funkcja pobierająca dena elementu
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus Ferroli
 * @version		1.0
 * @return array zwraca dane elementu
 */    
        
        public function PobierzDaneElementu($ID, $Typ = null) {
            $Dane = parent::PobierzDaneElementu($ID, $Typ);
            $Dane['zero_guided_start'] = $this->GetListOfDevicesParametersRequired($ID, 'zero_guided_start');
            if($Dane['zero_guided_start'] == false){
                $Dane['zero_guided_start'] = 'Brak wybranych parametrów';
            }
            $Dane['service_guided_start_parameters'] = $this->GetListOfDevicesParametersRequired($ID, 'service_guided_start_parameters');
            if($Dane['service_guided_start_parameters'] == false){
                $Dane['service_guided_start_parameters'] = 'Brak wybranych parametrów';
            }
            
            return $Dane;
        }
        
        /**
 * funkcja pobierająca listę wymaganych elementów sprawdzanych przy rozruchu dla określonego urządzenia
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus Ferroli
 * @version		1.0
 * @param integer   $ID id urządzenia
 * @return array zwraca listę elwmentów
 */    
        
        function GetListOfDevicesParametersRequired($ID, $Type){
            $ret =  $this->Baza->GetValues("SELECT categories_{$Type}_category_required_id FROM categories_{$Type} WHERE categories_{$Type}_categories_id = '$ID'");
            return $ret;
        }
        
        
}
/*
 *  end class ModulKategorie
 */
?>
