<?php
$headers = array('kzc'=>'Numer</br />katalogowy', 
            'title'=>'Nazwa części',
            'sum_quantity'=>'Ilość</br />zamówiona',
            'sum_quantity_approved' => 'Ilość</br />sprzedana',
            'amount'=>'Cena katalogowa<br />netto [EUR]',
            'discount'=> 'Rabat %',
            'amount_approved'=>'Wartość</br />netto',
            'pay_quantity'=> 'Do zapłaty',
            'comments'=>'Uwagi');

$AlgnLeft = array('quantity', 'amount', 'value', 'sum_quantity', 'sum_quantity_approved', 'discount', 'amount_approved', 'pay_quantity');
$Input = array('sum_quantity_approved', 'discount');
$Textarea = array('comments');

    foreach($headers as $Head){ ?>
        <div class="super-form-span<?php echo count($headers); ?>" style="font-weight:normal;text-align: center;" >
            <?php echo $Head; ?>
        </div>
<?php } ?>
<div class="clear"></div>
<?php
if(!empty($Wartosc ) && is_array($Wartosc))
    foreach($Wartosc as $NumItem=>$Item){
        foreach($headers as $Head=>$HeadName){ ?>
            <div class="super-form-span<?php echo count($headers); ?>" >
                <div style="line-height: 28px;<?php echo (in_array($Head, $AlgnLeft) ? 'text-align: right;margin-right:16px;' : '') ?>">
                    <?php echo empty($Item[$Head]) ? '&nbsp;' : $Item[$Head]; ?>
                </div>
            </div>
        <?php  } ?>
        <div class="clear"></div>
    <?php  } ?>