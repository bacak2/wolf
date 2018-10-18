<script type="text/javascript" src="js/intervention.js"></script>
<div class="komunikat_alert">
    <?php echo $this->CheckError; ?>
</div>
<div style="float: left; text-align: center;width: 90%; margin-left: 80px; ">
<?php 
    if($this->Uzytkownik->ZwrocInfo('role') != 'ai'){
?>
<a href='?modul=repairitems&akcja=dodawanie' class="like-button" style="float: left;">Naprawa pogwarancyjna</a>
<?php
}
/*
if($this->Uzytkownik->IsAdmin()){ ?>
        <a href="javascript:MakeIntervention(<?php echo $PackageID; ?>, 'repair');" class="like-button" style="float: left; margin-left: 20px;"> Naprawa gwarancyjna</a>
        <a href="javascript:MakeIntervention(<?php echo $PackageID; ?>, 'service');" class="like-button" style="float: left; margin-left: 20px;"> Przegląd gwarancyjny</a>
<?php
}
*/?>
</div>
