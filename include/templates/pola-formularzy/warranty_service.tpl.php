<?php if($this->FormWyswietl == 'WyswietlDane'){ ?>
    <div class="super-form-span1">
        <?php echo empty($Wartosc) ? 'brak' : $Wartosc; ?>
    </div>
<?php }else{
    if(isset($Wartosc) && !empty($Wartosc)){
        list($Wartosc, $warranty_service_months) = explode('|', $Wartosc );
    }else {
            $warranty_service_months = '';
            $Wartosc = '';
        }
?>
<div class="super-form-span8">
    <?php echo FormularzSimple::PoleCheckbox($this->MapaNazw[$Nazwa], 1, $Wartosc, '', 'id="chbox_warranty_service_months"') ;?>
</div>
<div class="super-form-span" style="width:87.5%;">
    <div class="super-form-field" >
        <?php 
        $warranty_service_months = isset($POST['warranty_service_months']) ? $POST['warranty_service_months'] : $warranty_service_months;
        $war = preg_replace('/[-\',]/','', $warranty_service_months);
        $tmp = explode(' ',trim($war));
        $war = implode(', ', $tmp);
        $Dodatki = $Wartosc === '0' ? 'disabled="disabled"' : '';
        echo FormularzSimple::PoleInputText('warranty_service_months', htmlspecialchars(stripslashes($war), ENT_QUOTES), $Dodatki, 'warranty_service_months');
        ?>
    </div>
</div>
<?php } ?>
<script type="text/javascript">
$(document).ready(function(){
    $("#chbox_warranty_service_months").click(function(){
        $('#chbox_warranty_service_months').prop('checked') ?
            $('#warranty_service_months').removeAttr('disabled')
            : $('#warranty_service_months').attr('disabled', 'disabled');
    });
});
</script>