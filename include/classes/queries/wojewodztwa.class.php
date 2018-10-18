<?php
/**
 * Województwa
 *
 * @author		Michal Bugański <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2012
 * @package		Panelplus 
 * @version		1.0
 */
class Wojewodztwa {
    /**
     * konstruktor
     */
    function  __construct(){

    }

    /**
     * Pobiera z bazy województwa w formie tablicy: id wojewodztwa => nazwa
     * @param DBMysql $Baza
     * @return array
     */
    static function GetProvinces($Baza){
        return $Baza->GetOptions("SELECT id, name FROM provinces ORDER BY name ASC");
    }
}
?>
