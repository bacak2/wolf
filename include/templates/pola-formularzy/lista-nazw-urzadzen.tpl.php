<?php
    $Elementy = Usefull::PolaczDwieTablice(array('0' => $this->Pola[$Nazwa]['opcje']['domyslny']),
            $this->Pola[$Nazwa]['opcje']['elementy']);
?>
<?php if($this->FormWyswietl == 'WyswietlDane'){ ?>
    <div class="super-form-span1">
        <?php echo $Wartosc; ?>
    </div>
<?php }else{ ?>
<script type='text/javascript' src='js/plugins/chosen.jquery.js'></script>
<table cellpadding="" cellspacing="0" class="no-border width_max">
    <tr>
        <td colspan="2" style="padding-left:0px;"><?php FormularzSimple::PoleSelect("{$this->MapaNazw[$Nazwa]}", $Elementy, $Wartosc, 'class="chzn-select"') ?></td>
    </tr>
    <tr>
        <td><?php FormularzSimple::PoleSubmit("edytuj_nazwe_urzadzenia", "Edytuj", 'onclick="javascript:ValueChange(\'OpcjaFormularza\',\'edytuj_nazwe_urzadzenia\');"'); ?></td>
        <td><?php FormularzSimple::PoleSubmit("dodaj_nazwe_urzadzenia", "Dodaj", 'onclick="javascript:ValueChange(\'OpcjaFormularza\',\'dodaj_nazwe_urzadzenia\');"'); ?></td>
    </tr>
</table>
<?php } ?>
