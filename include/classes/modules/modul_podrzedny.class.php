<?php
/**
 * Moduł podrzędny
 * 
 * @author		Lukasz Piekosz <mentat@mentat.net.pl>
 * @copyright	Copyright (c) 2004-2007 ARTplus
 * @package		PanelPlus
 * @version		1.0
 */

/**
 * Moduł podrzędny
 *
 */
abstract class ModulPodrzedny extends ModulBazowy {

	protected $ModulNadrzedny;
	protected $NadrzedneID;
	
	/**
	 * Konstruktor
	 *
	 */
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
		parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
		$this->ModulNadrzedny = (isset($_GET['nadrzedny']) ? $_GET['nadrzedny'] : $Parametr);
		$this->NadrzedneID = (isset($_GET['nadrzedny_id']) ? intval($_GET['nadrzedny_id']) : null);
		$this->LinkPowrotu = "?modul=$this->ModulNadrzedny";
		if ($this->NadrzedneID > 0) {
			$this->LinkPowrotu .= "&amp;akcja=szczegoly&amp;id=$this->NadrzedneID";
		}
	}
	
	function &GenerujFormularz() {
		$Formularz = parent::GenerujFormularz();
		$Formularz->UstawLinkRezygnacji($this->LinkPowrotu);
		return $Formularz;
	}
	
	function WyswietlPanel($ID = null) {
?>
			<p><b>Akcje</b></p>
			<div>
				<?php echo($this->Nazwa); ?>:
				<div style="margin: 10px 0 30px 5px;">
					<a href="?modul=<?php echo($this->Parametr); ?>&akcja=dodawanie&amp;<?php echo("nadrzedny=$this->ModulNadrzedny&amp;nadrzedny_id=$this->NadrzedneID"); ?>"><img src="images/add.gif" style="display: inline; vertical-align: middle;"> Dodaj</a><br>
<?php
		if ($ID) {
?>
					<a href="?modul=<?php echo($this->Parametr); ?>&akcja=duplikacja&id=<?php echo("$ID&amp;nadrzedny=$this->ModulNadrzedny&amp;nadrzedny_id=$this->NadrzedneID"); ?>"><img src="images/page_stack.gif" style="display: inline; vertical-align: middle;"> Duplikuj</a><br>
					<a href="?modul=<?php echo($this->Parametr); ?>&akcja=edycja&id=<?php echo("$ID&amp;nadrzedny=$this->ModulNadrzedny&amp;nadrzedny_id=$this->NadrzedneID"); ?>"><img src="images/pencil.gif" style="display: inline; vertical-align: middle;"> Edytuj</a><br>
					<a href="?modul=<?php echo($this->Parametr); ?>&akcja=kasowanie&id=<?php echo("$ID&amp;nadrzedny=$this->ModulNadrzedny&amp;nadrzedny_id=$this->NadrzedneID"); ?>"><img src="images/bin_empty.gif" style="display: inline; vertical-align: middle;"> Kasuj</a><br>
<?php
		}
?>
				</div>
			</div>
<?php	
	}
}
?>
