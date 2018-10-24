<?php
/**
* tutaj będzie komentarz
**/
session_start();
setlocale(LC_ALL, 'pl_PL.UTF-8');
ini_set('error_log', '_error_log/error.log');
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
if( APPLICATION_ENV == 'development' ){
    error_reporting(E_ALL);
}else{
    error_reporting(E_ERROR & ~E_NOTICE);
}
if(!defined('SCIEZKA_OGOLNA')) {
	define('SCIEZKA_OGOLNA', '');
}
$PathCron = '';
define('SCIEZKA_INCLUDE', SCIEZKA_OGOLNA.'include/');
define('SCIEZKA_KLAS', SCIEZKA_INCLUDE.'classes/');
define('SCIEZKA_SZABLONOW', SCIEZKA_INCLUDE.'templates/');
define('SCIEZKA_MODULOW', SCIEZKA_KLAS.'modules/');
define('SCIEZKA_QUERIES', SCIEZKA_KLAS.'queries/');
define('SCIEZKA_FORMULARZY', SCIEZKA_KLAS.'forms/');
define('SCIEZKA_DANYCH', $PathCron.'data/');
define('SCIEZKA_DANYCH_MODULOW', SCIEZKA_DANYCH.'modules/');
define('SCIEZKA_SMTPPHPMAILER', SCIEZKA_KLAS.'smtpphpmailer/');
define('SCIEZKA_PHPEXCEL', SCIEZKA_KLAS.'phpexcel/');
include("include/db_access.php");
include("include/classes.php");


?>