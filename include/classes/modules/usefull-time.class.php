<?php
/**
 * Funkcje pomocne na czasie i datach
 *
 * @author		Michal Bugański <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2012
 * @package		Panelplus 
 * @version		1.0
 */
class UsefullTime {

    /**
     * konstruktor
     */
    function  __construct(){

    }

    /**
     * Oblicza dni pomiędzy dwoma datami
     * @param date $DataStart - pierwsza data w formacie RRRR-MM-DD
     * @param date $DataEnd - druga data w formacie RRRR-MM-DD
     * @return integer ilość pełnych dni
     */
    static function ObliczDniPomiedzy($DataStart, $DataEnd){
		if($DataStart > $DataEnd){
			list($DataStart, $DataEnd) = array($DataEnd, $DataStart);
		}
		$Data2 = explode("-",$DataStart);
	    $date2 = mktime(0,0,0,$Data2[1],$Data2[2],$Data2[0]);
		$Data1 = explode("-",$DataEnd);
	    $date1 = mktime(0,0,0,$Data1[1],$Data1[2],$Data1[0]);
		$dateDiff = $date1 - $date2;
	    $fullDays = floor($dateDiff/(60*60*24));
	    return $fullDays;
	}

    /**
     * Sprawdza czy podany dzień to dzień weekendu
     * @param date $_date - data w formacie RRRR-MM-DD
     * @return boolean
     */
    static function isWeekendDay($_date){
       $_temp_date=explode('-',$_date);
        $_mktime=mktime(0,0,0,$_temp_date[1],$_temp_date[2],$_temp_date[0]);
        if(date('N',$_mktime)==6 || date('N',$_mktime)==7)
        {	/*dzień to sobota lub niedziela*/
                return true;
        }

        return false;
    }

    /**
     * Sprawdza poprawność daty
     * @param date $Data - data w formacie RRRR-MM-DD
     * @return boolean
     */
    static function CheckDate($Data){
        $date = explode("-", $Data);
        if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',$Data) && checkdate($date[1],$date[2],$date[0])){
            return true;
        }
        return false;
    }

    /**
     * Sprawdza poprawność daty i czasu
     * @param datetime $Data - data w formacie RRRR-MM-DD HH:MM:SS
     * @return boolean
     */
    static function CheckDateTime($Data){
        $datetime = explode(" ", $Data);
        $date = explode("-", $datetime[0]);
        $time = explode(":", $datetime[1]);
        if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/',$Data) && checkdate($date[1],$date[2],$date[0]) && $time[0] < 24 && $time[1] < 60 && $time[2] < 60){
            return true;
        }
        return false;
    }

    /**
     * Zwraca tekst ile czasu minęło od czynności
     * @param datetime $Time - data w formacie RRRR-MM-DD HH:MM:SS
     * @return string
     */
    static function TimeAgo($Time){
        $Exp = explode(" ", $Time);
        $Date = explode("-", $Exp[0]);
        $Hours = explode(":", $Exp[1]);
        $atime = mktime($Hours[0], $Hours[1], $Hours[2], $Date[1], $Date[2], $Date[0]);
        $dzis = time();
        $seconds = $dzis - $atime;
        $minutes = floor($seconds/60);
        $hours = floor($minutes/60);
        $days = floor($hours/24);
        $months = floor($days/30);
        $years = floor($months/12);
        if($years > 0){
            return ($years == 1 ? "rok temu" : ($years < 5 ? "$years lata temu" : "$years lat temu"));
        }
        if($months > 0){
            return ($months == 1 ? "miesiąc temu" : ($months < 5 ? "$months miesiące temu" : "$months miesięcy temu"));
        }
        if($days > 0){
            return ($days == 1 ? "dzień temu" : "$days dni temu");
        }
        if($hours > 0){
            return ($hours == 1 ? "godzinę temu" : ($hours < 5 ? "$hours godziny temu" : "$hours godzin temu"));
        }
        if($minutes > 0){
            return ($minutes == 1 ? "minutę temu" : "$minutes minut temu");
        }
        return "poniżej minuty temu";
    }

    /**
     * funkcja zmieniająca skrót dnia ENG (Mon, Tue...) na PL (Pon, Wto)
     * @param string $Data - skrócona nazwa dnia ENG
     */
    static function PolskiDzien($Data){
        return str_replace(array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'), array('pon', 'wto', 'śro', 'czw', 'pią', 'sob', 'nie'), $Data);
    }
    
    
    static function PolskiMiesiac( $time ){
        
        $key = explode('-', date( "d-m-Y" , $time ) );
        $miesiac = array( '01'=>'stycznia',
            '02'=>'lutego',
            '03'=>'marca',
            '04'=>'kwietnia',
            '05'=>'maja',
            '06'=>'czerwca',
            '07'=>'lipca',
            '08'=>'sierpnia',
            '09'=>'września',
            '10'=>'października',
            '11'=>'listopada',
            '12'=>'grudnia' );
        return $key[0].' '.$miesiac[$key[1]].' '.$key[2];
    }
}
?>
