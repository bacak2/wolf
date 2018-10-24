<div class="komunikat_alert">Brak Numeru seryjnego w systemie, to zlecenie zostanie przekazane do WOLF z oznaczeniem weryfikacji</div>
<div class="komunikat_info">Wybierz typ urządzenia z listy poniżej.</div>
<script type='text/javascript' src='js/plugins/chosen.jquery.js'></script>
<div style="width: 93%; margin: 25px auto;">
<div id="div-super-form" style="width: 70%; margin: 0 auto;">
<?php
    FormularzSimple::FormStart("check-serialnumber", "?modul={$this->Parametr}&akcja=dodawanie&act=intervention");
        $ListaUrzadzen = Usefull::PolaczDwieTablice(array("-1" => " -- wybierz urządzenie --"), $UrzadzeniaLista); ?>
<div class="super-form-span4">&nbsp;</div>
<div class="super-form-span2">
  <div class="super-form-label">
    <label for="SerialNumberCheck">Typ urządzenia</label>
  </div><div class="clear"></div>
  <div class="super-form-div">
    <div class="super-form-field">
	<?php FormularzSimple::PoleSelect("new-serial-device-id", $ListaUrzadzen, -1, "data-placeholder='-- wybierz urządzenie --' class='chzn-select'") ?>
    </div>
  </div>
  <div class="super-form-div">
    <div class="text_center">
      <?php FormularzSimple::PoleSubmit("make-setup", "Dodaj kocioł", "class='search-button'"); ?>
    </div>
  </div>
</div>
<div class="super-form-span4">&nbsp;</div>
<?php
    FormularzSimple::FormEnd();
?>
</div>