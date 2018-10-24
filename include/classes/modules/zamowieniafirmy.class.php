<?php
/**
 * Moduł Zamówienia Firmy - moduł do zarządzanie zamówieniami
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 */

/*
 *  begin class ModulZamowieniaFirmy
 */

class ModulZamowieniaFirmy extends ModulZamowienia {

    
/**
 * zmienne klasy
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright       Copyright (c) Maciej Kura
 * @package		PanelPlus 
 */    
    public $ErrorFV = '';
    
/**
 * konstruktor klasy Zamówienia Uzytkownicy
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @param resource &$Baza - zasób bazy danych
 * @param object $Uzytkownik - obiekt class Uzytkownik
 * @param string $Parametr - nazwa modułu
 * @param string $sciezka - tutaj czekam na opis :)
 * 
 */

	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->PoleID = 'company_id';
            $this->ParametrFiltry = 'zamowieniafirmy';
            $this->PoleSort = 'name';
            $this->SortHow = 'ASC';
            $this->Filtry[] = array('nazwa' => 'title', 'opis' => false, "typ" => "tekst", "placeholder" => "Szukaj zamówień", 'search' => true);       
/*            if($this->Uzytkownik->IsAdmin() || $this->Uzytkownik->FirmaRMC($this->Uzytkownik->ZwrocIdUzytkownika())){
                $this->LinkMenu = array( 'Lista zamówien'=>'?modul=zamowieniauzytkownicy&akcja=lista',  'Lista firm'=>'?modul=zamowieniafirmy&akcja=lista',);
            }
 */
            $this->ModulyBezEdycji[] = $this->Parametr;
            $this->ModulyBezKasowanie[] = $this->Parametr;
            $this->ShortNameList = array(
                'name' => array( 'IsTitle'=>true, 'lenght'=>'50'),
//                'simplename' => array( 'IsTitle'=>true, 'lenght'=>'50'),
            );
            
	}


/**
 * funkcja pobierająca oraz ustawiające listę wyświetlanych elementów
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 * @return array $Wynik - nagłówki tabeli
 */          
        function PobierzListeElementow($Filtry = array()) {
            $Wyniki = array(
                    'name' => array('naglowek' => 'Firma', ),
		    'count' => array('naglowek'=>'Ilość zamówień',  'td_styl' => 'width: 70px;text-align:center;' ),
                );
            $Where = $this->GenerujWarunki();
            $Sort = $this->GenerujSortowanie('c');
            $this->Baza->Query($this->QueryPagination("SELECT company_id, c.name, count(company_id) as count FROM $this->Tabela p 
                LEFT JOIN companies c ON (c.id = p.company_id)
                $Where
                GROUP BY company_id 
                $Sort
                    ", $this->ParametrPaginacji, 30));
            return $Wyniki;
	}

        function AkcjaSzczegoly($ID){
            $Dane = $this->PobierzDaneElementu($ID);
            
            if($this->Uzytkownik->FirmaRMC($this->UserID) || $this->Uzytkownik->IsAdmin()){
                $Dane['name'] .= $this->GetFirmRole($ID);
            }

            $Dane['top_name'] = $this->PobierzDaneFirmy($Dane['idrmc']);
            if($Dane['street'] != ""){
                $Adres[] = $Dane['street'];
            }
            if($Dane['postcode'] != "" || $Dane['city'] != ''){
                $Adres[] = $Dane['postcode']." ".$Dane['city'];
            }
            if($Dane['country'] != ""){
                $Adres[] = $Dane['country'];
            }
            $Dane['adres'] = implode("<br />", $Adres);
            $Dane['user'] = $Dane['name'].'tel: '.$Dane['phone_number'].'<br />'.$Dane['nip'].'<br />'.$Dane['adres'];            
            $Dane['discount'] = floatval($Dane['discount'])." %";          
            
            $SendReload =  $this->OperacjeNaZamowieniu($Dane);
            if( $SendReload ){
                echo '<script type="text/javascript" src="js/firmy-zamowienia.js"></script>';
                FormularzSimple::FormStart("zamowienie", "?modul=zamowieniafirmy&akcja=szczegoly&id=$ID");
                $Form = $this->GenerujFormularz($Dane);
                $Form->WyswietlDane($Dane);
                echo "<div class='clear'></div>";
            ?>
                <div style="width: 95%;margin: 0 auto;">
                    <div style='text-align: right;margin-bottom: 8px;'>
                        Razem: <?php echo $Dane['suma']; ?>
                    </div>
                    <div><?php !empty($this->ErrorFV) ? Usefull::ShowKomunikatError($this->ErrorFV) : ''; ?></div>
                <div style='text-align: right;'>Faktura: <?php FormularzSimple::PoleInputText('faktura', $Dane['faktura'], 'class="tabelka"'); ?></div>
                <div style='text-align: right;'><?php
                FormularzSimple::PoleSubmit("reload-purchases", "Przelicz", "class='search-button' style='margin: 10px auto;'");
                FormularzSimple::PoleSubmit("send-purchases", "Realizuj", "class='search-button' style='margin: 10px auto;'");
                ?></div></div><?php
                FormularzSimple::FormEnd();
                
            }
        }

        
        function PobierzDaneElementu($ID, $Typ = NULL){
            $firma = new ModulFirmy($this->Baza, $this->Uzytkownik, "test123", $this->Sciezka);
            $Dane = $firma->PobierzDaneElementu($ID);
            if( $this->Uzytkownik->ZwrocInfo('company_id') == '2' )
                $discount = 'il.discount';
            else
                $discount = '0 as discount';
            
            $values = $this->Baza->GetValues("SELECT id FROM $this->Tabela p 
                WHERE company_id = '$ID' AND ".$this->DomyslnyWarunek()."
                ");
             $Dane['parts'] = $this->Baza->GetRows( "SELECT part_id,
                        GROUP_CONCAT(il.id SEPARATOR '|') as ids, GROUP_CONCAT(il.purchase_id SEPARATOR '|') as ids_purchase_id,
                        sum(il.quantity_approved) as sum_quantity_approved,
                        sum(il.quantity) AS sum_quantity, il.amount_approved, il.amount,
                        sum(il.quantity_approved)*il.amount_approved AS pay_quantity,
                        $discount, p.kzc, p.title, GROUP_CONCAT(lic.comments SEPARATOR '|') as comments
                        FROM line_items il
                        LEFT JOIN parts p ON(p.id = il.part_id)
                        LEFT JOIN line_items_comments lic ON( il.id = lic.line_items_id )
                        WHERE il.purchase_id IN (".implode(',', $values).")
                        GROUP BY p.kzc, discount, amount
                        ORDER BY p.id ASC
                        " );
            return $Dane;
        }
        
        function PobierzNazweElementu($ID){
            if (isset($ID) && intval($ID) != 0) {
                $return = $this->Baza->GetValue("SELECT name FROM companies WHERE id = '$ID'");
                return "&nbsp;<span class='nazwa_elementu'>{$return}</span>";
            }else{
                return "";
            }
        }
        
        function DomyslnyWarunek($ISRMC = false){
            return "status = '3' AND p.idrmc = '".$this->Uzytkownik->ZwrocInfo('company_id')."'";
        }
        
        function OperacjeNaZamowieniu( &$Dane ){
            if($_SERVER['REQUEST_METHOD'] == "POST"){
                    $Dane['faktura'] = $_POST['faktura'];
                    if(isset($_POST['reload-purchases']) == "Przelicz"){
                        $this->Przelicz($Dane['parts']);
                    }elseif( empty($_POST['faktura']) ){
                        $this->Przelicz($Dane['parts']);
                        $this->ErrorFV = 'Proszę podać nr faktury';
                    }elseif( $_POST['send-purchases'] == "Realizuj"){
                        $this->Przelicz($Dane['parts']);
                        $this->Zapisz($Dane);
                        $this->include_tpls = 'message-add.tpl.php';
                        
                        $this->ShowOK();
                        return false;
                    }
            }else{
                $Dane['faktura'] = '';
            }
            $AmountApprovedSum = 0.00;
            $Dane['ids_purchase_id'] = array();
            foreach($Dane['parts'] as $Key=>&$Value){
                //$Value['title'] .= '<br />Zamówienie nr: '.preg_replace('/\|/', ',', $Value['ids_purchase_id']);
                foreach(explode('|', $Value['ids_purchase_id']) as $IDS){
                    if( !in_array($IDS, $Dane['ids_purchase_id'])){
                        $Dane['ids_purchase_id'][] = $IDS;
                    }
                }
                $Value['amount_approved'] = number_format($Value['amount_approved'], 2, '.', '');
                $AmountApprovedSum += $Value['pay_quantity'];
                $Value['pay_quantity'] = number_format($Value['pay_quantity'], 2, '.', '');
                $Value['sum_quantity_approved'] = '<input name="ids" type="hidden" value="'.$Value['ids'].'" /><div style="padding:2px;border: 1px solid silver;"><input name="sum_quantity_approved['.$Value['part_id'].']['.$Key.']" type="text" style="text-align:right;" class="tabelka sum_quantity_approved" max_val="'.$Value['sum_quantity'].'" value="'.$Value['sum_quantity_approved'].'"></div>';
                $Value['discount'] = '<div style="padding:2px;border: 1px solid silver;"><input name="discount['.$Value['part_id'].']['.$Key.']"  type="text" style="text-align:right;" class="tabelka discount" value="'.$Value['discount'].'"></div>';
                $Value['comments'] = '<div style="padding:2px;border: 1px solid silver;"><textarea name="comments['.$Value['part_id'].']['.$Key.']"  type="text" class="tabelka discount" style="height: 50px;">'.$Value['comments'].'</textarea></div>';
            }
            $Dane['suma'] = number_format($AmountApprovedSum, 2, '.', '');
            return true;

        }
        
        function przelicz(&$Dane){
            foreach($Dane as $Key=>&$Value){
                if( $_POST['sum_quantity_approved'][$Value['part_id']][$Key] > $Value['sum_quantity']){
                    $_POST['sum_quantity_approved'][$Value['part_id']][$Key] = $Value['sum_quantity'];
                    $this->Error = "";
                }
                $Value['amount_approved'] = number_format($Value['amount'] * (100 - $_POST['discount'][$Value['part_id']][$Key])/100,2, '.', '');
                $Value['pay_quantity'] = $Value['amount_approved'] * $_POST['sum_quantity_approved'][$Value['part_id']][$Key];
                $Value['sum_quantity_approved'] = $_POST['sum_quantity_approved'][$Value['part_id']][$Key];
                $Value['discount'] = $_POST['discount'][$Value['part_id']][$Key];
                $Value['comments'] = $_POST['comments'][$Value['part_id']][$Key];
           }
        }
        
       function Zapisz(&$Dane){
           $purchaseIDs = $PurchaseInfo = array();
           $Check = $Dane['parts'];
           foreach($Check as $Key=>$Value){
                $Value['comments'] = trim($Value['comments']);
                $ids = explode('|', $Value['ids']);
                foreach($ids as $id){
                    if( $this->Baza->GetValue("SELECT comments FROM line_items_comments WHERE line_items_id ='$id'") != false ){
                         $this->Baza->Query($this->Baza->PrepareUpdate('line_items_comments', array('comments'=>$Value['comments']), array( 'line_items_id'=> $id)));
                    }else{
                        if( !empty( $Value['comments'] ) ){
                            $this->Baza->Query($this->Baza->PrepareInsert('line_items_comments', array('comments'=>$Value['comments'], 'line_items_id'=>$id)));
                        }
                    }
                }
               if($Value['sum_quantity_approved'] > 0){
                   $ids_purchase_id = explode('|', $Value['ids_purchase_id']);
                   foreach($ids_purchase_id as $purchase_id){
                       if(!in_array($purchase_id, $purchaseIDs)){
                           $date = strtotime($this->Baza->GetValue("SELECT sent_at FROM purchases WHERE id = '$purchase_id'"));
                           while(array_key_exists($date, $PurchaseInfo)){
                               $date++;
                           }
                           $purchaseIDs[] = $purchase_id;
                           $PurchaseInfo[$date]['purchase_ids'] = $purchase_id;
                           $PurchaseInfo[$date]['line_items'] = $this->Baza->GetRows("SELECT * FROM line_items WHERE purchase_id = '$purchase_id'");
                       }
                   }
               }else{
                   unset($Check[$Key]);
               }
           }
            asort($PurchaseInfo);
            $backupCheck = $backuppurchase = array();
            $backupCheck = $Check;
            $backuppurchase = $PurchaseInfo;
            $LineItemsComments = array();
            foreach( $PurchaseInfo as $PurchasesDate => &$Purchases){
                if(!empty($Check)){
                    foreach($Purchases['line_items'] as $LineItemKey=>&$LineItemValue){
                        $LineItemValue['quantity_approved'] = 0;
                        $LineItemValue['status'] = 3;
                        $LineItemValue['invoice'] = $Dane['faktura'];                         
                        if(!empty($Check)){
                            foreach($Check as $CheckKey=>&$CheckValue){
                                if(array_key_exists('part_id', $CheckValue)
                                        && $LineItemValue['part_id'] == $CheckValue['part_id']
                                        && in_array($LineItemValue['purchase_id'], explode('|',$CheckValue['ids_purchase_id']))  ){
                                    if( (($LineItemValue['quantity']) > $CheckValue['sum_quantity_approved']) && $CheckValue['sum_quantity_approved'] > 0){
                                        $LineItemValue['quantity_approved'] = $CheckValue['sum_quantity_approved'];
                                        $LineItemValue['discount'] = $CheckValue['discount'];
                                        $LineItemValue['status'] = 5;
                                        $LineItemValue['amount_approved'] = $CheckValue['amount_approved'];
                                        $LineItemValue['invoice'] = $Dane['faktura'];
                                        if(!empty( $CheckValue['comments'] )){
                                            $LineItemValue['comments'] = $CheckValue['comments'];
                                        }                                        
                                        unset($Check[$CheckKey]);
                                    }elseif( (($LineItemValue['quantity']) == $CheckValue['sum_quantity_approved']) && $CheckValue['sum_quantity_approved'] > 0){
                                        $LineItemValue['quantity_approved'] = $CheckValue['sum_quantity_approved'];
                                        $LineItemValue['discount'] = $CheckValue['discount'];
                                        $LineItemValue['status'] = 5;
                                        $LineItemValue['amount_approved'] = $CheckValue['amount_approved'];
                                        $LineItemValue['invoice'] = $Dane['faktura'];
                                        if(!empty( $CheckValue['comments'] )){
                                            $LineItemValue['comments'] = $CheckValue['comments'];
                                        }
                                        unset($Check[$CheckKey]);
                                    }else{
                                        $CheckValue['sum_quantity_approved'] = $CheckValue['sum_quantity_approved'] - $LineItemValue['quantity'];
                                        $LineItemValue['quantity_approved'] = $LineItemValue['quantity'];
                                        $LineItemValue['discount'] = $CheckValue['discount'];
                                        $LineItemValue['status'] = 3;
                                        $LineItemValue['amount_approved'] = $CheckValue['amount_approved'];
                                        $LineItemValue['invoice'] = $Dane['faktura'];
                                        if(!empty( $CheckValue['comments'] )){
                                            $LineItemValue['comments'] = $CheckValue['comments'];
                                        }
                                    }
                                    break;
                                }
                            }
                        }
                    }
                }else{
                    unset($PurchaseInfo[$PurchasesDate]);
                }

            }            
            $InsertPurchases = $UpdatePurchases = $InsertLineItems = $UpdateLineItems = $InsertLineItemsComments = array();
            foreach( $PurchaseInfo as $PurchasesKey => &$PurchasesValue ){
                foreach( $PurchasesValue['line_items'] as &$UpdatePurchasesLineItems ){
                    $WhereID['id'] = $UpdatePurchasesLineItems['id'];
                    $Comments = isset($UpdatePurchasesLineItems['comments']) ? $UpdatePurchasesLineItems['comments'] : NULL;
                    unset($UpdatePurchasesLineItems['id']);
                    unset($UpdatePurchasesLineItems['comments']);
                    $UpdateLineItems[$PurchasesKey][] = $this->Baza->PrepareUpdate('line_items', $UpdatePurchasesLineItems, $WhereID);
                    if($UpdatePurchasesLineItems['quantity_approved'] == $UpdatePurchasesLineItems['quantity'] ){
                        unset($UpdatePurchasesLineItems);
                    }elseif($UpdatePurchasesLineItems['quantity_approved'] < $UpdatePurchasesLineItems['quantity']){
                        $UpdatePurchasesLineItems['quantity'] = $UpdatePurchasesLineItems['quantity'] - $UpdatePurchasesLineItems['quantity_approved'];
                        $UpdatePurchasesLineItems['status'] = 0;
                        $UpdatePurchasesLineItems['invoice'] = '';
                        $UpdatePurchasesLineItems['quantity_approved'] = $UpdatePurchasesLineItems['quantity'];
                        $InsertLineItems[$PurchasesKey][$WhereID['id']] = $this->Baza->PrepareInsert('line_items', $UpdatePurchasesLineItems);
                        if($Comments !== NULL){
                            $InsertLineItemsComments[$PurchasesKey][$WhereID['id']] = array( 'comments'=>$Comments, 'line_items_id' => $WhereID['id'] );
                        }else{
                            $Comments = $this->Baza->GetValue("SELECT comments FROM line_items_comments WHERE line_items_id='{$WhereID['id']}'");
                            if(!empty($Comments))
                                $InsertLineItemsComments[$PurchasesKey][$WhereID['id']] = array( 'comments'=>$Comments, 'line_items_id' => $WhereID['id'] );
                        }
                    }
                }
                    $this->Baza->Query("SELECT * FROM {$this->Tabela} WHERE id = '{$PurchasesValue['purchase_ids']}'");
                    $TempInsert = $this->Baza->GetRow();
                    unset($TempInsert['id']);
                    if( !empty($InsertLineItems[$PurchasesKey])){
                        $InsertPurchases[$PurchasesKey] = $this->Baza->PrepareInsert($this->Tabela, $TempInsert);
                    }
                    $TempInsert['status'] = '5';
                    $TempInsert['finalized_at'] = date("Y-m-d H:i:s");
                    $TempInsert['invoice'] = $Dane['faktura'];
                    $UpdatePurchases[$PurchasesKey] = $this->Baza->PrepareUpdate($this->Tabela, $TempInsert, array('id'=>$PurchasesValue['purchase_ids']));
            }

/*            $this->VAR_DUMP( $InsertPurchases);
            $this->VAR_DUMP( $UpdatePurchases);
            $this->VAR_DUMP( $InsertLineItems );
            $this->VAR_DUMP( $UpdateLineItems );
            $this->VAR_DUMP( $InsertLineItemsComments);
  */          
            foreach( $UpdatePurchases as $UpdatePurchasesKey=>$UpdatePurchasesValue){
                $this->Baza->Query($UpdatePurchasesValue);
                foreach($UpdateLineItems[$UpdatePurchasesKey] as $UpdateLineItem){
                    $this->Baza->Query($UpdateLineItem);
                }
                if( isset($InsertPurchases[$UpdatePurchasesKey] )){
                    $this->Baza->Query($InsertPurchases[$UpdatePurchasesKey]);
                    $NewID = $this->Baza->GetLastInsertID();
                    foreach($InsertLineItems[$UpdatePurchasesKey] as $InsertLineItemsKey => $InsertLineItem){
                        $InsertLineItem = preg_replace("/purchase_id = '[0-9]*',/", "purchase_id = '$NewID', ", $InsertLineItem);
                        $this->Baza->Query($InsertLineItem);
                        if(isset($InsertLineItemsComments[$UpdatePurchasesKey][$InsertLineItemsKey])){
                            $InsertLineItemsComments[$UpdatePurchasesKey][$InsertLineItemsKey]['line_items_id'] = $this->Baza->GetLastInsertID();
                            $InsertLineItemsCommentsQuery = $this->Baza->PrepareInsert('line_items_comments', $InsertLineItemsComments[$UpdatePurchasesKey][$InsertLineItemsKey] );
                            $this->Baza->Query($InsertLineItemsCommentsQuery);
                                    
                        }
                    }
                }
        }
    }
       
    
    function &GenerujFormularz(&$Wartosci = Array(), $Mapuj = false) {
        $Formularz = parent::GenerujFormularz($Wartosci, $Mapuj);
        $Formularz->DodajPole('warranty_mode', 'checkbox', 'Tryb gwarancyjny', array('atrybuty'=>array('class', 'warranty_mode'), 'show_label'=>true, 'div_class'=>'super-form-span4' ));
        $Formularz->DodajPole('title', 'tekst', 'Nazwa zamówienia', array( 'show_label'=>true, 'div_class'=>'super-form-span4'));            
        $Formularz->DodajPole('top_name', 'tekst', 'RMC*', array('show_label'=>true, 'div_class'=>'super-form-span4'));
        $Formularz->DodajPole('user', 'tekst', 'Firma*', array('show_label'=>true, 'div_class'=>'super-form-span4' ) );
        $Formularz->DodajPole('clear', 'clear', false);
        $Formularz->DodajPole('description', 'tekst_dlugi', 'Opis', array( 'show_label'=>true, 'div_class'=>'super-form-span1', 'arybuty' => array('class'=>'opis')));
        $Formularz->DodajPole('clear', 'clear', false);
        $Formularz->DodajPole('parts', 'zamowienia_czesci_realizacja', 'Zamówione części', array('show_label'=>true,'div_class'=>'super-form-span1',));
        $Formularz->DodajPole('clear', 'clear', '', array());
        return $Formularz;
    }
}