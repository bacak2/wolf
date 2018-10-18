<?php
/**
 * Obsługa formularzy
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright	Copyright (c) 2004-2008 ARTplus	
 * @version		1.0
 */

class FormularzSimple{
        
    /**
     * zmienna przechowująca czy zaincludowano plugin datapicker
     * @var boolean
     */
        protected $DatapickerPluginInstall = false;
        
        
	function __construct() {
	}
	
	static function FormStart($Name = "", $Action = "", $Method = "post", $TargetBlank = false, $IDName = ""){
		echo "<form ".($IDName != "" ? "id = '$IDName'" : ""). ($Name != "" ? "name = '$Name'" : "")."action='$Action' method='$Method'".($TargetBlank ? " target='_blank'" : "")." enctype='multipart/form-data'>";
	}
	
	static function FormEnd(){
		echo "</form>\n";
	}
	
	static function PoleSelect($Name, $Options, $Value = "", $Dodatki = "", $IDName = ""){
                echo "<select ".($IDName != '' ? " id='{$IDName}'" : '' )." name='{$Name}'".($Dodatki != "" ? " $Dodatki" : "").">\n";
			foreach($Options as $Key => $Wartosc){
				echo "<option value='$Key'".($Key == $Value ? " selected='selected'" : "").">$Wartosc</option>\n";
			}
		echo "</select>\n";		
	}
	
	static function PoleSelectMulti($Name, $Options, $Value = "", $Dodatki = "", $IDName = ""){
		echo "<select ".($IDName != '' ? "id='{$IDName}'" : '' )." name='$Name'".($Dodatki != "" ? " $Dodatki" : "")." multiple='multiple'>\n";
			foreach($Options as $Key => $Wartosc){
				echo "<option value='$Key'".(in_array($Key, $Value) ? " selected='selected'" : "").">$Wartosc</option>\n";
			}
		echo "</select>\n";		
	}
	
	static function PoleInputText($Name, $Value, $Dodatki = "", $IDName = ''){
//                echo '<div class="border">';
		echo "<input ".($IDName != '' ? "id='{$IDName}'" : '' )." type='text' name='$Name' value='".htmlspecialchars(stripslashes($Value), ENT_QUOTES)."'".($Dodatki != "" ? " $Dodatki" : "")." />\n";
//                echo '</div>';
	}
	
	static function PoleFile($Name, $Value, $Dodatki = "", $IDName = ''){
		echo "<input ".($IDName != '' ? "id='{$IDName}'" : '' )." type='file' name='$Name' value='$Value'".($Dodatki != "" ? " $Dodatki" : "")." />\n";
	}

	static function PolePassword($Name, $Value, $Dodatki = "", $IDName = ""){
		echo "<input ".($IDName != '' ? "id='{$IDName}'" : '' )." type='password' name='$Name' value='$Value'".($Dodatki != "" ? " $Dodatki" : "")." />\n";
	}
	
	static function PoleTextarea($Name, $Value, $Dodatki = "", $IDName = ""){
		echo "<textarea ".($IDName != '' ? "id='{$IDName}'" : '' )." name='$Name'".($Dodatki != "" ? " $Dodatki" : "").">$Value</textarea>\n";
	}
	
	static function PoleHidden($Name, $Value, $Dodatki = "", $IDName = ""){
		echo "<input ".($IDName != '' ? "id='{$IDName}'" : '' )." type='hidden' name='$Name' value='$Value'".($Dodatki != "" ? " $Dodatki" : "")." />\n";
	}
	
	static function PoleSubmitImage($Name, $Value, $Src, $Dodatki = "", $IDName = ""){
		echo "<input ".($IDName != '' ? "id='{$IDName}'" : '' )." type='image' src='$Src' name='$Name' value='$Value'".($Dodatki != "" ? " $Dodatki" : "")." />\n";
	}
	
	static function PoleButton($Name, $Value, $Dodatki = "", $IDName = ""){
		echo "<input ".($IDName != '' ? "id='{$IDName}'" : '' )." type='button' name='$Name' value='$Value'".($Dodatki != "" ? " $Dodatki" : "")." />\n";
	}

	static function PoleSubmit($Name, $Value, $Dodatki = "", $IDName = ""){
		echo "<input ".($IDName != '' ? "id='{$IDName}'" : '' )." type='submit' name='$Name' value='$Value'".($Dodatki != "" ? " $Dodatki" : "")." />\n";
	}
	
	static function SelectData($Nazwa, $YearStart, $YearEnd, $Wartosc, $Dodatki = ""){
		$WartoscExp = explode("-", $Wartosc);
		echo "<select id='{$Nazwa}_day' name='{$Nazwa}[day]'".($Dodatki != "" ? " $Dodatki" : "").">\n";
			for($Day = 1; $Day <= 31; $Day++){
				$D = ($Day < 10 ? "0" : "").$Day;
				echo "<option value='$D'".($D == $WartoscExp[2] ? " selected='selected'" : "").">$D</option>\n";
			}
		echo "</select>\n";
		echo "<select id='{$Nazwa}_month' name='{$Nazwa}[month]'".($Dodatki != "" ? " $Dodatki" : "").">\n";
			for($Month = 1; $Month <= 12; $Month++){
				$M = ($Month < 10 ? "0" : "").$Month;
				echo "<option value='$M'".($M == $WartoscExp[1] ? " selected='selected'" : "").">".$this->LiczbaNaMiesiac($M)."</option>\n";
			}
		echo "</select>\n";
		echo "<select id='{$Nazwa}_year' name='{$Nazwa}[year]'".($Dodatki != "" ? " $Dodatki" : "").">\n";
			for($Year = $YearStart; $Year <= $YearEnd; $Year++){
				echo "<option value='$Year'".($Year == $WartoscExp[0] ? " selected='selected'" : "").">$Year</option>\n";
			}
		echo "</select>\n";
	}
	
	static function LiczbaNaMiesiac($miesiac){
		if($miesiac < 10){
			$miesiac = "0".$miesiac;
		}
		$miesiac = str_replace("00", "0", $miesiac);
		switch($miesiac){
			case("01"): return KALENDARZ_MIESIACE_STYCZEN;
			case("02"): return KALENDARZ_MIESIACE_LUTY;
			case("03"): return KALENDARZ_MIESIACE_MARZEC;
			case("04"): return KALENDARZ_MIESIACE_KWIECIEN;
			case("05"): return KALENDARZ_MIESIACE_MAJ;
			case("06"): return KALENDARZ_MIESIACE_CZERWIEC;
			case("07"): return KALENDARZ_MIESIACE_LIPIEC;
			case("08"): return KALENDARZ_MIESIACE_SIERPIEN;
			case("09"): return KALENDARZ_MIESIACE_WRZESIEN;
			case("10"): return KALENDARZ_MIESIACE_PAZDZIERNIK;
			case("11"): return KALENDARZ_MIESIACE_LISTOPAD;
			case("12"): return KALENDARZ_MIESIACE_GRUDZIEN;
			default: return $miesiac;
		}
	}
	
	static function PoleCheckbox($Name, $Value, $Wartosc = null, $Etykieta = "", $Dodatki = ""){
		echo "<input type='checkbox' name='$Name' value='$Value'".("$Wartosc" == "$Value" ? " checked" : "")."".($Dodatki != "" ? " $Dodatki" : "")." /> $Etykieta\n";
	}
	
	static function PoleRadio($Name, $Value, $Wartosc = null, $Dodatki = ""){
		echo "<input type='radio' name='$Name' value='$Value'".($Wartosc == $Value ? " checked" : "")."".($Dodatki != "" ? " $Dodatki" : "")." />\n";
	}

        static function PoleData($Name, $Value, $ID = false, /* $Form = "formularz", $Wstecz = 0,*/ $Dodatki = ""){
            if(!$ID){
                $ID = $Name;
            }
            if($Value == "0000-00-00"){
                $Value = "";
            }
//            echo "<input type=\"text\" id=\"$ID\" name=\"$Name\" style='width: 100px;' value=\"$Value\"".($Dodatki != "" ? " $Dodatki" : "").">&nbsp;&nbsp;<img src='images/kalendarz.png' onclick='wstecz = $Wstecz; showKal(document.getElementById(\"$ID\"));' style='margin-left: 10px; cursor: pointer; vertical-align: middle;'>";
/*            if($this->DatapickerPluginInstall == false){
                ?>
                <script type='text/javascript' src='js/plugins/jquery.ui.core.js'></script>
                <script type='text/javascript' src='js/plugins/jquery.ui.datepicker.js'></script>
                <script type='text/javascript' src='js/plugins/datapicker-lang/jquery.ui.datepicker-pl.js'></script>
                <?php
                $this->DatapickerPluginInstall = true;
            }*/
            echo("<input type='text' id='$ID' class='data datapicker' name='{$Name}'".(isset($Value) ? " value='".htmlspecialchars(stripslashes($Value), ENT_QUOTES)."'" : '').($Dodatki != "" ? " $Dodatki" : "").">");
        }
}
?>
