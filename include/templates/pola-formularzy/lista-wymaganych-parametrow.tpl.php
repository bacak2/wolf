<?php
    $Elementy = $this->Pola[$Nazwa]['opcje']['elementy'];
?>
<?php if($this->FormWyswietl == 'WyswietlDane'){ ?>
    <div class="super-form-span1">
       <?php
        if(is_array($Wartosc)){
            foreach($Wartosc as $i_prametr_id){ ?>
                <div><?php echo $Elementy[$i_prametr_id]; ?></div>
           <?php } 
        } ?>
    </div>
<?php }else{ ?>
    <?php if(!isset($this->_Plugins['lista_parametrow_urzadzenia']) || !$this->_Plugins['lista_parametrow_urzadzenia']){ ?>
        <script type="text/javascript" src="js/urzadzenia.js"></script>
        <script type='text/javascript' src='js/plugins/chosen.jquery.js'></script>
    <?php } ?>
    <table cellpadding="0" cellspacing="0" class="no-border padding-left-0px width_max">
        <tr>
            <td style="padding-left:0px;width:80%;"><?php FormularzSimple::PoleSelect ($this->MapaNazw[$Nazwa].'_add', $Elementy, 0, "class='chzn-select' $AtrybutyDodatkowe", $this->MapaNazw[$Nazwa]); ?></td>
            <td style="vertical-align: middle;"><?php FormularzSimple::PoleSubmit($this->MapaNazw[$Nazwa].'_submit', "Dodaj", 'style="margin-left:10px;" onclick="javascript:ValueChange(\'OpcjaFormularza\',\''.$this->MapaNazw[$Nazwa].'\');"'); ?></td>
        </tr>
        <tr><th colspan="2">Lista wymaganych parametrów:</th></tr>
        <tr><td colspan="2">
            <table id="required_checkbox">
                <?php
                if(isset($Wartosc['add']) && is_array($Wartosc['add'])){
                    foreach($Wartosc['add'] as $i_prametr_id){ ?>
                <tr id="tr_<?php echo $i_prametr_id; ?>"><td><?php echo $this->Pola[$Nazwa]['opcje']['elementy'][$i_prametr_id]; FormularzSimple::PoleHidden( "{$this->MapaNazw[$Nazwa]}[add][]", $i_prametr_id, 'id=parametr_'.$i_prametr_id  ); ?></td><td><?php echo Usefull::DelSmallButton( 'delTR', $i_prametr_id ); ?></td></tr>
                    <?php }
                    unset($Wartosc['add']);
                }
                if(is_array($Wartosc)){
                    foreach($Wartosc as $i_prametr_id){ ?>
                        <tr id="tr_<?php echo $i_prametr_id; ?>"><td><?php echo $this->Pola[$Nazwa]['opcje']['elementy'][$i_prametr_id];FormularzSimple::PoleHidden( "{$this->MapaNazw[$Nazwa]}[]", $i_prametr_id, 'id=parametr_'.$i_prametr_id  ); ?></li></td><td><?php echo Usefull::DelSmallButton('delTR', $i_prametr_id ); ?></td></tr>
                   <?php }
                }
                ?>
            </table>
        </td></tr>
    </table>
<?php } ?>