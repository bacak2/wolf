<?php
$headers = array('kzc'=>'Numer katalogowy', 'title'=>'Nazwa części','quantity'=>'Ilość', 'amount'=>'Cena katalogowa<br />netto [EUR]', 'value'=>'Wartość netto', 'comments'=>'Uwagi');
    foreach($headers as $Head){ ?>
        <div class="super-form-span<?php echo count($headers); ?>" style="font-weight:normal" >
            <?php echo $Head; ?>
        </div>
<?php } ?>
<div class="clear"></div>
<?php
if(!empty($Wartosc ) && is_array($Wartosc))
    foreach($Wartosc as $Item){
        foreach($headers as $Head=>$HeadName){ ?>
            <div class="super-form-span<?php echo count($headers); ?>" >
                <div <?php echo (in_array($Head, array('quantity', 'amount', 'value')) ? 'style="text-align: right;margin-right:32px;"' : '') ?>>
                    <?php echo empty($Item[$Head]) ? '&nbsp;' : $Item[$Head]; ?>
                </div>
            </div>
        <?php  } ?>
        <div class="clear"></div>
    <?php  } ?>