<?php
/**
 * Moduł funkcji użytecznych
 *
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright       Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class UsefullBase{

	function __construct() {
	}

        /**
         * Wyciąga wszystkich użytkowników w formie tablicy
         * @param DBMySQL $Baza
         * @return array
         */
        function GetAllUsers($Baza){
            return $Baza->GetOptions("SELECT id, CONCAT(firstname,' ',surname) as name FROM users ORDER BY name ASC");
        }

        /**
         * Wyciąga wszystkie firmy w formie tablicy
         * @param DBMySQL $Baza
         * @return array
         */
        function GetAllCompanies($Baza){
            return $Baza->GetOptions("SELECT id, simplename FROM companies ORDER BY name");
        }

}
?>
