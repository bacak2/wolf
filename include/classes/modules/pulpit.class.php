<?php
/**
 * Moduł pulpit
 * 
 * @author		Michal Bugański <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2012
 * @package		Panelplus 
 * @version		1.0
 */

class ModulPulpit extends ModulBazowy {
        public $Modules;
        function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->ModulyBezMenuBocznego[] = $this->Parametr;
            $this->ModulyBezDodawania[] = $this->Parametr;
            $this->ModulyBezEdycji[] = $this->Parametr;
            $this->ModulyBezKasowanie[] = $this->Parametr;
	}

        function SetModules($Modules){
            $this->Modules = $Modules;
        }

        function  Wyswietl($ID = null) {
            $Role = $this->Uzytkownik->ZwrocInfo('role');
            if( in_array($Role, array('serwisant_g')) ){
                $KonczaceSieSzkolenia = $this->PobierzSzkolenia($this->UserID);
                $ExpireDate = (strtotime($this->Uzytkownik->ZwrocInfo('expire_date')) < time() ? $this->Uzytkownik->ZwrocInfo('expire_date') : false);
                if($ExpireDate){
                    $PrzypomnienieOSzkoleniu[] = array( 'type' => 'ShowKomunikatError',
                    'text' => '<div>Twoje uprawnienia wygasły w dniu '. date("Y-m-d", strtotime($ExpireDate))."</div>
                        <div>Aby mieć możliwość korzystania w pełnu funkcjonalności zapisz się na szkolenie.</div>
                        <div style='margin-top: 8px;'><a href='?modul=lista_szkolen' style='color: red;'>LISTA DOSTĘPNYCH SZKOLEŃ</a></div>",
                    'bold' => true,);
                }
                if($KonczaceSieSzkolenia){
                    foreach($KonczaceSieSzkolenia as $KSS){
                        if( $KSS['days'] < 0){
                            $tekst = 'Uprawnienia numer <strong>'.$KSS['certificate_numbers'].'</strong> dla szkolenia '.$KSS['training_name'].' wygasły <strong>'. date("Y-m-d", strtotime($KSS['date_of_powers'])).'</strong>';
                        }else{
                            $tekst = 'Uprawnienia numer <strong>'.$KSS['certificate_numbers'].'</strong> dla szkolenia '.$KSS['training_name'].' wygasną <strong>'. date("Y-m-d", strtotime($KSS['date_of_powers'])).'</strong>';
                        }
                        $PrzypomnienieOSzkoleniu[] = array( 'type' => ($KSS['days'] > 0 ? 'ShowKomunikatOstrzezenie' : 'ShowKomunikatError'),
                        'text' => $tekst,
                        'bold' => false,
                        );
                    }
                }
            }
            $user_id = $this->UserID;
            $company_id = $this->Uzytkownik->ZwrocInfo("company_id");
            $IsPackageSQL = "SELECT id FROM packages WHERE status=0 AND user_id = '$user_id' AND company_id = '$company_id'";            
            $ZlId = $this->Baza->GetValue($IsPackageSQL);
            $ModulZamowienia = new ModulZamowienia($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
            if( $ZlId )
                $ZlId = '&zlid='.$ZlId;
            if($this->Uzytkownik->FirmaRMC($this->UserID)){
                $Opoznione = $ModulZamowienia->GetZamowieniaByStatus(3, 5, array('id' => 8, 'znak' => '>'), "role DESC, ile_dni DESC", false);
                $Oczekujace = $ModulZamowienia->GetZamowieniaByStatus(3, 6, array('id' => 8, 'znak' => '<='), "role DESC, sent_at DESC", false);
                $Zaakceptowane = $ModulZamowienia->GetZamowieniaByStatus(5, 22, false, "finalized_at DESC", false);
            }else{
                $ModulZlecenia = new ModulZestawienieZlecen($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
                $Opoznione = $ModulZlecenia->GetZleceniaByStatus(3, 2, array('id' => 9, 'znak' => '>'));
                $Komentarze = $ModulZlecenia->GetComments();
                $Odrzucone = $ModulZlecenia->GetZleceniaByStatus(4, 2, false, "finalized_at DESC");
                $Zaakceptowane = $ModulZlecenia->GetZleceniaByStatus(5, 3, false, "finalized_at DESC");
                $Oczekujace = $ModulZlecenia->GetZleceniaByStatus(3, 4, array('id' => 9, 'znak' => '<='), "sent_at DESC");
            }
            //$ModulZamowienia = new ModulZamowienia($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
            $OpoznioneZamowienia = $ModulZamowienia->GetZamowieniaByStatus(3, 5, array('id' => 8, 'znak' => '>'), "role DESC, ile_dni DESC", $this->Uzytkownik->FirmaRMC($this->UserID));
            $OczekujaceZamowienia = $ModulZamowienia->GetZamowieniaByStatus(3, 6, array('id' => 8, 'znak' => '<='), "role DESC, sent_at DESC", $this->Uzytkownik->FirmaRMC($this->UserID));
            $ZrealizowaneZamowienia = $ModulZamowienia->GetZamowieniaByStatus(5, 22, false, "finalized_at DESC", $this->Uzytkownik->FirmaRMC($this->UserID));
            if( $this->Uzytkownik->FirmaRMC($this->UserID) || $this->Uzytkownik->IsAdmin()) {
                $WDrodze = $ModulZamowienia->GetZamowieniaByStatus(2, 5, false, "ile_dni ASC", $this->Uzytkownik->FirmaRMC($this->UserID));
            }
            
            include(SCIEZKA_SZABLONOW."pulpit.tpl.php");
        }
        
        function WyswietlAJAX($Akcja){
            if(isset($_POST['id']) && !empty($_POST['id']) && is_numeric($_POST['id'])
            && isset($_POST['type']) && !empty($_POST['type']) && is_string($_POST['type'])
            && isset($_POST['status']) && !empty($_POST['status']) && is_numeric($_POST['status'])){
               $this->RemoveBox($_POST['id'], $_POST['status'], $_POST['type']);
            }else{
                echo 'cos nie tak';
            }
        }
        
        function RemoveBox($id_param, $status, $type){
            $Values['users_id'] = $this->Uzytkownik->ZwrocIdUzytkownika();
            $Values['id_param'] = $id_param;
            $Values['status'] = $status;
            $Values['type'] = $type;
            $InsertQuery = $this->Baza->PrepareInsert('pulpit_hide_box', $Values);
            if( $this->Baza->Query($InsertQuery) ){
                exit('true');
            }
            exit('error');
//            echo $this->VAR_DUMP($_POST);
        }
        
        function PobierzSzkolenia($ID){
            return $this->Baza->GetRows("
                SELECT t.training_name, t.date_of_powers,
                CONCAT(tcn.number, tcn.year) as certificate_numbers,
                tu.users_id, t.date_of_powers, DATEDIFF( date_of_powers, NOW()) as days
                FROM training_users tu
                LEFT JOIN training t ON (t.training_id = tu.training_id)
                LEFT JOIN training_certificate_numbers tcn ON (tcn.training_id = tu.training_id AND tcn.users_id = tu.users_id)
                WHERE NOW() BETWEEN DATE_SUB(t.date_of_powers,INTERVAL 3 MONTH ) AND DATE_ADD(t.date_of_powers,INTERVAL 1 MONTH)
                AND tu.users_id = '{$ID}'
                AND tu.status = 5
                ORDER BY days ASC
                ");
        }
}
?>
