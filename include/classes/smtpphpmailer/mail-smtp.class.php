<?php
/**
 * Moduł wysyłki emaili przez SMTP
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright	Copyright (c) 2004-2008 ARTplus
 * @version		1.0
 */
class MailSMTP {
	public $Mail;
        public $Errors = false;
	
	function __construct() {
            include("class.phpmailer.php");
            $this->Mail = new PHPMailer();
            $this->Mail->MailerDebug = 10;
            $this->PluginDir = "";
            $this->Mail->IsSMTP();
            $this->Mail->SMTPSecure = 'tls';
            $this->Mail->From = "no-reply@....."; //adres naszego konta
            $this->Mail->FromName = "Wolf Technika Grzewcza Sp. z o.o.";//nagłówek From
            $this->Mail->Host = "wolfpolska.artplus24.pl";//adres serwera SMTP
            $this->Mail->Mailer = "smtp";
            $this->Mail->Username = "no-reply@...";//nazwa użytkownika
            $this->Mail->Password = "password";//nasze hasło do konta SMTP
            $this->Mail->SMTPAuth = true;
            $this->Mail->SetLanguage("pl", "language/");
	}
	
	function encodeSlowo($s) {
		return "=?utf-8?B?" . base64_encode($s) . "?=";
	}

        function SendEmail($Mail, $Tytul, $Tresc){
            $Tytul = $this->encodeSlowo($Tytul);
            $this->Mail->Subject = $Tytul;
            $this->Mail->CharSet = "utf-8";
            $this->Mail->IsHTML(true);
            $this->Mail->Body = $Tresc;
            $this->Mail->AddAddress($Mail);
            if($this->Mail->Send()){
                $this->Mail->ClearAddresses();           
                return true;
            }
            $this->Errors = $this->Mail->errorMessage();
            $this->Mail->ClearAddresses();
            return false;
        }

        function GetErrors(){
            return $this->Errors;
        }
        
        function _AddReplyTo($address, $name = '') {
            $this->Mail->AddReplyTo($address, $name);
        }
}
?>
