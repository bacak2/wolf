<?php
/**
 * Moduł Urządzenia - moduł do zarządzanie urządzeniami
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 */

/*
 *  begin class ModulUrzadzenia
 */

class ModulUrzadzenia extends ModulBazowy{

    
/**
 * zmienne klasy
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright       Copyright (c) Maciej Kura
 * @package		PanelPlus 
 */    
    
    protected $TabelaParametryWymaganeUrzadzania;
    protected $WymaganeParametry;
    protected $allowedImportTypes;
    protected $errors;
    protected $columnsFormat;
    protected $importTitle;

/**
 * konstruktor klasy Urzadzenia
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
            if(!$this->Uzytkownik->IsAdmin()){
                $this->ModulyBezDodawania[] = $this->Parametr;
                $this->ModulyBezEdycji[] = $this->Parametr;
                $this->ModulyBezKasowanie[] = $this->Parametr;
            }
            $this->ModulyBezMenuBocznego[] = $this->Parametr;
            $this->Tabela = 'devices';
            $this->PoleID = 'id';
            $this->PoleSort = 'kzu';
            $this->PoleNazwy = 'title';
            $this->Nazwa = 'Urządzenia';
            $this->PoleVisible = 'visible';
            $this->TabelaParametryWymaganeUrzadzania = 'devices_required_checkbox';
            $this->Filtry[] = array('nazwa' => 'category_id', 'opis' => 'Kategoria', 'opcje' => $this->GetListPathKategorii('categories'), "typ" => "lista", "domyslna" => "Kategoria");
            $this->Filtry[] = array('nazwa' => 'd.kzu|c.code|d.title', 'opis' => false, "typ" => "tekst", "placeholder" => "Znajdź urządzenie", 'search' => true);
            $this->ErrorsDescriptions['unikalne']['kzu'] = "Urządzenie o Numerze Katalogowym %s  jest już zapisany w bazie. Aby zapisać urządzenie należy podać unikatowy KZU";
            $this->BlockElements();
            $this->TabelaPlikowPrefix = 'devices_title_file_';
            $this->TabelaPlikow =  'devices_title_file';
            $this->PolePliku = $this->TabelaPlikowPrefix.'name';
            $this->PolePlikuOryginalnego = $this->TabelaPlikowPrefix.'orginal_name';
            $this->PoleZdjecia = $this->TabelaPlikowPrefix.'list';
            $this->PolePlikuID = $this->TabelaPlikowPrefix.'id';
            $this->PolePlikuHASH = $this->TabelaPlikowPrefix.'hash';
            $this->allowedImportTypes = array('xls','xlsx' ,'ods');
            $this->columnsFormat = '<tr><td>Lp.</td><td>Num</td></tr>';
            $this->importTitle = 'urządzenia';
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
                    'kzu' =>  array('naglowek' => 'Numer Katalogowy',  'td_styl' => 'width: 200px;'),
                    $this->PoleNazwy => array('naglowek' => 'Nazwa'),
                );
                    if($this->Uzytkownik->IsAdmin())
//                        $Wyniki['visible'] = array('naglowek' => 'Widoczne Serwis', 'elementy' => Usefull::GetCheckImg(), 'td_styl' => 'width: 50px;', 'td_class' => 'active-check');
                        $Wyniki['setup'] = array('naglowek' => 'Pierwsze uruchomienie', 'elementy' => Usefull::GetCheckImg(), 'td_styl' => 'width: 50px;', 'td_class' => 'active-check');
//                    'category_id' => array('naglowek' => 'Kategoria', 'elementy' => $this->GetListPathKategorii('categories')),

            $Where = $this->GenerujWarunki();
            $Sort = $this->GenerujSortowanie();
            $this->Baza->Query($this->QueryPagination("SELECT d.* FROM $this->Tabela d 
                LEFT JOIN categories c ON (d.category_id = c.id) 
                $Where $Sort", $this->ParametrPaginacji, 30));
            return $Wyniki;
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
            $Dodawanie = array('link' => "?modul=$this->Parametr&akcja=dodawanie", 'class' => 'dodawanie', 'img' => 'add.gif', 'desc' => 'Dodaj urządzenie');
//            $ListaNazw = array('link' => "?modul=urzadzenia_nazwa&akcja=lista", 'class' => 'lista', 'img' => 'list.gif', 'desc' => 'Lista nazw');
            $Lista = array('link' => "?modul=$this->Parametr&akcja=lista", 'class' => 'lista', 'img' => 'list.gif', 'desc' => 'Lista urządzeń');
            
            if(!in_array($this->Parametr, $this->ModulyBezDodawania)){
                $Akcje['akcja'][] = $Dodawanie;
            }
            $Akcje['akcja'][] = $Lista;
/*            if(!in_array($this->Parametr, $this->ModulyBezEdycji)){
                $Akcje['szczegoly'][] = array('link' => "?modul=$this->Parametr&akcja=edycja&id=$ID&back=details", 'class' => 'edycja', 'img' => 'pencil.gif', 'desc' => 'Edycja');
            }
*/
//            $Akcje['akcja'][] = $ListaNazw;
            return $Akcje;
        }
        
        
/**
 * funkcja wyświetlająca panel boczny z kategoriami i podkategoriami
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @param integer $ID id elementu
 */
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
        
/**
 * funkcja pobierająca listę kategorii i podkategorii
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @param string $tabela Nazwa tabeli z której należy pobrać listę kategorii i podkategorii
 * @return array $category
 */          
        
        protected function GetListKategoriePodkategorie( $tabela ){
        /* @var $category array */
            $category = $this->Baza->GetResultAsArray( "SELECT id, concat( title,' <b>[', code, ']</b>') as title, ancestry FROM $tabela ORDER BY code, LENGTH(code), path_to  ", 'id' );
            return $category;
        }
/**
 * funkcja ustawiająca domślny warunek na liście
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 */        
        function DomyslnyWarunek(){
            $where = array();
            if( !$this->Uzytkownik->IsAdmin() ){
                $where[] = ' visible = 1 ';
            }
            if( isset($_GET['kategoria']) && !empty($_GET['kategoria']) && is_numeric($_GET['kategoria']) ){
                 $where[] = " (category_id = '".$_GET['kategoria']."'
                    OR ancestry LIKE '%".$_GET['kategoria']."%')";
            }
            return implode(' AND ', $where);
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
        function &GenerujFormularz(&$Wartosci = array(), $Mapuj = false) {
            $Formularz = parent::GenerujFormularz();
            $Formularz->DodajPole('kzu', 'tekst', 'Numer Katalogowy*', array( 'atrybuty'=>array('class'=>'kzu'),'show_label'=>true,'div_class'=>'super-form-span3',));
            $Formularz->DodajPole('devices_title_devices_title_id', 'lista_nazw_urzadzen', 'Nazwa*', array('show_label'=>true,'div_class'=>'super-form-span3', 'elementy'=>$this->GetListaNazw(), 'domyslny'=>'wybierz nazwę' ));
            $Formularz->DodajPole('category_id', 'lista_kategoria_urzadzenia', 'Kategoria', array('show_label'=>true,'div_class'=>'super-form-span3', 'elementy'=>$this->GetListPathKategorii('categories'), 'title'=>'kategorie_polskie', 'selects'=>$this->GetSelectKategoriePodkategorie('categories')));
            $Formularz->DodajPole('clear', 'clear', '', array());
            $Formularz->DodajPole('setup_zero', 'checkbox', 'Pierwsze Uruchomienie', array('show_label'=>true,'div_class'=>'super-form-span3', 'atrybuty'=>array('class'=>'warranty_service') ));
            $Formularz->DodajPole('setup', 'checkbox', 'Uruchomienie serwisowe', array('show_label'=>true,'div_class'=>'super-form-span3', 'atrybuty'=>array('class'=>'warranty_service') ));
            $Formularz->DodajPole('clear', 'clear', '', array());
            if($this->Uzytkownik->IsAdmin()){
                $Formularz->DodajPole('visible', 'checkbox', 'Widoczne serwis', array('show_label'=>true,'div_class'=>'super-form-span3'));
            }
            $Formularz->DodajPole('warranty', 'tekst', 'Gwarancja (w miesiącach)', array('atrybuty'=>array('class' => 'cost'),'show_label'=>true,'div_class'=>'super-form-span3'));
            $Formularz->DodajPole('warranty_service', 'pole_warranty_service', 'Przeglądy gwarancyjne', array('show_label'=>true,'div_class'=>'super-form-span3', 'atrybuty'=>array('class', 'warranty_service') ));
            $Formularz->DodajPole('warranty_service_months', 'hidden', '', array());
            $Formularz->DodajPole('clear', 'clear', '', array());
            if($this->WykonywanaAkcja == 'szczegoly'){
                $Formularz->DodajPole('arrangement_of_parts', 'rozmieszczenie_czesci', 'Rozmieszczenie części', array( 'KatalogDanych'=> SCIEZKA_DANYCH_MODULOW.'kategorie_czesci/', 'kategorie_czesci'=>$this->GetSelect('KategorieCzesci'), 'show_label'=>true,'div_class'=>'super-form-span1'));
                $Formularz->DodajPole('clear', 'clear', '', array());
	    }
	    if($this->WykonywanaAkcja == 'szczegoly' || ($this->Parametr == 'zlecenia')){
                $Formularz->DodajPole('lista_zalacznikow', 'lista_plikow', 'Lista załączników', array('show_label'=>true,'div_class'=>'super-form-span1' ));
                $Formularz->DodajPole('clear', 'clear', '', array());
                $Formularz->DodajPole('devices_params', 'devices_params', 'Lista wymaganych parametrów', array( 'show_label'=>true,'div_class'=>'super-form-span1', 'elementy'=>$this->GetSelect('Wymagania')));
                $Formularz->DodajPole('clear', 'clear', '', array());
            }
            $Formularz->DodajPole('description', 'tekst_dlugi', 'Opis', array('show_label'=>true,'div_class'=>'super-form-span1', 'arybuty' => array('class'=>'opis')));
            $Formularz->DodajDoPolBezRamki('warranty_service');
            $Formularz->DodajDoPolBezRamki('setup_zero');
            $Formularz->DodajDoPolBezRamki('setup');
            $Formularz->DodajDoPolBezRamki('devices_title_devices_title_id');
            $Formularz->DodajDoPolBezRamki('category_id');
            $Formularz->DodajDoPolBezRamki('visible');
            $Formularz->DodajDoPolBezRamki('warranty_service_months');
            $Formularz->DodajDoPolBezLabel('warranty_service_months');
            
            $Formularz->DodajPoleWymagane("kzu", false);
            $Formularz->DodajPoleWymagane("devices_title_devices_title_id", false);
            $Formularz->DodajPoleNieDublujace('kzu', false);
            
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}
        
/**
 * funkcja pobierająca listę kategorii i podkategorii
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @param string $tabela Nazwa tabeli z której należy pobrać listę kategorii i podkategorii
 * @return array $category
 */          
        
        protected function GetSelectKategoriePodkategorie( $tabela ){
            /* @var $listOfSelects array */
            $listOfSelects = array();
            /* @var $category array */
            $category = $this->GetListKategoriePodkategorie( $tabela );
            
            foreach( $category as $id=>$kategoria){
                if( !empty($kategoria['ancestry']) ){
                    $ancestry = array_reverse(explode('/',$kategoria['ancestry']));
                    $level = count( $ancestry );
                    
                }else{
                    $level = 0;
                    $ancestry[0] = 0;
                }
                $listOfSelects[$level][$ancestry[0]][] = $kategoria;
            }
            ksort($listOfSelects);
 //           $this->VAR_DUMP($listOfSelects);

            return $listOfSelects;
        } 
/**
 * funkcja ustawiająca dane przed zapisem
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @return array dane do zpisu
 */

        function OperacjePrzedZapisem($Wartosci, $ID){
            if($ID){
                foreach($Wartosci as $FieldID => $Value){
                    if($Value != $Values[$FieldID]){
                        $Wartosci['updated_at'] = date("Y-m-d H:i:s");
                        break;
                    }
                }
            }
            $Wartosci['warranty_service_months'] = implode(', ', explode(',',preg_replace('/ /', '', isset( $_POST['warranty_service_months']) ? $_POST['warranty_service_months'] : '')));
           // $Wartosci['warranty_service'] = isset($_POST['warranty_service']) ? $_POST['warranty_service'] : 0;
            $Wartosci['title'] = $this->Baza->GetValue("SELECT devices_title_title FROM devices_title WHERE devices_title_id = {$Wartosci['devices_title_devices_title_id']}");
            return $Wartosci;
        }

/**
 * funkcja wywoływana przed wywołaniem funkcji bazowej zapisującej dane fomrularza do bazy danych
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @param array $Wartosci Wartości formularza
 * @param integer $ID ID elementu w bazie
 * @return array zwraca wartości formularza
 */
        
        function WykonajOperacjePoZapisie($Wartosci, $ID){
            return $Wartosci;
        }

/**
 * funkcja wywoływana podczas akcji "przeładowania" wykonująca operację na danych z formularza
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @param array $Wartosci Wartości formularza
 * @param object $Formularz  obiekt klasy Formularz
 * @param integer $ID ID elementu w bazie
 * @return array zwraca wartości formularza
 */
        
        public function AkcjaPrzeladowanie($Wartosci, $Formularz, $ID = null) {
            if($_POST['OpcjaFormularza'] == 'edytuj_nazwe_urzadzenia'){
                $_SESSION['post_last_devices_values'] = $_POST;
                $_SESSION['post_last_devices_values']['id'] = $Wartosci['devices_title_devices_title_id'];
                $_SESSION['get_last_devices_values'] = $_GET;
                $o_nazwa_urzadzenia = new ModulUrzadzeniaNazwa($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
                $a_nazwa_urzadzenia = $o_nazwa_urzadzenia->PobierzDaneElementu( $Wartosci['devices_title_devices_title_id'] );
                return $a_nazwa_urzadzenia;
            }elseif($_POST['OpcjaFormularza'] == 'dodaj_nazwe_urzadzenia'){
                $_SESSION['post_last_devices_values'] = $_POST;
                $_SESSION['get_last_devices_values'] = $_GET;
                return array();
            }elseif( isset($_SESSION['post_last_devices_values']) ){
                    $Formularz = $this->GenerujFormularz($_SESSION['post_last_devices_values'], true);
                    $PolaWymagane = $Formularz->ZwrocPolaWymagane();
                    $PolaZdublowane = $Formularz->ZwrocPolaNieDublujace();
                    $OpcjaFormularza = (isset($_POST['OpcjaFormularza']) ? $_POST['OpcjaFormularza'] : 'zapisz');
                    $Wartosci = $Formularz->ZwrocWartosciPol($_SESSION['post_last_devices_values']);
                    unset($_SESSION['post_last_devices_values']);
                    unset($_SESSION['get_last_devices_values']);
                    return $Wartosci; 
            }else{
                return $Wartosci; 
            }
        }
/**
 * funkcja wywoływana podczas akcji "przeładowania" wykonująca operację na formularzu
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @param array $Wartosci Wartości formularza
 * @param object $Formularz  obiekt klasy Formularz
 * @return object zwraca obiekt Formularz
 */        
        
        function AkcjaPrzeladowanieFormularz($Wartosci, $Formularz, $ID = NULL){
            if($_POST['OpcjaFormularza'] == 'edytuj_nazwe_urzadzenia' || $_POST['OpcjaFormularza'] == 'dodaj_nazwe_urzadzenia'){
                $o_nazwa_urzadzenia = new ModulUrzadzeniaNazwa($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
                if($_POST['OpcjaFormularza'] == 'edytuj_nazwe_urzadzenia' )
                    $o_nazwa_urzadzenia -> WykonywanaAkcja = 'edycja';
                else
                    $o_nazwa_urzadzenia -> WykonywanaAkcja = 'dodawanie';
                
                return $o_nazwa_urzadzenia->GenerujFormularz();
            }else{
                return $Formularz;
            }
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
 * funkcja wyświtlająca szczegóły urządzenia
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 */           
        
        function AkcjaSzczegoly($ID, $Simply = false){
            $Dane = $this->PobierzDaneElementu($ID);
            $Dane['description'] = nl2br($Dane['description']);
            $ListaNazw = $this->GetListaNazw();
            $Dane['devices_title_devices_title_id'] = $ListaNazw[$Dane['devices_title_devices_title_id']];
            $kat = $this->GetListPathKategorii('categories');
            $Dane['category_id'] = $kat[$Dane['category_id']];
            $Dane['warranty_service'] = $Dane['warranty_service_months'];
            $Formularz = $this->GenerujFormularz($Dane);
            $Formularz->WyswietlDane($Dane);
        }        

/**
 * funkcja zwracająca tablicę rozruchu
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @return array zwraca listę możliowści
 */         
        function GetListaRozruch(){
            return array('0'=>'Nie wymagany', '1'=>'Wymagany');
        }
        
/**
 * funkcja pobierająca dena elementu
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @return array zwraca dane elementu
 */    
        
        public function PobierzDaneElementu($ID, $Typ = null) {
            $Dane = parent::PobierzDaneElementu($ID, $Typ);
            $Dane['warranty_service'] = $Dane['warranty_service'].'|'.$Dane['warranty_service_months'];
            $Dane['devices_params'] = $this->GetListOfDevicesParametersRequired($Dane['category_id']);
            if(empty($Dane['devices_params'])){
                $Dane['devices_params'] = array('zero_guided_start' => 'Brak wybranych parametrów',
						'service_guided_start_parameters' => 'Brak wybranych parametrów');
            }
            if( !in_array($this->WykonywanaAkcja, array('edycja', 'dodawanie'))){
                $Dane['lista_zalacznikow'] = $this->GetListOfFile($Dane['devices_title_devices_title_id']);
                $Dane['arrangement_of_parts'] = $this->GetListArrangementOfPartsFile($Dane['devices_title_devices_title_id']);
            }
            return $Dane;
        }
        
/**
 * funkcja pobierająca listę wymaganych elementów sprawdzanych przy rozruchu dla określonego urządzenia
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @param integer   $ID id urządzenia
 * @return array zwraca listę elwmentów
 */    
        
        function GetListOfDevicesParametersRequired($ID){
	    $Kategorie = new ModulKategorie( $this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka );
            $DaneKategorii['zero_guided_start'] = $Kategorie->GetListOfDevicesParametersRequired($ID, 'zero_guided_start');
            $DaneKategorii['service_guided_start_parameters']  = $Kategorie->GetListOfDevicesParametersRequired($ID, 'service_guided_start_parameters');
            return $DaneKategorii;
        }
        
/**
 * funkcja pobierająca listę plików załączonych do nazwy urządzenia
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @param integer   $ID id urządzenia
 * @return array zwraca listę załączonych plików
 */
        function GetListOfFile($ID){
            $nazwa = new ModulUrzadzeniaNazwa( $this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka );
        /* @var $DeviceNameData object ModulUrzadzeniaNazwa */
            $DeviceNameData = $nazwa->PobierzDaneElementu($ID);

            return isset($DeviceNameData[$nazwa->PolePliku]) ? $DeviceNameData[$nazwa->PolePliku] : array();
        }
        
        function GetListArrangementOfPartsFile($ID){
            $nazwa = new ModulUrzadzeniaNazwa( $this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka );
        /* @var $DeviceNameData object ModulUrzadzeniaNazwa */
            $DeviceNameData = $nazwa->PobierzDaneElementu($ID);
            return isset($DeviceNameData['arrangement_of_parts']) ? $DeviceNameData['arrangement_of_parts'] : array();
        }
        
/**
 * funkcja pobierająca pełną listę wymaganych elementów sprawdzanych przy rozruchu
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @return array zwraca listę elwmentów wymaganych
 */           
        function GetListOfParametersRequired(){
            return $this->Baza->GetOptions("SELECT required_checkbox_id, required_checkbox_name FROM items_required_checkbox");
        }
/**
 * funkcja pobierająca pełną listę nazw urządzeń
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @return array zwraca listę opcji nazw urządzeń
 */  
        function GetList($Instalator = false){
/*            if($Instalator){
                $treinings = $this->Baza->GetValues("SELECT DISTINCT(devices_title_devices_title_id)
                        FROM training_users tu
                        LEFT JOIN training t ON ( t.training_id = tu.training_id )
                        LEFT JOIN training_devices td ON ( t.training_id = td.training_id )
                        WHERE users_id = {$this->UserID} 
                        AND end_date < CURDATE( )
                    ");
                return $this->Baza->GetOptions("SELECT d.$this->PoleID, CONCAT(dt.devices_title_title,' [',d.kzu,']') as name 
                    FROM $this->Tabela d
                    LEFT JOIN devices_title dt ON (dt.devices_title_id = d.devices_title_devices_title_id)
                    WHERE dt.devices_title_id IN ('".implode("','", $treinings)."')
                    ORDER BY name ASC");                
            }else{*/
                return $this->Baza->GetOptions("SELECT d.$this->PoleID, CONCAT(dt.devices_title_title,' [',d.kzu,']') as name FROM $this->Tabela d LEFT JOIN devices_title dt ON(dt.devices_title_id = d.devices_title_devices_title_id) ORDER BY name ASC");
//            }
        }
/**
 * funkcja pobierająca plik
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @return mixed funkcja zwracająca plik (wysyła do przeglądarki)
 */        
        function GetFile($ID) {
            $nazwa = new ModulUrzadzeniaNazwa( $this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka );
            return $nazwa->GetFile($ID);
        }
        /**
 * funkcja usuwająca plik z bazy danych i dysku
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @param $Prefix string prefix_do_pliku
 * @param $NazwaPola string nazwa pola w bazie w którym ma zostać zapisana nazwa pliku znajdującego się na dysku
 * @param $ID integer 
 */  
        
        function UsunPlik($ID, $Hash){
            $UrzadzeniaNazwaID = $this->Baza->GetValue( "SELECT devices_title_devices_title_id FROM $this->Tabela WHERE id='{$ID}'" );
            $nazwa = new ModulUrzadzeniaNazwa( $this->Baza, $this->Uzytkownik, 'urzadzenia_nazwa', $this->Sciezka );
            $nazwa->LinkPowrotu = "?modul=urzadzenia_nazwa&akcja=edycja&id=".$UrzadzeniaNazwaID;
            return $nazwa->UsunPlik($UrzadzeniaNazwaID, $Hash);
        }
/**
 * funkcja pobierająca części przypisane do danego urządzenia
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @param  integer $ID id urządzenia dla kturego poieramy listę części
 * @return array lista przypisanych urządzeń
 */   
        function GetParts($ID){
            $Dane = array();
            $Ceny = $this->Baza->GetResultAsArray("SELECT id, value FROM group_pricing ORDER BY id ASC", 'id');
            $partsIDs = $this->Baza->GetValues("SELECT part_id FROM device_parts WHERE device_id='$ID'");
            foreach($partsIDs as $ID){
                $Dane[$ID] = $this->GetPartsData($ID);
                $Dane[$ID]['amount'] = number_format($Ceny[$Dane[$ID]['amount_id']]['value'],2,',',' ')." zł";
                $Dane[$ID]['do_koszyka'] = Usefull::ShowBasketAddButton($ID);
            }
            return $Dane;
            
        }
 /**
 * funkcja pobierająca informacje o części
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @param  integer $ID ID cześci do pobrania
 * @return array informacje o części
 */         
        function GetPartsData($ID){
            $this->Baza->Query("SELECT * FROM parts WHERE id='$ID' LIMIT 1");
            $Dane = $this->Baza->GetRow();
            return $Dane;
        }
        
        function BlockElements(){
            $devices_id = $this->Baza->GetValues("SELECT DISTINCT(device_id) FROM serial_numbers");
            foreach($devices_id as $blocked_id){
                if(!in_array($blocked_id , $this->ZablokowaneElementyIDs['kasowanie']))
                    $this->ZablokowaneElementyIDs['kasowanie'][] = $blocked_id ;
            }
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

            exit();

        }
}
/*
 *  end class ModulUrzadzenia
 */
?>
