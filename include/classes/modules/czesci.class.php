<?php
/**
 * Moduł części
 * 
 * @author		Michal Bugański <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2012
 * @package		Panelplus 
 * @version		1.0
 */

class ModulCzesci extends ModulBazowy {
        private $TabelaReplacementParts;
        private $TabelaDeviceParts;
        private $TabelaPartsCategory;
        private $SaveReplacementParts = array();
        private $SaveDeviceParts = array();
        private $SavePartsCategory = array();
        private $Devices = false;

	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->ModulyBezMenuBocznego[] = $this->Parametr;
            if(!$this->Uzytkownik->IsAdmin()){
                $this->ModulyBezDodawania[] = $this->Parametr;
                $this->ModulyBezEdycji[] = $this->Parametr;
                $this->ModulyBezKasowanie[] = $this->Parametr;
            }
            $this->Tabela = 'parts';
            $this->TabelaReplacementParts = 'replacement_parts';
            $this->TabelaDeviceParts = 'device_parts';
            $this->TabelaPartsCategory = 'parts_category_parts';
            $this->PoleID = 'id';
            $this->PoleNazwy = "title";
            $this->PoleSort = "title";
            $this->PoleVisible = "visible";
            $this->Nazwa = 'Części';
            $this->Filtry[] = array('nazwa' => 'kzc|title', 'opis' => false, "typ" => "tekst", "placeholder" => "Znajdź część", 'search' => true);
            $this->ErrorsDescriptions['unikalne']['kzc'] = "Część zamienna o KZC %s  jest już zarejestrowana w bazie . Aby zarejestrować część zamienną należy podać unikatowy KZC";
            $this->CzySaOpcjeWarunkowe = true;
            $this->TabelaZdjecia =  $this->Tabela.'_file';
            $this->TabelaZdjeciaPrefix = $this->TabelaZdjecia.'_';
            $this->PoleZdjecia = $this->TabelaZdjeciaPrefix.'name';
            $this->PoleZdjeciaOryginalnego = $this->TabelaZdjeciaPrefix.'orginal_name';
            $this->PoleZdjeciaID = $this->TabelaZdjeciaPrefix.'id';
            $this->PoleZdjeciaHASH = $this->TabelaZdjeciaPrefix.'hash';
            
            
            
//            $this->NoSort = array('customer_order', 'delivery_order');
	}

        function &GenerujFormularz(&$Wartosci = Array(), $Mapuj = false) {
            $Formularz = parent::GenerujFormularz($Wartosci, $Mapuj);
            $Formularz->DodajPole('kzc', 'tekst', 'Numer katalogowy części*', array('show_label'=>true,'div_class'=>'super-form-span3',));
            $Formularz->DodajPole('title', 'tekst', 'Nazwa*', array('show_label'=>true,'div_class'=>'super-form-span3',));
            $Formularz->DodajPole('clear', 'clear', '', array());
            $Formularz->DodajPole('amount_id', 'lista-chosen-single', 'Cena', array('show_label'=>true,'div_class'=>'super-form-span3', 'elementy'=>$this->GetSelect('GrupyCenowe', true)));
            $Formularz->DodajPole('parts_category', 'lista-chosen', 'Kategoria części', array('show_label'=>true,'div_class'=>'super-form-span3','elementy'=> $this->GetSelect('KategorieCzesci')));
            $Formularz->DodajPole('clear', 'clear', '', array());
            $Formularz->DodajPole('description', 'tekst_dlugi', 'Opis', array('show_label'=>true,'div_class'=>'super-form-span1',));
            $Formularz->DodajPole('clear', 'clear', '', array());
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('device_parts', 'lista-chosen', 'Urządzenie', array('show_label'=>true,'div_class'=>'super-form-span3', 'elementy' => $this->GetDevices()));
                $Formularz->DodajPole('clear', 'clear', '', array());
            }
            $Formularz->DodajPoleWymagane("kzc", false);
            $Formularz->DodajPoleWymagane("title", false);
            $Formularz->DodajPoleNieDublujace("kzc", false);
            if($this->WykonywanaAkcja != 'dodawanie'){
                $Formularz->DodajPole ($this->PoleZdjecia, 'lista_obrazow', 'Załączniki',  array('show_label'=>true,'div_class'=>'super-form-span1', 'rozmiar_x'=>'800', 'rozmiar_y'=>'800' ) );
                $Formularz->DodajPole('clear', 'clear', '', array());
            }
            
            $Formularz->DodajDoPolBezRamki($this->PoleZdjecia);
            
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}

        function PobierzListeElementow($Filtry = array()) {
            $isTop = $this->Uzytkownik->FirmaRMC($this->Uzytkownik->ZwrocIdUzytkownika());
            $IDCompany = $this->Uzytkownik->ZwrocInfo('company_id');
            $Wyniki = array(
                'kzc' => 'Numer Katalogowy',
                'title' => "Nazwa",
//                'alternative' => array('naglowek' => 'Zamiennik', 'elementy' => Usefull::GetCheckImg(), 'td_class' => 'active-check', 'type' => 'checked-many'),
                'amount_id' => array('naglowek' => 'Cena EUR (netto)', 'type' => 'eur', 'td_styl' => 'text-align: right;',  'elementy'=>$this->GetSelect('GrupyCenowe', true)),
//                'delivery_order' => array('naglowek' => 'zamowienia dostawy', 'td_styl' => 'text-align: right;'),
            );

            if($this->Uzytkownik->IsAdmin()){
                $user = '';
            }elseif( $isTop ){
                $user = "AND company_id = '$IDCompany'";
            }else{
                $user = "AND user_id = '{$this->Uzytkownik->ZwrocIdUzytkownika()}'";
            }
/*            if($isTop || $this->Uzytkownik->IsAdmin()){
                $Wyniki['customer_order'] = array('naglowek' => 'zamowienia klienci', 'td_styl' => 'text-align: right;');
                $customer_order = ", (SELECT sum(li.quantity) FROM line_items li
                        LEFT JOIN  purchases p ON (li.purchase_id = p.id)
                        WHERE li.part_id = f.id
                        ".( $this->Uzytkownik->IsAdmin() ? "" : "AND p.idtop = '$IDCompany'" )."
                        AND p.status = 3) AS customer_order
                    ";
            }else{*/
                $customer_order = '';
//            }
//            $this->Baza->EnableLog();
            $Where = $this->GenerujWarunki();
            $Sort = $this->GenerujSortowanie();
            $this->Baza->Query($this->QueryPagination("SELECT f.*,
                    ( SELECT sum(li.quantity) FROM line_items li
                        LEFT JOIN  purchases p ON (li.purchase_id = p.id)
                        WHERE li.part_id = f.id
                        $user
                        AND p.status = 3) AS delivery_order
                        $customer_order
                    FROM $this->Tabela f
                    $Where $Sort", $this->ParametrPaginacji, 30));
            return $Wyniki;
	}

        function OperacjePrzedZapisem($Wartosci, $ID){
            if($Wartosci['alternative'] == 1){
                $this->SaveReplacementParts = $Wartosci['zamiennik'];
            }
            $this->SaveDeviceParts = $Wartosci['device_parts'];
            $this->SavePartsCategory = $Wartosci['parts_category'];
            unset($Wartosci['zamiennik']);
            unset($Wartosci['device_parts']);
            unset($Wartosci['parts_category']);
            if($ID){
                $Values = $this->PobierzDaneElementu($ID);
                foreach($Wartosci as $FieldID => $Value){
                    if($Value != $Values[$FieldID]){
                        $Wartosci['updated_at'] = date("Y-m-d H:i:s");
                        break;
                    }
                }
            }else{
                $Wartosci['created_at'] = date("Y-m-d H:i:s");
                $Wartosci['updated_at'] = date("Y-m-d H:i:s");
            }
            return $Wartosci;
        }

        function WykonajOperacjePoZapisie($Wartosci, $ID){
            $this->Baza->Query("DELETE FROM $this->TabelaReplacementParts WHERE replacement_id = '$ID'");
            if(is_array($this->SaveReplacementParts)){
                foreach($this->SaveReplacementParts as $RepID){
                    if(intval($RepID) > 0){
                        $Save['replacement_id'] = $ID;
                        $Save['part_id'] = $RepID;
                        $Save['created_at'] = date("Y-m-d H:i:s");
                        $Save['updated_at'] = date("Y-m-d H:i:s");
                        $Zapytanie = $this->Baza->PrepareInsert($this->TabelaReplacementParts, $Save);
                        $this->Baza->Query($Zapytanie);
                    }
                }
            }
            unset($Save);
            $this->Baza->Query("DELETE FROM $this->TabelaDeviceParts WHERE part_id = '$ID'");
            if(is_array($this->SaveDeviceParts)){
                foreach($this->SaveDeviceParts as $DevID){
                    if(intval($DevID) > 0){
                        $Save['device_id'] = $DevID;
                        $Save['part_id'] = $ID;
                        $Save['created_at'] = date("Y-m-d H:i:s");
                        $Save['updated_at'] = date("Y-m-d H:i:s");
                        $Zapytanie = $this->Baza->PrepareInsert($this->TabelaDeviceParts, $Save);
                        $this->Baza->Query($Zapytanie);
                    }
                }
            }
            unset($Save);
            $this->Baza->Query("DELETE FROM $this->TabelaPartsCategory WHERE part_id = '$ID'");
            if(is_array($this->SavePartsCategory)){
                foreach($this->SavePartsCategory as $CatID){
                    if(intval($CatID) > 0){
                        $Save['parts_category_id'] = $CatID;
                        $Save['part_id'] = $ID;
                        $Save['created_at'] = date("Y-m-d H:i:s");
                        $Save['updated_at'] = date("Y-m-d H:i:s");
                        $Zapytanie = $this->Baza->PrepareInsert($this->TabelaPartsCategory, $Save);
                        $this->Baza->Query($Zapytanie);
                    }
                }
            }
        }

        function GetActions($ID){
            $Dodawanie = array('link' => "?modul=$this->Parametr&akcja=dodawanie", 'class' => 'dodawanie', 'img' => 'add.gif', 'desc' => 'Nowa część');
            $Lista = array('link' => "?modul=$this->Parametr&akcja=lista", 'class' => 'lista', 'img' => 'list.gif', 'desc' => 'Lista części');
            if(!in_array($this->Parametr, $this->ModulyBezDodawania)){
                $Akcje['akcja'][] = $Dodawanie;
            }
            $Akcje['akcja'][] = $Lista;
            $Akcje['szczegoly'][] = array('img' => "koszyk.png", 'img_grey' => "koszyk_grey.png", 'desc' => "Dodaj do koszyka", "akcja_link" => "javascript:DoKoszyka({$this->ID}, ".(isset($_SESSION['basket-picked-order']) ? intval($_SESSION['basket-picked-order']) : '0').")");
/*            if($this->Uzytkownik->IsAdmin()){
                $Akcje['szczegoly'][] = array('link' => "?modul=$this->Parametr&akcja=edycja&id=$ID&back=details", 'class' => 'edycja', 'img' => 'pencil.gif', 'desc' => 'Edycja');
            }
 *
 */
            return $Akcje;
        }
        
        function GetPrices(){
            return $this->GetSelect('Prices');
        }

        function GetParts(){
            return $this->Baza->GetOptions("SELECT $this->PoleID, CONCAT($this->PoleNazwy,' [',kzc,'] ') as name_search FROM $this->Tabela ".(isset($_GET['id']) ? "WHERE $this->PoleID != '{$_GET['id']}'" : "")." ORDER BY $this->PoleNazwy ASC");
        }

        function GetDevices(){
            if($this->Devices != false){
                return $this->Devices;
            }
            $ModulUrzadzenia = new ModulUrzadzenia($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
            $this->Devices = $ModulUrzadzenia->GetList();
            return $this->Devices;
        }

        function &PobierzDaneDomyslne() {
            $Dane = parent::PobierzDaneDomyslne();
//            $Dane['item_id'] = "<small>Opcja dodana zostanie w II etapie po wprowadzeniu modułu Urządzenia</small>";
            $Dane['zamiennik'] = array();
            return $Dane;
        }

        function GetZamienniki($ID){
            return $this->Baza->GetValues("SELECT part_id FROM $this->TabelaReplacementParts WHERE replacement_id = '$ID'");
        }

        function GetDeviceParts($ID){
            return $this->Baza->GetValues("SELECT device_id FROM $this->TabelaDeviceParts WHERE part_id = '$ID'");
        }

        function GetKategorieCzesci($ID){
            return $this->Baza->GetValues("SELECT parts_category_id FROM $this->TabelaPartsCategory WHERE part_id = '$ID'");
        }
        
        function PobierzDaneElementu($ID, $Typ = null) {
            $Dane = parent::PobierzDaneElementu($ID, $Typ);
            $Dane['zamiennik'] = $this->GetZamienniki($ID);
            $Dane['parts_category'] = $this->GetKategorieCzesci($ID);
            if($Dane['zamiennik'] == false){
                $Dane['zamiennik'] = array();
            }
            $Dane['device_parts'] = $this->GetDeviceParts($ID);
            if($Dane['device_parts'] == false){
                $Dane['device_parts'] = array();
            }
            return $Dane;
        }

/*        function AkcjaSzczegoly($ID){
            $Dane = $this->PobierzDaneElementu($ID);
            $Dane['amount'] = number_format($Dane['amount'],2,',',' ')." zł";
            if(is_file($Dane['obraz'])){
                list($width, $height) = getimagesize($Dane['obraz']);
                $Zdjecie = array(
                    'src'=>$Dane['obraz'],
                    'width'=> $width,
                    'height' => $height,
                );
            }
            
            $PolaJeden['kzc'] = array("naglowek" => 'KZC', 'style' => 'width: 15%;');
            $PolaJeden['title'] = array("naglowek" => 'Nazwa', 'style' => '');
            $PolaJeden['amount'] = array("naglowek" => 'Cena (netto)', 'style' => 'width: 15%; text-align: right;');
            $PolaJeden['do_koszyka'] = array("naglowek" => "<img src='images/koszyk_grey.png' alt='Do koszyka' />",'style' => 'width: 100px;', "th_style" => "width: 130px;");
            if($Dane['amount'] > 0.00){
                $Dane['do_koszyka'] = Usefull::ShowBasketAddButton($ID);
            }else{
                $Dane['do_koszyka'] = "<img src='images/koszyk_grey.png' alt='Do koszyka' />";
            }
            
            $PolaDwa['description'] = array('naglowek' => 'Opis', 'long' => true);
            if($Dane['alternative'] > 0){
                $PolaDwa['zamiennik'] = array('naglowek' => '<b>Zamiennik</b>', 'long' => true, 'type' => 'zamiennik');
            }
            $PolaDwa['device_parts'] = array('naglowek' => '<b>Urządzenia</b>', 'long' => true, 'type' => 'urzadzenia');
            $Dane['created_at'] = UsefullTime::PolskiDzien(date("D, d.m.Y H:i:s", strtotime($Dane['created_at'])));
            $Dane['updated_at'] = UsefullTime::PolskiDzien(date("D, d.m.Y H:i:s", strtotime($Dane['updated_at'])));
            $PolaComments['created_at'] = 'Utworzono';
            $PolaComments['updated_at'] = 'Ostatnia zmiana';
            include(SCIEZKA_SZABLONOW."show.tpl.php");
        }*/

        function ShowUrzadzenia($DeviceParts, $Name = array()){
            $Dane = array();
            $ModulUrzadzenia = new ModulUrzadzenia($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
            $NazwyUrzadzen = $ModulUrzadzenia->GetListaNazw();
            foreach($DeviceParts as $ID){
                $Dane[$ID] = $ModulUrzadzenia->PobierzDaneElementu($ID);
                $Dane[$ID]['title'] = $NazwyUrzadzen[$Dane[$ID]['devices_title_devices_title_id']];
            }
            $PolaJeden['kzu'] = array("naglowek" => 'KZU', 'style' => 'width: 25%;');
            $PolaJeden['title'] = array("naglowek" => 'Nazwa', 'style' => 'width: 75%;');
            if(count($Dane) > 0){
                include(SCIEZKA_SZABLONOW."show-zamiennik.tpl.php");
            }
        }

        function ShowZamiennik($ZamiennikIDs = array(), $Name = array()){
            foreach($ZamiennikIDs as $ID){
                $Dane[$ID] = $this->PobierzDaneElementu($ID);
                $Dane[$ID]['amount'] = number_format($Dane[$ID]['amount'],2,',',' ')." zł";
                $Dane[$ID]['do_koszyka'] = Usefull::ShowBasketAddButton($ID);
            }
            $PolaJeden['kzc'] = array("naglowek" => 'KZC', 'style' => 'width: 25%;');
            $PolaJeden['title'] = array("naglowek" => 'Nazwa', 'style' => 'width: 60%;');
            $PolaJeden['amount'] = array("naglowek" => 'Cena', 'style' => 'width: 15%; text-align: right;');
            $PolaJeden['do_koszyka'] = array("naglowek" => "<img src='images/koszyk_grey.png' alt='Do koszyka' />", "th_style" => "width: 130px;");
            if(count($Dane) > 0){
                include(SCIEZKA_SZABLONOW."show-zamiennik.tpl.php");
            }
        }

        function PobierzAkcjeNaLiscie($Dane = array(), $TH = false){
		$Akcje = array();
                if( !empty($Dane['amount_id']) && $Dane['amount_id'] !== '0' ){
                    $Akcje[] = array('img' => "koszyk.png", 'img_grey' => "koszyk_grey.png", 'title' => "Do koszyka", "akcja_link" => "javascript:DoKoszyka({$Dane['id']}, ".(isset($_SESSION['basket-picked-order']) ? intval($_SESSION['basket-picked-order']) : '0').")");
                }else{
                    $Akcje[] = array('img' => "koszyk_grey.png", 'img_grey' => "koszyk_grey.png", 'title' => "Do koszyka", "akcja" => "szczegoly");
                }
                $Akcje[] = array('img' => "information.gif", 'img_grey' => "information_grey.gif", 'title' => "Pokaż", "akcja" => "szczegoly");
                if(!in_array($this->Parametr,$this->ModulyBezEdycji)){
                    $Akcje[] = array('img' => "pencil.gif", 'img_grey' => "pencil_grey.gif", 'title' => "Edycja", "akcja" => "edycja");
                }
		if(!in_array($this->Parametr,$this->ModulyBezKasowanie)){
			$Akcje[] = array('img' => "bin_empty.gif", 'img_grey' => "bin_empty_grey.gif", 'title' => "Kasowanie", "akcja" => "kasowanie");
		}
		return $Akcje;
	}
        
        

        
        /**
 * funkcja przeładowywująca 
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @param $Wartosci array
 * @param $Formularz object Formularz
 * @param $ID integer 
 */   
        
        function AkcjaPrzeladowanie($Wartosci, $Formularz, $ID = NULL){
            if( $_POST['OpcjaFormularza'] == 'dodaj_obrazek' ){
                $this->ZapiszZdjecie($this->Prefix,$Formularz->ZwrocNazweMapyPola($this->PoleZdjecia) , $ID, $Formularz->ZwrocOpcjePola($this->PoleZdjecia, 'rozmiar_x', false), $Formularz->ZwrocOpcjePola($this->PoleZdjecia, 'rozmiar_y', false), true);
                $Wartosci = $this->PobierzDaneElementu($ID);
            }
            return $Wartosci;
        }
        
        /**
 * funkcja usuwająca plik z bazy danych i dysku
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @param $Prefix string prefix_do_pliku
 * @param $NazwaPola string nazwa pola w bazie w którym ma zostać zapisana nazwa pliku znajdującego się na dysku
 * @param $ID integer 
 */   
	function UsunPlik($ID, $Plik){
            if(isset($_GET['usun_plik']) && $_GET['usun_plik'] == 1){
                $Plik = $this->Baza->GetValue("SELECT $this->PoleZdjecia FROM $this->TabelaZdjecia WHERE {$this->TabelaZdjeciaPrefix}{$this->PoleID} = '$ID' AND {$this->TabelaZdjeciaPrefix}name = '$Plik'");
                if($Plik == false){
                    Usefull::ShowKomunikatError("Załącznik nie istnieje!", true);
                    return false;
                }else{
                    if(unlink($this->KatalogDanych.$Plik)){
                        if($this->Baza->Query("DELETE FROM $this->TabelaZdjecia WHERE {$this->TabelaZdjeciaPrefix}{$this->PoleID} = '$ID' AND {$this->TabelaZdjeciaPrefix}name = '$Plik'")){
                            $this->LinkPowrotu = "?modul=$this->Parametr&akcja=edycja&id=$ID";
                            Usefull::ShowKomunikatOk("Załącznik <i>$Plik</i> został usunięty", true);
                            $this->ShowOK();
                            return true;
                        }
                        return false;
                    }else{
                        Usefull::ShowKomunikatError("Wystąpił błąd! Załącznik nie został usunięty!", true);
                        return false;
                    }
                }
            }
	}
        
}
?>
