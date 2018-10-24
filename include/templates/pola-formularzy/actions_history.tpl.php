<?php
$headers = array('type'=>'Czynność', 'happened_at'=>'Data zdarzenia','description'=>'Opis');
foreach($headers as $Head){ ?>
    <div class="super-form-span3">
	<?php echo $Head; ?>
    </div>
<?php }
if(!empty($Wartosc ) && is_array($Wartosc))
    foreach($Wartosc as $Item)
	foreach($headers as $KHead=>$Head){ ?>
	    <div class="super-form-span3">
		<?php echo ($KHead=='type' ? $this->Pola[$Nazwa]['opcje']['elementy'][$Item[$KHead]] : $Item[$KHead]); ?>
	    </div>
    <?php } ?>
