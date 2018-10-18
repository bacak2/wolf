<?php
/**
 * Moduł Urządzenia - moduł do zarządzanie nazwami urządzeń
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus
 * @version		1.0
 */

/*
 *  begin class ModulUrzadzenia
 */

class ModulUrzadzeniaNazwa extends ModulBazowy {

    
/**
 * zmienne klasy
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright       Copyright (c) Maciej Kura
 * @package		PanelPlus 
 */    
    
/**
 * Prefix do tableli plików
 * @var string
 */
    protected $TabelaPlikowPrefix;
    
/**
 * Pole z nazwą pliku zapisanego na dysku.
 * @var string
 */
    public $PolePliku;

/**
 * Pole z nazwą pliku zapisanego na dysku.
 * @var string
 */
    public $PolePlikuID;
    public $PolePlikuHASH;
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
            $this->ModulyBezMenuBocznego[] = $this->Parametr;
            $this->ModulyBezMenuBocznego[] = 'urzadzenia_nazwa';
            $this->KatalogDanych = SCIEZKA_DANYCH_MODULOW.'urzadzenia_nazwa/';
            $this->Tabela = 'devices_title';
            $this->PoleID = 'devices_title_id';
            $this->PoleNazwy = 'devices_title_title';
            $this->Nazwa = 'Urządzenia';
            $this->PoleVisible = '1';
            $this->TabelaPlikowPrefix = 'devices_title_file_';
            $this->TabelaPlikow =  'devices_title_file';
            $this->PolePliku = $this->TabelaPlikowPrefix.'name';
            $this->PolePlikuOryginalnego = $this->TabelaPlikowPrefix.'orginal_name';
            $this->PoleZdjecia = $this->TabelaPlikowPrefix.'list';
            $this->PolePlikuID = $this->TabelaPlikowPrefix.'id';
            $this->PolePlikuHASH = $this->TabelaPlikowPrefix.'hash';
            $this->BlockElements();
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
                    $this->PoleID => "ID",
                    $this->PoleNazwy => 'Nazwa',
            );
            $Where = $this->GenerujWarunki();
            $Sort = $this->GenerujSortowanie();
            $this->Baza->Query($this->QueryPagination("SELECT dn.* FROM $this->Tabela dn 
                $Where $Sort", $this->ParametrPaginacji, 30));
            if(isset($_SESSION['get_last_devices_values'])){
                unset($_SESSION['get_last_devices_values']);
            }
            return $Wyniki;
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
            $params = '';
            if($this->Parametr != "urzadzenia_nazwa"){
                $submit_form_link = preg_replace( '/urzadzenia&/', 'urzadzenia_nazwa&', $_SERVER['REQUEST_URI']);
                if(isset($_SESSION['get_last_devices_values'])){
                    $params = '&akcja='.$_SESSION['get_last_devices_values']['akcja'];
                    $params .= (isset($_SESSION['get_last_devices_values']['id']) ? '&id='.$_SESSION['get_last_devices_values']['id'] : '' );
                    $this->PrzyciskiFormularza['anuluj']['link']= $this->LinkPowrotu.$params;
                    if( $_SESSION['post_last_devices_values']['OpcjaFormularza'] == 'dodaj_nazwe_urzadzenia' ){
                        $submit_form_link = preg_replace( '/edycja/', 'dodawanie', $submit_form_link );
                    }elseif( $_SESSION['post_last_devices_values']['OpcjaFormularza'] == 'edytuj_nazwe_urzadzenia' ){
                        $submit_form_link = preg_replace( '/dodawanie/', 'edycja', $submit_form_link );
                        $submit_form_link = preg_replace( '/id=\d+/', '', $submit_form_link );
                        $submit_form_link .= '&id='.$_SESSION['post_last_devices_values']['id'];
                    }else{
                        $submit_form_link = preg_replace( '/id=\d+/', 'id='.$_SESSION['post_last_devices_values']['id'], $submit_form_link );
                    }
                 }
                $Formularz = new Formularz( $submit_form_link , $this->LinkPowrotu.$params, 'urzadzenia_nazwa');
            }elseif( isset($_SESSION['get_last_devices_values']) ){
                    $return_lnk = '?modul='.$_SESSION['get_last_devices_values']['modul'];
                    $return_lnk .= '&akcja='.$_SESSION['get_last_devices_values']['akcja'];
                    $return_lnk .= (isset($_SESSION['get_last_devices_values']['id']) ? '&id='.$_SESSION['get_last_devices_values']['id'] : '' );
                    $this->PrzyciskiFormularza['anuluj']['link'] = $this->LinkPowrotu = $return_lnk;
                    $submit_form_link = preg_replace("/&plik=[0-9a-zA-Z]*/", "", $_SERVER['REQUEST_URI']);
                    $submit_form_link = preg_replace("/&usun_plik=1/", "", $submit_form_link);
                    $Formularz = new Formularz( $submit_form_link , $return_lnk, 'urzadzenia_nazwa');
            }else{
                $Formularz = parent::GenerujFormularz();
            }
            
            $Formularz->DodajPole($this->PoleNazwy, 'tekst', 'Nazwa*', array('show_label'=>true,'div_class'=>'super-form-span3'));
            $Formularz->DodajPole('clear','clear',false);
            if($this->WykonywanaAkcja != 'dodawanie'){
                $Formularz->DodajPole ($this->PolePliku, 'lista_plikow', 'Załączniki', array('show_label'=>true,'div_class'=>'super-form-span1'));
                $Formularz->DodajPole('clear','clear',false);
                $Formularz->DodajPole('arrangement_of_parts', 'rozmieszczenie_czesci', 'Rozmieszczenie części', array( 'KatalogDanych'=> SCIEZKA_DANYCH_MODULOW.'kategorie_czesci/', 'kategorie_czesci'=>$this->GetSelect('KategorieCzesci'), 'show_label'=>true,'div_class'=>'super-form-span1'));
                $Formularz->DodajPole('clear','clear',false);
            }
            
            $Formularz->DodajDoPolBezRamki($this->PolePliku);
            $Formularz->DodajDoPolBezRamki('arrangement_of_parts');
            
            $Formularz->DodajPoleWymagane( $this->PoleNazwy, false);
            $Formularz->DodajPoleNieDublujace( $this->PoleNazwy, false);
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}

/**
 * funkcja zapisująca plik do bazy anych
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @param $Prefix string prefix_do_pliku
 * @param $NazwaPola string nazwa pola w bazie w którym ma zostać zapisana nazwa pliku znajdującego się na dysku
 * @param $ID integer 
 */   
	function ZapiszPlik($Prefix, $NazwaPola, $ID){
            if($this->PrzeslijObrazek($NazwaPola,$Prefix)){
			$NazwaPliku = mysql_real_escape_string($_FILES[$NazwaPola]['name']);
                        $size = $_FILES[$NazwaPola]['size'];
                        $type = $_FILES[$NazwaPola]['type'];
                            
			$this->Baza->Query("INSERT INTO $this->TabelaPlikow
                        SET 
                            $this->TabelaPlikowPrefix$this->PoleID = '$ID',
                            $this->PolePliku = '$this->NazwaPrzeslanegoPliku',
                            $this->PolePlikuOryginalnego ='$NazwaPliku',
                            {$this->TabelaPlikowPrefix}size = '$size',
                            {$this->TabelaPlikowPrefix}content_type = '$type',
                            {$this->TabelaPlikowPrefix}created_at = NOW(),
                            {$this->PolePlikuHASH} = sha1(concat('$ID$size$NazwaPliku$type'))
                        ");
                        $FileID = $this->Baza->GetLastInsertID();
                        if(isset($_POST["OpcjaFormularza"]) && $_POST["OpcjaFormularza"] == "dodaj_kategorieczesci"){
                            
                        }
		}else{
			Usefull::ShowKomunikatError('<b>Wystąpił problem. Plik nie został zapisany na serwerze.</b>');
		}
	}
/**
 * funkcja przeładowywująca 
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @param $Wartosci array
 * @param $Formularz object Formularz
 * @param $ID integer 
 */   
        
        function AkcjaPrzeladowanie($Wartosci, $Formularz, $ID = NULL){
            $this->KatalogDanych =  SCIEZKA_DANYCH_MODULOW.'urzadzenia/'.$ID.'/';
//            return parent::AkcjaPrzeladowanie($Wartosci, $Formularz, $ID);
            if( $_POST['OpcjaFormularza'] == 'dodaj_plik' ){
                $this->ZapiszPlik($this->Prefix,$Formularz->ZwrocNazweMapyPola($this->PolePliku) , $ID);
                $Wartosci = $this->PobierzDaneElementu($ID);
            }elseif( $_POST['OpcjaFormularza'] == 'dodaj_kategorieczesci' ){
                $this->ZapiszPlikKategorii($this->Prefix,$Formularz->ZwrocNazweMapyPola('arrangement_of_parts') , $ID, $Wartosci);
                $Wartosci = $this->PobierzDaneElementu($ID);
            }
            return $Wartosci;
        }
        
        
 /**
  * funkcja pobierająca dane elementu 
  * 
  * @author		Maciej Kura <Maciej.Kura@gmail.com>
  * @copyright           Copyright (c) 2012 Maciej Kura
  * @package		PanelPlus 
  * @version		1.0
  * @param $ID integer 
  * @param $Typ mixed
  * 
  */         
	function PobierzDaneElementu($ID, $Typ = null) {
            $this->KatalogDanych =  SCIEZKA_DANYCH_MODULOW.'urzadzenia/'.$ID.'/';          
	    $dane = parent::PobierzDaneElementu($ID, $Typ);
            return $dane;
        }

/**
 * funkcja dla dodatkwoych akcji niestandarowych
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @param $ID integer 
 */          
        function AkcjeNiestandardowe($ID){
            switch ($this->WykonywanaAkcja) {
                case 'pobierz':
                        $this->AkcjaPobierz($ID);
                        break;
		default:
                    parent::AkcjeNiestandardowe($ID);
                    break;
            }
	}
 /**
  * funkcja pobierająca nazwę pliku zapisanego na dysku z bazy danych o id = $ID
  * 
  * @author		Maciej Kura <Maciej.Kura@gmail.com>
  * @copyright          Copyright (c) 2012 Maciej Kura
  * @package		PanelPlus 
  * @version		1.0
  * @param $ID integer id elementu
  * @return array ($DiskFile, $OryginalFileName)
  */         
        function GetFile($ID){
            
            list($ID, $HASH) = explode('_', $ID);
            if( $this->Baza->Query("SELECT * FROM $this->TabelaPlikow WHERE $this->PolePlikuHASH = '$HASH' AND $this->PolePlikuID = '$ID' LIMIT 1")){
                $file = $this->Baza->GetRow();
                    $str_to_hash = $file[$this->TabelaPlikowPrefix.$this->PoleID].$file[$this->TabelaPlikowPrefix.'size'].$file[$this->PolePlikuOryginalnego].$file[$this->TabelaPlikowPrefix.'content_type'];
                    if( $file && Usefull::CheckHash( $str_to_hash, $HASH, 'sha1')==0 ){
                    $this->KatalogDanych = SCIEZKA_DANYCH_MODULOW.'urzadzenia/'.$file[$this->TabelaPlikowPrefix.$this->PoleID].'/';
                    if(is_file($this->KatalogDanych.$file[$this->PolePliku])){
                        return array($this->KatalogDanych.$file[$this->PolePliku], $file[$this->PolePlikuOryginalnego]);
                    }else{
                        return array(false, null);
                    }
                }else{
                    return array(false, null);
                }
            }else{
                return array(false, null);
            }
        }        
        
/**
 * funkcja ustawiająca dane przed zapisem
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @return array dane domyślne
 */

        function OperacjePrzedZapisem($Wartosci, $ID){
            unset($Wartosci['arrangement_of_parts']);
            if($ID){
                $Values = $this->PobierzDaneElementu($ID);
                foreach($Wartosci as $FieldID => $Value){
                    if($Value != $Values[$FieldID] && $FieldID == 'devices_title_cost_of_repair'){
                        if(is_numeric($Value))
                        $this->Baza->Query("UPDATE devices 
                            SET devices_cost_of_repair = '$Value'
                            WHERE devices_title_devices_title_id = '$ID'");
                    }
                }
            }
            return $Wartosci;
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
            if(isset($_GET['usun_plik']) && $_GET['usun_plik'] == 1 && !isset($_GET['kategoria_czesci'])){
                $Plik = $this->Baza->GetValue("SELECT devices_title_file_name FROM $this->TabelaPlikow WHERE {$this->TabelaPlikowPrefix}devices_title_id = '$ID' AND {$this->TabelaPlikowPrefix}hash = '$Hash'");
                if($Plik == false){
                    Usefull::ShowKomunikatError("Załącznik nie istnieje!", true);
                    return false;
                }else{
                    $this->KatalogDanych =  SCIEZKA_DANYCH_MODULOW.'urzadzenia/'.$ID.'/';
                    if(unlink($this->KatalogDanych.$Plik)){
                        if($this->Baza->Query("DELETE FROM $this->TabelaPlikow WHERE {$this->TabelaPlikowPrefix}devices_title_id = '$ID' AND {$this->TabelaPlikowPrefix}hash = '$Hash'")){
                            Usefull::ShowKomunikatOk("Załącznik <i>$Plik</i> został usunięty", true);
                            $this->ShowOK();
                            return true;
                        }
                        return false;
                    }else{
                        Usefull::ShowKomunikatError("Wystąpił błąd! Załącznik nie został usunięty!", true);
                        return false;
                    }
                }
            }elseif(isset($_GET['usun_plik']) && $_GET['usun_plik'] == 1 && isset($_GET['kategoria_czesci']) && is_numeric ($_GET['kategoria_czesci']) ){
                $Plik = $this->Baza->GetValue("SELECT arrangement_of_parts_files_name FROM arrangement_of_parts_files WHERE arrangement_of_parts_files_devices_title_id = '$ID' AND arrangement_of_parts_files_hash = '$Hash'");
                if($Plik == false){
                    Usefull::ShowKomunikatError("Załącznik nie istnieje!", true);
                    return false;
                }else{
                    $this->KatalogDanych =  SCIEZKA_DANYCH_MODULOW.'kategorie_czesci/'.$_GET['kategoria_czesci'].'/';
                    if(unlink($this->KatalogDanych.$Plik)){
                        if($this->Baza->Query("DELETE FROM arrangement_of_parts_files WHERE arrangement_of_parts_files_hash = '$Hash'")){
                            Usefull::ShowKomunikatOk("Załącznik <i>$Plik</i> został usunięty", true);
                            $this->ShowOK();
                            return true;
                        }
                        return false;
                    }else{
                        Usefull::ShowKomunikatError("Wystąpił błąd! Załącznik nie został usunięty!", true);
                        return false;
                    }
                }
            }
	}
 
        function BlockElements(){
            $devices_id = $this->Baza->GetValues("SELECT DISTINCT(devices_title_devices_title_id) FROM devices");
            foreach($devices_id as $blocked_id){
                if(!in_array($blocked_id , $this->ZablokowaneElementyIDs['kasowanie']))
                    $this->ZablokowaneElementyIDs['kasowanie'][] = $blocked_id;
            }
        }
        
        function AkcjaEdycja($ID) {
            parent::AkcjaEdycja($ID);
        }


	function ZapiszPlikKategorii($Prefix, $NazwaPola, $ID, $Wartosci){
                $this->TabelaPlikow = 'arrangement_of_parts_files';
                $this->TabelaPlikowPrefix =$this->TabelaPlikow.'_';
                $this->PolePliku = $this->TabelaPlikowPrefix.'name';
                $this->PolePlikuOryginalnego = $this->TabelaPlikowPrefix.'orginal_name';
                $this->PoleZdjecia = $this->TabelaPlikowPrefix.'list';
                $this->PolePlikuID = $this->TabelaPlikowPrefix.'id';
                $this->PolePlikuHASH = $this->TabelaPlikowPrefix.'hash';
                $PoleIDKategoriiCzesci = $this->TabelaPlikowPrefix.'parts_category';
            foreach ($_FILES[$NazwaPola]['name'] as $i=>$File){
                $this->KatalogDanych =  SCIEZKA_DANYCH_MODULOW.'kategorie_czesci/'.$Wartosci['arrangement_of_parts'][$i]['KatID'].'/';
                if($this->PrzeslijObrazekArray($NazwaPola,$i,$Prefix)){
			$NazwaPliku = mysql_real_escape_string($_FILES[$NazwaPola]['name'][$i]);
                        $size = $_FILES[$NazwaPola]['size'][$i];
                        $type = $_FILES[$NazwaPola]['type'][$i];
			$this->Baza->Query("INSERT INTO $this->TabelaPlikow
                        SET 
                            $this->TabelaPlikowPrefix$this->PoleID = '$ID',
                            $this->PolePliku = '$this->NazwaPrzeslanegoPliku',
                            $this->PolePlikuOryginalnego ='$NazwaPliku',
                            {$this->TabelaPlikowPrefix}size = '$size',
                            {$this->TabelaPlikowPrefix}content_type = '$type',
                            {$this->TabelaPlikowPrefix}created_at = NOW(),
                            {$this->PolePlikuHASH} = sha1(concat('$ID$size$NazwaPliku$type{$Wartosci['arrangement_of_parts'][$i]['KatID']}')),
                            {$PoleIDKategoriiCzesci} = '{$Wartosci['arrangement_of_parts'][$i]['KatID']}'
                        ");
		}else{
			Usefull::ShowKomunikatError('<b>Wystąpił problem. Plik nie został zapisany na serwerze.</b>');
		}
            }
	}

        function PobierzDodatkoweDane($Typ, $Pole, $Dane){
            switch ($Pole){
                case 'arrangement_of_parts':
                    $prefix = 'arrangement_of_parts_files_';
                     $war = $this->Baza->GetRows("
                        SELECT
                            {$prefix}id, {$prefix}name, {$prefix}hash, {$prefix}orginal_name, {$prefix}parts_category
                        FROM 
                            arrangement_of_parts_files
                        WHERE
                            {$prefix}{$this->PoleID} = {$Dane[$this->PoleID]}
                        ");
                        if($war!= FALSE)    
                        foreach($war as $wKey=>$wVal){
                            $Dane['arrangement_of_parts'][$wVal['arrangement_of_parts_files_parts_category']] = $wVal;
                        }
                    break;
                default :
                    break;
            }
            
            return $Dane;
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
            $Dodawanie = array('link' => "?modul=$this->Parametr&akcja=dodawanie", 'class' => 'dodawanie', 'img' => 'add.gif', 'desc' => 'Dodaj nazwę');
            $Lista = array('link' => "?modul=$this->Parametr&akcja=lista", 'class' => 'lista', 'img' => 'list.gif', 'desc' => 'Lista nazw');
            if(!in_array($this->Parametr, $this->ModulyBezDodawania)){
                $Akcje['akcja'][] = $Dodawanie;
            }
            $Akcje['akcja'][] = $Lista;
            return $Akcje;
        }        
        
        
}

?>
