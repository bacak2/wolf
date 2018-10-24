<?php
$PlikiKlas = array(
//klasy
        'Panel' => SCIEZKA_KLAS.'panel.class.php',
	'DBConnectionSettings' => SCIEZKA_KLAS.'mysql.class.php',
	'DBMySQL' => SCIEZKA_KLAS.'mysql.class.php',
	'DBQueryResult' => SCIEZKA_KLAS.'mysql.class.php',
	'Uzytkownik' => SCIEZKA_KLAS.'uzytkownik.class.php',
	'Menu' => SCIEZKA_KLAS.'menu.class.php',
//formularze
        'Formularz' => SCIEZKA_FORMULARZY.'formularz.class.php',
	'FormularzSimple' => SCIEZKA_FORMULARZY.'forms.class.php',
//moduly
        'ModulBazowy' => SCIEZKA_MODULOW.'modul_bazowy.class.php',
	'ModulPodrzedny' => SCIEZKA_MODULOW.'modul_podrzedny.class.php',
	'ModulPusty' => SCIEZKA_MODULOW.'modul_pusty.class.php',
	'ModulZabroniony' => SCIEZKA_MODULOW.'modul_zabroniony.class.php',
        'Usefull' => SCIEZKA_MODULOW.'usefull.class.php',
        'UsefullBase' => SCIEZKA_MODULOW.'usefull-base.class.php',
        'UsefullTime' => SCIEZKA_MODULOW.'usefull-time.class.php',
        'Download' =>  SCIEZKA_MODULOW.'download.class.php',
        'ModulFirmy' => SCIEZKA_MODULOW.'firmy.class.php',
        'ModulDaneFirmy' => SCIEZKA_MODULOW.'firmy_dane.class.php',
        'ModulUzytkownicy' => SCIEZKA_MODULOW.'uzytkownicy.class.php',
        'ModulProfilUzytkownika' => SCIEZKA_MODULOW.'uzytkownicy_profil.class.php',
        'ModulUrzadzenia' => SCIEZKA_MODULOW.'urzadzenia.class.php',
        'ModulUrzadzeniaNazwa' => SCIEZKA_MODULOW.'urzadzenianazwa.class.php',
        'ModulCzesci' => SCIEZKA_MODULOW.'czesci.class.php',
        'ModulNumerySeryjne' => SCIEZKA_MODULOW.'numery_seryjne.class.php',
        'ModulZamowienia' => SCIEZKA_MODULOW.'zamowienia.class.php',
        'ModulZamowieniaUzytkownicy' => SCIEZKA_MODULOW.'zamowieniauzytkownicy.class.php',
        'ModulZamowieniaFirmy' => SCIEZKA_MODULOW.'zamowieniafirmy.class.php',
        'ModulKoszyk' => SCIEZKA_MODULOW.'koszyk.class.php',
        'ModulZestawienieZlecen' => SCIEZKA_MODULOW.'zlecenia.class.php',
        'ModulKategorie' => SCIEZKA_MODULOW.'kategorie.class.php',   
    
//Konfiguracja
    'ModulUstawienia' => SCIEZKA_MODULOW.'ustawienia.class.php',
    'ModulGrupyCenowe' => SCIEZKA_MODULOW.'grupy_cenowe.class.php',
    'ModulKategorieCzesci' => SCIEZKA_MODULOW.'kategorie_czesci.class.php',
    'ModulKategorieWymagan' => SCIEZKA_MODULOW.'kategorie_wymagan.class.php',
    'ModulWymagania' => SCIEZKA_MODULOW.'wymagania.class.php',
    'ModulRMC'=> SCIEZKA_MODULOW.'rmc.class.php',
    
//generator plików EXCEL 
        'ModulGenerateExcel' =>  SCIEZKA_MODULOW.'generate_excel.class.php',
//Zapytania
        'Wojewodztwa' => SCIEZKA_QUERIES.'wojewodztwa.class.php',
        'Role' => SCIEZKA_QUERIES.'role.class.php',
//Zewnętrzne klasy
        'MailSMTP' => SCIEZKA_SMTPPHPMAILER.'mail-smtp.class.php',
//        'PHPExcel' => SCIEZKA_PHPEXCEL.'PHPExcel.php', // musi być includowany wewnątrz głównej klasy a nie przez autolouda
    
);

function __autoload($NazwaKlasy) {
	global $PlikiKlas;
        if (file_exists($PlikiKlas[$NazwaKlasy])) {
            require_once($PlikiKlas[$NazwaKlasy]);
	}
	else {
            require_once($PlikiKlas['ModulPusty']);
            error_log("WARNING: Nie znaleziono modulu $NazwaKlasy: Plik: {$PlikiKlas[$NazwaKlasy]}");
            eval("class $NazwaKlasy extends ModulPusty {}");
	}
}
?>