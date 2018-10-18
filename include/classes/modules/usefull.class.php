<?php
/**
 * Moduł funkcji użytecznych
 *
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright       Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class Usefull{

	function __construct() {
	}

        static function SortMyArray($array, $on, $order='SORT_DESC'){
            $new_array = array();
            $sortable_array = array();
            if (count($array) > 0) {
                foreach ($array as $k => $v) {
                    if (is_array($v)) {
                        foreach ($v as $k2 => $v2) {
                            if ($k2 == $on) {
                                $sortable_array[$k] = strtolower($v2);
                            }
                        }
                    } else {
                        $sortable_array[$k] = strtolower($v);
                    }
                }
                switch($order){
                    case 'SORT_ASC':
                        asort($sortable_array);
                        break;
                    case 'SORT_DESC':
                        arsort($sortable_array);
                        break;
                }
                foreach($sortable_array as $k => $v) {
                    $new_array[] = $array[$k];
                }
            }
            return $new_array;
        }

        static function GetFormStandardRow(){
            return array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1, 'td_end' => 1, 'tr_end' => 1);
        }

        static function GetFormWithoutTHRow(){
            return array('tr_start' => 1, 'td_start' => 1, 'td_end' => 1, 'tr_end' => 1);
        }

        static function GetFormButtonRow(){
            return array("tr_start" => 1, "td_start" => 1, "td_colspan" => 2, "td_style" => "text-align: center;", "td_end" => 1, "tr_end" => 1);
        }

	/**
	 * Funkcja wyświetlająca paginacje
	 *
	 * @param string $LinkPodstawowy - link na którym dokonywana jest paginacja
	 * @param $pagin - numer strony w paginacji
	 * @param $WidoczneNaStronie - ile kolejnych stron ma być dostępnych z obecnej strony
	 * @param $IleStronPaginacji - ilość wszystkich stron
	 */
	static function ShowPagination($LinkPodstawowy, $pagin = 0, $WidoczneNaStronie = 15, $IleStronPaginacji){
		if ($IleStronPaginacji > 1){
			$WidoczneNaStronie = ceil($WidoczneNaStronie/2);
			$Poczatek = $pagin - $WidoczneNaStronie;
			if ($Poczatek < 1){
				$Poczatek = 0;
			}
			$Koniec = $pagin + $WidoczneNaStronie + 1;
			if ($Koniec > $IleStronPaginacji){
				$Koniec = $IleStronPaginacji;
			}
                        echo "<span class='split'><a href=\"$LinkPodstawowy&pagin=0\">Pierwsza</a></span> ";
                        if($pagin > 0){
                            echo "<span class='split'><a href=\"$LinkPodstawowy&pagin=".($pagin-1)."\">&lt;&lt;</a></span> ";
                        }
			for ($i=$Poczatek;$i<$Koniec;$i++){
				$IWyswietl = $i+1;
				$klasa = "";
				if ($pagin == $i){
					$klasa = "class=\"paginationBold\"";
				}
				echo "<a href=\"$LinkPodstawowy&pagin=$i\" $klasa>$IWyswietl</a> ";
			}
                        if($pagin < $IleStronPaginacji-1){
                            echo "<span class='split'><a href=\"$LinkPodstawowy&pagin=".($pagin+1)."\">&gt;&gt;</a></span> ";
                        }
                        echo "<span class='split'><a href=\"$LinkPodstawowy&pagin=".($IleStronPaginacji-1)."\">Ostatnia</a></span> ";
		}
	}

	static function ZmienFormatKwoty($Kwota){
		$NowaKwota = number_format($Kwota,2,"."," ");
		return $NowaKwota;
	}

	static function WstawienieDoTablicy($klucz, $wartosc, $Tablica = null){
		if(is_null($Tablica) || $Tablica == false){
			$TablicaWynik = array($klucz => "$wartosc");
		}else{
			$TablicaWynik = array($klucz => "$wartosc")+$Tablica;
		}
		return $TablicaWynik;
	}

        static function prepareURL($sText){
            // pozbywamy się polskich znaków diakrytycznych
          $aReplacePL = array(
          'ą' => 'a', 'ę' => 'e', 'ś' => 's', 'ć' => 'c',
          'ó' => 'o', 'ń' => 'n', 'ż' => 'z', 'ź' => 'z', 'ł' => 'l',
          'Ą' => 'A', 'Ę' => 'E', 'Ś' => 'S', 'Ć' => 'C',
          'Ó' => 'O', 'Ń' => 'N', 'Ż' => 'Z', 'Ź' => 'Z', 'Ł' => 'L'
          );
          $sText = str_replace(array_keys($aReplacePL), array_values($aReplacePL),$sText);
          // dla przejrzystości wszystko z małych liter
          $sText = strtolower($sText);
          // zmieniamy encje na zwykłe znaki
          $sText = html_entity_decode($sText);
          $sText = str_replace(' & ', '-&-', $sText);
          // wszystkie spacje i przecinki zamieniamy na myślniki
          $sText = str_replace(array(' ', ','), '_', $sText);
          // wszystkie + zamieniamy na myślniki
          $sText = str_replace('+', '-', $sText);
          // usuń wszytko co jest niedozwolonym znakiem
          $sText = preg_replace('/[^0-9a-z\_]+/', '', $sText);
          // zredukuj liczbę myślników do jednego obok siebie
          $sText = preg_replace('/[\_]+/', '_', $sText);
          // usuwamy możliwe myślniki na początku i końcu
          $sText = trim($sText, '_');
          return $sText;
        }

        static function prepareFileName($Nazwa){
            $Ext = self::GetExtension($Nazwa);
            $NewName = str_replace(".$Ext", "", $Nazwa);
            $NewName = self::prepareURL($NewName).".$Ext";
            return $NewName;
        }

        static function GetExtension($File){
            $Rozszerzenie = explode(".", $File);
            $El = count($Rozszerzenie)-1;
            $Exp = strtolower($Rozszerzenie[$El]);
            return $Exp;
        }

	static function ShowKomunikatOK($Tekst, $Bolder = false){
            echo "<div style='clear: both;'></div>\n";
            echo '<div class="komunikat_ok" id="Komunikaty2">'.($Bolder ? "<b>$Tekst</b>" : $Tekst).'</div>';
	}

	static function ShowKomunikatError($Tekst, $Bolder = false){
            echo "<div style='clear: both;'></div>\n";
            echo '<div class="komunikat_blad" id="Komunikaty2">'.($Bolder ? "<b>$Tekst</b>" : $Tekst).'</div>';
	}

	static function ShowKomunikatOstrzezenie($Tekst, $Bolder = false){
            echo "<div style='clear: both;'></div>\n";
            echo '<div class="komunikat_ostrzezenie" id="Komunikaty2">'.($Bolder ? "<b>$Tekst</b>" : $Tekst).'</div>';
	}

        static function ActiveUpperText($Text){
            $Text = strtoupper($Text);
            $Text = str_replace(array("ó", "ł", "ś", "ń", "ź", "ż", "ć", "ą", "ę", "ü", "ö", "ä"), array("Ó", "Ł", "Ś", "Ń", "Ź", "Ż", "Ć", "Ą", "Ę", "Ü", "Ö", "Ä"), $Text);
            return $Text;
        }

        static function PolaczDwieTablice($Array1, $Array2){
		foreach($Array2 as $Key => $Value){
			if(is_array($Value)){
				$Array1[$Key] = Usefull::PolaczDwieTablice($Array1[$Key], $Array2[$Key]);
			}else{
				$Array1[$Key] = $Array2[$Key];
			}
		}
		return $Array1;
	}

        static function GetTakNie(){
            return array(1 => 'Tak', 0 => 'Nie');
        }

        static function GetFirstKey($Wartosci){
		$Keys = array_keys($Wartosci);
		return $Keys[0];
	}

        static function SetKey($Elementy, $IDElementu){
            $Key = (isset($Elementy[$IDElementu]) ? $IDElementu : 0);
            if(is_null($Key)){
                    $Key = 0;
            }
            return $Key;
        }

        static function CheckNIP($str){
            $str = preg_replace("/[^0-9]+/","",$str);
            if (strlen($str) != 10){
                return false;
            }

            $arrSteps = array(6, 5, 7, 2, 3, 4, 5, 6, 7);
            $intSum=0;
            for ($i = 0; $i < 9; $i++){
                $intSum += $arrSteps[$i] * $str[$i];
            }
            $int = $intSum % 11;

            $intControlNr=($int == 10)?0:$int;
            if ($intControlNr == $str[9]){
                return true;
            }
            return false;
        }

        static function FormatNip($str){
            $str = preg_replace("/[^0-9]+/","",$str);
            return substr($str, 0, 3)."-".substr($str,3,3)."-".substr($str,6,2)."-".substr($str,8,2);
        }

        static function SortArray($Elementy, $Pole, $Wartosc){
            $ToSortuj = array();
            foreach($Elementy as $Element){
                $ToSortuj[] = intval($Element[$Pole]);
            }
            array_multisort($ToSortuj, ($Wartosc == "ASC" ? SORT_ASC : SORT_DESC), $Elementy);
            return $Elementy;
        }

        static function AddParamsToLink($PaginParam = 0){
            $Return = ($PaginParam > 0 ? "&pagin=$PaginParam" : "");
            $Return .= (isset($_GET['sort']) ? "&sort={$_GET['sort']}" : "");
            $Return .= (isset($_GET['sort_how']) ? "&sort_how={$_GET['sort_how']}" : "");
            return $Return;
        }

        static function GetCheckImg(){
            $Array[0] = '&nbsp;';
            $Array[1] = "<img src='images/good.png' alt='checked' />";
            return $Array;
        }

        static function GetFileType($filename, $Type = null){
            $CurrentVer = phpversion();
                switch (version_compare($CurrentVer, '5.3.0') ) {
                    case -1:
                        $file_type = $Type;
                        break;
                    case 0:
                    case 1:
                        $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
                        $file_type = finfo_file($finfo, getcwd().'\\'.$filename);
                        finfo_close($finfo);
                        break;
                }
            return $file_type;
        }
    static function GetLI( $to_li, $lista, $modul = null ){
        $LI = '<ol>';
        if(is_array($to_li)){
            if(is_array($lista)){
                foreach( $to_li as $a_li ){
                    $LI .= "<li>$lista[$a_li]</li>";
                }
            }elseif(is_string ($lista) && $lista == 'lista_plikow'){
                foreach( $to_li as $a_li ){
                    $LI .= "<li><a class='getfile' target='blank' href='?modul=".($modul ? $modul : $_GET['modul'])."&akcja=pobierz&id={$a_li['plik_id']}&hash={$a_li['plik_hash']}'>{$a_li['nazwapliku']}</a></li>";
                }
            }
        }elseif(is_string($to_li)){
            $LI .= "<li>$to_li</li>";
        }
        $LI .= '</ol>';
        return $LI;
    }
    
    static function DelSmallButton( $class , $id=null){
            $button = '<img class= "'.$class.'" '.( is_null($id) ? '' : 'remove-id="'.$id.'"' ).' border="0" alt="Usuń" title="Usuń" src="images/cancel.gif">';
        return $button;
    }

    static function GetPackageStatuses( $admin = false ){
        return array('0' => 'szkic', /*'1' => 'gotowe', '2' => 'wysłane', */'3' => ($admin ? 'oczekujące' : 'wyslane'), '4' => 'odrzucone', '5' => 'Zaakceptowane');
    }

    static function GetPurchasesStatuses( $admin = false, $Top = false ){
        $PurchasesStatuses = array('0' => 'szkic', /*'1' => 'gotowe', '2' => 'w drodze',*/ '3' => ($admin ? 'oczekujące' : 'wyslane'), '4' => 'zamknięte', '5' => 'Zrealizowane');
/*        if(!$admin && !$Top ){
            unset( $PurchasesStatuses['2'] );
        }elseif($Top){
            $PurchasesStatuses['3'] = array( 'wyslane', 'oczekujące' );
        } */
        return $PurchasesStatuses ;
    }

    static function GetTrainingStatuses(){
        return array('0' => 'Zapisany', '1' => 'Wypisany', '2' => 'nieobecny', /*'3' => '', */'4' => 'Nie ukończył', '5' => 'Ukończył');
    }
    
    static function GetUsersTrainingStatuses(){
        return array('0' => 'Wygasły', '1' => 'Aktualny');
    }   
    static function GetUsersTrainingStatusesColor(){
        return array('0' => 'label', '1' => 'label success');
    }
    
    static function GetPackageStatusesColor(){
        return array('0' => 'label', '1' => 'label notice', '2' => 'label send', '3' => 'label warning', '4' => 'label important', '5' => 'label success');
    }
    
    
    static function CheckHash( $string, $hash, $type = 'MD5'){
        switch ($type){
            case 'sha1':
                $comp_hash = sha1($string);
                break;
            case 'md5':
            default :
                $comp_hash = md5($string);
                break;
        }
        $cmp = strcmp( $hash, $comp_hash );
        return $cmp;
    }

    static  function GetTRClass($type){
        switch ($type){
            case 'order':
                $color = array('0' => 'order_label', '1' => 'order_label notice', '2' => 'order_label send', '3' => 'order_label warning', '4' => 'order_label important', '5' => 'order_label success');
                break;
            default:
                $color = array();
                break;
        }
            return $color;
    }
    
     static  function GetTRColor($type){
        switch ($type){
            case 'role':
                $color = array('0' => '', '1' => 'style="border: 2px solid #FFAAAA"');
                break;
            default:
                $color = array();
                break;
        }
            return $color;
    }   
/**
 * funkcja zwracająca tablicę skróconej nazyw i tytułu  (najczęściej wykorzystywana do listy)
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @param array $Element tablica zawierająca pole do skrócenia
 * @param string $Name Nazwa pola do skrócenia (default name)
 * @param integer $lenght max długość pola po skróceniu (default 30)
 * @return array tablica nazwa => skróconanazwa, tytul -> pełna nazwa
 */
    
    static function NameAndTitle( $Element, $Name='name', $lenght=30){
        $title = $Element[$Name];
        $ElementName = self::ShortString($Element, $Name, $lenght);
            return array(
                $Name => $ElementName,
                'TitleListName' => $title,
            );
    }
    
    
/**
 * funkcja zwracająca skrócony tekst (najczęściej wykorzystywana do listy)
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @param array $Element tablica zawierająca pole do skrócenia
 * @param string $Name Nazwa pola do skrócenia (default name)
 * @param integer $lenght max długość pola po skróceniu (default 30)
 * @return string skrócony tekst
 */

    public static function ShortString( $Element, $Name='name', $lenght=30){
        $Exp = explode(" ", $Element[$Name]);
        $PiceName = array();
        $Licz = 0;
        foreach($Exp as $String){
            $PiceName[] = $String;
            $Licz += strlen($String);
            if($Licz > $lenght){
                break;
            }
        }
        return implode(" ", $PiceName).(count($PiceName) != count($Exp) ? "..." : "");
    }

    public static function ShowBasketAddButton($ID){
        return "<a href='javascript:DoKoszyka($ID, ".intval(isset($_SESSION['basket-picked-order']) ? $_SESSION['basket-picked-order'] : '').")' class='pokaz'><img src='images/do_koszyka.png' alt='Do koszyka' /></a>";
    }

    public static function GetItemTypes(){
        return array('SetupItem' => 'Rozruch', 'WarrantyRepairItem' => 'Naprawa gwarancyjna', 'WarrantyServiceItem' => 'Przegląd gwarancyjny');
    }

      static  function RedirectLocation($Href){
            ?>
            <script type="text/javascript">
                location.href="<?php echo $Href; ?>";
            </script>
            <?php
        }
    
    static function CheckEMail($email){
        $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/'; 
        // Run the preg_match() function on regex against the email address
        if (preg_match($regex, $email)) {
            return true;
        } else { 
            return false;
        } 
    }
    
        
    public static function CreateHash( $string, $type = 'MD5'){
        switch ($type){
            case 'sha1':
                $hash = sha1($string);
                break;
            case 'md5':
            default :
                $hash = md5($string);
                break;
        }
        return $hash;
    }
    
}

?>
