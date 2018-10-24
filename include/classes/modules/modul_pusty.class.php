<?php
/**
 * Obsługa pustej strony
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright	Copyright (c) 2004-2008 ARTplus
 * @package		PanelPlus
 * @version		1.0
 */

/**
 * Moduł pusty
 *
 */
class ModulPusty {
	private $Uprawnienia;
	
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
	}

	function Wyswietl($Akcja) {
		print("<center><br><br><p><b>ART</b>plus - projekty internetowe<br><br><br></center>");
	}
	
	function UstalUprawnienia($TablicaUprawnien){
		$this->Uprawnienia = $TablicaUprawnien;
	}
        
        function GetIDName(){
            return array();
        }
}
?>
