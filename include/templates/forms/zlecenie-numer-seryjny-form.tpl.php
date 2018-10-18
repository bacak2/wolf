<div id="div-super-form" style="width: 98%; margin: 0 auto;">
<?php
$DaneDomyslne = array( 'serial_numbers' => $Values['SerialNumberCheck'],
			'devices_name_id'=>$DaneUrzadzenia['devices_title_devices_title_id'],
			'devices_name' => $DaneUrzadzenia['title'],
			'devices_id' => $DaneUrzadzenia['id'],
			);

if(isset($Items) && $ActionSerialNumber > 3 ){
  $Pola = $this->GetPolaDanychKlienta();
  foreach($Pola as $Key => $Value){
    $Dane['client_dane'][$Key] = $Items[$Key];
  }
  $DaneDomyslne += $Dane;
}


    if($this->Error != false){
        Usefull::ShowKomunikatError($this->Error, true);
    }
    if( $ActionSerialNumber < 4){
      FormularzSimple::FormStart("check-serialnumber", "?modul={$this->Parametr}&akcja=dodawanie");
  ?>
      <div class="super-form-span4">&nbsp;</div>
      <div class="super-form-span2">
	<div class="super-form-label">
	    <label for="SerialNumberCheck">Numer Seryjny</label>
	</div><div class="clear"></div>
	<div class="super-form-div">
	    <div class="super-form-field">
		<?php FormularzSimple::PoleInputText("SerialNumberCheck", isset($Values['SerialNumberCheck']) ? $Values['SerialNumberCheck'] : '', ($BlockSerialNumber ? "readonly='readonly' style='background-color: #DFDFDF;'" : "")); ?>
	    </div>
	</div>
	<div class="super-form-div">
	    <div class="text_center">
	      <?php
		if($ActionSerialNumber < 1){
		    FormularzSimple::PoleSubmit("check-serial-number", "Sprawdź", "class='search-button' style='align: center;'");
		}else if($BlockSerialNumber && $ActionSerialNumber < 4){
		    FormularzSimple::PoleSubmit("change-serial-number", "Zmień", "class='search-button' style='align: center;'");
		}
	      ?>
	  </div>
	</div>
    </div>
    <div class="super-form-span4">&nbsp;</div>
    <div class="clear"></div>
  <?php   
      FormularzSimple::FormEnd();
  }
    if($ActionSerialNumber == -1 || $ActionSerialNumber == -2){
        if($ActionSerialNumber == -2){
            $Urzadzenia->AkcjaSzczegoly($DaneUrzadzenia['id']);
            ?><div class="clear"></div><?php
        }
        include(SCIEZKA_SZABLONOW."forms/zlecenie-numer-seryjny-zablokowany.tpl.php");
    }
    ////Dodawanie nowego numeru seryjnego którego nie ma w bazie.
    if($ActionSerialNumber == 1){
        include(SCIEZKA_SZABLONOW."forms/zlecenie-nowy-numer-seryjny.tpl.php");
    }
    if($ActionSerialNumber == 2){
       $Urzadzenia->AkcjaSzczegoly($DaneUrzadzenia['id']);
        include(SCIEZKA_SZABLONOW."forms/zlecenie-historia-numer-seryjny.tpl.php");
        ?><br /><br />
        <div style="text-align: center; <?php echo ($this->CzyMaBycJeszczePrzeglad == true ? "width: 50%; margin: 0 auto;" : "width: 180px; margin-left: 300px;"); ?> ">
            <?php
                if($this->CzyMozeWykonacNaprawe($this->UserID)){
            ?>
            <a href="?modul=<?php echo $this->Parametr; ?>&akcja=dodawanie&act=repair" class="like-button" style="float: left;"> Naprawa gwarancyjna</a>
            <?php
                }
                if($this->CzyMaBycJeszczePrzeglad == true){
            ?>
            <a href="?modul=<?php echo $this->Parametr; ?>&akcja=dodawanie&act=service" class="like-button" style="float: left; margin-left: <?php echo ($this->Uzytkownik->IsASPGWithService() == false ? "15px" : "90px") ?>;"> Przegląd gwarancyjny</a>
            <?php
                }
            ?>
        </div>
        <?php
    }
    if($ActionSerialNumber == 3 || $ActionSerialNumber == 8 || $ActionSerialNumber == 16 ){
        ?></div>
        <div style="width: 100%; margin: 0 auto;">
        <?php
	  $Urzadzenia->AkcjaSzczegoly($_SESSION['new-serial-device-id'], true);
	  ?>
	  <a href="?modul=zlecenia&akcja=dodawanie&act=setup" class="like-button" style="margin: 0 auto; float: none; width: 70px;">Dalej</a>
        <?php
    }
    if($ActionSerialNumber == 4){
	$Kategorie =  new ModulKategorie($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
	$DaneKategorii = $Kategorie->PobierzDaneElementu($DaneUrzadzenia['category_id']);
        if(isset($_POST['OpcjaFormularza'])){
	  $Wartosci['devices_name_id'] = $DaneUrzadzenia['devices_title_devices_title_id'];
	  $Wartosci['devices_name'] = $DaneUrzadzenia['title'];
	  $Formularz->Wyswietl($Wartosci, false);
        }else{
            $DaneDomyslne += array(
				  'amount' => $DaneKategorii[$typ[$this->Type]."_amount"],
				  'happened_at' => date("Y-m-d"),
				  );
            $Formularz = $this->GenerujFormularz($DaneDomyslne, false);
            $Formularz->Wyswietl($DaneDomyslne, false);
        }
    }

    if($ActionSerialNumber == 5){
//        $Urzadzenia->AkcjaSzczegoly($DaneUrzadzenia['id']);
        if(isset($_POST['OpcjaFormularza'])){
            $Formularz->Wyswietl($Wartosci, false);
        }else{
            if($this->DataDozwolonejAkcji == 'Today'){
                $DataDozwolonejAkcji = date('Y-m-d');
            }else{
                $DataDozwolonejAkcji = $this->DataDozwolonejAkcji;
            }
            $DaneDomyslne += array('happened_at' => $DataDozwolonejAkcji);
            $Formularz = $this->GenerujFormularz($DaneDomyslne, false);
            $Formularz->Wyswietl($DaneDomyslne, false);
        }
    }
       if($ActionSerialNumber == 6){
//        $Urzadzenia->AkcjaSzczegolyProste($DaneUrzadzenia);
	$Kategorie =  new ModulKategorie($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
	$DaneKategorii = $Kategorie->PobierzDaneElementu($DaneUrzadzenia['category_id']);
	$pola = array_keys($this->GetPolaUrzadzenie());
	
        if(isset($_POST['OpcjaFormularza'])){
	  $Wartosci['device_dane']['device_name'] = $DaneUrzadzenia['title'];
	  $Wartosci['device_dane']['device_setup'] = $Items['happened_at'];
	  $Wartosci['device_dane']['device_serial_number'] = $Values['SerialNumberCheck'];
	  $Wartosci['device_dane']['device_firm'] = $this->Baza->GetValue("SELECT name FROM companies WHERE id = '{$Items['company_id']}'");
	  $Wartosci['device_dane']['device_email'] = $this->Baza->GetValue("SELECT email FROM users WHERE id = '{$Items['user_id']}'");;
	  $Wartosci['device_dane']['device_tel'] = $this->Baza->GetValue("SELECT phone_number FROM companies WHERE id = '{$Items['company_id']}'");;;
	  $Formularz->Wyswietl($Wartosci, false);
        }else{
            $DaneDomyslne += array(
		    'rbh_amount' => $DaneKategorii["rbh_amount"],
		    'happened_at' => date("Y-m-d"),
		    'holidays' => '0',
		    'number_of_people' => '1',
		    'holidays_amount' => $DaneKategorii['holidays_amount'],
		    'next_rbh_amount' => $DaneKategorii['next_rbh_amount'],
		    'kmrbh_amount' => $DaneKategorii['kmrbh_amount'],
		    );
            
	    $DaneDomyslne['device_dane']['device_name'] = $DaneUrzadzenia['title'];
	    $DaneDomyslne['device_dane']['device_setup'] = $Items['happened_at'];
	    $DaneDomyslne['device_dane']['device_serial_number'] = $Values['SerialNumberCheck'];
	    $DaneDomyslne['device_dane']['device_firm'] = $this->Baza->GetValue("SELECT name FROM companies WHERE id = '{$Items['company_id']}'");
	    $DaneDomyslne['device_dane']['device_email'] = $this->Baza->GetValue("SELECT email FROM users WHERE id = '{$Items['user_id']}'");;
	    $DaneDomyslne['device_dane']['device_tel'] = $this->Baza->GetValue("SELECT phone_number FROM companies WHERE id = '{$Items['company_id']}'");;;
            $UserCompanyID = $this->Uzytkownik->ZwrocInfo('company_id');
            $Wartosci['company_id'] = $UserCompanyID;
            $Firma = new ModulFirmy($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
            $DaneFirmy = $Firma->PobierzDaneElementu($UserCompanyID);
            $DaneDomyslne['firm_dane'] = array(
                      'firm_name' => $DaneFirmy['name'],
                      'firm_nip' => $DaneFirmy['nip'],
                      'firm_email' => $this->Uzytkownik->ZwrocInfo('email'),
                      'firm_user' => $this->Uzytkownik->ZwrocInfo('concat(firstname, " ", surname)'),
                      'firm_phone' => $DaneFirmy['phone_number'],
                      'firm_fax' => $DaneFirmy['fax'],
            );				  
            $Formularz = $this->GenerujFormularzNaprawa($DaneDomyslne, false);
            $Formularz->Wyswietl($DaneDomyslne, false);
        }
    }

?>
</div>