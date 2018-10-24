<?php
/**
 * Moduł Zablokowany
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright	Copyright (c) 2004-2007 ARTplus
 * @package		PanelPlus
 * @version		1.0
 */

/**
 * Moduł zablokowany
 *
 */
class ModulZabroniony {

	/**
	 * Konstruktor
	 *
	 */
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
	}

	function Wyswietl() {
		print("<center><br><br><p>Nie masz uprawnień do przeglądania tej strony!<br><br><br></center>");
	}
}
?>
