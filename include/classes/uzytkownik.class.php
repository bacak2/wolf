<?php
/**
 * Zarządzanie użytkownikami aplikacji
 * 
 * @author		Lukasz Piekosz <mentat@mentat.net.pl>; Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright	Copyright (c) 2004-2008 ARTplus
 * @package		PanelPlus
 * @version		1.0
 */

/**
 * Zarządzanie użytkownikami aplikacji
 *
 */
class Uzytkownik {

	/**
	 * Tekst służący do generowania klucza identyfikującego użytkownika.
	 * UWAGA! Po zmianie wartości, założone już konta przestaną funkcjonować!
	 *
	 */
	const HASH = 'as879Ds0ijbhvt0D245kl6tnc79snFwqoc8zaYrwldfs0';
	
	private $Baza = null;
	/**
	 * Nazwa tabeli w bazie danych przechowująca dane o kontach użytkowników.
	 *
	 * @var string
	 */
	private $TabelaUzytkownik;
	/**
	 * Nazwa tabeli w bazie danych przechowująca dane o operacjach które można wykonać poprzez aplikację.
	 *
	 * @var string
	 */
	private $TabelaOperacja;
	/**
	 * Nazwa tabeli w bazie danych przechowująca dane o operacjach które może wyknać poszczególny uzytkownik.
	 *
	 * @var string
	 */
	private $TabelaUprawnieniaUzytkownika;
	/**
	 * Nazwa tabeli w bazie danych przechowująca dane o operacjach które może wyknać poszczególna grupa.
	 * 
	 * @var string
	 */
	private $TabelaUprawnieniaGrupy;
        /**
	 * Nazwa pola zawierającego login
	 *
	 * @var string
	 */
	private $PoleLogin;
        /**
         * Nazwa pola zawierającego hash zabezpieczający
         * @var string
         */
	private $PoleHash;
        /**
         * Nazwa pola zawierającego hasło użytkownika
         * @var <string
         */
	private $PoleHaslo;
        /**
         * Nazwa pola zawierającego id użytkownika
         * @var string
         */
	private $PoleID;
        /**
         * Nazwa pola zawierającego uprawnienia (jeżeli jest zapisane w 1 polu)
         * @var string
         */
	private $PoleUprawnienia;
        /**
         * Nazwa pola zawierającego status użytkownika (aktywny czy nie)
         * @var string
         */
	private $PoleStatus;
        /**
         * Zawiera nazwę poziomu uprawnień
         * @var string
         */
        private $PoziomUprawnien;

        /**
         * Tablica zawierająca dostępne moduły dla użytkownika
         *  @var array
         */
        private $DostepneModuly = array();
        /**
         * Zawiera id zalogowanego użytkownika
         * @var int or false
         */
        private $IdZalogowanegoUzytkownika = false;

        /**
         * Przechowuje czy zalogowany user jest upoważniony do zmiany danych firmy
         * @var boolean
         */
        private $Upowazniony = false;

        /**
         * Tablica przechowująca dane zalogowanego użytkownika
         * @var array
         */
        private $DaneZalogowanego = array();
	/**
	 * Konstruktor
	 *
	 */
	function __construct(&$Baza, $TabelaUzytkownik, $TabelaOperacja = null, $TabelaUprawnieniaUzytkownika = null, $TabelaUprawnieniaGrupy = null) {
		$this->Baza = $Baza;
		$this->TabelaUzytkownik = $TabelaUzytkownik;
		$this->TabelaOperacja = $TabelaOperacja;
		$this->TabelaUprawnieniaUzytkownika = $TabelaUprawnieniaUzytkownika;
		$this->TabelaUprawnieniaGrupy = $TabelaUprawnieniaGrupy;
		$this->PoleLogin = "email";
		$this->PoleHash = "verification_hash";
		$this->PoleHaslo = "encrypted_new_password";
		$this->PoleID = "id";
		$this->PoleStatus = "active";
		$this->PoleUprawnienia = "role";
                if($this->CzyZalogowany()){
                    $this->DaneZalogowanego = $this->Baza->GetData("SELECT $this->PoleID, $this->PoleUprawnienia, entitled, company_id, aspg_can_service, email, expire_date FROM $this->TabelaUzytkownik WHERE $this->PoleID = '{$_SESSION['id_uzytkownik']}'");
                    $this->PoziomUprawnien = $this->DaneZalogowanego[$this->PoleUprawnienia];
                   /* if($this->PoziomUprawnien == 'serwisant' && strtotime($this->DaneZalogowanego['expire_date']) < time()  ){
                        $this->DostepneModuly = array( 
                            "pulpit",
                            "uzytkownik",
                            "firma",
                            "lista_szkolen",
                            );
                    }else{*/
                        $this->DostepneModuly = Role::GetModulAccess($this->Baza, $this->PoziomUprawnien);
                    //}
                    $this->Upowazniony = $this->DaneZalogowanego['entitled'] == 1 ? true : false;
                    $this->IdZalogowanegoUzytkownika = $this->DaneZalogowanego[$this->PoleID];
                }
	}

	/**
	 * Sprawdza czy istnieje użytkownik o podanym loginie i haśle.
	 *
	 * @param string $Login
	 * @param string $Haslo
	 * @return boolean
	 */
	function CzyIstnieje($Login, $Haslo){
		return (1 == $this->Baza->GetValue("SELECT count(*) FROM $this->TabelaUzytkownik WHERE $this->PoleLogin = '$Login' AND $this->PoleHaslo = '$Haslo' AND $this->PoleStatus = '1'"));
	}

	/**
	 * Sprawdza czy istnieje użytkownik o podanym loginie i kluczu hash.
	 *
	 * @param string $Login
	 * @param string $Hash
	 * @return boolean
	 */
	function CzyIstniejeHash($Login, $Hash) {
		return (1 == $this->Baza->GetValue("SELECT count(*) FROM $this->TabelaUzytkownik WHERE $this->PoleLogin = '$Login' AND $this->PoleHash = '$Hash' AND $this->PoleStatus = '1'"));
	}

	/**
	 * Wyznacza klucz hash użytkownika na podstawie loginu i stałej
	 *
	 * @param string $Login
	 * @return string
	 */
	function WyznaczHash($Login) {
		return md5($Login.self::HASH);
	}

	/**
	 * Sprawdza czy aktualny użytkownik jest zalogowany.
	 *
	 * @return boolean
	 */
	function CzyZalogowany() {
		if (isset($_SESSION['login']) && isset($_SESSION['hash']) && ($_SESSION['hash'] == $this->WyznaczHash($_SESSION['login'])) && $this->CzyIstniejeHash($_SESSION['login'], $_SESSION['hash'])) {
                    return true;
		}
		return false;
	}

	/**
	 * Loguje użytkownika do aplikacji
	 *
	 * @param string $Login
	 * @param string $Haslo
	 * @return boolean
	 */
	function Zaloguj($Login, $Haslo) {
		if($Login != '' && $Haslo != '' && $this->CzyIstnieje($Login, md5($Haslo))) {
                    $_SESSION['login'] = $Login;
                    $_SESSION['hash'] = $this->WyznaczHash($Login);
                    $this->DaneZalogowanego = $this->Baza->GetData("SELECT * FROM $this->TabelaUzytkownik WHERE $this->PoleLogin = '{$_SESSION['login']}' AND $this->PoleHash = '{$_SESSION['hash']}'");
                    $_SESSION['id_uzytkownik'] = $this->DaneZalogowanego[$this->PoleID];
                    $this->IdZalogowanegoUzytkownika = $this->DaneZalogowanego[$this->PoleID];
                    $this->PoziomUprawnien = $this->DaneZalogowanego[$this->PoleUprawnienia];
                    /*if($this->PoziomUprawnien == 'serwisant' && strtotime($this->DaneZalogowanego['expire_date']) < time()  ){
                        $this->DostepneModuly = array( 
                            "pulpit",
                            "uzytkownik",
                            "firma",
                            "lista_szkolen",
                            );
                    }else{*/
                        $this->DostepneModuly = Role::GetModulAccess($this->Baza, $this->PoziomUprawnien);
                    //}
                    $this->Upowazniony = $this->DaneZalogowanego['entitled'] == 1 ? true : false;
                    $this->OdnotujZalogowanie($this->DaneZalogowanego[$this->PoleID]);
                    $this->UstawDomyslnyKoszyk($this->DaneZalogowanego[$this->PoleID]);
                    return true;
		}
		return false;
	}

        /**
         * Zapisuje datę ostatniego zalogowania
         * @param integer $UserID
         */
        function OdnotujZalogowanie($UserID){
            $Save['last_sign_in_at'] = date("Y-m-d H:i:s");
            $Save['last_sign_in_ip'] = $_SERVER['REMOTE_ADDR'];
            $Zapytanie = $this->Baza->PrepareUpdate($this->TabelaUzytkownik, $Save, array($this->PoleID => $UserID));
            $this->Baza->Query($Zapytanie);
        }

        function UstawDomyslnyKoszyk($UserID){
            if($this->IsAdmin() == false){
                $Szkic = $this->Baza->GetValue("SELECT id FROM purchases WHERE user_id = '$UserID' AND status = 0");
                if($Szkic){
                    $_SESSION['basket-picked-order'] = $Szkic;
                    return true;
                }
            }
            return false;
        }

	/**
	 * Wylogowuje użytkownika z aplikacji.
	 *
	 */
	function Wyloguj()
	{
		$_SESSION = array();
		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time()-42000, '/');
		}
		session_destroy();
	}

	/**
	 * Sprawdza czy aktualny użytkownik posiada podane uprawnienie.
	 *
	 * @param string $Uprawnienie
	 * @return boolean
	 */
	function SprawdzUprawnienie($Uprawnienie, $TablicaUprawnien) {
            
            if($this->PoziomUprawnien == "administrator" || (count($TablicaUprawnien) == 1 && $TablicaUprawnien[0] == "*")){
                    return true;
            }else{
                    return in_array($Uprawnienie, $TablicaUprawnien);
            }
	}
	
	/**
	 * Dodaje użytkownika do aplikacji.
	 *
	 * @param array Dane z formularza
	 * @return boolean
	 */
	function Dodaj($Dane) {
                $UprawnieniaKolumn = $Dane['uprawnienia_kolumn'];
                unset($Dane['uprawnienia_kolumn']);
		$Dane[$this->PoleHaslo] = md5($Dane['pass']);
                $Dane['haslo'] = $Dane['pass'];
		unset($Dane['pass']);
		$Dane[$this->PoleHash] = $this->WyznaczHash($Dane[$this->PoleLogin]);
                $Dane['uprawnienia'] = $this->ZapiszUprawnienia($Dane['uprawnienia']);
		$Zapytanie = $this->Baza->PrepareInsert($this->TabelaUzytkownik, $Dane);
		if($this->Baza->Query($Zapytanie)) {
                    $ID = $this->Baza->GetLastInsertId();
                    $this->ZapiszUprawnieniaKolumn($UprawnieniaKolumn, $ID);
                    return true;
		}
	}
	
	function Edytuj($ID, $Dane) {
            if(isset($Dane['pass']) && $Dane['pass'] != ""){
                $Dane[$this->PoleHaslo] = md5($Dane['pass']);
                $Dane['haslo'] = $Dane['pass'];
            }
            unset($Dane['pass']);
            $UprawnieniaKolumn = $Dane['uprawnienia_kolumn'];
            unset($Dane['uprawnienia_kolumn']);
            $Dane[$this->PoleHash] = $this->WyznaczHash($Dane[$this->PoleLogin]);
            $Dane['uprawnienia'] = $this->ZapiszUprawnienia($Dane['uprawnienia']);
            $Zapytanie = $this->Baza->PrepareUpdate($this->TabelaUzytkownik, $Dane, array($this->PoleID => $ID));
            if($this->Baza->Query($Zapytanie)) {
                $this->ZapiszUprawnieniaKolumn($UprawnieniaKolumn, $ID);
                return true;
            }
            return false;
	}

        function ZapiszUprawnienia($Upr){
            $Uprawnienia = implode(',', $Upr['nad']);
            if(isset($Upr['pod'])){
                foreach($Upr['pod'] as $Param => $Pody){
                    foreach($Pody as $Pod){
                        if(!in_array($Param, $Upr['nad'])){
                            $Uprawnienia .= ($Uprawnienia != "" ? "," : "").$Param;
                        }
                        $Uprawnienia .= ($Uprawnienia != "" ? "," : "").$Pod;
                    }
                }
            }
            return $Uprawnienia;
        }

        function ZapiszUprawnieniaKolumn($UprawnieniaKolumn, $ID){
            $this->Baza->Query("DELETE FROM orderplus_uzytkownik_tabela_rozliczen WHERE id_uzytkownik = '$ID'");
            foreach($UprawnieniaKolumn as $Widok => $Kolumny){
                foreach($Kolumny as $Kolumna){
                    $Zap['id_uzytkownik'] = $ID;
                    $Zap['tabela_widok'] = $Widok;
                    $Zap['tabela_kolumna'] = $Kolumna;
                    $Zapytanie = $this->Baza->PrepareInsert("orderplus_uzytkownik_tabela_rozliczen", $Zap);
                    $this->Baza->Query($Zapytanie);
                }
            }
        }

	/**
	 * Zmienia hasło podanego użytkownika.
	 *
	 * @param integer $ID
	 * @param string $HasloNowe
	 * @param string $HasloNowePowtorzenie
	 * @return boolean
	 */
	function ZmienHaslo($ID, $HasloNowe, $HasloNowePowtorzenie) {
		if(($HasloNowe != '') && ($HasloNowe == $HasloNowePowtorzenie)) {
			if($this->Baza->Query("UPDATE $this->TabelaUzytkownik SET $this->PoleHaslo='".md5($HasloNowe)."' WHERE $this->PoleID='$ID'")) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Usuwa konto użytkownika z aplikacji.
	 *
	 * @param integer $ID
	 * @return boolean
	 */
	function Usun($ID) {
		if($this->Baza->Query("DELETE from $this->TabelaUzytkownik WHERE $this->PoleID='$ID'")) {
			return true;
		}
		return false;
	}

        /**
         * Blokuje dostęp użytkownikowi
         * @param integer $ID
         * @return boolean
         */
	function Blokuj($ID){
		if($this->Baza->Query("UPDATE $this->TabelaUzytkownik SET $this->PoleStatus = 1-$this->PoleStatus WHERE $this->PoleID = '$ID'")){
			return true;
		}
		return false;
	}

	/**
         * Sprawdza czy użytkownik jest zablokowany
         * @param int $ID
         * @return boolean
         */
	function CzyZablokowany($ID){
		if($this->Baza->GetValue("SELECT $this->PoleStatus FROM $this->TabelaUzytkownik WHERE $this->PoleID = '$ID'") == 1){
			return true;
		}
		return false;
	}

        /**
         * Zwraca tablicę dostępnych modułów dla użytkownika
         * @return array
         */
	function ZwrocTabliceUprawnien(){
		return $this->DostepneModuly;
	}

        /**
         * Zwraca id zalogowanego użytkownika
         * @return int
         */
	function ZwrocIdUzytkownika() {
            return $this->IdZalogowanegoUzytkownika;
	}

        /**
         * Sprawdza czy hasło i jego powtórzenie są zgodne
         * @param string $HasloNowe
         * @param string $HasloNowePowtorzenie
         * @return boolean
         */
	function SprawdzPowtorzoneHaslo($HasloNowe, $HasloNowePowtorzenie){
		if(($HasloNowe != '') && ($HasloNowe == $HasloNowePowtorzenie)) {
			return true;
		}
		return false;		
	}

        /**
         * Sprawdza czy wprowadzony login nie istnieje już w bazie
         * @param string $Login
         * @param string $ID
         * @return boolean
         */
	function CzyNieZdublowanoLoginu($Login, $ID = null){
		if($this->Baza->GetValue("SELECT COUNT(*) FROM $this->TabelaUzytkownik WHERE $this->PoleLogin='$Login' ".(!is_null($ID) ? "AND $this->PoleID != '$ID'" : "")."") == 0) {
			return true;
		}
		return false;
	}

        /**
         * Zwraca id ostatnio dodanego użytkownika
         * @return int
         */
        function GetLastUserId(){
            return $this->Baza->GetLastInsertId();
        }

        /**
         * Sprawdza czy zalogowany jest administratorem
         * @return boolean
         */
        function IsAdmin(){
            if($this->PoziomUprawnien == "administrator"){
                return true;
            }
            return false;
        }

        /**
         * Sprawdza czy zalogowany użytkownik ma uprawnienie do zmiany danych firmy
         * @return boolean
         */
        function CzyUprawniony(){
        //na razie każdy
	    return true;
            /*if($this->Upowazniony || $this->IsAdmin()){
                return true;
            }
            return false;*/
        }

        /**
         * Zwraca info z danych zalogowanego usera
         * @param string $Pole - nazwa pola jakie chcemy zobaczyć
         * @return string or false
         */
        function ZwrocInfo($Pole){
            if(isset($this->DaneZalogowanego[$Pole])){
                return $this->DaneZalogowanego[$Pole];
            }else{
	      $value = $this->Baza->GetValue("SELECT {$Pole} FROM {$this->TabelaUzytkownik} WHERE {$this->DaneZalogowanego[$this->PoleID]}");
	      if($value){
		return $value;
	      }
            }
            return false;
        }

        /**
         * Zwraca czy user ma uprawnienia do wykonywania przeglądów i napraw gwarancyjnych
         * @return bool
         */
        function IsWarrantyService(){
            if($this->IsAdmin() || $this->ZwrocInfo("role") == "serwisant_g" || $this->IsASPGWithService()){
                return true;
            }
            return false;
        }

        /**
         * Sprawdza czy user jest Serwisantem pogwarancyjnym z możliwością wykonywania przeglądów gwarancyjnych
         */
        function IsASPGWithService(){
            if(($this->ZwrocInfo("role") == "serwisant" && $this->ZwrocInfo("aspg_can_service") == 1)){
                return true;
            }
            return false;
        }
        
        function PrzypomnijHaslo($s_email){
            if(Usefull::CheckEMail($s_email)){
               $s_email = mysql_real_escape_string($s_email);
               $User = $this->Baza->GetData("SELECT id, name, province_id, province_id, firstname, surname, role, email
                                            FROM users WHERE email LIKE '$s_email' LIMIT 1");
               if( is_numeric($User['id']) ){
                   $reset_password_token = Usefull::CreateHash( $User['name'].$User['firstname'].self::HASH.$User['role'].$User['surname'].time(false) ,'sha1');
                   $link = 'https://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']."?resetpassword=".$reset_password_token;
                   $SendDate = date("Y-m-d H:i:s");
                   $NewMail = new MailSMTP();
                   $this->Baza->Query("UPDATE users SET reset_password_token = '{$reset_password_token}', reset_password_sent_at = '$SendDate'
                       WHERE id = '{$User['id']}'");
                   $logo = base64_encode(file_get_contents(SCIEZKA_OGOLNA.'images/logo-ferolli.jpg'));
                   $type = 'jpg';
                   $TemplateResetPasswordContentMail = file_get_contents(SCIEZKA_SZABLONOW."mail/reset-password-mail.tpl.php");
                   $pattern = array( "/--LOGO--/", "/--TYPE--/", "/--RESETPASSWORDTOKEN--/", "/--SENDDATE--/", "/--USERNAME--/", "/--LINK--/");
                   $replacement = array($logo, $type, $reset_password_token, $SendDate, $User['firstname']." ".$User['surname'], $link);
                   $ResetPasswordContentMail = preg_replace( $pattern, $replacement, $TemplateResetPasswordContentMail);
                   $Tytul = "Przypomnienie hasła do aplikacji";
                   if( $NewMail->SendEmail( $User['email'], $Tytul, $ResetPasswordContentMail) ){
                        $SendMailStatus = "Na podany adres e-mail został wysłany link do zmiany hasła";
                   }else{
                       $SendMailStatus = $NewMail->GetErrors()."<br/>W razie problemów proszę o kontakt z serwisem";
                   }
                  return array( "func"=>"ShowKomunikatOK",
                        "tresc"=>$SendMailStatus);
               }else{
                    return array( "func"=>"ShowKomunikatError",
                        "tresc"=>"Nie znaleziono takiego adresu e-mail w bazie kont.");   
               }
               return 1;
               //array( "func"=>"ShowKomunikatOK", "tresc"=>"id = $id<br>SELECT id FROM users WHERE emial LIKE '$s_email'");
            }else{
                return array( "func"=>"ShowKomunikatError",
                    "tresc"=>"Nieprawidłowy adres e-mail");
            }
                
//                reset_password_token
//                reset_password_sent_at
        }
        
        function ResetHaslo($resetpassword, $token, $password){
            $password = md5(mysql_real_escape_string($password));
            if($this->Baza->Query("UPDATE $this->TabelaUzytkownik SET encrypted_new_password= '{$password}' WHERE reset_password_token = '$resetpassword' AND sha1(reset_password_sent_at) = '$token'")){
                return true;
            }else{
                return false;
            }
        }
        
        function CreateToken($GetColumn, $WhereColumn, $WhereValue ){
            $val = $this->Baza->GetValue("SELECT {$GetColumn} FROM {$this->TabelaUzytkownik} WHERE {$WhereColumn}='{$WhereValue}' LIMIT 1");
            if( $val != false)
                return Usefull::CreateHash($val, 'sha1');
            else
                return false;
        }
        function FirmaRMC($ID){
            return $this->Baza->GetValue("SELECT IF(role LIKE 'rmc', 1, 0) as rmc FROM users u WHERE u.id = '$ID'");
        }  
        
}
?>
