<?php
include("configure.php");

$Panel = new Panel($BazaParametry);
include("include/modules.php");

if(isset($_GET['akcja']) && $_GET['akcja'] == "pobierz"){
    $Panel->WyswietlPobierz();
}elseif(isset($_GET['typ']) && $_GET['typ'] == "drukuj"){
    $Panel->WyswietlDrukuj( $_GET['modul'], $_GET['akcja'] );
}else{
    $Panel->Wyswietl();    
}


?>
