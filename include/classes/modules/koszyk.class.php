<?php
/**
 * Moduł koszyk
 * 
 * @author		Michal Bugański <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2012
 * @package		Panelplus 
 * @version		1.0
 */

class ModulKoszyk extends ModulBazowy {
    
    private $ChoseOrder = true;

        function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->ModulyBezMenuBocznego[] = $this->Parametr;
            $this->ModulyBezDodawania[] = $this->Parametr;
            $this->ModulyBezEdycji[] = $this->Parametr;
            $this->ModulyBezKasowanie[] = $this->Parametr;
            $this->FiltersAndActionsWidth = true;
            $this->Tabela = 'line_items';
            $this->PoleID = 'id';
            $this->PoleNazwy = 'title';
            $this->Nazwa = 'Koszyk';
            $this->clickable_rows = false;
            if(!$this->Uzytkownik->CzyUprawniony() && !isset($_SESSION['basket-picked-order'])){
                $PurchaseID = $this->Baza->GetValue("SELECT id FROM purchases WHERE user_id = '$this->UserID' AND status = '0'");
                if($PurchaseID != false){
                    $_SESSION['basket-picked-order'] = $PurchaseID;
                }
            }
            if(isset($_GET['mode']) && $_GET['mode'] == 'zmien_zamowienie') $this->ChoseOrder = false;
	}

        function  Wyswietl($Akcja) {
            $this->OperacjeNaKoszyku();
            $Akcja = 'lista';
            parent::Wyswietl($Akcja);
        }
        
        function PobierzAkcjeNaLiscie($Dane = array(), $TH = false){
            $Akcje = array();
            return $Akcje;
	}

        function PobierzListeElementow($Filtry = array()) {
            if($this->Uzytkownik->IsAdmin()){
                if(isset($_SESSION['basket-picked-order'])){
                   $Wyniki = $this->GetBasketContent($_SESSION['basket-picked-order']);
                }else{
                    ?><script type="text/javascript" src="js/basket-zamowienia.js"></script><?php
                    $Wyniki = $this->GetAllOrders(false, 3);
                    $this->ChoseOrder = false;
                }
            }else if($this->Uzytkownik->CzyUprawniony()){
                if(isset($_SESSION['basket-picked-order'])){
                    $Wyniki = $this->GetBasketContent($_SESSION['basket-picked-order']);
                }else{
                ?><script type="text/javascript" src="js/basket-zamowienia.js"></script><?php
                    $Wyniki = $this->GetAllOrders($this->Uzytkownik->ZwrocInfo("company_id"), 1);
                     $this->ChoseOrder = false;
                }
            }else{
                if(isset($_SESSION['basket-picked-order'])){
                    $Wyniki = $this->GetBasketContent($_SESSION['basket-picked-order']);
                }
            }
            return $Wyniki;
	}

        function PobierzNazweDoSciezki(){
             if(isset($_SESSION['basket-picked-order'])){
                $Dane = $this->Baza->GetData("SELECT p.title, CONCAT(u.firstname,' ',u.surname) as client FROM purchases p LEFT JOIN users u ON(u.id = p.user_id) WHERE p.id = '{$_SESSION['basket-picked-order']}'");
                return "&nbsp;&gt;&nbsp;<span class='nazwa_elementu'>Koszyk dla zamówienia {$Dane['title']} dla użytkownika {$Dane['client']}</span>";
             }else{
                return "";
             }
        }

        /**
         * Pobiera wszystkie zamówienia dostępne dla admina
         * @param integer or false $CompanyID
         */
        function GetAllOrders($CompanyID = false, $status = 0){
            $Wyniki = array(
                    'id' => array('naglowek' => 'ID',  'td_styl' => 'width: 50px;', 'class' => 'idk'),
                    'title' => array('naglowek' => 'Nazwa'),
                    'status' => array('naglowek' => 'Status', 'td_styl' => 'width: 200px;', "elementy" => Usefull::GetPackageStatuses($this->Uzytkownik->IsAdmin()), "class" => Usefull::GetPackageStatusesColor()),
                    'client' => array('naglowek' => 'Użytkownik', 'td_styl' => 'width: 200px;')
                );
            $this->Baza->Query("SELECT p.id, p.title, p.status, CONCAT(u.firstname,' ',u.surname) as client FROM purchases p
                                    LEFT JOIN users u ON(u.id = p.user_id) WHERE p.status <= $status
                                    ".($CompanyID != false ? " AND ( (p.company_id = '$CompanyID' AND p.status IN(1)) OR p.user_id = '{$this->UserID}')" : "")."
                                    ORDER BY id DESC");
            return $Wyniki;
        }

        /**
         * Pobiera produkty w koszyku danego zamówienia
         * @param integer $OrderID
         */
        function GetBasketContent($OrderID){
            $Wyniki = array(
//                    'id' => array('naglowek' => 'ID', 'td_styl' => 'width: 50px;text-align:right;'),
                    'kzc' => array('naglowek' => 'Numer katalogowy', 'td_styl' => 'text-align:center;'),
                    'title' => array('naglowek' => 'Nazwa', 'td_styl' => 'text-align:center;'),
                    'quantity_input' => array('naglowek' => 'Ilość', 'type' => 'text-input', 'td_styl' => 'text-align: center; width: 10px;'),
                    'amount' => array('naglowek' => 'Cena EUR', 'td_styl' => 'text-align: right; width: 100px;'),                
                    'usun' => array('naglowek' => 'Usuń', 'type' => 'text-input-delete', 'td_styl' => 'text-align: center; width: 60px;'),
                );
                $this->Baza->Query("SELECT il.id, il.amount, il.quantity, p.kzc, p.title FROM $this->Tabela il
                                    LEFT JOIN parts p ON(p.id = il.part_id)
                                    WHERE il.purchase_id = '$OrderID'
                                    ORDER BY p.id ASC");
            return $Wyniki;
        }

        function AkcjaLista($Filtry = array()){
            if(isset($_SESSION['basket-picked-order'])){
                FormularzSimple::FormStart("koszyczek", "?modul=koszyk", "post", false, "koszyczek");
                    parent::AkcjaLista();
                    if(count($this->ListIDs) > 0){
                        ?><div style='text-align: right;'><?php
                        FormularzSimple::PoleSubmit("save-basket", "Zapisz zmiany", "class='search-button' style='margin: 10px auto;'");
                        $this->Status = $this->Baza->GetValue("SELECT status FROM purchases WHERE id = '{$_SESSION['basket-picked-order']}'");
                        /*
                        if($this->Status  == 0){
                            echo '<a class="like-button" href="?modul=zamowieniauzytkownicy&akcja=gotowe&id='.$_SESSION['basket-picked-order'].'" style="padding: 5px 15px; margin-top:10px; margin-left:4px; float:right;">Gotowe</a>';
                            $Akcje['szczegoly'][] = array('link' => "?modul=$this->Parametr&akcja=gotowe&id='{$_SESSION['basket-picked-order']}", 'class' => 'gotowe', 'img' => 'tick_blue.gif', 'desc' => 'Gotowe');
                        }elseif($this->Status  == 1){
                            echo '<a class="like-button" href="?modul=zamowieniauzytkownicy&akcja=zamow&id='.$_SESSION['basket-picked-order'].'" style="padding: 5px 15px; margin-top:10px; margin-left:4px; float:right;">Zamów</a>';
                            $Akcje['szczegoly'][] = array('link' => "?modul=$this->Parametr&akcja=zamow&id='{$_SESSION['basket-picked-order']}", 'class' => 'gotowe', 'img' => 'tick_blue.gif', 'desc' => 'Gotowe');
                        }
                        */
                        if($this->Status  < 1){
                            FormularzSimple::PoleSubmit("send-basket", "Zamów", "class='search-button' style='margin: 10px auto;'");
                        }
                        
                        ?></div><?php
                    }
                FormularzSimple::FormEnd();
            }else{
                parent::AkcjaLista();
            }
        }

        function ShowPaginationTable(){
            if(isset($_SESSION['basket-picked-order']) && count($this->ListIDs) == 0){
                ?><div style='font-style: italic; margin: 15px auto; text-align: center; font-size: 12px;'>Brak produktów w koszyku</div><?php
            }else if(!isset($_SESSION['basket-picked-order']) && count($this->ListIDs) == 0){
                ?><div style='font-style: italic; margin: 15px auto; text-align: center; font-size: 12px;'>Brak zamówień</div><?php
            }

            if(($this->Uzytkownik->IsAdmin() || $this->Uzytkownik->CzyUprawniony())&& !isset($_SESSION['basket-picked-order'])){
                FormularzSimple::FormStart("wybor-zamowienia", "?modul=koszyk");
                    FormularzSimple::PoleHidden("basket-picked-order", isset($_SESSION['basket-picked-order']) ? $_SESSION['basket-picked-order'] : '', "id='basket-picked-order'");
                    ?><div style="text-align: right;"><?php
                    if($this->Uzytkownik->IsAdmin() || $this->Baza->GetValue("SELECT count(*) FROM purchases WHERE user_id = '{$this->UserID}' AND status = 0") == 0){
                        FormularzSimple::PoleSubmit("add-order", "Dodaj nowe zamówienie", "class='search-button' style='color: #4b8c23; margin: 10px;'");
                    }
                    FormularzSimple::PoleSubmit("set-order", "Ustaw zaznaczone zamówienie", "class='search-button' style='margin: 10px;'");
                    ?></div><?php
                FormularzSimple::FormEnd();
            }
        }

        function GetActions($ID){
            $Akcje = array();
            if(isset($_SESSION['basket-picked-order'])){
                $Akcje['lista'][] = array('link' => "?modul=$this->Parametr&mode=wyczysc_koszyk", 'class' => 'usun', 'img' => 'cancel.gif', 'desc' => 'Opróżnij koszyk');
                if($this->Uzytkownik->IsAdmin() || $this->Uzytkownik->CzyUprawniony()){
                    $Akcje['lista'][] = array('link' => "?modul=$this->Parametr&mode=zmien_zamowienie", 'class' => 'edycja', 'img' => 'pencil.gif', 'desc' => 'Zmień zamówienie');
                }
            }
            return $Akcje;
        }

        function OperacjeNaKoszyku(){
            if(isset($_GET['mode']) && $_SERVER['REQUEST_METHOD'] != "POST"){
                if($_GET['mode'] == "zmien_zamowienie"){
                    unset($_SESSION['basket-picked-order']);
                }else if($_GET['mode'] == "wyczysc_koszyk" && isset($_SESSION['basket-picked-order'])){
                   $this->Baza->Query("DELETE FROM $this->Tabela WHERE purchase_id = '{$_SESSION['basket-picked-order']}'");
                }
            }
            if(isset($_POST['basket-picked-order']) && isset($_POST['set-order'])){
                if($_POST['basket-picked-order'] == ""){
                    unset($_SESSION['basket-picked-order']);
                }else{
                    $_SESSION['basket-picked-order'] = $_POST['basket-picked-order'];
                }
            }else if(isset($_POST['add-order'])){
                $this->ZrobNoweZamowienie();
            }
            if(isset($_SESSION['basket-picked-order'])){
                $_GET['id'] = $_SESSION['basket-picked-order'];
                if(isset($_POST['save-basket']) == "Zapisz zmiany"){
                    foreach($_POST['quantity'] as $ID => $Quantity){
                        $Zap = $this->Baza->PrepareUpdate($this->Tabela, array('quantity' => $Quantity, 'quantity_approved' => $Quantity), array('id' => $ID));
                        $this->Baza->Query($Zap);
                    }
                }
                if(isset($_POST['send-basket']) && $_POST['send-basket'] == "Zamów"){
                    $ObecnyStatus = $this->Baza->GetValue("SELECT status FROM purchases WHERE id = '{$_SESSION['basket-picked-order']}'");
                    if($this->Uzytkownik->CzyUprawniony() && $ObecnyStatus < 3){
                        $this->Baza->Query("UPDATE purchases SET status = '3', sent_at = now() WHERE id = '{$_SESSION['basket-picked-order']}'"); 
                    }elseif($ObecnyStatus < 2){
                        $this->Baza->Query("UPDATE purchases SET status = '1' WHERE id = '{$_SESSION['basket-picked-order']}' AND user_id = '$this->UserID'");
                    }
                    unset($_SESSION['basket-picked-order']);
                }
                if(isset($_GET['del_item']) && is_numeric($_GET['del_item'])){
                    $this->Baza->Query("DELETE FROM $this->Tabela WHERE purchase_id = '{$_SESSION['basket-picked-order']}' AND id='{$_GET['del_item']}'");
                }
            }
        }

        function ObrobkaDanychLista($Elementy){
            if( $this->ChoseOrder ){
                foreach($Elementy as $ElementyKey => $Element){
                    $Elementy[$ElementyKey]['quantity_input'] = "<input type='text' name='quantity[{$Element['id']}]' value='{$Element['quantity']}' style='width: 50px;' class='tabelka' />";
                    $Elementy[$ElementyKey]['usun'] = "<a href='?modul=koszyk&del_item={$Element[$this->PoleID]}'><img src='images/cancel.gif' alt='Opróżnij koszyk' class='inlineimg'></a>";
                }
            }
            return $Elementy;
        }

        function ZrobNoweZamowienie(){
            $Save['user_id'] = $this->UserID;
            $Save['company_id'] = $this->Uzytkownik->ZwrocInfo("company_id");
            $Save['title'] = "";
            $Save['created_at'] = date("Y-m-d H:i:s");
            $rmc = $this->Baza->GetValue("SELECT IF(role LIKE 'rmc', 1, 0) as rmc FROM users u WHERE u.id = '$this->UserID'");
            if( $rmc === '1'){
                $rmc = 3;
            }else{
                $Query = "SELECT tp.company_id FROM rmc_provinces tp 
                                LEFT JOIN companies c ON (tp.province_id = c.province_id)
                                LEFT JOIN users u ON (u.company_id = c.id)
                                WHERE u.id = '{$this->UserID}'";
                $rmc = $this->Baza->GetValue($Query);
            }
            $Save['idrmc'] = is_numeric($rmc) ? $rmc : 3;
            $Query = $this->Baza->PrepareInsert("purchases", $Save);
            $this->Baza->Query($Query);
        }

        function WyswietlAJAX($Akcja){
            if($Akcja == "check"){
                if($this->Uzytkownik->UstawDomyslnyKoszyk($this->UserID)){
                    echo $_SESSION['basket-picked-order'];
                }else{
                    echo 0;
                }
            }else{
                if($_POST['basket_id'] == 0){
                    if($this->Uzytkownik->UstawDomyslnyKoszyk($this->UserID)){
                        $BasketID = $_SESSION['basket-picked-order'];
                    }else{
                        $this->ZrobNoweZamowienie();
                        $BasketID = $this->Baza->GetLastInsertID();
                        $_SESSION['basket-picked-order'] = $BasketID;
                    }
                }else{
                    $BasketID = $_POST['basket_id'];
                }
                $Czesci = new ModulCzesci($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
                $DaneCzesci = $Czesci->PobierzDaneElementu($_POST['part_id']);
                $CenaCzesci = $this->Baza->GetValue( "SELECT value FROM group_pricing WHERE id = '{$DaneCzesci['amount_id']}'");
                $Rabat = intval($this->Baza->GetValue("SELECT discount FROM companies WHERE id = '{$this->Uzytkownik->ZwrocInfo("company_id")}'"));
                if($this->Baza->GetValue("SELECT count(*) FROM line_items WHERE purchase_id = '$BasketID' AND part_id = '{$_POST['part_id']}' AND amount = '$CenaCzesci' AND discount = '$Rabat'") == 0){
                    $Insert['amount'] = $CenaCzesci;
                    $Insert['purchase_id'] = $BasketID;
                    $Insert['part_id'] = $_POST['part_id'];
                    $Insert['created_at'] = date("Y-m-d H:i:s");
                    $Insert['updated_at'] = date("Y-m-d H:i:s");
                    $Insert['quantity'] = 1;
                    $Insert['quantity_approved'] = 1;
                    $CenaCzesciPoRabacie = number_format($CenaCzesci - ($CenaCzesci*($Rabat/100)),2,".","");
                    $Insert['amount_approved'] = $CenaCzesciPoRabacie;
                    $Insert['discount'] = $Rabat;
                    $Insert['status'] = 0;
                    $Zapytanie = $this->Baza->PrepareInsert("line_items", $Insert);
                }else{
                    $Zapytanie = "UPDATE line_items SET updated_at = now(), quantity = quantity+1, quantity_approved=quantity_approved+1 WHERE purchase_id = '$BasketID' AND part_id = '{$_POST['part_id']}' AND amount = '$CenaCzesci' AND discount = '$Rabat'";
                }
                if($this->Baza->Query($Zapytanie)){
                    $Ile = $this->Baza->GetValue("SELECT quantity FROM line_items WHERE purchase_id = '$BasketID' AND part_id = '{$_POST['part_id']}' AND amount = '$CenaCzesci' AND discount = '$Rabat'");
                    echo "Dodano część <b>{$DaneCzesci['kzc']}</b> do koszyka.<br />W koszyku znajduje się <b>$Ile</b> ".($Ile == 1 ? "sztuka" : ($Ile < 5 ? "sztuki" : "sztuk"))." tej części";
                }else{
                    echo "Wystąpił błąd! Część nie została dodana!";
                }
            }
        }
        
        function AkcjeNiestandardowe($ID){
            $this->AkcjaLista();
	}
        
        
}
?>
