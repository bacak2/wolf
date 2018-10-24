<?php
    include("configure.php");
    $Panel = new Panel($BazaParametry);
    $Panel->WyswietlAJAX("ModulKoszyk", "check", "koszyk");
?>
