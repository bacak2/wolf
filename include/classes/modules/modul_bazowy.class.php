<?php
/**
 * Moduł bazowy
 * 
 * @author		Lukasz Piekosz <mentat@mentat.net.pl>
 * @copyright	Copyright (c) 2004-2007 ARTplus
 * @package		PanelPlus
 * @version		1.0
 */

/**
 * Moduł bazowy
 *
 */
abstract class ModulBazowy {

        /**
         * Referencja do obiektu bazy
         * @var DBMysql
         */
	protected $Baza;
        /**
         * parametr modułu używany w $_GET
         * @var string
         */
	protected $Parametr;
        /**
         * Referencja do obiektu użytkownika
         * @var Uzytkownik
         */
	protected $Uzytkownik;
        /**
         * Scieżka do modułu
         * @var array
         */
	protected $Sciezka;

        /**
         * Tabela użyta w module
         * @var string
         */
	protected $Tabela;

        /**
         * Pole ID
         * @var string
         */
	protected $PoleID;

        /**
         * Pole nazwy, używane w modułach
         * @var string
         */
	protected $PoleNazwy;

        /**
         * Pole po którym następuje domyślne sortowanie, jeżeli nie zmienione to sortuje po $this->PoleNazwy
         * @var string
         */
	protected $PoleSort;
        /**
         * Domyślny kierunek sortowania, jeżeli nie zmieniony to sortuje ASC
         * @var string
         */
	protected $SortHow;

        /**
         * Nazwa tabeli dodatkowej na obrazki używne w module
         * @var string
         */
	protected $TabelaZdjecia;

        /**
         * Nazwa pola obrazka w dodatkowej tabli
         * @var string
         */
	protected $PoleZdjecia;

        /**
         * Nazwa pola definiującego widoczność elementu
         * @var string
         */
	protected $PoleVisible;

        protected $PoleVisibleValue = 1;
        
        /**
         * Nazwa dodatkowej tableli zawierającej opisy, nazwy itp. (najczęściej używana gdy strona jest w kilku językach
         * @var string
         */
	protected $TabelaDescription;

        /**
         * Nazwa modułu wyświetlana w panelu
         * @var string
         */
	protected $Nazwa;

        /**
         * Nazwa elementu
         * @var string
         */
	protected $NazwaElementu;

        /**
         * Ścieżka do katalogu zawierającego pliku dla modułu
         * @var string
         */
	protected $KatalogDanych;

        /**
         * Wykonywana akcja
         * @var string
         */
	protected $WykonywanaAkcja;

        /**
         * Link powrotu
         * @var string
         */
	protected $LinkPowrotu;

        /**
         * Link podstawowy stosowany przy paginacji do którego dokładany jest pagin = x
         * @var <type>
         */
        protected $LinkPodstawowy;

        /**
         * Filtry do listy elementów
         * @var array
         */
	protected $Filtry;

        /**
         * domyślne filtry do listy elementów
         * @var array
         */
	protected $DefaultFilters;
        
        
        /**
         * Jeśli trzeba zmień Parametr (moduł)
         * @var array
         */
	protected $ParametrFiltry = null;
        
        /**
         * Ilość stron wyświetlanych w paginacji
         * @var integer
         */
	protected $IleStronPaginacji;

        /**
         * Która strona paginacji
         * @var integer
         */
	protected $ParametrPaginacji;

        /**
         * Ilość elementów na stronę
         * @var integer
         */
	protected $IloscNaStrone;

        /**
         * Tablica z modułami bez wyszukiwarki
         * @var array
         */
	protected $ModulyBezWyszukiwarki = array();

        /**
         * Tablica zawierająca moduły bez panelu bocznego
         * @var array
         */
	protected $ModulyBezMenuBocznego = array();

        /**
         * Tablica zawierająca moduły bez opcji duplikacji elementu
         * @var array
         *
         */
	protected $ModulyBezDuplikacji = array();

        /**
         * Tablica zawierająca moduły bez opcji dodawania elementu
         * @var array
         */
	protected $ModulyBezDodawania = array();

        /**
         * Tablica zawierająca moduły bez opcji edycji elementu
         * @var array
         */
	protected $ModulyBezEdycji = array();
        
        /**
         * Tablica zawierająca moduły bez opcji usuwania
         * @var array
         */
	protected $ModulyBezKasowanie = array();

        /**
         * Tablica zawierająca elementy listy które nie mają domyślnie sorotowania w kolumnie
         * @var array
         */
        protected $NoSort = array();

        /**
         * Tablica zawierająca wymagane pola, które nie zostały wypełnione
         * @var array
         */
	protected $PolaWymaganeNiewypelnione = array();

        /**
         * Tablica zawierająca pola nie mogące się dublować, które zostały zdublowane
         * @var array
         */
	protected $PolaZdublowane = array();

        /**
         * ID elementu
         * @var integer
         */
	protected $ID = null;

        /**
         * Domyślny język
         * @var integer
         */
	protected $DefaultLanguageID = 1;

        /**
         * Przechowuje dzisiejszą datę (Y-m-d)
         * @var date
         */
	protected $Dzis;

        /**
         * Tablica z dostępnymi modułami
         * @var array
         */
	protected $Uprawnienia;

        /**
         * Definiuje czy opcje na liście muszą być generowane oddzielnie dla każdego elementu (np. jeżeli opcja zależy od jakiejś wartości w bazie)
         * @var boolean
         */
	protected $CzySaOpcjeWarunkowe = false;

        /**
         * Nazwa przesłanego pliku
         * @var string
         */
	protected $NazwaPrzeslanegoPliku;

        /**
         * Nazwa przesłanego pliku
         * @var string
         */
        protected $TypPrzeslanegoPliku;
        
        
        /**
         * PrefixTabeli Plików
         * @var string
         */
        protected $TabelaPlikowPrefix = '';

        /**
         * Pole z nazwą oryginalną pliku 
         * @var string
         */

        public $PolePlikuOryginalnego;
        
 
        /**
         * Czy pokazać PolaDwa
         * @var bool
         */
       
        public $bShowPolaDwa = true;
        
        /**
         * lista dołączanych templatek do show
         * @var mixed
         */
        
        public $include_tpls;
        /**
         * dane dołączone do inkludowanych tpl
         * @var mixed compact
         */
        
        public $include_tpls_data;
        
        /**
         * Tablica zawierająca ID elementów blokowaych dla każdej akcji
         * defultowa akcje są puste, jeśli stworzysz jakąś niestandardową akcje w klasie
         * musisz w jej kontruktorze dołożyć do tablicy o kluczu niestandardowej akcji pustą tabelę
         * @var array
         */
	protected $ZablokowaneElementyIDs = array(
			'lista' => array(),
			'szczegoly' => array(),
			'dodawanie' => array(),
			'duplikacja' => array(),
			'edycja' => array(),
			'kasowanie' => array(),
			'add_photo' => array(),
			'tinymce_list' => array(),
			'blokowanie' => array(),
			'akcja' => array(),
        );

        /**
         * Przyciski w formularzu
         * @var array
         */
	protected $PrzyciskiFormularza = array( "zapisz" => array('etykieta' => 'Zapisz', 'src' => 'disk.gif', 'type' => 'button'),
                                                "anuluj" => array('etykieta' => 'Anuluj', 'src' => 'cancel.gif'));

        /**
         * Zmienna zawierająca błąd przy przesyłaniu formularza
         * @var string
         */
        protected $Error = false;


        /**
         * Tablica z błędami wyświetlamymi na stronie
         * dostępne keys:
         * [unikalne][nazwa_pola] -> obsługa błędu gdy probujemy wprowadzic w polu nazwa pola wartość która już jest w bazie
         * jeżeli pole nie jest wprowadzone do tej tabeli to wyświetlają się domyślne błędy.
         * @var array
         */
        protected $ErrorsDescriptions = array();
        /**
         * ID użytkownika
         * @var integer or false
         */
        protected $UserID = false;

        /**
         * Session_id
         */
        protected $SID;

        /**
         * Tablica zawierająca id elementów wyświetlanech na aktualnej podstronie listy
         * @var array
         */
        protected $ListIDs = array();

        
        
        /**
         * Tablica zawierająca elementy których nazwa ma zostać skrócona na liście
         * konstrukcja string => array ( IsTitle => true/folse, lenght => integer)
         * @var array
         */
        protected $ShortNameList = array();
                
 
        /**
         * zmienna bool ustalająca czy ma być liczba porządkowa na liście pokazywana default false
         * @var bool
         */
        protected $lp = false;

        /**
         * definiuje czy po kliknięciu wwiersz w danym module wchodzimy w szczegóły
         * @var bool
         */
        protected $clickable_rows = true;


        protected $FiltersAndActionsWidth = true;
        
        protected $Komunikat = false;
        
        protected $RedirectLocation = true;
        
        /**
         * konstruktor
         * @param DBMysql $Baza
         * @param Uzytkownik $Uzytkownik
         * @param string $Parametr
         * @param array $Sciezka
         */
	function __construct(&$Baza, &$Uzytkownik, $Parametr, $Sciezka) {
		$this->Baza = $Baza;
		$this->Uzytkownik = $Uzytkownik;
		$this->Parametr = $Parametr;
                $this->FiltersAndActionsWidth = ( isset($_GET['akcja']) && in_array($_GET['akcja'], array('szczegoly', 'akcja')) ? true : false );
                $this->ParametrFiltry = $Parametr;
		$this->Sciezka = $Sciezka;
		$this->KatalogDanych = SCIEZKA_DANYCH_MODULOW.$this->Parametr.'/';
		$this->Filtry = array();
                $this->DefaultFilters = array();
		$this->ParametrPaginacji = isset($_GET['pagin']) ? $_GET['pagin'] : (isset($_SESSION['sort'][$this->Parametr]['pagin']) ? $_SESSION['sort'][$this->Parametr]['pagin'] : 0);
		$this->IloscNaStrone = 30;
		$this->LinkPowrotu = "?modul=$this->Parametr".Usefull::AddParamsToLink($this->ParametrPaginacji);
                if(isset($_GET['id']) && is_numeric($_GET['id'])){
                    $this->ID = $_GET['id'];
                }
                if(isset($_GET['akcja']) && $_GET['akcja'] == "edycja" && isset($_GET['back']) && $_GET['back'] == "details"){
                    $this->LinkPowrotu = "?modul=$this->Parametr&akcja=szczegoly&id={$this->ID}";
                    $this->PrzyciskiFormularza['anuluj']['link'] = $this->LinkPowrotu;
                }
		$this->PoleSort = null;
		$this->SortHow = "ASC";
		$this->Dzis = date("Y-m-d");
                if(!$this->UserID){
                    $this->UserID = $this->Uzytkownik->ZwrocIdUzytkownika();
                }
                if(isset($_SESSION['sort']))
                    foreach($_SESSION['sort'] as $Par => $Value){
                            if($Par != $this->Parametr){
                                    unset($_SESSION['sort'][$Par]);
                            }
                    }
		if(isset($_GET['sort'])){
			$_SESSION['sort'][$this->Parametr]['sort'] = $_GET['sort'];
		}
		if(isset($_GET['sort_how'])){
			$_SESSION['sort'][$this->Parametr]['sort_how'] = $_GET['sort_how'];
		}
		if(isset($_GET['pagin'])){
			$_SESSION['sort'][$this->Parametr]['pagin'] = $_GET['pagin'];
		}
                if(isset($_POST['filtr_0'])){
                    unset($_SESSION['sort'][$this->Parametr]);
                }
                $SID = session_id();
                $this->LinkPodstawowy = '';
                foreach($_GET as $key => $value){
                    if($key != 'pagin'){
                        $this->LinkPodstawowy .= ($this->LinkPodstawowy == "" ? "?" : "&").$key."=".$value;
                    }
                }
	}

        /**
         * Ustawia tablicę uprawnień
         * @param array $TablicaUprawnien
         */
	function UstalUprawnienia($TablicaUprawnien){
		$this->Uprawnienia = $TablicaUprawnien;
	}

        /**
         * Generuje formularz - dodaje pola do formularza
         * @param array $Wartosci
         * @param boolean $Mapuj
         * @return Formularz
         */
	function &GenerujFormularz(&$Wartosci = array(), $Mapuj = false) {
            $Formularz = new Formularz($_SERVER['REQUEST_URI'], $this->LinkPowrotu, $this->Parametr);
            $Formularz->SetToolTipOn($this->Uzytkownik->ZwrocInfo('tooltip') || $this->GetGlobalConfig('tooltip'));
            return $Formularz;
	}

        /**
         * Pobiera nazwę aktualnie wyświetlanego elementu
         * @param integer $ID
         * @return string
         */
	function PobierzNazweElementu($ID) {
		$this->NazwaElementu = stripslashes($this->Baza->GetValue("SELECT $this->PoleNazwy FROM $this->Tabela WHERE $this->PoleID = '$ID'"));
		return $this->NazwaElementu;
	}

        /**
         * Funkcja pobiera nazwę elementu do ścieżki jeżeli nie ma Id
         * @return string
         */
        function PobierzNazweDoSciezki(){
            return "";
        }

        /**
         * Pobiera dane domyślne do formularza
         * @return array
         */
	function &PobierzDaneDomyslne() {
		$Dane = array();
		return $Dane;
	}

        /**
         * Pobiera dane elementu do formularza
         * @param integer $ID
         * @param string $Typ
         * @return array
         */
	function PobierzDaneElementu($ID, $Typ = null) {
		$this->Baza->Query("SELECT * FROM $this->Tabela WHERE $this->PoleID = '$ID' LIMIT 1");
		$Dane = $this->Baza->GetRow();
		$Formularz = $this->GenerujFormularz();
		$Pola = $Formularz->ZwrocPola();
//                $this->VAR_DUMP($this);
		foreach ($Pola as $Pole) {
			$Typ = $Formularz->ZwrocTypPola($Pole, false);
			switch ($Typ) {
				case 'obraz':
					$Opcje = $Formularz->ZwrocOpcjePola($Pole, null, false);
					if($Dane[$Pole] != "" && !is_null($Dane[$Pole])){
						$Dane[$Pole] = $this->KatalogDanych.$Dane[$Pole];
					}else{
						unset($Dane[$Pole]);
					}
					break;
				case 'lista_obrazow':
					if($this->WykonywanaAkcja != "duplikacja"){
                                                $this->Baza->Query("SELECT $this->PoleZdjecia FROM $this->TabelaZdjecia WHERE {$this->TabelaZdjeciaPrefix}{$this->PoleID} = '$ID'");
						$d=0;
						while($Zdjecie = $this->Baza->GetRow()){
							$Dane[$Pole][$d]["nazwapliku"] = $Zdjecie[$this->PoleZdjecia];
							$Dane[$Pole][$d]["sciezka"] = $this->KatalogDanych.$Zdjecie[$this->PoleZdjecia];
                                                        $Dane[$Pole][$d]["sciezka_min"] = $this->KatalogDanych.'120px/'.$Zdjecie[$this->PoleZdjecia];
							$ZdjecieNazwaPolacz = "";
							$ZdjecieNazwaExp = explode("_",$Zdjecie[$this->PoleZdjecia]);
							for($i=2;$i<count($ZdjecieNazwaExp);$i++){
								$ZdjecieNazwaPolacz .= ($ZdjecieNazwaPolacz == "" ? $ZdjecieNazwaExp[$i] : "_".$ZdjecieNazwaExp[$i]);
							}
							$Dane[$Pole][$d]["nazwa"] = $ZdjecieNazwaPolacz;
							$d++;
						}
					}
					break;
				case 'lista_plikow':
					if($this->WykonywanaAkcja != "duplikacja"){
						$this->Baza->Query("SELECT $this->PolePlikuHASH, $this->PolePlikuID, $this->PolePliku, $this->PolePlikuOryginalnego FROM $this->TabelaPlikow WHERE $this->TabelaPlikowPrefix$this->PoleID = '$ID'");
						$d=0;
						while($Zdjecie = $this->Baza->GetRow()){
                                                        $Dane[$Pole][$d]["nazwapliku"] = $Zdjecie[$this->PolePlikuOryginalnego];
							$Dane[$Pole][$d]["sciezka"] = $this->KatalogDanych.$Zdjecie[$this->PolePliku];
                                                        $Dane[$Pole][$d]["plik_hash"] = $Zdjecie[$this->PolePlikuHASH];
                                                        $Dane[$Pole][$d]["plik_id"] = $Zdjecie[$this->PolePlikuID];
							$ZdjecieNazwaPolacz = "";
							$ZdjecieNazwaExp = explode("_",$Zdjecie[$this->PolePliku]);
							for($i=2;$i<count($ZdjecieNazwaExp);$i++){
								$ZdjecieNazwaPolacz .= ($ZdjecieNazwaPolacz == "" ? $ZdjecieNazwaExp[$i] : "_".$ZdjecieNazwaExp[$i]);
							}
                                                        if( empty($ZdjecieNazwaPolacz) )
                                                            $ZdjecieNazwaPolacz = $Zdjecie[$this->PolePliku];
							$Dane[$Pole][$d]["nazwa"] = $ZdjecieNazwaPolacz;
							$d++;
						}
					}
					break;
				case 'podzbiór':
				case 'podzbiór_checkbox_1n':
					$Opcje = $Formularz->ZwrocOpcjePola($Pole, null, false);
					$Dane[$Pole] = $this->Baza->GetSet1n($Opcje['tabela'], $Opcje['pole_where'], $Opcje['pole'], $ID);
					break;

				case 'podzbiór_checkbox_nn':
					$Opcje = $Formularz->ZwrocOpcjePola($Pole, null, false);
					$Dane[$Pole] = $this->Baza->GetSetnn($Opcje['tabela_wartosci'], $Opcje['pole_id_grupy'], $Opcje['pole_wartosci'], $Dane[$Pole]);
					break;
                                case 'lista_inny':
                                case 'checkbox_tekst_dlugi':
                                    $Exp = explode("-$-", $Dane[$Pole]);
                                    unset($Dane[$Pole]);
                                    $Dane[$Pole]['check'] = $Exp[0];
                                    $Dane[$Pole]['value'] = $Exp[1];
                                    break;
                                default:
                                    $Dane = $this->PobierzDodatkoweDane($Typ, $Pole, $Dane);
                                    break;
			}
		}
		return $Dane;
	}

        /**
         * Pobiera dane dodatkowe - jeżeli typ pola nie jest standardowy
         * @param string $Typ
         * @param string $Pole
         * @param array $Dane
         * @return array
         */
        function PobierzDodatkoweDane($Typ, $Pole, $Dane){
            return $Dane;
        }

        /**
         * Generuje warunek WHERE w zapytaniu do listy elementów
         * @param string $AliasTabeli
         * @return string
         */
	function GenerujWarunki($AliasTabeli = null) {
		$Where = $this->DomyslnyWarunek();
//$this->VAR_DUMP($_SESSION['Filtry']);
		if (isset($_SESSION['Filtry']) && count($_SESSION['Filtry'])) {
			for ($i = 0; $i < count($this->Filtry); $i++) {
				$Pole = (isset($this->Filtry[$i]['nazwa']) ? $this->Filtry[$i]['nazwa'] : 'brak');
				if (isset($_SESSION['Filtry'][$Pole])) {
                                    $Wartosc = $_SESSION['Filtry'][$Pole];
                                
                                    $ExpPole = explode("|", $Pole);
                                    $WhereArray = "";
                                    foreach($ExpPole as $NewPole){
                                        if($this->Filtry[$i]['typ'] == "lista" || $this->Filtry[$i]['typ'] == "checkbox"){
                                            if(is_array($Wartosc)){
                                                $WhereArraySecond = "";
                                                foreach($Wartosc as $Value){
                                                    $WhereArraySecond .= ($WhereArraySecond != '' ? ' OR ' : '').($AliasTabeli ? "$AliasTabeli." : '')."$NewPole = '$Value'";
                                                }
                                                if($WhereArraySecond != ""){
                                                    $WhereArray .= ($WhereArray != '' ? ' OR ' : '').($AliasTabeli ? "$AliasTabeli." : '')."($WhereArraySecond)";
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
                                        }else{
                                            $WhereArray .= ($WhereArray != '' ? ' OR ' : '').($AliasTabeli ? "$AliasTabeli." : '')."$NewPole REGEXP '".preg_replace( '/([*?])/', '.\1', $Wartosc)."'";
                                        }
                                    }
                                    $Where .= ($Where != '' ? ' AND ' : '')."($WhereArray)";
				}
			}
		}
                return ($Where != '' ? "WHERE $Where" : '');
	}

        /**
         * Ustawia domyślny warunek w zapytaniu do listy elementów
         * @return string
         */
	function DomyslnyWarunek(){
		return "";
	}

        /**
         * Generuje sortowanie w zapytaniu do listy elementów
         * @param string $AliasTabeli
         * @return string
         */
	function GenerujSortowanie($AliasTabeli = null) {
		if(is_null($this->PoleSort)){
			$this->PoleSort = $this->PoleNazwy;
		}
		$Sort = ($AliasTabeli ? "$AliasTabeli." : '').(isset($_GET['sort']) ? $_GET['sort'] : (isset($_SESSION['sort'][$this->Parametr]['sort']) ? $_SESSION['sort'][$this->Parametr]['sort'] : $this->PoleSort));
		$Jak = (isset($_GET['sort_how'])) ? $_GET['sort_how'] : (isset($_SESSION['sort'][$this->Parametr]['sort_how']) ? $_SESSION['sort'][$this->Parametr]['sort_how'] : $this->SortHow);
		return ($Sort != '' ? "ORDER BY $Sort $Jak" : '');
	}

        /**
         * Pobiera listę elementów na liście
         * @param array $Filtry
         * @return array
         */
	function PobierzListeElementow($Filtry = array()) {
		return array();
	}
        
        /**
         * Zapisuje formularz do tabeli
         * @param Formularz $Formularz
         * @param array $Wartosci
         * @param array $PrzeslanePliki
         * @param integer $ID
         * @return boolean 
         */
	function ZapiszDaneElementu($Formularz, $Wartosci, &$PrzeslanePliki = null, $ID = null) {
		$PoInsercie = array();
		$Pola = $Formularz->ZwrocPola();
		foreach ($Pola as $Pole) {
			$Typ = $Formularz->ZwrocTypPola($Pole, false);
			switch ($Typ) {
				case 'podzbiór':
				case 'podzbiór_checkbox_1n':
					if ($ID) {
						$Opcje = $Formularz->ZwrocOpcjePola($Pole, null, false);
                                                if(isset($Opcje['tabela'])){
                                                    $this->Baza->SaveSet1n($Opcje['tabela'], $Opcje['pole_where'], $Opcje['pole'], $ID, $Wartosci[$Pole]);
                                                }
					}
					else {
						$PoInsercie[] = array('pole' => $Pole, 'typ' => $Typ, 'wartosci' => $Wartosci[$Pole]);
					}
					unset($Wartosci[$Pole]);
					break;		
				case 'podzbiór_checkbox_nn':
					$Opcje = $Formularz->ZwrocOpcjePola($Pole, null, false);
					$Wartosci[$Pole] = $this->Baza->SaveSetnn($Opcje['tabela_grup'], $Opcje['tabela_wartosci'], $Opcje['pole_nazwy_grupy'], $Opcje['pole_id_grupy'], $Opcje['pole_wartosci'], $Wartosci[$Pole]);
					break;
				case 'tekst_link':
					$Wartosci[$Pole] = str_replace(array(' ','ó','ń','ł','ś','ź','ż','ą','ę','ć'),array('_','o','n','l','s','z','z','a','e','c'),$Wartosci[$Pole]);
					break;
				case 'checkbox':
					$Wartosci[$Pole] = (isset($Wartosci[$Pole]) ? 1 : 0);
					break;
                                case 'checkbox_tekst_dlugi':
                                        $Value = (isset($Wartosci[$Pole]['check']) ? 1 : 0);
                                        if($Value == 1){
                                            $Value .= "-$-".$Wartosci[$Pole]['value'];
                                        }
                                        $Wartosci[$Pole] = $Value;
                                        break;
                                case 'lista_inny':
                                        $Value = $Wartosci[$Pole]['check'];
                                        if($Value == -1){
                                            $Value .= "-$-".$Wartosci[$Pole]['value'];
                                        }
                                        $Wartosci[$Pole] = $Value;
                                    break;
                                default:
                                    $Wartosci = $this->ZapiszDaneSzczegolowe($Wartosci, $Typ, $Pole);
                                    break;
			}
			if($Formularz->ZwrocCzyDecimal($Pole, false) && isset($Wartosci[$Pole])){
				$Wartosci[$Pole] = str_replace(',','.',$Wartosci[$Pole]);
			}
		}
                $Wartosci = $this->OperacjePrzedZapisem($Wartosci, $ID);
		if ($ID) {
			$Zapytanie = $this->Baza->PrepareUpdate($this->Tabela, $Wartosci, array($this->PoleID => $ID));
		}
		else {
			$Zapytanie = $this->Baza->PrepareInsert($this->Tabela, $Wartosci);
		}
		if ($this->Baza->Query($Zapytanie)) {
			if (!$ID) {
				$ID = $this->Baza->GetLastInsertID();
			}
			$this->ID = $ID;
			foreach ($PoInsercie as $Dane) {
				switch ($Dane['typ']) {
					case 'podzbiór':
					case 'podzbiór_checkbox_1n':
						$Opcje = $Formularz->ZwrocOpcjePola($Dane['pole'], null, false);
                                                if(isset($Opcje['tabela'])){
                                                    $this->Baza->SaveSet1n($Opcje['tabela'], $Opcje['pole_where'], $Opcje['pole'], $ID, $Dane['wartosci']);
                                                }
						break;
				}
			}
			if (count($PrzeslanePliki)) {
				foreach ($PrzeslanePliki as $Pole => $PrzeslanyPlik) {
					if (is_uploaded_file($PrzeslanyPlik['tmp_name'])) {
						$Plik = $this->KatalogDanych.$PrzeslanyPlik['name'];
						$Sciezka = dirname($Plik);
						$StaryUmask = umask(0);
						if (!file_exists($Sciezka)) {
							mkdir($Sciezka, 0777, true);
						}
						if (move_uploaded_file($PrzeslanyPlik['tmp_name'], $Plik)) {
							chmod($Plik, 0777);
							$Opcje = $Formularz->ZwrocOpcjePola($Pole,null,false);
							if(isset($Opcje['rozmiar_x']) || isset($Opcje['rozmiar_y'])){
								$this->ResizeImage($Opcje['rozmiar_x'],$Opcje['rozmiar_y'],$Plik);
							}
							$PrzeslanePlikiBaza[$Pole] = $PrzeslanyPlik['name'];
						}else{
                                                    var_dump("BLAD MOVE UPLOADED FILE");
                                                }
						umask($StaryUmask);
					}else{
                                            var_dump("BLAD IS UPLOADED FILE");
                                        }
				}
				if ($ID && count($PrzeslanePlikiBaza) > 0) {
					$Zapytanie = $this->Baza->PrepareUpdate($this->Tabela, $PrzeslanePlikiBaza, array($this->PoleID => $ID));
					$this->Baza->Query($Zapytanie);
				}
			}
                        $this->WykonajOperacjePoZapisie($Wartosci, $ID);
			return true;
		}
		else {
			return false;
		}
	}

        /**
         * Wykonuje operację po operacji zapisu
         * @param array $Wartosci
         * @param integer $ID
         */
        function WykonajOperacjePoZapisie($Wartosci, $ID){
            
        }

        /**
         * Wykonuje operację na wartości do zapisania w bazie, jeżeli pole nie jest standardowe
         * @param array $Wartosci
         * @param string $Typ
         * @param string $Pole
         * @return array
         */
        function ZapiszDaneSzczegolowe($Wartosci, $Typ, $Pole){
            return $Wartosci;
        }

        /**
         * Wykonuje operacje na wartościach przed zapisem do bazy
         * @param array $Wartosci
         * @param integer $ID
         * @return array
         */
        function OperacjePrzedZapisem($Wartosci, $ID){
            return $Wartosci;
        }

        /**
         * Usuwa element z bazy
         * @param integer $ID
         * @return boolean
         */
	function UsunElement($ID) {
		return $this->Baza->Query("DELETE FROM $this->Tabela WHERE $this->PoleID = '$ID'");
	}

        /**
         * Blokuje/odblokowuje element z bazy (wyłącza i włącza jego widoczność
         * @param integer $ID
         * @return boolean
         */
	function Blokuj($ID){
		if($this->Baza->Query("UPDATE $this->Tabela SET $this->PoleVisible = 1-$this->PoleVisible WHERE $this->PoleID = '$ID'")){
			return true;
		}
		return false;
	}

	/**
         * Sprawdza czy element jest widoczny czy nie
         * @param integer $ID
         * @return boolean
         */
	function CzyPokazywane($ID){
		return $this->Baza->GetValue("SELECT $this->PoleVisible FROM $this->Tabela WHERE $this->PoleID = '$ID'");
	}	

        /**
         * Pobiera ID poprzedniego elementu do nawigacji
         * @param integer $ID
         * @return integer or false
         */
	function IDPoprzedniego($ID) {
		return $this->Baza->GetValue("SELECT $this->PoleID FROM $this->Tabela WHERE $this->PoleNazwy < (SELECT $this->PoleNazwy FROM $this->Tabela WHERE $this->PoleID = '$ID' LIMIT 1) ORDER BY $this->PoleNazwy DESC LIMIT 1");
	}

        /**
         * Pobiera ID następnego elementu do nawigacji
         * @param integer $ID
         * @return integer or false
         */
	function IDNastepnego($ID) {
		return $this->Baza->GetValue("SELECT $this->PoleID FROM $this->Tabela WHERE $this->PoleNazwy > (SELECT $this->PoleNazwy FROM $this->Tabela WHERE $this->PoleID = '$ID' LIMIT 1) ORDER BY $this->PoleNazwy ASC LIMIT 1");
	}

        /**
         * Mapuje nazwę wykonywanej akcji - na podstawie $_GET['akcja'] wyświetla odpowiedni tekst w nagłówku
         * @return string
         */
	function MapujNazweAkcji(){
		$WyswietlanaAkcja = $this->WykonywanaAkcja;
		switch($WyswietlanaAkcja){
			case 'szczegoly': $WyswietlanaAkcja = "pokaż"; break;
			case 'zmiana_hasla': $WyswietlanaAkcja = "Zmiana hasła"; break;
                        case 'dodaj_element': $WyswietlanaAkcja = "Dodaj element"; break;
		}
		return $WyswietlanaAkcja;
	}

	function WyswietlPanel($ID = null) {

	}

	function WyswietlAkcje($ID = null) {
		$this->WykonywaneAkcje($ID);
	}

	function WykonywaneAkcje($ID){
		switch ($this->WykonywanaAkcja) {
			case 'lista':
				$this->AkcjaLista();
				break;
			case 'szczegoly':
                            if(!in_array($ID, $this->ZablokowaneElementyIDs['szczegoly'])){
				$this->AkcjaSzczegoly($ID);
                            }else{
                                $this->AkcjaLista();
                            }
				break;
			case 'dodawanie':
				if(!in_array($this->Parametr, $this->ModulyBezDodawania)){
					$this->AkcjaDodawanie();
				}else{
					$this->AkcjaLista();
				}
				break;
			case 'duplikacja':
				if(!in_array($this->Parametr, $this->ModulyBezDuplikacji) && !in_array($ID, $this->ZablokowaneElementyIDs['duplikacja'])){
					$this->AkcjaDuplikacja($ID);
				}else{
					$this->AkcjaLista();
				}
				break;
			case 'edycja':
				if(!in_array($this->Parametr, $this->ModulyBezEdycji) && !in_array($ID, $this->ZablokowaneElementyIDs['edycja'])){
                                    $this->AkcjaEdycja($ID);
				}else{
                                    $this->AkcjaLista();
                                }
				break;
			case 'kasowanie':
				if(!in_array($this->Parametr,$this->ModulyBezKasowanie) && $this->MozeBycOperacja($ID) && !in_array($ID, $this->ZablokowaneElementyIDs['kasowanie'])){
					$this->AkcjaKasowanie($ID);
				}else{
					$this->AkcjaLista();
				}
				break;
			case 'add_photo':
				$ModulPopup = new ModulBazowyDodawanieZdjecPopup($this->Baza, $this->Parametr);
				$ModulPopup->DodajZdjeciaDoElementu($_POST);
				break;
			case 'tinymce_list':
				$this->GetTinyMCEList($ID);
				break;
			case 'blokowanie':
				$this->Blokowanie($ID);
				break;
                        case 'pobierz':
                                $this->AkcjaPobierz($ID);
                                break;
            case 'import': $this->Import(); break;
            case 'importFile': $this->ImportFile(); break;
			default:
				$this->AkcjeNiestandardowe($ID);
				break;
		}		
	}
	
	function AkcjeNiestandardowe($ID){
		//$this->AkcjaLista();
                $this->AkcjaAkcje($ID);
	}
	
	function ShowFiltersAndActions($ID){
                $Akcje = $this->GetActions($ID);
                if(isset($Akcje[$this->WykonywanaAkcja]) && count($Akcje[$this->WykonywanaAkcja])){
                    echo "<div class='filters-and-actions'>";
                    echo "<div style='float: left; width: 100%'>";
                        $this->ShowActions($ID, $Akcje);
                    echo "</div>\n";
                    echo "</div>\n";
                    echo "<div style='clear: both'></div>\n";
                }
                if($this->WykonywanaAkcja == "lista" && count($this->Filtry)){
                    echo "<div class='filters-and-actions".($this->Parametr == "zlecenia" && $_GET['akcja'] == "dodawanie" ? " no-padding" : "")."'>";                    
                    echo "<div style='float: right; width: 100%;'>";
                        $this->ShowFilters();
                    echo "</div>";
                    echo "</div>\n";
                    echo "<div style='clear: both'></div>\n";
                }
           

	}

        function ShowActions($ID, $Akcje){
            $Brk = false;
            if(!in_array($this->WykonywanaAkcja, array("akcja", "lista", "dodawanie", "edycja", "kasowanie"))){
                ?><a href="<?php echo $this->LinkPowrotu; ?>" style='margin-right: 15px; background-color: #D1EAFF;'><img src="images/arrow_undo.gif" alt="Powrót" class="inlineimg" /> Powrót</a><?php
                $Brk = true;
            }
            if(isset($Akcje[$this->WykonywanaAkcja])){
                foreach($Akcje[$this->WykonywanaAkcja] as $Idx => $Action){
                    ?>
                    <?php
                    $Action['title'] = (isset($Action['title'])) ? $Action['title'] : '';
                    echo ($Brk && !$this->FiltersAndActionsWidth ? "<br/>" : ""); ?>
                        <?php if(isset($Action['akcja_link']) && !empty($Action['akcja_link'])){ 
                            echo "<a href=\"{$Action['akcja_link']}\"".(isset($Action['target']) && $Action['target'] ? " target='_blank'" : "")."><img src=\"images/{$Action['img']}\" title=\"{$Action['title']}\" alt=\"{$Action['title']}\" class=\"inlineimg\" >{$Action['desc']}</a>";
                        } else {?>
                            <a href="<?php echo $Action['link']; ?>" <?php echo (isset($Action['class']) ? "class='{$Action['class']}'" : ""); ?>><img src="images/<?php echo $Action['img']; ?>" alt="<?php echo $Action['desc']; ?>" class="inlineimg" /> <?php echo $Action['desc']; ?></a>
                    <?php
                        }
                    $Brk = true;
                }
            }
        }

        function GetActions($ID){
            return array();
        }

        function ShowFilters(){
           include(SCIEZKA_SZABLONOW."filters.tpl.php");
        }

	function Wyswietl($Akcja) {
		$this->WykonywanaAkcja = $Akcja;
                if($this->WykonywanaAkcja == 'akcja'){
                    $this->FiltersAndActionsWidth = true;
                }elseif(in_array($this->WykonywanaAkcja, array('szczegoly', 'edycja', 'dodawanie'))){
                    $this->LinkPowrotu = $this->LinkPowrotu.'&akcja=lista';
                    $this->PrzyciskiFormularza['anuluj']['link'] = $this->LinkPowrotu;
                }
		if (isset($_GET['id']) && intval($_GET['id']) != 0) {
			$ID = intval($_GET['id']);
		}
		else {
			$ID = null;
		}
		if (isset($_POST['filters-do-it']) && $_POST['filters-do-it'] == 1) {
			$i = 0;
			while (isset($_POST['filtr_'.$i]) || isset($this->Filtry[$i])){
                                if(isset($_POST['filtr_'.$i]) && is_array($_POST['filtr_'.$i])){
                                    if($this->Filtry[$i]['typ'] == "checkbox"){
                                        $_SESSION['Filtry'][$this->Filtry[$i]['nazwa']] = array();
                                    }else{
                                        unset($_SESSION['Filtry'][$this->Filtry[$i]['nazwa']]);
                                    }
                                    foreach($_POST['filtr_'.$i] as $key => $value){
                                        if($value != ''){
                                            $_SESSION['Filtry'][$this->Filtry[$i]['nazwa']][$key] = $value;
                                        }else{
                                            unset($_SESSION['Filtry'][$this->Filtry[$i]['nazwa']][$key]);
                                        }
                                    }
                                }else if(isset($_POST['filtr_'.$i]) && $_POST['filtr_'.$i] != ''){
                                        if(isset($this->Filtry[$i]['domyslna']) && ($_POST['filtr_'.$i] != $this->Filtry[$i]['domyslna'])){
                                            $_SESSION['Filtry'][$this->Filtry[$i]['nazwa']] = $_POST['filtr_'.$i];
                                        }else{
                                            $_SESSION['Filtry'][$this->Filtry[$i]['nazwa']] = $_POST['filtr_'.$i];
                                        }
				}
				else {
                                    if( isset($this->Filtry[$i]['nazwa']))
                                        unset($_SESSION['Filtry'][$this->Filtry[$i]['nazwa']]);
				}
				$i++;
			}
		}
                ### Czyszczenie filtrów po wciśnięciu wyczyść ###
                if(isset($_POST['wyczysc']) && $_POST['wyczysc'] == "Wyczyść"){
                    $_SESSION['Filtry'] = array();
                }
                $this->SetDefaultFilters();
                if(in_array($ID, $this->ZablokowaneElementyIDs[$this->WykonywanaAkcja])){
                    $this->WykonywanaAkcja = "akcja";
                }
                $AkcjeCheck = $this->GetActions($ID);
                if((count($this->Filtry) > 0 && $this->WykonywanaAkcja == "lista") || !in_array($this->WykonywanaAkcja, array("lista", "dodawanie", "edycja", "kasowanie")) || isset($AkcjeCheck[$this->WykonywanaAkcja])){
                    $this->ShowFiltersAndActions($ID);
                }
                $TableWidth = (in_array($this->Parametr,$this->ModulyBezMenuBocznego) && $this->WykonywanaAkcja == "lista" ? "95" : "99");
                if(in_array($ID, $this->ZablokowaneElementyIDs[$this->WykonywanaAkcja]) && $this->Error != false){
                    Usefull::ShowKomunikatError($this->Error);
                }
                if( $this->Komunikat != false ){
                    $k = $this->Komunikat['type'];
                    call_user_func_array(array(Usefull, $this->Komunikat['type']), array($this->Komunikat['text'], (isset($this->Komunikat['bold']) ? $this->Komunikat['bold'] : false) ));
                }
                if($this->WykonywanaAkcja !== "akcja"){
                ?>
                <table width="<?php echo $TableWidth; ?>%" border="0" cellspacing="0" cellpadding="12" style="margin: 0 auto;">
                        <tr>
                <?php
                if(!in_array($this->Parametr,$this->ModulyBezMenuBocznego) && ($this->WykonywanaAkcja == "lista" || in_array($ID, $this->ZablokowaneElementyIDs[$this->WykonywanaAkcja]))){
                ?>
		<td id="lewemenu" width="210px" valign="top" style="border-right: 1px solid #000000;">
                    <?php $this->WyswietlPanel($ID); ?>
		</td>
            <?php } ?>
		<td valign="top" class="calosc">
                    <?php 
//                        if(!in_array($ID, $this->ZablokowaneElementyIDs[$this->WykonywanaAkcja])){
//                            echo('<div class="sciezka"'.(in_array($this->WykonywanaAkcja, array('dodawanie', 'edycja', 'dodaj_element')) ? " style='width: 65%;'" : ($this->WykonywanaAkcja == "szczegoly" ? " style='width: 90%;'" : "")).'>'.implode('&nbsp;&gt;&nbsp;', $this->Sciezka).(isset($ID) && !in_array($ID, $this->ZablokowaneElementyIDs[$this->WykonywanaAkcja]) && $this->WykonywanaAkcja != "lista" ? '&nbsp;&gt;&nbsp;<span class="nazwa_elementu">'.$this->PobierzNazweElementu($ID).'</span>' : $this->PobierzNazweDoSciezki()).'&nbsp;:&nbsp;<span class="nazwa_akcji">'.$this->MapujNazweAkcji($this->WykonywanaAkcja).'</span></div>');
//                        }
			if(isset($this->LinkMenu) && is_array($this->LinkMenu) && !empty($this->LinkMenu) && $this->WykonywanaAkcja == 'lista'){
			echo '<div class="f_right linkmenu">';
				foreach($this->LinkMenu as $sName=>$sLink ){
					echo ('<div class="f_left"><a class="linkemnu" href="'.$sLink.'">'.$sName.'</a></div>');
				}
			echo '</div>';
			}
                        ?>
                    <?php $this->WyswietlAkcje($ID); ?>
		</td>
                    </tr>
            </table>
                <?php } ?>
<?php
	}
	
	function WyswietlAJAX($Akcja) {
		$this->WykonywanaAkcja = $Akcja;
		if (isset($_GET['id']) && intval($_GET['id']) != 0) {
			$ID = intval($_GET['id']);
		}else {
			$ID = null;
		}
		$this->WykonywaneAkcje($ID);
	}
	
	function PobierzAkcjeNaLiscie($Dane = array(), $TH = false){
		$Akcje = array();
                $Akcje[] = array('img' => "information.gif", 'img_grey' => "information_grey.gif", 'title' => "Pokaż", "akcja" => "szczegoly");
                if(!in_array($this->Parametr,$this->ModulyBezEdycji)){
                    $Akcje[] = array('img' => "pencil.gif", 'img_grey' => "pencil_grey.gif", 'title' => "Edycja", "akcja" => "edycja");
                }
		if(!in_array($this->Parametr,$this->ModulyBezKasowanie)){
			$Akcje[] = array('img' => "bin_empty.gif", 'img_grey' => "bin_empty_grey.gif", 'title' => "Kasowanie", "akcja" => "kasowanie");
		}
		return $Akcje;
	}
	
	function WykonajAkcjeDodatkowa(){
		
	}

	function AkcjaLista($Filtry = array()){
		$this->WykonajAkcjeDodatkowa();
		$Pola = $this->PobierzListeElementow($Filtry);
                $TR_APPS = array();
                if(isset($Pola['tr_color'])){
                    $TR_APPS['color'] = $Pola['tr_color'];
                    unset($Pola['tr_color']);
                }
                if(isset($Pola['tr_class'])){
                    $TR_APPS['class'] = $Pola['tr_class'];
                    unset($Pola['tr_class']);
                }
                if(isset($Pola['tr_attr'])){
                    $TR_APPS['attr'] = $Pola['tr_attr'];
                    unset($Pola['tr_attr']);
                }
                $Elementy = array();
                while ($Element = $this->Baza->GetRow()) {
                    $Elementy[] = $Element;
                    $this->ListIDs[] = $Element[$this->PoleID];
                }
                $Elementy = $this->ObrobkaDanychLista($Elementy);
                $Puste = array();
		$AkcjeNaLiscie = $this->PobierzAkcjeNaLiscie($Puste, true);
?>
<table id="lista" class="lista">
	<tr>
<?php
if($this->lp){
    echo '<th class="lp">LP</th>';
}
foreach ($Pola as $NazwaPola => $Opis) {
?>
		<?php 
		$Styl = '';
		if(is_array($Opis)){
			$Styl = (isset($Opis['styl']) ? " style='{$Opis['styl']}'" : '');
                        $Styl .= (isset($Opis['td_class']) ? " class='{$Opis['td_class']}'" : "");
			$Opis = $Opis['naglowek'];
		}
                $this->ShowTH($NazwaPola, $Styl, $Opis);
		?>
<?php
}
	foreach($AkcjeNaLiscie as $Actions){ 
		echo "<th class='ikona'><img class='ikonka' src='images/{$Actions['img_grey']}' title='{$Actions['title']}' alt='{$Actions['title']}'></th>";
	}
?>
	</tr>
<?php

$Licznik = $this->ParametrPaginacji*$this->IloscNaStrone;
foreach($Elementy as $Element){
    $Licznik++;
    $this->ShowTR($Pola, $Element, $Licznik, $AkcjeNaLiscie, (!empty($TR_APPS) ? $TR_APPS : null ) );
}
?>
</table>
<?php
        $this->ShowPaginationTable();
	}

        function ShowPaginationTable(){
            include(SCIEZKA_SZABLONOW."tabelka-paginacji.tpl.php");
        }


        function ObrobkaDanychLista($Elementy){
            if( is_array($this->ShortNameList) && !empty($this->ShortNameList)){
                foreach($Elementy as $ElementyKey => $Element){
                    foreach( $this->ShortNameList as $ShortElementName=>$Params){
                        if( isset($Params['IsTitle']) && $Params['IsTitle'] == true ){
                            $Elementy[$ElementyKey][$ShortElementName] = Usefull::NameAndTitle($Element, $ShortElementName, $Params['lenght']);
                        }else{
                            $Elementy[$ElementyKey][$ShortElementName] = Usefull::ShortString($Element, $ShortElementName, $Params['lenght']);
                        }
                    }
                }
            }
            return $Elementy;
        }

        function ShowRecord($Element, $Nazwa, $Styl, $Element_class,  $Title){
            echo("<td$Styl $Title><span $Element_class>".stripslashes($Element[$Nazwa])."</span></td>");
        }

        function ShowTR($Pola, $Element, $Licznik, $AkcjeNaLiscie, $TR_APPS = null ){
            $ElementDef = $Element;
            $tr_class = ($this->clickable_rows ? 'class="showek"' : "");
            $tr_attr='';
            if( $TR_APPS != null && isset($TR_APPS['attr']) && is_array($TR_APPS['attr'])){
                foreach($TR_APPS['attr']['attr'] as $AttrKey=>$AttrValue)
                    $tr_attr .= $AttrKey.'="'.(isset($TR_APPS['attr']['element_name']) ? $AttrValue.'-'.$Element[$TR_APPS['attr']['element_name']] : $AttrValue).'" ';
            }
            if($TR_APPS != null && isset($TR_APPS['class']) ){
                if( is_array($TR_APPS['class']) && isset($TR_APPS['class']['element_name']) ){
                    if(isset($Element[$TR_APPS['class']['element_name']])){
                        $tr_class = 'class="'.$TR_APPS['class']['class'][$Element[$TR_APPS['class']['element_name']]].($this->clickable_rows ? " showek" : "").'"';
                    }
                 }elseif(is_string($TR_APPS['class'])){
                     $tr_class = 'class="'.$TR_APPS['class'].($this->clickable_rows ? " showek" : "").'"';
                 }
            }
            if($TR_APPS != null && isset($TR_APPS['color']) ){
                if( $TR_APPS['color'] === false ){
                    $style= '';
                }elseif(is_array($TR_APPS['color']) &&  isset($TR_APPS['color']['element_name'])){
                    $style = $TR_APPS['color']['color'][$Element[$TR_APPS['color']['element_name']]];
                }elseif(is_string($TR_APPS['color'])){
                    $style = 'style="background-color: '.$TR_APPS['color'].';"';
                }else{
                    $KolorWiersza = ($Licznik % 2 != 0 ? '#FFFFFF' : '#F0F0F0');
                    $style = 'style="background-color: '.$KolorWiersza.';"';
                }
            }else{
                $KolorWiersza = ($Licznik % 2 != 0 ? '#FFFFFF' : '#F0F0F0');
                $style = 'style="background-color: '.$KolorWiersza.';"';
            }
            echo("<tr $tr_class $style $tr_attr>");
            if($this->lp){
                echo "<td class=\"lp\">$Licznik</td>";
            }
            foreach ($Pola as $Nazwa => $Opis) {
                    $Styl = "";
                    $Title = "";
                    if( array_key_exists($Nazwa, $this->ShortNameList) ){
                        if($this->ShortNameList[$Nazwa]['IsTitle'] == true){
                            $Title = 'title="'.htmlspecialchars($Element[$Nazwa]['TitleListName']).'"';
                            $Element[$Nazwa] = $Element[$Nazwa][$Nazwa];
                        }
                    }
                    $Element_class = null;
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
//                                }elseif(isset($Opis['type']) && $Opis['type'] == "top" && $Element[$Nazwa] == '3'){
//                                    $Element[$Nazwa] = $Opis['elementy'][$Element[$Nazwa]][$Element['role']];
                                }else{
                                    if( isset($Element[$Nazwa]) && isset($Opis['elementy'][$Element[$Nazwa]]))
                                        $Element[$Nazwa] = $Opis['elementy'][$Element[$Nazwa]];
                                    else
                                        $Element[$Nazwa] = '';
                                }
                        }
                        if(isset($Opis['type']) && $Opis['type'] == "date"){
                            $Element[$Nazwa] = ($Element[$Nazwa] == "0000-00-00" ? "&nbsp;" : $Element[$Nazwa]);
                        }elseif(isset($Opis['type']) && $Opis['type'] == "pln"){
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

        function ShowTH($NazwaPola, $Styl, $Opis){
            echo "<th$Styl>";
            $SortHow = (!isset($_GET['sort']) ? "ASC" : ($_GET['sort'] != $NazwaPola ? "ASC" : ((!isset($_GET['sort_how']) || $_GET['sort_how'] == "DESC") ? "ASC" : "DESC")));
            $SortBy = (isset($_GET['sort']) ? $_GET['sort'] : $this->PoleSort);
            $NowSortHow = (isset($_GET['sort_how']) ? $_GET['sort_how'] : $this->SortHow);
            if(!in_array($NazwaPola, $this->NoSort)){
                echo "<a href='?modul=$this->Parametr&akcja=$this->WykonywanaAkcja&sort=$NazwaPola&sort_how=$SortHow'>$Opis".($SortBy == $NazwaPola  ? " <img src='images/".($NowSortHow == "ASC" ? "sort-arrow-top.gif" : "sort-arrow-down.gif")."' alt='sortowanie' style='display: inline; vertical-align: middle' />" : "")."</a>";
            }else{
                echo $Opis;
            }
            echo "</th>";
        }
	
	function ShowActionsList($AkcjeNaLiscie, $Element){
		foreach ($AkcjeNaLiscie as $Actions){
                    echo("<td class='ikona'>");
                            if(!isset($Actions['hidden']) || !$Actions['hidden']){
                                if(isset($Actions['img'])){ 
                                    if( !in_array($Element[$this->PoleID],
                                            (isset($Actions['akcja']) && isset($this->ZablokowaneElementyIDs[$Actions['akcja']]) ? $this->ZablokowaneElementyIDs[$Actions['akcja']] : array() ) ) ){
                                        $Tooltip = (isset($Actions['tooltip']) ? " class='tooltip_trigger' tooltip='#div_tooltip_{$Element[$this->PoleID]}'" : "");
                                        $Showek = ($Actions['img'] == "information.gif" ? " rel='information'" : "");
                                        if(isset($Actions['akcja']) && $Actions['akcja']){
                                            echo "<a href=\"?modul=$this->Parametr&akcja={$Actions['akcja']}&id={$Element[$this->PoleID]}".Usefull::AddParamsToLink($this->ParametrPaginacji)."\"$Tooltip$Showek><img src=\"images/{$Actions['img']}\"  title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\"></a>";
                                        }else if( isset($Actions['akcja_href']) &&  $Actions['akcja_href']){
                                            echo "<a href=\"{$Actions['akcja_href']}id={$Element[$this->PoleID]}\" target='_blank'$Tooltip$Showek><img src=\"images/{$Actions['img']}\" title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\"></a>";
                                        }else if(isset($Actions['akcja_link']) && $Actions['akcja_link']){
                                            echo "<a href=\"{$Actions['akcja_link']}\"".(isset($Actions['target']) ? " target='_blank'" : "")."$Tooltip$Showek><img src=\"images/{$Actions['img']}\" title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\"></a>";
                                        }else{
                                            echo "<img src=\"images/{$Actions['img']}\" class='ikonka' title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\">";
                                        }
                                        if($Tooltip != ""){
                                            ?><div id="div_tooltip_<?php echo $Element[$this->PoleID]; ?>" class="tooltip_content"><?php echo $Element['tooltip_content']; ?></div><?php
                                        }
                                    }else{
                                            echo "<img src=\"images/{$Actions['img_grey']}\" class='ikonka' title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\">";
                                    }
                                }else{
                                    echo "&nbsp;";
                                }
                            }
                    echo "</td>\n";
		}
	}

        function ShowActionsEdit($AkcjeNaLiscie, $Element){
		foreach ($AkcjeNaLiscie as $Actions){
                    if(!isset($Actions['hidden']) || !$Actions['hidden']){
                        if(!in_array($Element[$this->PoleID], $this->ZablokowaneElementyIDs) && $this->WykonywanaAkcja != $Actions['akcja']){ 
                            if($Actions['akcja']){
                                echo "<a href=\"?modul=$this->Parametr&akcja={$Actions['akcja']}&id={$Element[$this->PoleID]}".Usefull::AddParamsToLink($this->ParametrPaginacji)."\"><img src=\"images/{$Actions['img']}\" title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\"></a>";
                            }else if($Actions['akcja_href']){
                                echo "<a href=\"{$Actions['akcja_href']}&id={$Element[$this->PoleID]}\" target='_blank'><img src=\"images/{$Actions['img']}\"  title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\"></a>";
                            }else if($Actions['akcja_link']){
                                        echo "<a href=\"{$Actions['akcja_link']}\"".($Actions['target'] ? " target='_blank'" : "")."><img src=\"images/{$Actions['img']}\" title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\"></a>";
                            }else{
                                echo "<img src=\"images/{$Actions['img']}\" title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\">";
                            }
                        }else{
                                echo "<img src=\"images/{$Actions['img_grey']}\" title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\">";
                        }
                    }
		}
	}

	function AkcjaSzczegoly($ID) {
            $Dane = $this->PobierzDaneElementu($ID);
            $Formularz = $this->GenerujFormularz($Dane);
            $Formularz->WyswietlDane($Dane);
	}

        function ShowOK(){
            Usefull::ShowKomunikatOK('<b>Rekord został zapisany</b><br/><br/><a href="'.$this->LinkPowrotu.'"><img src="images/arrow_undo.gif" title="Powrót" alt="Powrót" style="display: inline; vertical-align: middle;"> Powrót</a>');
            if($this->RedirectLocation)
                Usefull::RedirectLocation($this->LinkPowrotu);
        }

        function SprawdzPliki($Pliki){
            return true;
        }

	function AkcjaDodawanie() {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$Formularz = $this->GenerujFormularz($_POST, true);
			$PolaWymagane = $Formularz->ZwrocPolaWymagane();
			$PolaZdublowane = $Formularz->ZwrocPolaNieDublujace();
			$OpcjaFormularza = (isset($_POST['OpcjaFormularza']) ? $_POST['OpcjaFormularza'] : 'zapisz');
//                        $this->VAR_DUMP($_POST);
                        $Wartosci = $Formularz->ZwrocWartosciPol($_POST);
//                        $this->VAR_DUMP($Wartosci);
			if ($OpcjaFormularza == 'zapisz'){
                                echo "<div style='clear: both;'></div>\n";
				if($this->SprawdzDane($Formularz, $Wartosci) && $this->SprawdzPliki($Formularz->ZwrocDanePrzeslanychPlikow()) && $this->SprawdzPolaWymagane($PolaWymagane, $Wartosci) && $this->SprawdzPolaNieDublujace($PolaZdublowane, $Wartosci)){
					if ($this->ZapiszDaneElementu($Formularz, $Wartosci, $Formularz->ZwrocDanePrzeslanychPlikow())) {
                                                $this->ShowOK();
						return true;
					}
					else {
						Usefull::ShowKomunikatError('<b>Wystąpił problem. Dane nie zostały zapisane.</b><br/>'.$this->Baza->GetLastErrorDescription());
					}
				}else{
					Usefull::ShowKomunikatError('<b>'.$this->Error.'</b>');
				}
			}
                        $Wartosci = $this->AkcjaPrzeladowanie($Wartosci, $Formularz);
                        $Formularz = $this->AkcjaPrzeladowanieFormularz($Wartosci, $Formularz);
			foreach($this->PolaWymaganeNiewypelnione as $NazwaPola){
				$Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
			}
			foreach($this->PolaZdublowane as $NazwaPola){
				$Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
			}
			$Formularz->Wyswietl($Wartosci, false);
		}
		else {
                        $DaneDomyslne = $this->PobierzDaneDomyslne();
			$Formularz = $this->GenerujFormularz($DaneDomyslne, false);
			$Formularz->Wyswietl($DaneDomyslne, false);
		}
	}

	function AkcjaDuplikacja($ID) {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$Formularz = $this->GenerujFormularz($_POST);
			$PolaWymagane = $Formularz->ZwrocPolaWymagane();
			$PolaZdublowane = $Formularz->ZwrocPolaNieDublujace();
			$OpcjaFormularza = (isset($_POST['OpcjaFormularza']) ? $_POST['OpcjaFormularza'] : 'zapisz');
                        $Wartosci = $Formularz->ZwrocWartosciPol($_POST);
			if ($OpcjaFormularza == 'zapisz') {
				if($this->SprawdzDane($Formularz, $Wartosci) && $this->SprawdzPolaWymagane($PolaWymagane, $Wartosci) && $this->SprawdzPolaNieDublujace($PolaZdublowane, $Wartosci)){
					if ($this->ZapiszDaneElementu($Formularz, $Wartosci, $Formularz->ZwrocDanePrzeslanychPlikow())) {
						$this->ShowOK();
						return;
					}
					else {
						Usefull::ShowKomunikatError('<b>Wystąpił problem! Dane nie zostały zapisane.</b><br/>'.$this->Baza->GetLastErrorDescription());
					}
				}else{
                                    Usefull::ShowKomunikatError('<b>'.$this->Error.'</b>');
				}
			}
                        $Wartosci = $this->AkcjaPrzeladowanie($Wartosci, $Formularz);
                        $Formularz = $this->AkcjaPrzeladowanieFormularz($Wartosci, $Formularz);
			foreach($this->PolaWymaganeNiewypelnione as $NazwaPola){
				$Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
			}
			foreach($this->PolaZdublowane as $NazwaPola){
				$Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
			}
			$Formularz->Wyswietl($Wartosci, false);
		}
		else {
			$Dane = $this->PobierzDaneElementu($ID);
			$Formularz = $this->GenerujFormularz($Dane);
			$Formularz->Wyswietl($Dane, false);
		}
	}

	function AkcjaEdycja($ID) {
		if (isset($_GET['usun_obraz'])) {
			$Plik = $this->KatalogDanych.$_GET['obraz'];
			if (file_exists($Plik)) {
				unlink($Plik);
			}
		}
                if(isset($_GET['usun_plik'])){
                    $this->UsunPlik($ID, $_GET['plik']);
                }
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$Formularz = $this->GenerujFormularz($_POST, true);

			$PolaWymagane = $Formularz->ZwrocPolaWymagane();
			$PolaZdublowane = $Formularz->ZwrocPolaNieDublujace();
			$OpcjaFormularza = (isset($_POST['OpcjaFormularza']) ? $_POST['OpcjaFormularza'] : 'zapisz');
                        $Wartosci = $Formularz->ZwrocWartosciPol($_POST);
			if ($OpcjaFormularza == 'zapisz') {
                                echo "<div style='clear: both;'></div>\n";
				if($this->SprawdzDane($Formularz, $Wartosci, $ID) && $this->SprawdzPolaWymagane($PolaWymagane, $Wartosci) && $this->SprawdzPolaNieDublujace($PolaZdublowane, $Wartosci, $ID)){
					if ($this->ZapiszDaneElementu($Formularz, $Wartosci, $Formularz->ZwrocDanePrzeslanychPlikow(), $ID)) {
                                            $this->ShowOK();
						return;
					}
					else {
						Usefull::ShowKomunikatError('<b>Wystąpił problem! Dane nie zostały zapisane.</b><br/>'.$this->Baza->GetLastErrorDescription());
					}
				}else{
					Usefull::ShowKomunikatError('<b>'.$this->Error.'</b>');
				}
			}
                        $Wartosci = $this->AkcjaPrzeladowanie($Wartosci, $Formularz, $ID);
                        $Formularz = $this->AkcjaPrzeladowanieFormularz($Wartosci, $Formularz, $ID);
			foreach($this->PolaWymaganeNiewypelnione as $NazwaPola){
				$Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
			}
			foreach($this->PolaZdublowane as $NazwaPola){
				$Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
			}
			$Formularz->Wyswietl($Wartosci, false);
		}
		else {
			$Dane = $this->PobierzDaneElementu($ID);
			$Formularz = $this->GenerujFormularz($Dane, false);
			$Formularz->Wyswietl($Dane, false);
		}
	}

        function AkcjaPrzeladowanie($Wartosci, $Formularz, $ID = null){
            if($_POST['OpcjaFormularza'] == 'dodaj_plik' ){
                $this->ZapiszPlik($this->Prefix,$Formularz->ZwrocNazweMapyPola($this->PolePliku) , $ID);
                $Wartosci = $this->PobierzDaneElementu($ID);
            }
            return $Wartosci;
        }

        function AkcjaPrzeladowanieFormularz($Wartosci, $Formularz, $ID = null){
            return $Formularz;
        }

	function AkcjaKasowanie($ID) {
            $this->PobierzNazweElementu($ID);
            if(!in_array($ID, $this->ZablokowaneElementyIDs)){
		if (!isset($_GET['del']) || $_GET['del'] != 'ok') {
			Usefull::ShowKomunikatOstrzezenie("<b>Czy na pewno chcesz skasować <span style='color: #006c67;'>$this->NazwaElementu</span> ?</b><br/><br/><br/><a href=\"{$_SERVER['REQUEST_URI']}&del=ok\"><img src=\"images/skasuj.gif\" style='display: inline; vertical-align: middle;'></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"$this->LinkPowrotu\"><img src=\"images/anuluj.gif\" style='display: inline; vertical-align: middle;'></a><br/><br/><br/><b>UWAGA! Dane zostaną utracone bezpowrotnie!</b>");
		}
		else {
                    if ($this->UsunElement($ID)) {
                            Usefull::ShowKomunikatOK("<b>Pozycja skasowana.</b><br/><br/><a href=\"$this->LinkPowrotu\"><img src=\"images/arrow_undo.gif\" title=\"Powrót\" alt=\"Powrót\" style=\"display: inline; vertical-align: middle;\"> Powrót</a>");
                    }
                    else {
                            Usefull::ShowKomunikatError("<b>Wystąpił problem. Dane nie zostały skasowane.</b><br/><br/>".$this->Baza->GetLastErrorDescription()."<br/><br/><a href=\"$this->LinkPowrotu\"><img src=\"images/arrow_undo.gif\" title=\"Powrót\" alt=\"Powrót\" style=\"display: inline; vertical-align: middle;\"> Powrót</a>");
                    }
		}
            }else{
                Usefull::ShowKomunikatError("<b>Nie możesz usunąć tego elementu.</b><br/><br/><a href=\"$this->LinkPowrotu\"><img src=\"images/arrow_undo.gif\" title=\"Powrót\" alt=\"Powrót\" style=\"display: inline; vertical-align: middle;\"> Powrót</a>");
            }
	}

        function MozeBycOperacja($ID){
            return true;
        }

	function AkcjaDrukuj($ID, $Akcja = false){
		echo "Wydruk";
	}

	function QueryPagination($Query, $pagin = 0, $IloscNaStrone = 30){
		$this->ParametrPaginacji = (is_null($pagin) ? 0 : $pagin);
		$LiczbaWszystkich = $this->Baza->GetNumRows($this->Baza->Query($Query));
		$this->IleStronPaginacji = ceil($LiczbaWszystkich/$IloscNaStrone);
		if($this->ParametrPaginacji > $this->IleStronPaginacji){
			$this->ParametrPaginacji = $this->IleStronPaginacji-1;
		}
		$LimitDolny = $IloscNaStrone * $this->ParametrPaginacji;
		$Query .="  LIMIT $LimitDolny, $IloscNaStrone";
		return $Query;
	}

        /**
         * Sprawdza czy wszystkei pola wymagane zostały wprowadzone
         * @param array $TablicaPolWymaganych
         * @param array $TablicaWartosci
         * @return boolean
         */
	function SprawdzPolaWymagane($TablicaPolWymaganych, $TablicaWartosci){
		foreach($TablicaPolWymaganych as $NazwaPola){
                    if($TablicaWartosci[$NazwaPola] == "" || ($TablicaWartosci[$NazwaPola] == "0" && strlen($TablicaWartosci[$NazwaPola]) == 1 ) ){
                        $this->PolaWymaganeNiewypelnione[] = $NazwaPola;
                        break;
                    }
		}
		if(count($this->PolaWymaganeNiewypelnione) > 0){
                    $this->Error = "Proszę wypełnić wymagane pola.";
                    return false;
		}else{
			return true;
		}
	}

        /**
         * Sprawdza czy nie zostały wprowadzone dane które powinny być unikalne a są zdublowane
         * @param array $TablicaPolNieDublujacych
         * @param array $TablicaWartosci
         * @param integer $ID - id elementu, który wyłączamy ze sprawdzenia
         * @return boolean
         */
	function SprawdzPolaNieDublujace($TablicaPolNieDublujacych, $TablicaWartosci, $ID = null){
		$WhereID = !is_null($ID) ? "AND $this->PoleID != '$ID'" : "";
		foreach($TablicaPolNieDublujacych as $NazwaPola){
			if($this->Baza->GetValue("SELECT COUNT(*) FROM $this->Tabela WHERE $NazwaPola = '{$TablicaWartosci[$NazwaPola]}' $WhereID") > 0){
				$this->PolaZdublowane[] = $NazwaPola;
                                if(isset($this->ErrorsDescriptions['unikalne'][$NazwaPola])){
                                    $this->Error = sprintf($this->ErrorsDescriptions['unikalne'][$NazwaPola], $TablicaWartosci[$NazwaPola]);
                                    return false;
                                }
			}
		}
		if(count($this->PolaZdublowane) > 0){
                        $this->Error = "Wprowadzona wartość już istnieje w bazie danych.";
			return false;
		}else{
			return true;
		}
	}

        function SprawdzDane($Formularz, $Wartosci, $ID=null){
            $Pola = $Formularz->ZwrocPola();
            foreach ($Pola as $Pole) {
                $Typ = $Formularz->ZwrocTypPola($Pole, false);
                switch ($Typ) {
                    case 'tekst_data':
                        if(UsefullTime::CheckDate($Wartosci[$Pole]) == false){
                            $this->Error = 'Błędny format daty.<br />Poprawny format to RRRR-MM-DD';
                            return false;
                        }
                        break;
                    case 'tekst_datatime':
                        if(UsefullTime::CheckDateTime($Wartosci[$Pole]) == false){
                            $this->Error = 'Błędny format daty.<br />Poprawny format to RRRR-MM-DD HH:MM:SS';
                            $this->PolaWymaganeNiewypelnione[] = $Pole;
                            return false;
                        }
                        break;
                    case 'haslo':
                        if($Wartosci[$Pole] != $_POST['reply_pass']){
                            $this->Error = 'Hasło nie zostało potwierdzone prawidłowo';
                            $this->PolaWymaganeNiewypelnione[] = $Pole;
                            return false;
                        }
                        break;
                }
            }
            return true;
        }

	function KwotaSlownie($Kwota, $Waluta) {

		$Potegi = array(
		9 => array("miliard", "miliardy", "miliardów"),
		6 => array("milion", "miliony", "milionów"),
		3 => array("tysiąc", "tysiące", "tysięcy"),
		0 => array()
		);

		$Liczby = array(
		1 => 'jeden', 2 => 'dwa', 3 => 'trzy', 4 => 'cztery', 5 => 'pięć', 6 => 'sześć', 7 => 'siedem', 8 => 'osiem', 9 => 'dziewięć', 10 => 'dziesięć',
		11 => 'jedenaście', 12 => 'dwanaście', 13 => 'trzynaście', 14 => 'czternaście', 15 => 'piętnaście', 16 => 'szesnaście', 17 => 'siedemnaście', 18 => 'osiemnaście', 19 => 'dziewiętnaście',
		20 => 'dwadzieścia', 30 => 'trzydzieści', 40 => 'czterdzieści', 50 => 'pięćdziesiąt', 60 => 'sześćdziesiąt', 70 => 'siedemdziesiąt', 80 => 'osiemdziesiąt', 90 => 'dziewięćdziesiąt',
		100 => 'sto', 200 => 'dwieście', 300 => 'trzysta', 400 => 'czterysta', 500 => 'pięćset', 600 => 'sześćset', 700 => 'siedemset', 800 => 'osiemset', 900 => 'dziewięćset'
		);

		$Slownie = '';
		$Kwota = round($Kwota, 2);
		foreach ($Potegi as $Potega => $Odmiany) {
			$Ilosc = intval($Kwota / (pow(10, $Potega))) % 1000;
			if ($Ilosc) {
				$Setki = 100 * intval($Ilosc / 100);
				$Dziesiatki = 10 * intval(($Ilosc - $Setki) / 10);
				$Jednosci = $Ilosc - $Setki - $Dziesiatki;
				if ($Setki) {
					$Slownie .= $Liczby[$Setki].' ';
				}
				if ($Dziesiatki == 10) {
					$Slownie .= $Liczby[$Dziesiatki+$Jednosci].' ';
				}
				else {
					if ($Dziesiatki) {
						$Slownie .= $Liczby[$Dziesiatki].' ';
					}
					if ($Jednosci) {
						if (!(($Potega > 0) && ($Ilosc == 1))) {
							$Slownie .= $Liczby[$Jednosci].' ';
						}
					}
				}
				if ($Potega > 0) {
					if ($Ilosc == 1) {
						$Slownie .= $Odmiany[0].' ';
					}
					elseif (in_array($Jednosci, array(2, 3, 4)) && $Dziesiatki != 10) {
						$Slownie .= $Odmiany[1].' ';
					}
					else {
						$Slownie .= $Odmiany[2].' ';
					}
				}
			}
		}

		if (!($Zlote = intval($Kwota))) {
   			($Waluta == 'PLN') ? $Slownie .= 'zero złotych ': $Slownie .= 'zero '.$Waluta;
		}elseif ($Zlote == 1) {
	   		($Waluta == 'PLN') ? $Slownie .= 'złoty ': $Slownie .= ' '.$Waluta;
		}elseif (in_array($Jednosci, array(2, 3, 4)) && $Dziesiatki != 10) {
	   		($Waluta == 'PLN') ? $Slownie .= 'złote ': 	$Slownie .= ' '.$Waluta;
		}else {
	   		($Waluta == 'PLN') ? $Slownie .= 'złotych ': $Slownie .= ' '.$Waluta;
		}
	   	$Grosze = round(100 * ($Kwota - $Zlote)) % 100;
	   	if ($Grosze) {
	    	$Slownie .= " $Grosze/100";
	   	}
		return trim($Slownie);
	}

	function PrzeslijObrazek($PoleImage, $Prefix = null, $MaxSz = null, $MaxW = null, $CreateMiniatures = false){
            if (is_uploaded_file($_FILES[$PoleImage]['tmp_name'])) {
			$NazwaPliku = (!is_null($Prefix) ? $Prefix.'_'.$_FILES[$PoleImage]['name'] : $_FILES[$PoleImage]['name']);
			$NazwaPliku = $this->ObrobkaNazwyPliku($NazwaPliku);
			$Plik = $this->KatalogDanych.$NazwaPliku;
                        if(!file_exists($Plik)){
                            $Sciezka = dirname($Plik);
                            $StaryUmask = umask(0);
                            if (!file_exists($Sciezka)) {
                                    mkdir($Sciezka, 0777, true);
                            }
                            if (move_uploaded_file($_FILES[$PoleImage]['tmp_name'], $Plik)) {
                                    chmod($Plik, 0777);
                                    if(!is_null($MaxSz) || !is_null($MaxW)){
                                            $this->ResizeImage($MaxSz, $MaxW, $Plik, $this->KatalogDanych."resize/".$NazwaPliku);
                                    }
                                    if($CreateMiniatures){
                                        $this->ResizeImage(840, 460, $Plik, $this->KatalogDanych."840px/".$NazwaPliku);
                                        chmod($this->KatalogDanych."840px/".$NazwaPliku, 0777);
                                        $this->ResizeImage(375, null, $Plik, $this->KatalogDanych."375px/".$NazwaPliku);
                                        chmod($this->KatalogDanych."475px/".$NazwaPliku, 0777);
                                        $this->ResizeImage(120, null, $Plik, $this->KatalogDanych."120px/".$NazwaPliku);
                                        chmod($this->KatalogDanych."120px/".$NazwaPliku, 0777);
                                        $this->ResizeImage(50, 30, $Plik, $this->KatalogDanych."50px/".$NazwaPliku);
                                        chmod($this->KatalogDanych."50px/".$NazwaPliku, 0777);
                                    }
                                    $this->NazwaPrzeslanegoPliku = $NazwaPliku;
                                    $this->FileType = Usefull::GetFileType($Plik, $_FILES[$PoleImage]['type']);
                            }
                            umask($StaryUmask);
                            return true;
                        }
		}
                return false;
	}

        function PrzeslijObrazekArray($PoleImage, $IDFile, $Prefix = null, $MaxSz = null, $MaxW = null){
                if (is_uploaded_file($_FILES[$PoleImage]['tmp_name'][$IDFile])) {
			$NazwaPliku = (!is_null($Prefix) ? $Prefix.'_'.$_FILES[$PoleImage]['name'][$IDFile] : $_FILES[$PoleImage]['name'][$IDFile]);
			$NazwaPliku = $this->ObrobkaNazwyPliku($NazwaPliku);
			$Plik = $this->KatalogDanych.$NazwaPliku;
                        if(!file_exists($Plik)){
                            $Sciezka = dirname($Plik);
                            $StaryUmask = umask(0);
                            if (!file_exists($Sciezka)) {
                                    mkdir($Sciezka, 0777, true);
                            }
                            if (move_uploaded_file($_FILES[$PoleImage]['tmp_name'][$IDFile], $Plik)) {
                                    chmod($Plik, 0777);
                                    if(!is_null($MaxSz) || !is_null($MaxW)){
                                            $this->ResizeImage($MaxSz, $MaxW, $Plik);
                                    }
                                    $this->NazwaPrzeslanegoPliku = $NazwaPliku;
                                    $this->FileType = Usefull::GetFileType($Plik, $_FILES[$PoleImage]['type'][$IDFile]);
                            }
                            umask($StaryUmask);
                            return true;
                        }
		}
                return false;
	}
        
        
        function ObrobkaNazwyPliku($NazwaPliku){
            $Usefull = new Usefull();
            return $Usefull->prepareFileName($NazwaPliku);
        }

	function ResizeImage($max_szer,$max_wys,$plik, $newplik = null){
		$Wielkosci = $this->WielkoscObrazka($max_szer, $max_wys, $plik);
		$newwidth = $Wielkosci[0];
		$newheight = $Wielkosci[1];
		$obrazek = imagecreatetruecolor($newwidth, $newheight);
		# ustalamy rodzaj obrazka
		$tabplix = explode('.', $plik);
		$idx = count($tabplix) - 1;
		$rozszerzenie = $tabplix[$idx];
		if(strtolower($rozszerzenie) == 'jpg' || strtolower($rozszerzenie) == 'jpeg'){
			$source = imagecreatefromjpeg($plik);
		}else{
			$source = imagecreatefromgif($plik);
		}
		$PlikWstaw = (!is_null($newplik) ? $newplik : $plik);
                $Sciezka = dirname($PlikWstaw);
                $StaryUmask = umask(0);
                if (!file_exists($Sciezka)) {
                        mkdir($Sciezka, 0777, true);
                }
		imagecopyresampled($obrazek, $source, 0, 0, 0, 0, $newwidth, $newheight, $Wielkosci[2], $Wielkosci[3]);
		imagejpeg($obrazek, $PlikWstaw, 95);
		imagedestroy($obrazek);
	}
	
	function WielkoscObrazka($max_szer, $max_wys, $plik){
		list($width, $height) = getimagesize($plik);
		$SzerokoscMax = (is_null($max_szer) ? $width : $max_szer);
		$WysokoscMax = (is_null($max_wys) ? $height : $max_wys);
		$aspekt_x=$SzerokoscMax/$width;
		$aspekt_y=$WysokoscMax/$height;
		if (($width<=$SzerokoscMax)&&($height<=$WysokoscMax)) {
			$newwidth=$width;
			$newheight=$height;
		}
		else if (($aspekt_x*$height)<$WysokoscMax) {
			$newheight=ceil($aspekt_x*$height);
			$newwidth=$SzerokoscMax;
		}
		else {
			$newwidth=ceil($aspekt_y*$width);
			$newheight=$WysokoscMax;
		}
		return array($newwidth, $newheight, $width, $height);
	}
	
	function MapujNazweMiesiaca($miesiac){
		if($miesiac < 10){
			$miesiac = "0".$miesiac;
		}
		$miesiac = str_replace("00", "0", $miesiac);
		switch($miesiac){
			case("01"): return "styczeń";
			case("02"): return "luty";
			case("03"): return "marzec";
			case("04"): return "kwiecień";
			case("05"): return "maj";
			case("06"): return "czerwiec";
			case("07"): return "lipiec";
			case("08"): return "sierpień";
			case("09"): return "wrzesień";
			case("10"): return "październik";
			case("11"): return "listopad";
			case("12"): return "grudzień";
			default: return $miesiac;
		}
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
	function ZapiszZdjecie($Prefix, $NazwaPola, $ID, $MaxSz = null, $MaxW = null, $CreateMiniatures = false){
                if($this->PrzeslijObrazek($NazwaPola,$Prefix, $MaxSz, $MaxW, $CreateMiniatures)){
			$NazwaPliku = mysql_real_escape_string($_FILES[$NazwaPola]['name']);
                        $size = $_FILES[$NazwaPola]['size'];
                        $type = $_FILES[$NazwaPola]['type'];
                            
			$this->Baza->Query("INSERT INTO $this->TabelaZdjecia
                        SET 
                            $this->TabelaZdjeciaPrefix$this->PoleID = '$ID',
                            $this->PoleZdjecia = '$this->NazwaPrzeslanegoPliku',
                            $this->PoleZdjeciaOryginalnego ='$NazwaPliku',
                            {$this->TabelaZdjeciaPrefix}size = '$size',
                            {$this->TabelaZdjeciaPrefix}content_type = '$type',
                            {$this->TabelaZdjeciaPrefix}created_at = NOW(),
                            {$this->PoleZdjeciaHASH} = sha1(concat('$ID$size$NazwaPliku$type'))
                        ");
                        $FileID = $this->Baza->GetLastInsertID();
		}else{
			Usefull::ShowKomunikatError('<b>Wystąpił problem. Obraz nie został zapisany na serwerze.</b>');
		}
	}
	
	function ZapiszPlik($Prefix, $NazwaPola, $ID){
		if($this->PrzeslijObrazek($NazwaPola,$Prefix)){
			$NazwaPliku = $Prefix.'_'.$_FILES[$NazwaPola]['name'];
			$this->Baza->Query("INSERT INTO $this->TabelaPlikow SET $this->PoleID='$ID', $this->PolePliku='$NazwaPliku'");
		}else{
			Usefull::ShowKomunikatError('<b>Wystąpił problem. Plik nie został zapisany na serwerze.</b>');
		}
	}
	
	function WyswietlSelected($Wartosc, $Opcja){
		if($Wartosc == $Opcja){
			return " selected";
		}
		return "";
	}
	
	function SprawdzUprawnienie($Uprawnienie) {
		if(count($this->Uprawnienia) == 1 && $this->Uprawnienia[0] == "*"){
			return true;
		}else{
			return in_array($Uprawnienie, $this->Uprawnienia);
		}
	}
	
	function CloseKomunikaty(){
		echo "<script type='text/javascript'>\n";
			echo "AutomaticClose();";
		echo "</script>\n";
	}
	
	function VAR_DUMP($Wartosci, $die=false){
                echo "<pre>\n";
                        var_dump($Wartosci);
                echo "</pre>\n";
                if( $die ) die('var_dump die');
	}
	
	function Blokowanie($ID){
            $this->PobierzNazweElementu($ID);
            if(!in_array($ID, $this->ZablokowaneElementyIDs)){
		if (!isset($_GET['del']) || $_GET['del'] != 'ok') {
                    $Now = $this->CzyPokazywane($ID);
                    Usefull::ShowKomunikatOstrzezenie("<b>Czy na pewno chcesz ".($Now == 1 ? "odblokować " : "zablokować ")."użytkownika <span style='color: #006c67;'>$this->NazwaElementu</span> ?<br/><br/><br/><a href=\"{$_SERVER['REQUEST_URI']}&del=ok\"><img src=\"images/ok.gif\" style='display: inline; vertical-align: middle;'></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"$this->LinkPowrotu\"><img src=\"images/anuluj.gif\" style='display: inline; vertical-align: middle;'></b></a><br/><br/>");
		}
		else {
                    if ($this->Blokuj($ID)) {
                        if($this->CzyPokazywane($ID) == 1){
                            Usefull::ShowKomunikatOK("<b>Użytkownik został zablokowany.</b><br/><br/><a href=\"$this->LinkPowrotu\"><img src='images/ok.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'></a>");
                        }else{
                            Usefull::ShowKomunikatOK("<b>Użytkownik został odblokowany.</b><br/><br/><a href=\"$this->LinkPowrotu\"><img src='images/ok.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'></a>");
                        }
                    }
                    else {
                        Usefull::ShowKomunikatError("<b>Wystąpił problem. Operacja nie powiodła się.</b><br/><br/>".$this->Baza->GetLastErrorDescription()."<br/><br/><a href=\"$this->LinkPowrotu\"><img src='images/ok.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'></a>");
                    }
		}
            }else{
                Usefull::ShowKomunikatError("<b>Nie możesz zablokować tego elementu.</b><br/><br/><a href=\"$this->LinkPowrotu\"><img src='images/ok.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'></a>");
            }
	}
	
	function GetList($ID = false){
		return $this->Baza->GetOptions("SELECT $this->PoleID, $this->PoleNazwy FROM $this->Tabela ORDER BY ".(is_null($this->PoleSort) ? $this->PoleNazwy : $this->PoleSort));
	}

        function ShowNaglowekDrukuj($Akcja){
            include(SCIEZKA_SZABLONOW.'naglowek_drukuj.tpl.php');
        }

        function SetWykonywanaAkcja($Akcja){
            $this->WykonywanaAkcja = $Akcja;
        }
 
 /**
  * Akcja Pobierz - generuje pobranie pliku
  * 
  * @author		Maciej Kura <Maciej.Kura@gmail.com>
  * @copyright          Copyright (c) 2012 Maciej Kura
  * @package		PanelPlus 
  * @version		1.0
  * @return file file - plik do pobrania lub komunikat błędu
  */         
        function AkcjaPobierz($ID){
            list($sciezka, $filename) = $this->GetFile($ID);
            if( !empty($sciezka) ){
                Download::DownloadFile($sciezka, $filename );
            }else{
                include(SCIEZKA_SZABLONOW.'naglowek.tpl.php');
                Usefull::ShowKomunikatError("<b>Wystąpił problem. problem z pobraniem załącznika.</b>");
                include(SCIEZKA_SZABLONOW.'stopka.tpl.php');
            }
        }
 /**
  * funkcja pobierająca nazwę pliku zapisanego na dysku z bazy danych o id = $ID
  * 
  * @author		Maciej Kura <Maciej.Kura@gmail.com>
  * @copyright          Copyright (c) 2012 Maciej Kura
  * @package		PanelPlus 
  * @version		1.0
  * @return array ($DiskFile, $OryginalFileName)
  */         
        function GetFile($ID){
            if( $this->Baza->Query("SELECT $this->PolePliku FROM $this->TabelaPlikow WHERE $this->PoleID = '$ID' LIMIT 1")){
                $file = $this->Baza->GetRow();
                return array($file[$this->PolePliku], null);
            }else{
                return array(false, null);
            }
        }
 /**
  * funkcja pobierz nie generuje tabelek w outpucie
  * 
  * @author		Maciej Kura <Maciej.Kura@gmail.com>
  * @copyright          Copyright (c) 2012 Maciej Kura
  * @package		PanelPlus 
  * @version		1.0
  * @param  $Akcja string nazwa akcji
  */          
        function Pobierz($Akcja){
            	$this->WykonywanaAkcja = $Akcja;
		if ( isset($_GET['id']) && intval($_GET['id']) != 0 &&
                     isset($_GET['hash']) && !preg_match('/\W/', $_GET['hash'])) {
                        $ID = intval($_GET['id']);
                        $HASH = $_GET['hash'];
		}
		else {
			$ID = null;
                        $HASH = NULL;
//                        header('Location: ');
		}                
                $this->WykonywaneAkcje($ID.'_'.$HASH);
        }
        
        
 /**
  * funkcja ustawiająca domyślne filtry jeśli nie ma żadnych ustawionych
  * 
  * @author		Maciej Kura <Maciej.Kura@gmail.com>
  * @copyright          Copyright (c) 2012 Maciej Kura
  * @package		PanelPlus 
  * @version		1.0
  */          
        function SetDefaultFilters(){
            if(empty($_SESSION['Filtry']) && !empty($this->DefaultFilters)){
                foreach( $this->DefaultFilters as $sKeyFilter=>$aValueFilter ){
                    foreach($this->Filtry as $Filtr){
                        if( in_array( "$sKeyFilter", $Filtr, TRUE ) ){
                            $_SESSION['Filtry'][$sKeyFilter] = $aValueFilter;
                        }
                    }
                }
            }
        }
        
        public function AkcjaAkcje($ID){
            $this->GetActions($ID);
            include(SCIEZKA_SZABLONOW."action.tpl.php");
        }
        
                
        public function GetSelect($modul, $default = null, $Where = null){
            $_Modul = 'Modul'.$modul;
            $_modul = new $_Modul($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
            return $_modul->GetIDName($default, null, $Where);
        }
        
        public function GetIDName($default, $Query = null, $Where = null){
            if($Query == null){
                $Visible = (isset($this->PoleVisible) && !empty($this->PoleVisible) ?  "{$this->PoleVisible} = {$this->PoleVisibleValue} " : "");
                if($Where != null && !empty($Where) && !empty($Visible)){
                    $Where .= ' AND '.$Visible;
                }elseif(!empty($Visible)){
                    $Where = $Visible;
                }
                $Query = "SELECT $this->PoleID, $this->PoleNazwy FROM $this->Tabela WHERE $Where";
            }
            $Select = $this->Baza->GetOptions($Query);
            if($default === TRUE){
                $Select = Usefull::PolaczDwieTablice( array(''=>' --Wybierz-- '), $Select );
            }elseif( is_string($default) ){
                $Select = Usefull::PolaczDwieTablice( array(''=>$default), $Select );
            }elseif(is_array($default)){
                $Select = Usefull::PolaczDwieTablice( $default, $Select );
            }
            return $Select;
        }
        
        function GetGlobalConfig($Config){
            return true;
            //return ModulConfig::GetGlobalConfig($Config);
        }

    function ImportFile(){
        require(SCIEZKA_PHPEXCEL.'PHPExcel.php');

        $tmp_name = $_FILES['fileToUpload']['tmp_name'];
        $filename = $_FILES['fileToUpload']['name'];
        $importDir = __DIR__.'/../../../public/importFiles/';

        if(is_uploaded_file($tmp_name)){
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            if(!in_array($extension, $this->allowedImportTypes)){
                echo Usefull::ShowKomunikatError("Wystąpił błąd. Załącznik ma niepoprawne rozszerzenie. Dozwolone rozszerzenia to: ".implode(", ", $this->allowedImportTypes), true);
                return $this->showImportForm();
            }
            if(!move_uploaded_file($tmp_name,$importDir.$filename)){
                echo Usefull::ShowKomunikatError("Wystąpił błąd. Załącznik ma niepoprawne rozszerzenie. Dozwolone rozszerzenia to: ".implode(", ", $this->allowedImportTypes), true);
                return $this->showImportForm();
            }
        }else{
            echo Usefull::ShowKomunikatError("Wystąpił błąd podczas przesyłania pliku.", true);
            return $this->showImportForm();
        }

        $objPHPExcel = PHPExcel_IOFactory::load($importDir.$filename);
        $rowsAmount = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

        for($rowNumber = 2; $rowNumber < $rowsAmount; $rowNumber++){
            //put imported companies to DB
            $this->putImportedElements($objPHPExcel, $rowNumber);
        }

        echo Usefull::ShowKomunikatOK("Import pliku zakończony sukcesem.", true);

    }

    function Import(){
        $this->showImportForm();
    }

    function showImportForm(){
        echo '
            <form action="/?modul='.$_GET['modul'].'&akcja=importFile" method="post" enctype="multipart/form-data">
                <h1>Import - '.$this->importTitle.'</h1>
                <p>Format pliku:</p>
                <table class="exampleExcel">'.$this->columnsFormat.'</table>
                Wybierz plik do importu:
                <br>
                <input type="file" name="fileToUpload" id="fileToUpload">
                <br>
                <input type="submit" value="Zaimportuj" name="submit">
            </form>
        ';
    }

    function showImported($cellIterator){
        echo '<tr>';
        foreach ($cellIterator as $cell) {
            echo '<td>'.$cell->getValue().'</td>';
        }
        echo '</tr>';
    }
        
}
?>