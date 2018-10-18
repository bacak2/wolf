<?php
/**
 * Role
 *
 * @author		Michal Bugański <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2012
 * @package		Panelplus 
 * @version		1.0
 */
class Role {
    /**
     * konstruktor
     */
    function  __construct(){

    }

    /**
     * Pobiera tablicę z rolami w jakich mogą występować użytkownicy
     * @return array
     */
    static function GetRoles($Baza){
        return $Baza->GetOptions("SELECT role_id, role_name FROM user_roles ORDER BY role_id ASC");
    }

    /**
     * Zwraca moduły dostępne dla podanej roli
     * @param DBMySQL $Baza - ref do obiektu bazy danych
     * @param string $Role - szukana rola
     * @return array
     */
    static function GetModulAccess($Baza, $Role = false){
        if($Role == false){
            return array();
        }
        
        $Access = $Baza->GetValue("SELECT role_modul_access FROM user_roles WHERE role_id = '$Role'");
        return explode(",", $Access);
    }
}
?>
