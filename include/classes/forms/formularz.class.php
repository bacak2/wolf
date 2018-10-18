<?php
/**
 * Generowanie i walidacja formularzy.
 * 
 * @author		Lukasz Piekosz <mentat@mentat.net.pl>
 * @copyright	Copyright (c) 2004-2007 ARTplus
 * @package		PanelPlus
 * @version		1.0
 */
class Formularz {

	/**
	 * Tablica zawierająca konfigurację pól formularza formularza
	 *
	 * @var array
	 */
	protected $Pola = array();

        /**
         * Tablica zawierająca przyciski formularza
         * @var array
         */
	protected $TablicaPrzyciskow = array();

        /**
         * Tablica zawierająca oryginalne nazwy pól z bazy
         * @var array
         */
	protected $Kolejnosc = array();

        /**
         * Link na który wysyłany jest formularz
         * @var string
         */
	protected $LinkAkceptacji;

        /**
         * Link anulowania formularza, do którego idziemy klikając Anuluj
         * @var string
         */
	public $LinkRezygnacji;

        /**
         * Prefix dla pól formularza (użyty w mapowaniu nazw pól)
         * @var string
         */
	protected $Prefix;

        /**
         * Tablica zawierająca zamapowane nazwy pól: klucz = oryginalna nazwa; wartość = zmapowana nazwa wyświetlona w formularzu
         * @var array
         */
	protected $MapaNazw = array();

        /**
         * Tytuł formularza
         * @var string
         */
	protected $Tytul;

        /**
         * Tablica zawierająca pola wymagane
         * @var array
         */
	protected $PolaWymagane = array();

        /**
         * Tablica zawierająca pole które nie mogą się zdublować w bazie
         * @var array
         */
	protected $PolaNieDublujace = array();

        /**
         * Tablica zawierająca pola które będą miały edytor HTML (w tej wersji TinyMCE)
         * @var array
         */
	protected $ElementyZEdytorem = array();

        /**
         * Referencja do obiektu formularza zwykłego (zawierającego szybkie pola itp.)
         * @var FormularzSimple
         */
        protected $Form;

        /**
         *  zmienna przechowująca czy zaincludowano plugin chosen.jquery
         */
        protected $ChosenPluginInstall = false;

        /**
         * zmienna przechowująca czy zaincludowano plugin datapicker
         * @var boolean
         */
        protected $DatapickerPluginInstall = false;

        /**
         * Szerokość kontenera;
         */
        protected $ContainerWidth = "95%";
        
        
        /*
         * podstawowa klasa div'a
         */
        protected $_DefaultDivClass = 'super-form-span1';
        
        
        protected $_PolaBezRamki = array('clear', 'zapisz');
        
        protected $_PolaBezLabel = array('clear', 'zapisz');
        
        protected $_SamePola = array();
        
        protected $_ToolTipOn = true;
        
        protected $_Plugins = array();
        
        /**
         * konstruktor
         * @param string $LinkAkceptacji
         * @param string $LinkRezygnacji
         * @param string $Prefix
         * @param string $Tytul
         */
	function __construct($LinkAkceptacji = null, $LinkRezygnacji = null, $Prefix = null, $Tytul = null) {
		$this->LinkAkceptacji = $LinkAkceptacji;
		$this->LinkRezygnacji = $LinkRezygnacji;
		$this->Prefix = ($Prefix != '' ? $Prefix.'_' : '');
		$this->Tytul = $Tytul;
                $this->Form = new FormularzSimple();
	}

        /**
         * Ustawienie linku rezygnacji (anulowania)
         * @param string $Link
         */
	function UstawLinkRezygnacji($Link) {
		$this->LinkRezygnacji = $Link;
	}	

        /**
         * Ustawia opcję pole formularza
         * @param string $Nazwa - nazwa pola
         * @param string $NazwaOpcji
         * @param string $Wartosc
         * @param boolean $MapujPola - czy użyta nazwa pola jest oryginalna (false) czy zmapowana (true)
         */
	function UstawOpcjePola($Nazwa, $NazwaOpcji, $Wartosc, $MapujPola = true) {
		if ($MapujPola) {
			$OdwrotnaMapaNazw = array_flip($this->MapaNazw);
			$Nazwa = $OdwrotnaMapaNazw[$Nazwa];
		}
		if ($NazwaOpcji) {
			$this->Pola[$Nazwa]['opcje'][$NazwaOpcji] = $Wartosc;
		}
		else {
			$this->Pola[$Nazwa]['opcje'] = $Wartosc;
		}
	}

        /**
         * Ustawienie opisu pola formularza (wyświetlanego w TH tabeli)
         * @param string $Nazwa - nazwa pola
         * @param string $Wartosc
         * @param boolean $MapujPola - czy użyta nazwa pola jest oryginalna (false) czy zmapowana (true)
         */
        function UstawOpisPola($Nazwa, $Wartosc, $MapujPola = true) {
		if ($MapujPola) {
			$OdwrotnaMapaNazw = array_flip($this->MapaNazw);
			$Nazwa = $OdwrotnaMapaNazw[$Nazwa];
		}
                $this->Pola[$Nazwa]['opis'] = $Wartosc;
	}

        /**
         * Ustawia opcję przycisku formularza (jeżeli jest potrzeba aby zmienić domyślne
         * @param string $Nazwa
         * @param string or false $NazwaOpcji - jeżeli jest false to wymienia wszystkie opcje, jeżeli nazwa opcji to tylko tą jedną opcję
         * @param array, string $Wartosc - wartość która zostanie podstawiona
         */
	function UstawOpcjePrzycisku($Nazwa, $NazwaOpcji = false, $Wartosc = null) {
		if ($NazwaOpcji) {
			$this->TablicaPrzyciskow[$Nazwa]['opcje'][$NazwaOpcji] = $Wartosc;
		}
		else {
			$this->TablicaPrzyciskow[$Nazwa]['opcje'] = $Wartosc;
		}
	}

        /**
         * Zmienia typ pola formularza
         * @param string $Nazwa
         * @param string $NowyTyp
         * @param boolean $MapujPola - czy użyta nazwa pola jest oryginalna (false) czy zmapowana (true)
         */
	function ZmienTypPola($Nazwa,$NowyTyp,$MapujPola = true){
		if ($MapujPola) {
			$OdwrotnaMapaNazw = array_flip($this->MapaNazw);
			$Nazwa = $OdwrotnaMapaNazw[$Nazwa];	
		}
		if ($NowyTyp) {
			$this->Pola[$Nazwa]['typ'] = $NowyTyp;
		}
	}

	/**
	 * Dodaje pole formularza do formularza
	 *
	 * @param string $Nazwa
	 * @param string $Typ
	 * @param string $Opis
	 * @param array $Opcje
	 * Wartości w tabeli $Opcje:
	 * elementy - lista elementów dla pola select
	 * stan - dodatkowy atrybut readonly|disabled itp.
	 */
	function DodajPole($Nazwa, $Typ, $Opis, $Opcje = null) {
		$this->Kolejnosc[] = $Nazwa;
		$this->MapaNazw[$Nazwa] = $this->Prefix.'pole_'.count($this->Kolejnosc);
		$this->Pola[$Nazwa] = array(
			'opis' => $Opis,
			'typ' => $Typ,
			'opcje' => $Opcje
		);
	}

        /**
         * Dodaje pole do tablicy pól wymaganych
         * @param string $Nazwa
         * @param boolean $MapujPola - czy użyta nazwa pola jest oryginalna (false) czy zmapowana (true)
         */
	function DodajPoleWymagane($Nazwa, $MapujPola = true) {
		if ($MapujPola) {
			$OdwrotnaMapaNazw = array_flip($this->MapaNazw);
			$Nazwa = $OdwrotnaMapaNazw[$Nazwa];	
		}
		$this->PolaWymagane[] = $Nazwa;
	}

        /**
         * Dodaje pole do tablicy pól nie mogących się zdublować w bazie
         * @param string $Nazwa
         * @param boolean $MapujPola - czy użyta nazwa pola jest oryginalna (false) czy zmapowana (true)
         */
	function DodajPoleNieDublujace($Nazwa, $MapujPola = true) {
		if ($MapujPola) {
			$OdwrotnaMapaNazw = array_flip($this->MapaNazw);
			$Nazwa = $OdwrotnaMapaNazw[$Nazwa];	
		}
		$this->PolaNieDublujace[] = $Nazwa;
	}

        /**
         * Dodaje pole do tablicy pól z edytorem HTML
         * @param string $Nazwa
         * @param boolean $MapujPola - czy użyta nazwa pola jest oryginalna (false) czy zmapowana (true)
         */
	function DodajEdytor($Nazwa, $MapujPola = true){
            if ($MapujPola) {
			$OdwrotnaMapaNazw = array_flip($this->MapaNazw);
			$Nazwa = $OdwrotnaMapaNazw[$Nazwa];
//                        $Nazwa = $this->MapaNazw[$Nazwa];
		}
		$this->ElementyZEdytorem[] = $Nazwa;
	}

        /**
         * Waliduje pola
         * @param string $Nazwa
         * @param string $Wartosc
         * @return boolean
         */
	function WalidujPole($Nazwa, &$Wartosc) {
		return true;
	}

        /**
         * Wykonuje walidację wszystkich pól
         * @param array $Wartosci
         * @return boolean
         */
	function Waliduj(&$Wartosci) {
		for ($i = 0; $i < count($this->Kolejnosc); $i++) {
			if (!$this->WalidujPole($this->Kolejnosc[$i], $Wartosci[$this->Kolejnosc[$i]])) {
				return false;
			}
		}
		return true;
	}

        /**
         * Zwraca nazwę zmapowaną pola
         * @param string $Nazwa - nazwa oryginalna pola
         * @return string or false
         */
	function ZwrocNazweMapyPola($Nazwa){
		if($this->MapaNazw[$Nazwa]){
			return $this->MapaNazw[$Nazwa];
		}else{
			return false;
		}
	}

        /**
         * Wyświetla pole formularza
         * @param string $Nazwa
         * @param string $Wartosc
         */
	function WyswietlPole($Nazwa, &$Wartosc = null) {
		$AtrybutyDodatkowe = '';
                $AtrybutyDodatkoweTR = '';
		$OpisDodatkowy = '';
                $OpisDodatkowyPrzed = '';
                $OpisDodatkowyZa = '';
		if (isset($this->Pola[$Nazwa]['opcje']['submit'])) {
			foreach ($this->Pola[$Nazwa]['opcje']['submit'] as $Atrybut) {
				$AtrybutyDodatkowe .= " $Atrybut='this.form.elements[\"OpcjaFormularza\"].value = \"zmien\"; this.form.submit();'";
			}
		}
		if (isset($this->Pola[$Nazwa]['opcje']['stan'])) {
			foreach ($this->Pola[$Nazwa]['opcje']['stan'] as $Atrybut) {
				$AtrybutyDodatkowe .= " $Atrybut='$Atrybut'";
			}
		}
		if (isset($this->Pola[$Nazwa]['opcje']['atrybuty'])) {
			foreach ($this->Pola[$Nazwa]['opcje']['atrybuty'] as $Atrybut => $WartoscAtrubutu) {
				$AtrybutyDodatkowe .= " $Atrybut='$WartoscAtrubutu'";
			}
		}
		if (isset($this->Pola[$Nazwa]['opcje']['opis_dodatkowy_za'])) {
			$OpisDodatkowyZa = $this->Pola[$Nazwa]['opcje']['opis_dodatkowy_za'];
		}
		if (isset($this->Pola[$Nazwa]['opcje']['opis_dodatkowy_przed'])) {
			$OpisDodatkowyPrzed = $this->Pola[$Nazwa]['opcje']['opis_dodatkowy_przed'];
		}
                $pole_id = (isset($this->Pola[$Nazwa]['opcje']['id']) ? $this->Pola[$Nazwa]['opcje']['id'] : $this->MapaNazw[$Nazwa]);
                $divFormClass = (isset($this->Pola[$Nazwa]['opcje']['div_class']) ? $this->Pola[$Nazwa]['opcje']['div_class'] : $this->_DefaultDivClass);
                $divFormAttr = (isset($this->Pola[$Nazwa]['opcje']['div_attr']) ? $this->Pola[$Nazwa]['opcje']['div_attr'] : '');
                
                                
	      if( !in_array($Nazwa, $this->_SamePola)){
                echo "<div class='{$divFormClass}' {$divFormAttr}>";
                if( !in_array($Nazwa, $this->_PolaBezLabel)){
                    if(isset($this->Pola[$Nazwa]['opcje']['show_label']) && $this->Pola[$Nazwa]['opcje']['show_label'] === true) {
                        echo "<div class='super-form-label'><label for='{$this->MapaNazw[$Nazwa]}'>{$OpisDodatkowyPrzed} {$this->Pola[$Nazwa]['opis']}</label>";
                    }elseif(isset($this->Pola[$Nazwa]['opcje']['show_label']) && $this->Pola[$Nazwa]['opcje']['show_label'] !== false) {
                        echo "<div class='super-form-label'>{$OpisDodatkowyPrzed}&nbsp;</label>";
                    }else{
                        echo "<div class='super-form-label'>&nbsp;</label>";
                    }
                    if( isset($this->Pola[$Nazwa]['opcje']['tooltip']) && !empty($this->Pola[$Nazwa]['opcje']['tooltip']) && $this->_ToolTipOn) {
                        ?><div class='tooltip_trigger' tooltip='#div_tooltip_<?php echo $this->MapaNazw[$Nazwa]; ?>'><img src="images/information.gif" title="" alt=""></div>
                        <div id="div_tooltip_<?php echo $this->MapaNazw[$Nazwa]; ?>" class="tooltip_content"><?php echo $this->Pola[$Nazwa]['opcje']['tooltip']; ?></div><?php
                    }
                    echo "</div><div class='clear'></div>";
                }
		echo "<div class='super-form-div'>";
		if( !in_array($Nazwa, $this->_PolaBezRamki)){
		    echo "<div class='super-form-field'>";
		}
	      }
		switch ($this->Pola[$Nazwa]['typ']) {
                        case 'brak':
                            break;
                        case 'clear':
                            echo '<div class="clear">&nbsp;</div>';
                            break;
			case 'tekst_link':
			case 'tekst':
			case 'email':
                                $this->Form->PoleInputText($this->MapaNazw[$Nazwa], htmlspecialchars(stripslashes($Wartosc), ENT_QUOTES), $AtrybutyDodatkowe, $pole_id);
				break;
                        case 'tekst_datatime':
                                if($this->DataTimepickerPluginInstall == false){
                                    //<script type='text/javascript' src='js/plugins/jquery.datetimepicker.js'></script>
                                    ?>
                                    <script type='text/javascript' src='js/plugins/jquery-ui-timepicker.js'></script>
                                    <?php
                                    $this->DataTimepickerPluginInstall = true;
                                }
                                $this->Form->PoleInputText($this->MapaNazw[$Nazwa], htmlspecialchars(stripslashes($Wartosc), ENT_QUOTES), $AtrybutyDodatkowe.' class="datatime datatimepicker"', $pole_id);
				break;
			case 'tekst_data':
                                if(isset($this->Pola[$Nazwa]['opcje']['min'])){
                                    $Dzis = date("Y-m-d");
                                    $OdKiedy = date("Y-m-d", strtotime($this->Pola[$Nazwa]['opcje']['min']));
                                    $IleDni = UsefullTime::ObliczDniPomiedzy($Dzis, $OdKiedy);
                                }
                                if(isset($this->Pola[$Nazwa]['opcje']['max_today'])){
                                    $Dzis = date("Y-m-d");
                                    $DoKiedy = date("Y-m-d", strtotime($this->Pola[$Nazwa]['opcje']['max_today']));
                                    $IleDniMax = UsefullTime::ObliczDniPomiedzy($Dzis, $DoKiedy);
                                }
                                $this->Form->PoleInputText($this->MapaNazw[$Nazwa], htmlspecialchars(stripslashes($Wartosc), ENT_QUOTES), $AtrybutyDodatkowe.' class="data '.(isset($this->Pola[$Nazwa]['opcje']['max_today']) ? "datapicker_today" : "datapicker").'"', $pole_id);
				break;
			case 'tekst_lang':
				foreach($this->Pola[$Nazwa]['opcje']['languages'] as $LanguageID => $Flag){
					$Ikona = "flags/$Flag";
					echo("<img src='$Ikona' style='display: inline; vertical-align: middle; margin: 6px 3px;'/>");
                                        $this->Form->PoleInputText($this->MapaNazw[$Nazwa]."[{$LanguageID}]", htmlspecialchars(stripslashes($Wartosc[$LanguageID]), ENT_QUOTES), $AtrybutyDodatkowe, $this->MapaNazw[$Nazwa]."#".$LanguageID);
				}
				break;
			case 'password':
                            $this->Form->PolePassword($this->MapaNazw[$Nazwa], htmlspecialchars(stripslashes($Wartosc), ENT_QUOTES), $AtrybutyDodatkowe, $pole_id);
                            break;				
			case 'tekst_dlugi_lang':
				foreach($this->Pola[$Nazwa]['opcje']['languages'] as $LanguageID => $Flag){
					$Ikona = "flags/$Flag";
					echo("<img src='$Ikona' style='display: inline; vertical-align: middle; margin: 6px 3px;'/>");
                                        $this->Form->PoleTextarea($this->MapaNazw[$Nazwa]."[$LanguageID]", ($Wartosc ? stripslashes(htmlspecialchars($Wartosc[$LanguageID], ENT_QUOTES)) : ''), $AtrybutyDodatkowe, "{$this->MapaNazw[$Nazwa]}#{$LanguageID}");
				}
				break;
			case 'tekst_dlugi':
                                $this->Form->PoleTextarea($this->MapaNazw[$Nazwa], ($Wartosc ? stripslashes(htmlspecialchars($Wartosc, ENT_QUOTES)) : ''), $AtrybutyDodatkowe, $this->MapaNazw[$Nazwa]);
				break;
			case 'obraz':
				if ($Wartosc && file_exists($Wartosc)){
					$Wymiary = $this->ZmienWielkoscWyswietlonegoObrazka(350,350,$Wartosc);
					echo("<img src='".$Wartosc.'?'.time()."' width='{$Wymiary['x']}' height='{$Wymiary['y']}' \><br /><br />");
					echo("<a href='$this->LinkAkceptacji&obraz=".basename($Wartosc)."&usun_obraz=1' onclick='return confirm(\"Obrazek zostanie bezpowrotnie utracony. Kontynuować kasowanie?\");'><img src='images/bin_empty.gif' style='display: inline; vertical-align: middle;'> Kasuj obrazek</a><br /><br />");
				}else if ($this->Pola[$Nazwa]['opcje']['wartoscdomyslna']) {
                                    if(file_exists($this->Pola[$Nazwa]['opcje']['wartoscdomyslna'])) {
                                        $Wymiary = $this->ZmienWielkoscWyswietlonegoObrazka(350,350,$this->Pola[$Nazwa]['opcje']['wartoscdomyslna']);
                                        echo("<img src='".$this->Pola[$Nazwa]['opcje']['wartoscdomyslna'].'?'.time()."' width='{$Wymiary['x']}' height='{$Wymiary['y']}' \><br /><br />");
                                        echo("<a href='$this->LinkAkceptacji&obraz=".basename($this->Pola[$Nazwa]['opcje']['wartoscdomyslna'])."&usun_obraz=1' onclick='return confirm(\"Obrazek zostanie bezpowrotnie utracony. Kontynuować kasowanie?\");'><img src='images/bin_empty.gif' style='display: inline; vertical-align: middle;'> Kasuj obrazek</a><br /><br />");
                                    }
				}else{
                                    $this->Form->PoleFile($this->MapaNazw[$Nazwa], $Value, $Dodatki, $pole_id);
				}
				break;
			case 'lista_obrazow':
				echo("<div><div style='float:left;margin: 10px 10px 0 0'><input type='file' id='$pole_id' name='{$this->MapaNazw[$Nazwa]}'></div><input type='button' value='Dodaj obrazek' onclick='ValueChange(\"OpcjaFormularza\",\"dodaj_obrazek\");return false;'></div><div class='clear'></div>");
				if (is_array($Wartosc) && count($Wartosc)){
                                    echo '<div class="zdjecia">';
					foreach($Wartosc as $Zdjecie){
						if(file_exists($Zdjecie["sciezka"])) {
							echo "<div>";
								echo "<div>";
									echo "{$Zdjecie['nazwa']}&nbsp;&nbsp;";
								echo "</div>";
								echo "<div>";
									echo("<a href='?modul={$_GET['modul']}&akcja={$_GET['akcja']}&id={$_GET['id']}&plik={$Zdjecie['nazwapliku']}&usun_plik=1' onclick='return confirm(\"Obrazek zostanie bezpowrotnie utracony. Kontynuować kasowanie?\");'><img src='images/bin_empty.gif' style='display: inline; vertical-align: middle;'> Kasuj obrazek</a>");
								echo "</div>";
								list($width,$height) = getimagesize($Zdjecie["sciezka"]);
								echo "<div>";
									echo("<a href='javascript:NewWindow(\"{$Zdjecie["sciezka"]}\",\"podglad\",$width,$height,\"no\")'><img src='images/magnifier.gif' style='display: inline; vertical-align: middle;'> Pokaż obrazek</a>");
								echo "</div>";
							echo "</div>";
						}
					}
					echo "</div>";
				}
				break;
			case 'lista_plikow':
				echo("<div class='f_left padding_8'><input type='file' id='$pole_id' name='{$this->MapaNazw[$Nazwa]}'></div><div class='f_left'><input type='button' value='Dodaj plik' onclick='ValueChange(\"OpcjaFormularza\",\"dodaj_plik\");return false;'></div><div style='clear:both;'></div>");
				if (is_array($Wartosc) && count($Wartosc)){
					echo "<table style='border: 0;' cellpadding='0' cellspacing='2' style='width: 100%;'>";
					foreach($Wartosc as $Plik){
						if(file_exists($Plik["sciezka"])) {
							echo "<tr>";
								echo "<td style='border: 0; width: 250px; vertical-align: top; text-align: left;'>";
									echo "<a target='blank' href='?modul={$_GET['modul']}&akcja=pobierz&id={$Plik['plik_id']}&hash={$Plik['plik_hash']}' >{$Plik['nazwapliku']}</a>&nbsp;&nbsp;";
								echo "</td>";
								echo "<td style='border: 0; vertical-align: top; text-align: left;'>";
									echo("<a href='?modul={$_GET['modul']}&akcja={$_GET['akcja']}&id={$_GET['id']}&plik={$Plik['plik_hash']}&usun_plik=1' onclick='return confirm(\"Plik zostanie bezpowrotnie utracony. Kontynuować kasowanie?\");'><img src='images/bin_empty.gif' style='display: inline; vertical-align: middle;'> Kasuj plik</a>");
								echo "</td>";
								
							echo "</tr>";
						}
					}
					echo "</table>";
				}
				break;
				
			case 'lista':
                                $this->Form->PoleSelect($this->MapaNazw[$Nazwa], $this->Pola[$Nazwa]['opcje']['elementy'], $Wartosc, $AtrybutyDodatkowe);
				break;
                        case 'lista-chosen':
                                if($this->ChosenPluginInstall == false){
                                    ?><script type='text/javascript' src='js/plugins/chosen.jquery.js'></script><?php
                                    $this->ChosenPluginInstall = true;
                                }
                                $Domyslna = (isset($this->Pola[$Nazwa]['opcje']['domyslna']) ? $this->Pola[$Nazwa]['opcje']['domyslna'] : " -- wybierz --");
                                $this->Form->PoleSelectMulti($this->MapaNazw[$Nazwa].'[]', $this->Pola[$Nazwa]['opcje']['elementy'], $Wartosc, $AtrybutyDodatkowe. "class='chzn-select' data-placeholder='$Domyslna' ", $pole_id);
                                
				echo("</select>");
                                if( isset($this->Pola[$Nazwa]['opcje']['script']) && $this->Pola[$Nazwa]['opcje']['script'] ){
                                    echo $this->Pola[$Nazwa]['opcje']['script'];
                                }
				break;
                        case 'lista-chosen-single':
                                if($this->ChosenPluginInstall == false){
                                    ?><script type='text/javascript' src='js/plugins/chosen.jquery.js'></script><?php
                                    $this->ChosenPluginInstall = true;
                                }
                                $Domyslna = (isset($this->Pola[$Nazwa]['opcje']['domyslna']) ? $this->Pola[$Nazwa]['opcje']['domyslna'] : " -- wybierz --");
				echo("<select id='$pole_id' name='{$this->MapaNazw[$Nazwa]}' class='chzn-select' data-placeholder='$Domyslna' $AtrybutyDodatkowe>");
				foreach ($this->Pola[$Nazwa]['opcje']['elementy'] as $WartoscPola => $OpisPola) {
					echo("<option value='$WartoscPola'".($WartoscPola == $Wartosc ? " selected='selected'" : "").">$OpisPola</option>");
				}
				echo("</select>");
                                if( isset($this->Pola[$Nazwa]['opcje']['script']) && $this->Pola[$Nazwa]['opcje']['script'] ){
                                    echo $this->Pola[$Nazwa]['opcje']['script'];
                                }
				break;
			case 'lista_z_pustym':
				echo("<select id='$pole_id' name='{$this->MapaNazw[$Nazwa]}'$AtrybutyDodatkowe>");
				echo("<option value='0'".("0" == $Wartosc ? " selected='selected'" : '').">--- wybierz ---</option>");
				foreach ($this->Pola[$Nazwa]['opcje']['elementy'] as $WartoscPola => $OpisPola) {
					echo("<option value='$WartoscPola'".($WartoscPola == $Wartosc ? " selected='selected'" : '').">$OpisPola</option>");
				}
				echo("</select>");
				break;
				
			case 'podzbiór':
				echo("<select id='$pole_id' name='{$this->MapaNazw[$Nazwa]}[]' multiple='multiple'$AtrybutyDodatkowe>");
                                if(isset($this->Pola[$Nazwa]['opcje']['wybierz']) && $this->Pola[$Nazwa]['opcje']['wybierz']){
                                    $Domyslna = (isset($this->Pola[$Nazwa]['opcje']['domyslna']) ? $this->Pola[$Nazwa]['opcje']['domyslna'] : " -- wybierz --");
                                    echo("<option value='0'".(0 == $Wartosc ? " selected='selected'" : "")."> $Domyslna </option>");
                                }
				foreach ($this->Pola[$Nazwa]['opcje']['elementy'] as $WartoscPola => $OpisPola) {
					echo("<option value='$WartoscPola'".(is_array($Wartosc) && in_array($WartoscPola , $Wartosc) ? " selected='selected'" : '').">$OpisPola</option>");
				}
				echo("</select>");
				break;
			case 'podzbiór_checkbox_1n':
			case 'podzbiór_checkbox_nn':
				foreach ($this->Pola[$Nazwa]['opcje']['elementy'] as $WartoscPola => $OpisPola) {
                                        $width = (isset($this->Pola[$Nazwa]['opcje']['krotkie']) ? 'width: 150px;' : 'width: 80%;'); 
					echo("<div style='$width float: left; border: 1px solid #F0F0F0; padding: 3px;'><input type='checkbox' name='{$this->MapaNazw[$Nazwa]}[]' value='$WartoscPola' style='vertical-align: middle;'".(is_array($Wartosc) && in_array($WartoscPola , $Wartosc) ? " checked='checked'" : '')."$AtrybutyDodatkowe /> $OpisPola</div>");
				}
				break;
			case 'podzbiór_checkbox_1n_zakladki':
				foreach ($this->Pola[$Nazwa]['opcje']['elementy'] as $WartoscPola => $OpisPola) {
					if(isset($this->Pola[$Nazwa]['opcje']['nadrzedne'][$WartoscPola])){
						echo "<div style='width: 100%; float: left; white-space: nowrap; height: 4px;'>&nbsp;</div>";
						echo("<div style='white-space: nowrap; float: left; border: 1px solid #F0F0F0; padding: 3px;'><input type='checkbox' name='{$this->MapaNazw[$Nazwa]}[]' value='$WartoscPola' style='vertical-align: middle;'".(is_array($Wartosc) && in_array($WartoscPola , $Wartosc) ? " checked='checked'" : '')."$AtrybutyDodatkowe /> <b>$OpisPola</b></div>");
					}else{
						echo("<div style='white-space: nowrap; float: left; border: 1px solid #F0F0F0; padding: 3px;'><input type='checkbox' name='{$this->MapaNazw[$Nazwa]}[]' value='$WartoscPola' style='vertical-align: middle;'".(is_array($Wartosc) && in_array($WartoscPola , $Wartosc) ? " checked='checked'" : '')."$AtrybutyDodatkowe /> $OpisPola</div>");
					}
				}
				break;
			case 'podzbiór_radio':
				foreach ($this->Pola[$Nazwa]['opcje']['elementy'] as $WartoscPola => $OpisPola) {
					echo("<div style='white-space: nowrap; float: left; border: 1px solid #F0F0F0; padding: 3px;'><input type='radio' name='{$this->MapaNazw[$Nazwa]}' value='$WartoscPola' style='vertical-align: middle;'".($WartoscPola == $Wartosc ? " checked='checked'" : '')."$AtrybutyDodatkowe /> $OpisPola</div>");
				}
				break;
			case 'checkbox':
				echo("<input type='checkbox' name='{$this->MapaNazw[$Nazwa]}' value='1' id='$pole_id' style='vertical-align: middle;'".($Wartosc == 1 ? " checked='checked'" : '')."$AtrybutyDodatkowe />");
				break;
			case 'hidden':
				echo("<input type='hidden' id='$pole_id' name='{$this->MapaNazw[$Nazwa]}' value='$Wartosc'>");
				break;
                        case 'hidden_lista':
				echo("<input type='hidden' id='$pole_id' name='{$this->MapaNazw[$Nazwa]}' value='$Wartosc'>");
                                echo $this->Pola[$Nazwa]['opcje']['elementy'][$Wartosc];
				break;
			case 'sam_tekst':
				echo("{$this->Pola[$Nazwa]['opis']}");
				break;
			case 'tekstowo':
				echo $Wartosc;
				break;
			case 'tekstowo_lista':
				echo $this->Pola[$Nazwa]['opcje']['elementy'][$Wartosc];
				break;
			case 'zapisz_anuluj':
                                ?><div style="width: 210px; margin: 0 auto;"><?php
				foreach($this->Pola[$Nazwa]['opcje']['elementy'] as $Pola){
					if(isset($Pola['type']) && $Pola['type'] == "button"){
                                                $Onclick = (isset($Pola['onclick']) ? $Pola['onclick'] : "SubmitForm();");
                                                FormularzSimple::PoleSubmit("{$Pola['etykieta']}", "{$Pola['etykieta']}", "class='search-button' style='float: left; padding: 7px 20px; margin-top: 5px;'");
					}else if(isset($Pola['type']) && $Pola['type'] == "submit"){
                                            $this->Form->PoleSubmit("zapisz", "", "class='search-button'");
                                        }else{
                                            ?>
                                            <a href="<?php echo $Pola['link']; ?>" class="like-button" style="float: left; margin-left: 15px; color: #FF0000;"><?php echo $Pola['etykieta']; ?></a>
                                            <?php
					}
				}
                                ?></div><?php
				break;
                             case 'zapisz_anuluj_old':
				foreach($this->Pola[$Nazwa]['opcje']['elementy'] as $Pola){
					if(isset($Pola['type']) && $Pola['type'] == "button"){
                                                $Onclick = (isset($Pola['onclick']) ? $Pola['onclick'] : "SubmitForm();");
						echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='image' onclick='$Onclick' src='images/{$Pola['src']}' title='{$Pola['etykieta']}' alt='{$Pola['etykieta']}' style='display: inline; vertical-align: middle; margin-top: 1px;' class='butt-zapisz'>");
					}else if(isset($Pola['type']) && $Pola['type'] == "submit"){
                                            $this->Form->PoleSubmit("zapisz", "", "class='zapisz'");
                                        }else{
						echo ("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='{$Pola['link']}'><img src='images/{$Pola['src']}' title='{$Pola['etykieta']}' alt='{$Pola['etykieta']}' style='display: inline; vertical-align: middle;'> </a>");
					}
				}
				break;
			default:
				$this->PolaFormularzaNiestandardowe($this->Pola[$Nazwa]['typ'], $Nazwa, $Wartosc, $AtrybutyDodatkowe, $pole_id);
				break;
		}
		if($OpisDodatkowyZa != ''){
			echo "$OpisDodatkowyZa";
		}
	      if( !in_array($Nazwa, $this->_SamePola)){
                echo "</div>";
                if( !in_array($Nazwa, $this->_PolaBezRamki)){
                    echo "</div>";
                }
                echo "</div>";
	      }
 
	}

        /**
         * Wyświetla formularz
         * @param array $Wartosci
         * @param boolean $MapujPola - czy mapy pola są oryginalne (false) czy zmapowane (true)
         */
	function Wyswietl(&$Wartosci = null, $MapujPola = true) {
                $this->FormWyswietl = __FUNCTION__;
		if(count($this->ElementyZEdytorem) > 0){
                    ?>
                    <script type="text/javascript" src="js/CERTEditorCK/ckeditor.js"></script>
                    <script src="js/CERTEditorCK/adapters/jquery.js"></script>
                    <script type="text/javascript">
                        $( document ).ready( function() {
                        <?php
                            foreach($this->ElementyZEdytorem as $areas){
                                ?>$( 'textarea#<?php echo $areas; ?>' ).ckeditor();
                            <?php
                                }
                        ?>
                        } );
                    </script>
                    <?php
                }
                echo "<div style='margin: 16px auto; width:$this->ContainerWidth;'>";
                echo '<div id="div-super-form">';
                $this->Form->FormStart('formularz', $this->LinkAkceptacji, "post", false, "super-form");
		if ($this->Tytul) {
			echo '<div class="super-form-title">'.$this->Tytul.'</div>';
		}
		echo("<input type='hidden' id='OpcjaFormularza' name='OpcjaFormularza' value='zapisz'>");
                
		for ($i = 0; $i < count($this->Kolejnosc); $i++) {
			$NazwaPola = ($MapujPola ? $this->MapaNazw[$this->Kolejnosc[$i]] : $this->Kolejnosc[$i]);
			if (isset($Wartosci[$NazwaPola])) {
				$this->WyswietlPole($this->Kolejnosc[$i], $Wartosci[$NazwaPola]);
			}else if(isset($_SESSION['Filtry'][$NazwaPola])){
				$this->WyswietlPole($this->Kolejnosc[$i], $_SESSION['Filtry'][$NazwaPola]);
			}else {
				$this->WyswietlPole($this->Kolejnosc[$i]);
			}
		}
                $this->Form->FormEnd();
		echo("</div>\n</div>");
	}

        /**
         * Wyświetla pole w formie bez edycji (wersji tekstowej)
         * @param string $Nazwa
         * @param string, array $Wartosc
         */
	function WyswietlPoleDanych($Nazwa, &$Wartosc) {
            $AtrybutyDodatkowe = $OpisDodatkowyZa = $OpisDodatkowyPrzed = '';
/*            $AtrybutyDodatkoweTR = '';
		if (isset($this->Pola[$Nazwa]['opcje']['tabelka'])) {
			if (isset($this->Pola[$Nazwa]['opcje']['tabelka']['tr_start'])) {
				echo "<tr$AtrybutyDodatkoweTR>";
			}
			if (isset($this->Pola[$Nazwa]['opcje']['tabelka']['th_show'])) {
				echo "<th>{$this->Pola[$Nazwa]['opis']}</th>";
			}
			if (isset($this->Pola[$Nazwa]['opcje']['tabelka']['td_start'])) {
					$StylTedek = (isset($this->Pola[$Nazwa]['opcje']['tabelka']['td_style']) ? " style = '{$this->Pola[$Nazwa]['opcje']['tabelka']['td_style']}'" : '');
					$ColspanTedek = (isset($this->Pola[$Nazwa]['opcje']['tabelka']['td_colspan']) ? " colspan = '{$this->Pola[$Nazwa]['opcje']['tabelka']['td_colspan']}'" : '');
					$RowspanTedek = (isset($this->Pola[$Nazwa]['opcje']['tabelka']['td_rowspan']) ? " rowspan = '{$this->Pola[$Nazwa]['opcje']['tabelka']['td_rowspan']}'" : '');
				echo "<td$StylTedek$ColspanTedek$RowspanTedek>";
			}
		}*/
		if (isset($this->Pola[$Nazwa]['opcje']['atrybuty'])) {
                    foreach ($this->Pola[$Nazwa]['opcje']['atrybuty'] as $Atrybut => $WartoscAtrubutu) {
                        $AtrybutyDodatkowe .= " $Atrybut='$WartoscAtrubutu'";
                    }
		}
		if (isset($this->Pola[$Nazwa]['opcje']['opis_dodatkowy_za'])) {
                    $OpisDodatkowyZa = $this->Pola[$Nazwa]['opcje']['opis_dodatkowy_za'];
		}
		if (isset($this->Pola[$Nazwa]['opcje']['opis_dodatkowy_przed'])) {
			$OpisDodatkowyPrzed = $this->Pola[$Nazwa]['opcje']['opis_dodatkowy_przed'];
		}
                $pole_id = (isset($this->Pola[$Nazwa]['opcje']['id']) ? $this->Pola[$Nazwa]['opcje']['id'] : $this->MapaNazw[$Nazwa]);
                $divFormClass = (isset($this->Pola[$Nazwa]['opcje']['div_class']) ? $this->Pola[$Nazwa]['opcje']['div_class'] : $this->_DefaultDivClass);
                $divFormAttr = (isset($this->Pola[$Nazwa]['opcje']['div_attr']) ? $this->Pola[$Nazwa]['opcje']['div_attr'] : '');
                
                                
	      if( !in_array($Nazwa, $this->_SamePola)){
                echo "<div class='{$divFormClass}' {$divFormAttr}>";
                if( !in_array($Nazwa, $this->_PolaBezLabel)){
                    if(isset($this->Pola[$Nazwa]['opcje']['show_label']) && $this->Pola[$Nazwa]['opcje']['show_label'] === true) {
                        echo "<div class='super-form-label'><label for='{$this->MapaNazw[$Nazwa]}'>{$OpisDodatkowyPrzed} {$this->Pola[$Nazwa]['opis']}</label>";
                    }elseif(isset($this->Pola[$Nazwa]['opcje']['show_label']) && $this->Pola[$Nazwa]['opcje']['show_label'] !== false) {
                        echo "<div class='super-form-label'>{$OpisDodatkowyPrzed}&nbsp;</label>";
                    }else{
                        echo "<div class='super-form-label'>&nbsp;</label>";
                    }
                    if( isset($this->Pola[$Nazwa]['opcje']['tooltip']) && !empty($this->Pola[$Nazwa]['opcje']['tooltip']) && $this->_ToolTipOn) {
                        ?><div class='tooltip_trigger' tooltip='#div_tooltip_<?php echo $this->MapaNazw[$Nazwa]; ?>'><img src="images/information.gif" title="" alt=""></div>
                        <div id="div_tooltip_<?php echo $this->MapaNazw[$Nazwa]; ?>" class="tooltip_content"><?php echo $this->Pola[$Nazwa]['opcje']['tooltip']; ?></div><?php
                    }
                    echo "</div><div class='clear'></div>";
                }
		echo "<div class='super-form-div-dane'>";
		if( !in_array($Nazwa, $this->_PolaBezRamki)){
		    echo "<div class='super-form-field-dane'>";
		}
	      }
		switch ($this->Pola[$Nazwa]['typ']) {
                        case 'brak':
                            break;
                        case 'clear':
                            echo '<div class="clear">&nbsp;</div>';
                            break;
			case 'tekst_link':#pole niestandardowe
			case 'tekst_data':
			case 'tekst':
			case 'tekstowo':
				echo stripslashes($Wartosc);
				break;
			case 'tekst_lang':
				foreach($this->Pola[$Nazwa]['opcje']['languages'] as $LanguageID => $Flag){
					$Ikona = "flags/$Flag";
					echo("<img src='$Ikona' style='display: inline; vertical-align: middle; margin: 6px 3px;'/>&nbsp;&nbsp;".nl2br(stripslashes($Wartosc[$LanguageID]))."<br />\n");
				}
				break;	
			case 'email':
				echo("<a href='mailto:".htmlspecialchars($Wartosc, ENT_QUOTES)."'>".htmlspecialchars($Wartosc, ENT_QUOTES)."</a>");
				break;
				
			case 'tekst_dlugi':
				echo("".nl2br(stripslashes($Wartosc))."\n");
				break;
			case 'tekst_dlugi_lang':
				foreach($this->Pola[$Nazwa]['opcje']['languages'] as $LanguageID => $Flag){
					$Ikona = "flags/$Flag";
					echo("<img src='$Ikona' style='display: inline; vertical-align: middle; margin: 6px 3px;'/><br />".nl2br(stripslashes($Wartosc[$LanguageID]))."<br />\n");
				}
				break;
				
			case 'obraz':
				if (file_exists($Wartosc)) {
					$Wymiary = $this->ZmienWielkoscWyswietlonegoObrazka(350,350,$Wartosc);
					echo("<img src='".$Wartosc.'?'.time()."' width='{$Wymiary['x']}' height='{$Wymiary['y']}' \>");
				}
				else {
					echo('<div style="width: 100px; height: 100px; padding: 0; text-align: center; border: 1px solid gray; vertical-align: middle; line-height: 100px;">brak zdjęcia</div>');
				}
				break;
			
			case 'podzbiór_radio':
                        case 'hidden_lista':
			case 'lista':
                        case 'lista_to_input':
			case 'tekstowo_lista':
                        case 'lista-chosen':
			      if(is_array($Wartosc ))
				foreach($Wartosc as $war)
				  echo(isset($this->Pola[$Nazwa]['opcje']['elementy'][$war]) ? $this->Pola[$Nazwa]['opcje']['elementy'][$war].", " : '');
			      break;
                        case 'lista-chosen-single':
                                echo(isset($this->Pola[$Nazwa]['opcje']['elementy'][$Wartosc]) ? $this->Pola[$Nazwa]['opcje']['elementy'][$Wartosc] : '');
				break;
				
			case 'podzbiór':
			case 'podzbiór_lista':
			case 'podzbiór_checkbox_1n':
			case 'podzbiór_checkbox_nn':
			case 'podzbiór_checkbox_1n_zakladki':
				$Opcje = array();
				for ($i = 0; $i < count($Wartosc); $i++) {
					$Opcje[] = $this->Pola[$Nazwa]['opcje']['elementy'][$Wartosc[$i]];
				}
				echo("".(implode(', ', $Opcje))."");
				break;			
			case 'lista_obrazow':
                            if (is_array($Wartosc) && count($Wartosc)){
                                echo "<div>";
                                    foreach($Wartosc as $Zdjecie){
                                        if(file_exists($Zdjecie["sciezka"]) && file_exists($Zdjecie["sciezka_min"])) { ?>
                                            <div class="getphoto">
                                            <a href="<?php echo $Zdjecie["sciezka"]; ?>"
                                               target="_blank"><img src='<?php echo $Zdjecie["sciezka_min"]; ?>'></a>
                                            </div>
                                            <?php
                                        }
                                    }
                                    echo "</div>";
				}
				break;
			case 'lista_plikow':
				if (is_array($Wartosc) && count($Wartosc)){
					echo "<table style='border: 0;' cellpadding='0' cellspacing='2'>";
					foreach($Wartosc as $Zdjecie){
						if(file_exists($Zdjecie["sciezka"])) {
							echo "<tr>";
								echo "<td style='border: 0; width: 250px; vertical-align: top;'>";
									//echo "{$Zdjecie['nazwapliku']}"; ?>
                                                                        <div class="getfile">
                                                                        <a href="<?php echo $Zdjecie["sciezka"]; ?>"
                                                                           target="_blank"><?php echo $Zdjecie['nazwapliku']; ?></a>
                                                                        </div>
                                                                        <?php
								echo "</td>";
							echo "</tr>";
						}
					}
					echo "</table>";
				}
				break;
                        case 'sam_tekst':
				echo $Wartosc;
				break;
                        case 'checkbox':
				echo ($Wartosc == 1) ? '<img src="images/good.png" alt="checked">' : '<img src="images/cross.gif" alt="checked">';
				break;
			default:
				$this->PolaFormularzaNiestandardoweDane($this->Pola[$Nazwa]['typ'], $Nazwa, $Wartosc, $AtrybutyDodatkowe, $pole_id);
				break;
		}
		if($OpisDodatkowyZa != ''){
			echo "$OpisDodatkowyZa";
		}
                if( !in_array($Nazwa, $this->_SamePola)){
                    echo "</div>";
                    if( !in_array($Nazwa, $this->_PolaBezRamki)){
                        echo "</div>";
                    }
                    echo "</div>";
	      }
/*		if (isset($this->Pola[$Nazwa]['opcje']['tabelka'])) {
			if (isset($this->Pola[$Nazwa]['opcje']['tabelka']['tr_end'])) {
				echo "</tr>\n";
			}
			if (isset($this->Pola[$Nazwa]['opcje']['tabelka']['td_end'])) {
				echo "</td>";
			}
		}*/
	}

         /**
         * Wyświetla formularz w formie bez edycji (bez pól formularza tylko treść)
         * @param array $Wartosci
         */
	function WyswietlDane(&$Wartosci) {
                $this->FormWyswietl = __FUNCTION__;
                echo "<div style='margin: 16px auto; width:$this->ContainerWidth;'>";
                echo '<div id="div-super-form">';
                for ($i = 0; $i < count($this->Kolejnosc); $i++) {
			$this->WyswietlPoleDanych($this->Kolejnosc[$i], $Wartosci[$this->Kolejnosc[$i]]);
		}
/*                if(isset($this->LinkRezygnacji)){ ?>
                    <div class="<?php echo $this->_DefaultDivClass; ?>">
                    <a href="<?php echo $this->LinkRezygnacji; ?>" class="like-button" style="float: left; margin-left: 15px; color: #FF0000;">Powrót</a>
                    </div>
                <?php } */ ?>
		  <div class="clear"></div>
		</div>
            </div>
	<?php } 
 
        /**
 
         * Wyświetla formularz w formie do wydruku
         * @param array $Wartosci
         */
	function WyswietlWydruk(&$Wartosci) {
            	echo('<div class="formularz" style="width: 600px; margin: 0 auto;">');
                echo "<div style='margin: 16px auto; width:$this->ContainerWidth;'>";
                echo '<div id="div-super-form">';
		for ($i = 0; $i < count($this->Kolejnosc); $i++) {
			$this->WyswietlPoleDanych($this->Kolejnosc[$i], $Wartosci[$this->Kolejnosc[$i]]);
		}
                echo '</div></div></div>';
	}

        /**
         *  Zwraca wartosci wszystkich pól zmieniając nazwy pol na oryginalne
         *  @param array $Wartosci
         *  @param boolean $MapujPola - czy pola mają oryginalne nazwy czy zmapowane
         *  @return array
         */
	function ZwrocWartosciPol(array &$Wartosci, $MapujPola = true) {
		$Wynik = array();
		for ($i = 0; $i < count($this->Kolejnosc); $i++) {
			$Nazwa = ($MapujPola ? $this->MapaNazw[$this->Kolejnosc[$i]] : $this->Kolejnosc[$i]);
			if (isset($Wartosci[$Nazwa])) {
			$Wynik[$this->Kolejnosc[$i]] = $Wartosci[$Nazwa];
			}
		}
		return $Wynik;
	}

        /**
         * Zwraca wartość pola zmieniając nazwę pola na oryginalną
         * @param array $Wartosci
         * @param string $Nazwa
         * @param boolean $MapujPola - określa czy użyta nazwa jest oryginalna czy zmapowana
         * @return wartość pola
         */
	function ZwrocWartoscPola(array &$Wartosci, $Nazwa, $MapujPola = true) {
		$Wynik = false;
		if ($MapujPola) {
			$Nazwa = $this->MapaNazw[$Nazwa];	
		}
		if (isset($Wartosci[$Nazwa])) {
			$Wynik = $Wartosci[$Nazwa];
		}
		return $Wynik;
	}

        /**
         * Zwraca tablicę przesłanych plików - używane przy wgrywaniu obrazków w formularzu
         * @return array
         */
	function ZwrocDanePrzeslanychPlikow() {
		$Wynik = array();
		for ($i = 0; $i < count($this->Kolejnosc); $i++) {
			if ($this->Pola[$this->Kolejnosc[$i]]['typ'] == 'obraz') {
				if (is_uploaded_file($_FILES[$this->MapaNazw[$this->Kolejnosc[$i]]]['tmp_name'])) {
					$Wynik[$this->Kolejnosc[$i]] = $_FILES[$this->MapaNazw[$this->Kolejnosc[$i]]];
					$Wynik[$this->Kolejnosc[$i]]['prefix'] = $this->Pola[$this->Kolejnosc[$i]]['opcje']['prefix'];
				}
			}
		}
		return $Wynik;
	}

        /**
         * Zwraca opcje pola
         * @param string $Nazwa
         * @param string $NazwaOpcji
         * @param boolean $MapujPola - czy nazwa pola jest oryginalna czy zmapowana
         * @return mixed
         */
	function ZwrocOpcjePola($Nazwa, $NazwaOpcji = null, $MapujPola = true) {
		if ($MapujPola) {
			$OdwrotnaMapaNazw = array_flip($this->MapaNazw);
			$Nazwa = $OdwrotnaMapaNazw[$Nazwa];	
		}
		if ($NazwaOpcji) {
			if (isset($this->Pola[$Nazwa]['opcje'][$NazwaOpcji])) {
				return $this->Pola[$Nazwa]['opcje'][$NazwaOpcji];
			}
			else {
				return null;
			}
		}
		else {
			return $this->Pola[$Nazwa]['opcje'];
		}
	}

        /**
         * Zwraca nazwy wszystkich pól, można zawęzić do określonego typu
         * @param string $Typ
         * @return array
         */
	function ZwrocPola($Typ = null) {
		$Wynik = array();
		for ($i = 0; $i < count($this->Kolejnosc); $i++) {
			if (!$Typ || $this->Pola[$this->Kolejnosc[$i]]['typ'] == $Typ) {
				$Wynik[] = $this->Kolejnosc[$i];
			}
		}
		return $Wynik;
	}

        /**
         * Zwraca typ pola
         * @param string $Nazwa
         * @param boolean $MapujPola - czy nazwa pola jest oryginalna czy zmapowana
         * @return string
         */
	function ZwrocTypPola($Nazwa, $MapujPola = true) {
		if ($MapujPola) {
			$OdwrotnaMapaNazw = array_flip($this->MapaNazw);
			$Nazwa = $OdwrotnaMapaNazw[$Nazwa];	
		}
		return (isset($this->Pola[$Nazwa]) ? $this->Pola[$Nazwa]['typ'] : null);
	}

        /**
         * Sprawdza czy pole ma ustawioną opcję decimal = true
         * @param string $Nazwa
         * @param boolean $MapujPola - czy nazwa pola jest oryginalna czy zmapowana
         * @return boolean
         */
	function ZwrocCzyDecimal($Nazwa, $MapujPola = true) {
		if ($MapujPola) {
			$OdwrotnaMapaNazw = array_flip($this->MapaNazw);
			$Nazwa = $OdwrotnaMapaNazw[$Nazwa];	
		}
		if(isset($this->Pola[$Nazwa]) && isset($this->Pola[$Nazwa]['opcje']) && isset($this->Pola[$Nazwa]['opcje']['decimal'])){
		  return $this->Pola[$Nazwa]['opcje']['decimal'];
		}else{
		  return false;
		}
	}

        /**
         * Zwraca tablicę pól wymaganych
         * @return array
         */
	function ZwrocPolaWymagane(){
		return $this->PolaWymagane;
	}

        /**
         * Zwraca tablicę pól które nie mogą być zdublowane
         * @return <type>
         */
	function ZwrocPolaNieDublujace(){
            return $this->PolaNieDublujace;
	}

        /**
         * Ustawia action formularza
         * @param string $Link
         */
	function UstawLinkAkceptacji($Link){
		$this->LinkAkceptacji = $Link;
	}

        /**
         * Zmienia wielkość wyświetlanego w formularzu obrazka, zwraca nowe wymiary
         * @param int $max_szer
         * @param int $max_wys
         * @param string $plik - ścieżka do pliku
         * @return array
         */
	function ZmienWielkoscWyswietlonegoObrazka($max_szer,$max_wys,$plik){
		list($width, $height) = getimagesize($plik);
		$SzerokoscMax = (is_null($max_szer) ? $width : $max_szer);
		$WysokoscMax = (is_null($max_wys) ? $height : $max_wys);
		$aspekt_x=$SzerokoscMax/$width;
		$aspekt_y=$WysokoscMax/$height;
		if (($width<=$SzerokoscMax)&&($height<=$WysokoscMax)) {
			$newwidth=$width;
			$newheight=$height;
		}
		else if (($aspekt_x*$height)<$WysokoscMax) {
			$newheight=ceil($aspekt_x*$height);
			$newwidth=$SzerokoscMax;
		}
		else {
			$newwidth=ceil($aspekt_y*$width);
			$newheight=$WysokoscMax;
		}
		
		$TablicaZwroc = array('x' => $newwidth, 'y' => $newheight);
		return $TablicaZwroc;
	}

        /**
         * Wyświetla pola niestandardowe - których nie ma w funkcji WyswietlPole
         * @param string $Typ
         * @param string $Nazwa
         * @param string $Wartosc
         * @param string $AtrybutyDodatkowe
         * @param string $pole_id
         */
	function PolaFormularzaNiestandardowe($Typ, $Nazwa, $Wartosc, $AtrybutyDodatkowe, $pole_id){
		switch($Typ){
                    case 'haslo':
                        include(SCIEZKA_SZABLONOW."pola-formularzy/haslo-generator.tpl.php");
                        break;
                    case 'uprawnienia':
                        include(SCIEZKA_SZABLONOW."pola-formularzy/uprawnienia.tpl.php");
                        break;
                    case 'lista_kategoria_urzadzenia':
                        include(SCIEZKA_SZABLONOW."pola-formularzy/lista-kategoria-urzadzenia.tpl.php");
                        break;
                    case 'przeglad_gwarancyjny_miesiace':
                        include(SCIEZKA_SZABLONOW."pola-formularzy/przeglad-gwarancyjny-miesiace.tpl.php");
                        break;
                    case 'lista_nazw_urzadzen':
                        include(SCIEZKA_SZABLONOW."pola-formularzy/lista-nazw-urzadzen.tpl.php");
                        break;
                    case 'lista_parametrow_urzadzenia':
                        include(SCIEZKA_SZABLONOW."pola-formularzy/lista-wymaganych-parametrow.tpl.php");
                        $this->_Plugins['lista_parametrow_urzadzenia'] = true;
                        break;
                    case 'pole_warranty_service':
                        include(SCIEZKA_SZABLONOW."pola-formularzy/warranty_service.tpl.php");
                        break;
                    case 'rozmieszczenie_czesci':
                        include(SCIEZKA_SZABLONOW."pola-formularzy/rozmieszczenie_czesci.tpl.php");
                        break;
                    case 'required_checkbox':
                        include(SCIEZKA_SZABLONOW."pola-formularzy/required-checkbox.tpl.php");
                        break;
                    case 'dane_klienta':
                        include(SCIEZKA_SZABLONOW."pola-formularzy/dane-klienta.tpl.php");
                        break;
		}
	}

        /**
         * Wyświetla pola niestandardowe bez edycji - których nie ma w funkcji WyswietlPoleDane
         * @param string $Typ
         * @param string $Nazwa
         * @param string $Wartosc
         */
	function PolaFormularzaNiestandardoweDane($Typ, $Nazwa, $Wartosc, $AtrybutyDodatkowe, $pole_id){
            switch($Typ){
		    case 'uprawnienia':
                        include(SCIEZKA_SZABLONOW."pola-formularzy/uprawnienia.tpl.php");
                        break;
                    case 'lista_kategoria_urzadzenia':
                        include(SCIEZKA_SZABLONOW."pola-formularzy/lista-kategoria-urzadzenia.tpl.php");
                        break;
                    case 'przeglad_gwarancyjny_miesiace':
                        include(SCIEZKA_SZABLONOW."pola-formularzy/przeglad-gwarancyjny-miesiace.tpl.php");
                        break;
                    case 'lista_nazw_urzadzen':
                        include(SCIEZKA_SZABLONOW."pola-formularzy/lista-nazw-urzadzen.tpl.php");
                        break;
                    case 'lista_parametrow_urzadzenia':
                        include(SCIEZKA_SZABLONOW."pola-formularzy/lista-wymaganych-parametrow.tpl.php");
                        break;
                    case 'pole_warranty_service':
                        include(SCIEZKA_SZABLONOW."pola-formularzy/warranty_service.tpl.php");
                        break;
                    case 'rozmieszczenie_czesci':
                        include(SCIEZKA_SZABLONOW."pola-formularzy/rozmieszczenie_czesci.tpl.php");
                        break;
                    case 'historia_czynnosci':
                        include(SCIEZKA_SZABLONOW."pola-formularzy/actions_history.tpl.php");
                        break;
                    case 'zamowienia_czesci':
                        include(SCIEZKA_SZABLONOW."pola-formularzy/parts_purchases.tpl.php");
                        break;
                    case 'zamowienia_czesci_realizacja':
                        include(SCIEZKA_SZABLONOW."pola-formularzy/parts_purchases_realization.tpl.php");
                        break;
                    case 'required_checkbox':
                        include(SCIEZKA_SZABLONOW."pola-formularzy/required-checkbox.tpl.php");
                        break;
                    case 'dane_klienta':
                        include(SCIEZKA_SZABLONOW."pola-formularzy/dane-klienta.tpl.php");
                        break;                        
                        
            }
	}

        function SetContainerWidth($Width){
            $this->ContainerWidth = $Width;
        }
        function UsunZKolejnosci($Nazwa){
            //$KolejnoscID = array_search($Nazwa, $this->Kolejnosc);
            
            //unset( $this->Kolejnosc[$KolejnoscID]);
            array_pop( $this->Kolejnosc );
            //unset( $this->MapaNazw[$Nazwa]);
            
        }
        
        function DodajDoKolejnosci($Nazwa){
            $this->Kolejnosc[] = $Nazwa;
            $this->MapaNazw[$Nazwa] = $this->Prefix.'pole_'.count($this->Kolejnosc); ;
        }
        
        function DodajDoPolBezRamki($Nazwa){
            $this->_PolaBezRamki[] = $Nazwa;
        }
        
        function DodajDoPolBezLabel($Nazwa){
            $this->_PolaBezLabel[] = $Nazwa;
        }
        
	function DodajDoSamePola($Nazwa){
            $this->_SamePola[] = $Nazwa;
        }
        
        function SetToolTipOn($val = true){
            $this->_ToolTipOn = $val;
        }
        
                /**
         * Zwraca nazwę zmapowaną pola
         * @param string $Nazwa - nazwa oryginalna pola
         * @return string or false
         */
	function ZwrocNazwePola($Nazwa){
            $flip = array_flip($this->MapaNazw);
            if($flip[$Nazwa]){
                    return $flip[$Nazwa];
            }else{
                    return false;
            }
	}
}
?>
