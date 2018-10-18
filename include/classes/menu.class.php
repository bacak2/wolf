<?php
/**
 * Obsługa menu aplikacji
 * 
 * @author		Lukasz Piekosz <mentat@mentat.net.pl>; Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright	Copyright (c) 2004-2007 ARTplus
 * @package		PanelPlus
 * @version		1.0
 */

/**
 * Obsługa menu aplikacji
 *
 */
class Menu {

	/**
	 * Wartość parametru oznaczająca korzeń drzewa modułów.
	 *
	 */
	const KORZEN = '-=KORZEN=-';
	/**
	 * Tablica asocjacyjna zawierająca parametry podrzędnych modułu do modułu o parametrze równym kluczowi.
	 *
	 * @var array
	 */
	private $Podrzedne = array();
	/**
	 * Tablica asocjacyjna zawierająca parametr modułu nadrzędnego do modułu o parametrze równym kluczowi.
	 *
	 * @var array
	 */
	private $Nadrzedne = array();
	/**
	 * Tablica zawierająca parametry modułów ukrytych.
	 *
	 * @var array
	 */
	private $Ukryte = array();
	/**
	 * Tablica asocjacyjna zawierająca nazwy modułów do wyświetlenia dla poszczególnych parametrów.
	 *
	 * @var array
	 */
	private $Nazwy = array();
	/**
	 * Parametr aktywnego modułu.
	 *
	 * @var string
	 */
	private $Modul = null;
	/**
	 * Wykonywana akcja.
	 *
	 * @var string
	 */
	private $Akcja = 'akcja';
	/**
	 * Tablica zawierająca aktywne moduły od korzenia w głąb.
	 *
	 * @var array
	 */
	private $Sciezka = array(self::KORZEN);
	private $TablicaUprawnien = array();
	/**
	 * Konstruktor
	 *
	 */
	function __construct() {
	}

	/**
	 * Wyznacza tablicę reprezentującą drogę od korzenia menu do wybranego modułu.
	 * Jeżeli wybrany moduł posiada podmoduły do ścieżki dodawane są pierwsze elementy aż do osiągnięcia krańcowego elementu.
	 *
	 */
	function WyznaczSciezke() {
		if(isset($_REQUEST['modul'])) {
                    $this->Modul = $_REQUEST['modul'];
		}elseif(!empty($this->Nadrzedne)){
                    $Keys = array_keys($this->Nadrzedne);
                    $this->Modul = $Keys[0];
                }else{
                    $this->Modul = null;
                }
		if(key_exists($this->Modul, $this->Nadrzedne)){
			if (isset($_REQUEST['akcja']) && $_REQUEST['akcja'] != '') {
				$this->Akcja = $_REQUEST['akcja'];
			}
			while (isset($this->Podrzedne[$this->Modul]) && is_array($this->Podrzedne[$this->Modul]) && count($this->Podrzedne[$this->Modul])) {
				$i = 0;
				while ($i < count($this->Podrzedne[$this->Modul])) {
					if (!in_array($this->Podrzedne[$this->Modul][$i], $this->Ukryte)) {
						$this->Modul = $this->Podrzedne[$this->Modul][$i];
						break;
					}
					$i++;
				}
				if ($i >= count($this->Podrzedne[$this->Modul])) {
					break;
				}
			}
			$Sciezka = array();
			$Element = $this->Modul;
			while ($Element != self::KORZEN) {
					$Sciezka[] = $Element;
					$Element = $this->Nadrzedne[$Element];
			}
			$this->Sciezka = array_merge($this->Sciezka, array_reverse($Sciezka));
		}
	}

	/**
	 * Zwraca aktywny moduł
	 *
	 * @return string|null
	 */
	function AktywnyModul() {
		return $this->Modul;
	}

	/**
	 * Zwraca aktualnie wykonywaną akcję
	 *
	 * @return unknown
	 */
	function WykonywanaAkcja() {
		return $this->Akcja;
	}

	/**
	 * Dodaje moduł do menu.
	 *
	 * @param string $parametr
	 * @param string $nazwa
	 * @param string $nadrzedny
	 */
	function DodajModul($parametr, $nazwa, $nadrzedny = null, $ukryty = false) {
		if (!isset($nadrzedny)) {
			$nadrzedny = self::KORZEN;
		}
		$this->Nazwy[$parametr] = $nazwa;
		$this->Podrzedne[$nadrzedny][] = $parametr;
		$this->Nadrzedne[$parametr] = $nadrzedny;
		if ($ukryty) {
			$this->Ukryte[] = $parametr;
		}
	}

	/**
	 * Wyświetla poziomą linię podmodułów dla modułu o podanym parametrze.
	 * Wybrany moduł jest wyróżniany.
	 *
	 * @param string $parametr
	 * @param string $wybrany
	 */
	function WyswietlPodmenu($parametr, $wybrany = null, $uprawnienia) {
            ?>
            <table class="width_max">
                <tr>
                    <td class="main-menu">
                        <table class="width_max">
                            <tr>
                                <?php
                                for ($i = 0; $i < count($this->Podrzedne[$parametr]); $i++) {
                                        if(!in_array($this->Podrzedne[$parametr][$i], $this->Ukryte) && $this->SprawdzUprawnienie($this->Podrzedne[$parametr][$i],$uprawnienia)){
                                                if($this->Podrzedne[$parametr][$i] == $wybrany) {
                                ?>
                                <td class="menu-select"><a href='?modul=<?php echo($this->Podrzedne[$parametr][$i]); ?>'><div style='white-space: nowrap;'><?php echo($this->Nazwy[$this->Podrzedne[$parametr][$i]]); ?></div></a></td>
                            <?php } else { ?>
                                <td class="menu-unselect"><a href='?modul=<?php echo($this->Podrzedne[$parametr][$i]); ?>'><div style='white-space: nowrap;'><?php echo($this->Nazwy[$this->Podrzedne[$parametr][$i]]); ?></div></a></td>
                            <?php } ?>
                                <td class="menu-break">&nbsp;</td>
                            <?php }
                                } 
                            ?>
                            </tr>
			</table>
                    </td>
                </tr>
            </table>
        <?php
        }

	/**
	 * Wyświetla poziome menu.
	 *
	 */
	function Wyswietl($TablicaUprawnien) {
		if (!isset($this->Podrzedne[self::KORZEN]) || !count($this->Podrzedne[self::KORZEN])) {
			return;
		}
		$this->WyswietlPodmenu(self::KORZEN, ($this->Modul ? $this->Sciezka[1] : null), $TablicaUprawnien);
		for ($i = 1; $i < count($this->Sciezka) - 1; $i++) {
			if (!in_array($this->Sciezka[$i + 1], $this->Ukryte) && $this->SprawdzUprawnienie($this->Sciezka[$i],$TablicaUprawnien)) {
				$this->WyswietlPodmenu($this->Sciezka[$i], $this->Sciezka[$i + 1], $TablicaUprawnien);
			}
		}
	}
	
        /**
         *  Wyświetla Moduł w poziomie na ekranie startowym
         * @param string $parametr
         * @param array $uprawnienia - tablica uprawnień
         */
	function WyswietlModul($parametr,$uprawnienia) {
		if($this->SprawdzUprawnienie($parametr,$uprawnienia)){
?>
			<table border='0' cellspacing='0' cellpadding='0'>
				<tr>
					<td width='35'><img src='images/button_in.gif' alt='' height='23' width='23' border='0'></td>
					<td><a href='<?php echo("?modul=$parametr"); ?>' class='se'><?php echo($this->Nazwy[$parametr]); ?></a></td>
					<td align='right' valign='middle' width='43'><img src='images/button_out.gif' alt='' height='28' width='46' border='0'></td>
				</tr>
			</table><br>
<?php
		}
	}

        /**
         * Wyświetla poziome menu na ekranie startowym
         * @param array $TablicaUprawnien
         */
	function WyswietlModuly($TablicaUprawnien) {
		if (!count($this->Podrzedne[self::KORZEN])) {
			return;
		}
		for ($i = 0; $i < count($this->Podrzedne[self::KORZEN]); $i++) {
			$this->WyswietlModul($this->Podrzedne[self::KORZEN][$i],$TablicaUprawnien);
		}
	}

	/**
         * Zwraca tablicę zawierającą ścieżkę do aktywnego modułu
         * @return array $Wynik
         */

	function ZwrocSciezke() {
		$Wynik = array();
		for ($i = 1; $i < count($this->Sciezka); $i++) {
			$Wynik[] = $this->Nazwy[$this->Sciezka[$i]];
		}
		return $Wynik;
	}
	/**
         * Zwraca tablicę wszystkich modułów
         * @return array
         */
	function ZwrocTabliceZakladek(){
            $ReturnTable = array();
            foreach($this->Nazwy as $par => $name){
                if(!in_array($par, $this->Ukryte)){
                    $ReturnTable[$par] = $name;
                }
            }
            return $ReturnTable;
	}

	/**
         * Zwraca tablicę modułów nadrzędnych
         * @return array
         */
	function ZwrocTabliceNadrzednych(){
		$TabZwroc = array();
		foreach($this->Nadrzedne as $modul => $nadrzedny){
			if($nadrzedny == "-=KORZEN=-"){
				$TabZwroc[$modul] = 1;
			}
		}
		return $TabZwroc;
	}

        /**
         * Sprawdza czy moduł jest dostępny dla użytkownika
         * @param string $Uprawnienie - parametr modułu
         * @param array $TablicaUprawnien - tablica zawierająca wszyskie dostepne moduły
         * @return bool
         */
	function SprawdzUprawnienie($Uprawnienie, $TablicaUprawnien) {
		if(count($TablicaUprawnien) == 1 && $TablicaUprawnien[0] == "*"){
			return true;
		}else{
			return in_array($Uprawnienie, $TablicaUprawnien);
		}
	}

        /**
         * Wczytuje tablicę uprawnień do zmiennej
         * @param array $TabUpr
         */
	function WczytajTabliceUprawnien($TabUpr){
		$this->TablicaUprawnien = $TabUpr;
	}
}
?>
