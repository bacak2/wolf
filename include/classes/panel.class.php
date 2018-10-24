<?php
/**
 * Obsługa akcji panelu
 * 
 * @author		Lukasz Piekosz <mentat@mentat.net.pl>; Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2004-2008 ARTplus
 * @package		PanelPlus
 * @version		1.0
 */
class Panel {

        /**
         * Zawiera referencję do obiektu bazy
         * @var DBMySQL
         */
        private $Baza = null;
        /**
         * Obiekt klasy Menu
         * @var Menu
         */
        private $Menu = null;
        /**
         * Obiekt klasy Uzytkownik
         * @var Uzytkownik
         */
        private $Uzytkownik = null;
        /**
         * Tablica zawierająca moduły aplikacji
         * @var array
         */
        private $Moduly = array();
        /**
         * Tablica zawierająca dostępne moduły
         * @var array
         */
        private $TablicaUprawnienia = array();

        /**
         * konstruktor
         * @param array $BazaParametry
         */
	function __construct($BazaParametry) {
		$DBConnectionSettings = new DBConnectionSettings($BazaParametry);
		$this->Baza = new DBMySQL($DBConnectionSettings);
//LOGI                
		$this->Baza->EnableLog();
                
		$this->Uzytkownik = new Uzytkownik($this->Baza, 'users', null, null, null);
		if(($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['logowanie']) && (!$this->Uzytkownik->CzyZalogowany())) {
                    $this->Uzytkownik->Zaloguj($_POST['pp_login'],$_POST['pp_haslo']);
		}
                $this->TablicaUprawnienia = $this->Uzytkownik->ZwrocTabliceUprawnien();

		if(isset($_POST['wyloguj'])) {
			$this->Uzytkownik->Wyloguj();
		}
		$this->Menu = new Menu();
	}

        /**
         * Dodaje moduł do tablicy modułów
         * @param string $NazwaKlasy
         * @param string $Parametr
         * @param string $Nazwa - nazwa w menu
         * @param string $Nadrzedny - parametr modułu nadrzędnego
         * @param boolean $Ukryty - jest ukryty (nie widoczny w menu) czy nie
         */
	function DodajModul($NazwaKlasy, $Parametr, $Nazwa, $Nadrzedny = null, $Ukryty = false) {
            if(!$Ukryty && $this->Uzytkownik->IsAdmin() == false && $this->Uzytkownik->SprawdzUprawnienie($Nadrzedny, $this->TablicaUprawnienia)){
                $this->TablicaUprawnienia[] = $Parametr;
            }
            if($this->Menu->SprawdzUprawnienie($Parametr, $this->TablicaUprawnienia)){
                $this->Moduly[$Parametr] = $NazwaKlasy;
                $this->Menu->DodajModul($Parametr, $Nazwa, $Nadrzedny, $Ukryty);
            }
	}

        /**
         * Zwraca nazwę klasy na podstawie parametru
         * @param string $Parametr
         * @return string
         */
	function GetClassName($Parametr){
		return $this->Moduly[$Parametr];
	}

        /**
         * Odpowiada za wyświetlenie panelu -> czyli generalnie wszystkiego co widać w przeglądarce
         */
	function Wyswietl() {
            $this->Menu->WyznaczSciezke();
            if ($this->Baza->Connected()) {
                    if($this->Uzytkownik->CzyZalogowany()) {
                            $this->Menu->WczytajTabliceUprawnien($this->TablicaUprawnienia);
                            include(SCIEZKA_SZABLONOW.'naglowek.tpl.php');
            ?>
            <table class='width_max'>
                <tr>
                    <td align="center" valign="middle">
					<table id="envelope_table">
						<tr>
							<td style="">
								<div style="margin:4px;float:left;">
									<?php echo(ucfirst(strftime('%A, %e %B %Y')).", <a href='?modul=uzytkownik' style='font-size: 12px;'>zalogowany jako: {$_SESSION['login']}</a>"); ?>
								</div>
								<div style="margin:4px;float:right;">
									<form action="." method="post" style="line-height: 20px;">
										<input type="hidden" name="wyloguj" value="1">
										<input type="image" src="images/door_in.gif" alt="wyloguj" title="Wyloguj" style="vertical-align:bottom;">
									</form>
								</div>
								<div style="margin:4px;float:right;">
									<a href="?modul=koszyk" style="margin-right: 16px;">
										<img src="images/koszyk.png" title="Koszyk" alt="Koszyk" style="border: 0;">
									</a>
								</div>
							</td>
						</tr>
					</table>
					<table id="envelope_table">
                            <tr class="envelope_table">
                                <td class="envelope_table laczka">
                                    <table  class='width_max'>
                                        <tr>
                                            <td style="width: auto;vertical-align: bottom;">
                                            </td>
											<td style="width: 250px;text-align:center;">
                                                <div style="margin-top: 16px"><img src="images/wolf_logo.png" alt="" border="0" /></div>
                                                <div style="margin-top: 8px;margin-bottom:8px;"><img src="images/serwisplus.png" alt="" border="0" /></div>
                                            </td>											
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr class="envelope_table">
                                <td class="envelope_table">
                                <?php
                                    $this->Menu->Wyswietl($this->TablicaUprawnienia);
                                ?>
                                </td>
                            </tr>
                            <tr class="envelope_table">
                                <td class="envelope_table">
                                    <table  class='width_max'>
                                        <tr>
                                            <td class="">
                                           <?php
                                                if($this->Uzytkownik->SprawdzUprawnienie($this->Menu->AktywnyModul(), $this->TablicaUprawnienia)){
                                                    $Modul = new $this->Moduly[$this->Menu->AktywnyModul()]($this->Baza, $this->Uzytkownik, $this->Menu->AktywnyModul(), $this->Menu->ZwrocSciezke());
                                                    $Modul->UstalUprawnienia($this->TablicaUprawnienia);
                                                    if($this->Menu->AktywnyModul() == "pulpit"){
                                                        $Modul->SetModules($this->Menu->ZwrocTabliceZakladek());
                                                    }
                                                    $Modul->Wyswietl($this->Menu->WykonywanaAkcja());
                                                }else{
                                                    $Modul = new ModulZabroniony($this->Baza, $this->Uzytkownik, $this->Menu->AktywnyModul(), $this->Menu->ZwrocSciezke());
                                                    $Modul->Wyswietl();
                                                }
                                            ?>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr class="envelope_table envelope_table_bottom">
                                <td class="envelope_table envelope_table_bottom"></td>
                            </tr>
                            <tr class="envelope_table">
                                <td class="envelope_table"><p class="logowanie_dol">powered by <a href="http://www.artplus.pl" target="_blank" class="log">ARTplus</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        <?php
            }elseif(isset($_GET['haslo']) && $_GET['haslo']==='new' ){
                if(($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['przypomnij']) && (!$this->Uzytkownik->CzyZalogowany())){
                    $Komunikat = $this->Uzytkownik->PrzypomnijHaslo($_POST['pp_email']);
                }
                include(SCIEZKA_SZABLONOW.'naglowek-logowanie.tpl.php');
                include(SCIEZKA_SZABLONOW.'logowanie-new.tpl.php');
            }elseif(isset($_GET['resetpassword']) && preg_match ( '/[a-zA-Z0-9]/', $_GET['resetpassword']) ){
                $token = $this->Uzytkownik->CreateToken( 'reset_password_sent_at', 'reset_password_token', $_GET['resetpassword'] );
                if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['token']) && preg_match( '/[a-zA-Z0-9]/', $_POST['token']) && $_POST['token'] == $token) {
                    $IsOk = $this->Uzytkownik->ResetHaslo( $_GET['resetpassword'], $_POST['token'], $_POST['pp_haslo']);
                    if($IsOk){
                        $Komunikat = array( "func"=>"ShowKomunikatOK",
                        "tresc"=>"Hasło zostało zmienione<br />Proszę się zalogować");
                    }else{
                        $Komunikat = array( "func"=>"ShowKomunikatError",
                        "tresc"=>"Hasło nie zostało zmienione<br />Prosimy skontaktować się z serwisem");
                    }
                }elseif( isset($_POST['token']) && preg_match( '/[a-zA-Z0-9]/', $_POST['token']) && $_POST['token'] != $token ){
                    $Komunikat = array( "func"=>"ShowKomunikatError",
                        "tresc"=>"Niepoprawne dane");
                }elseif(empty($token)){
                    $Komunikat = array( "func"=>"ShowKomunikatError",
                        "tresc"=>"Brak użytkownika dla którego chcesz zmienić hasło");                    
                }
                include(SCIEZKA_SZABLONOW.'naglowek-logowanie.tpl.php');
                include(SCIEZKA_SZABLONOW.'logowanie-reset.tpl.php');
            }else{
                include(SCIEZKA_SZABLONOW.'naglowek-logowanie.tpl.php');
                include(SCIEZKA_SZABLONOW.'logowanie.tpl.php');
            }
        }else{
            include(SCIEZKA_SZABLONOW.'naglowek-logowanie.tpl.php');
            include(SCIEZKA_SZABLONOW.'przerwa_techniczna.tpl.php');
        }
        include(SCIEZKA_SZABLONOW.'stopka.tpl.php');
    }

        /**
         * Funkcja przez którą przechodzą wszystkie akcje AJAX-owe
         * @param string $Modul
         * @param string $Action
         * @param string $Parametr 
         */
	function WyswietlAJAX($Modul, $Action, $Parametr = null) {
            if ($this->Baza->Connected()) {
                if($this->Uzytkownik->CzyZalogowany()) {
                    if($this->Uzytkownik->SprawdzUprawnienie($Parametr, $this->TablicaUprawnienia)){
                        $Modul = new $Modul($this->Baza, $this->Uzytkownik, $Parametr, null);
                        $Modul->WyswietlAJAX($Action);
                    }
                }
            }
	}
        
                /**
         * Funkcja przez którą przechodzą wszystkie akcje AJAX-owe
         * @param string $Modul
         * @param string $Action
         * @param string $Parametr 
         */
	function ReturnJsonData($Modul, $Action, $Parametr = null, $CheckLogin = false ) {
            if ($this->Baza->Connected()) {
                if($this->Uzytkownik->CzyZalogowany() && $CheckLogin) {
                    if($this->Uzytkownik->SprawdzUprawnienie($Parametr, $this->TablicaUprawnienia)){
                        $Modul = new $Modul($this->Baza, $this->Uzytkownik, $Parametr, null);
                        $Json = $Modul->ReturnJsonData($Action);
                    }
                }else{
                    $Modul = new $Modul($this->Baza, $this->Uzytkownik, $Parametr, null);
                    $Json = $Modul->ReturnJsonData($Action);
                }
                echo json_encode($Json);
            }
            exit();
	}
        
        /**
         * Funkcja przez którą są wywoływane funkcje CRON-a
         * @param string $Modul
         * @param string $Parametr
         */
        function WykonajCron($Modul, $Parametr = null) {
            if ($this->Baza->Connected()) {
                $Modul = new $Modul($this->Baza, $this->Uzytkownik, $Parametr, null);
                $Modul->Cron();
            }
	}

        /**
         * Wyświetlanie zawartości okna Popup (wyświetla się bez niepotrzebnych nagłówków)
         * @param Modul $Modul
         * @param <type> $Action
         * @param <type> $Parametr
         */
	function WyswietlPopup($Modul, $Action, $Parametr = null) {
		include(SCIEZKA_SZABLONOW.'naglowek.tpl.php');
		if ($this->Baza->Connected()) {
			if($this->Uzytkownik->CzyZalogowany()) {
                            if($this->Uzytkownik->SprawdzUprawnienie($Parametr, $this->TablicaUprawnienia)){
                                $Modul = new $Modul($this->Baza, $this->Uzytkownik, $Parametr, null);
                                $Modul->WyswietlAJAX($Action);
                            }
			}
		}
		include(SCIEZKA_SZABLONOW.'stopka.tpl.php');
	}
        
        /**
         * Wyświetla widok do wydruku
         * @param string $Modul
         * @param string $Akcja
         * @param string $Parametr
         */
        function WyswietlDrukuj($Modul, $Akcja, $Parametr = null) {
            if ($this->Baza->Connected()) {
                if($this->Uzytkownik->CzyZalogowany()) {
                        $mod = $this->GetClassName($Modul);
                        $Modul = new $mod($this->Baza, $this->Uzytkownik, $Parametr, null);
                        $Modul->UstalUprawnienia($this->TablicaUprawnienia);
                        $Modul->ShowNaglowekDrukuj($Akcja);
                        $Modul->AkcjaDrukuj($_GET['id'], $Akcja);
                }
            }
            include(SCIEZKA_SZABLONOW.'stopka_drukuj.tpl.php');
	}

        /**
         * funkcja zwraca czy jest zalogowany czy nie
         * @return boolean
         */
        function CzyZalogowany(){
            return $this->Uzytkownik->CzyZalogowany();
        }
        
        /**
         * Wyświetl Pobranie Pliku
         */
        
        function WyswietlPobierz(){
            $this->Menu->WyznaczSciezke();
            if ($this->Baza->Connected())
            {
                if($this->Uzytkownik->CzyZalogowany()) {
                    $this->Menu->WczytajTabliceUprawnien($this->TablicaUprawnienia);
                    if($this->Uzytkownik->SprawdzUprawnienie($this->Menu->AktywnyModul(), $this->TablicaUprawnienia)){
                        $Modul = new $this->Moduly[$this->Menu->AktywnyModul()]($this->Baza, $this->Uzytkownik, $this->Menu->AktywnyModul(), $this->Menu->ZwrocSciezke());
                        $Modul->UstalUprawnienia($this->TablicaUprawnienia);
                        $Modul->Pobierz($this->Menu->WykonywanaAkcja());
                    }else{
                        $Modul = new ModulZabroniony($this->Baza, $this->Uzytkownik, $this->Menu->AktywnyModul(), $this->Menu->ZwrocSciezke());
                        $Modul->Wyswietl();
                    }
                }else{
                    include(SCIEZKA_SZABLONOW.'naglowek-logowanie.tpl.php');
                    include(SCIEZKA_SZABLONOW.'logowanie.tpl.php');
                }
            }
            else
            {
                include(SCIEZKA_SZABLONOW.'naglowek.tpl.php');
                include(SCIEZKA_SZABLONOW.'przerwa_techniczna.tpl.php');
            }
        }
        
}
?>