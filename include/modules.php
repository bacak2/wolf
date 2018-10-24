<?php

    $Panel->DodajModul('ModulUrzadzenia', 'urzadzenia', 'Urządzenia');
    $Panel->DodajModul('ModulUrzadzeniaNazwa', 'urzadzenia_nazwa', 'Nazwa', 'urzadzenia', true);
    $Panel->DodajModul('ModulCzesci', 'czesci', 'Części');
    $Panel->DodajModul('ModulNumerySeryjne', 'numery_seryjne', 'Numery Seryjne');
    $Panel->DodajModul('ModulZestawienieZlecen', 'zlecenia', 'Zlecenia');
    $Panel->DodajModul('ModulZamowieniaUzytkownicy', 'zamowieniauzytkownicy', 'Zamówienia');
    $Panel->DodajModul('ModulZamowieniaFirmy', 'zamowieniafirmy', 'Firmy', 'zamowieniauzytkownicy', true);
    $Panel->DodajModul('ModulKategorie', 'kategorie', 'Kategorie');
    $Panel->DodajModul('ModulFirmy', 'firmy', 'Firmy');
    $Panel->DodajModul('ModulDaneFirmy', 'firma', 'Firmy', null, true);
    $Panel->DodajModul('ModulUzytkownicy', 'uzytkownicy', 'Użytkownicy');
    $Panel->DodajModul('ModulProfilUzytkownika', 'uzytkownik', 'Użytkownicy', null, true);
    $Panel->DodajModul('ModulUstawienia', 'ustawienia', 'Ustawienia');
    $Panel->DodajModul('ModulGrupyCenowe', 'grupy_cenowe', 'Grupy Cenowe', 'ustawienia', true );
    $Panel->DodajModul('ModulKategorieWymagan', 'kategorie_wymagan', 'Kategorie Wymagan', 'ustawienia', true );
    $Panel->DodajModul('ModulWymagania', 'wymagania', 'Wymagania', 'ustawienia', true );
    $Panel->DodajModul('ModulKategorieCzesci', 'kategorie_czesci', 'Kategorie cześci', 'ustawienia', true );
    $Panel->DodajModul('ModulRMC', 'rmc', 'RMC', 'ustawienia', true );
    $Panel->DodajModul('ModulKoszyk', 'koszyk', 'Koszyk', null, true);
   
?>
