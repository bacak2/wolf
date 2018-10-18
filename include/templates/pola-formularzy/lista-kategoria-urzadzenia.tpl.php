<?php if($this->FormWyswietl == 'WyswietlDane'){ ?>
    <div class="super-form-span1">
        <?php echo $Wartosc; ?>
    </div>
<?php }else{ ?>
    <table cellpadding="3" cellspacing="0" class="no-border width_max" >
        <tr>
            <th class="kategorie" style="padding-left: 0px;">
                <div  style="min-height: 21px;" class="<?php echo $this->Pola[$Nazwa]['opcje']['title'];?>_name"><?php echo (isset($this->Pola[$Nazwa]['opcje']['elementy'][$Wartosc]) ? $this->Pola[$Nazwa]['opcje']['elementy'][$Wartosc] :  "&nbsp;"); ?></div>
                <?php FormularzSimple::PoleHidden($this->MapaNazw[$Nazwa], $Wartosc, 'id="'.$this->Pola[$Nazwa]['opcje']['title'].'"' ); ?>
            </th>
        </tr>
        <tr>
            <td style="vertical-align: middle">
                <a href="javascript:kategorie_show('<?php echo $this->Pola[$Nazwa]['opcje']['title'];?>')" class="like-button">Wybierz</a>
            </td>
        </tr>
    </table>

    <div id="select_<?php echo $this->Pola[$Nazwa]['opcje']['title'];?>" class="kategorie">
    <?php
    FormularzSimple::PoleHidden('temp_'.$this->MapaNazw[$Nazwa], $Wartosc, 'id="temp_'.$this->Pola[$Nazwa]['opcje']['title'].'"' ); 
    FormularzSimple::PoleHidden('temp_title_'.$this->MapaNazw[$Nazwa], $Wartosc, 'id="temp_title_'.$this->Pola[$Nazwa]['opcje']['title'].'"' ); 

        $select = array();
        $kategoria_class = '';
            foreach($this->Pola[$Nazwa]['opcje']['selects'] as $key=>$value){
                    $kategoria_class .= $this->Pola[$Nazwa]['opcje']['title'].'_'.$key.' ';
                foreach($value as $level_key => $level_values){
                    if( $key == 0 )
                        $select[$key][$level_key] = '<select class="kategoria '.$kategoria_class.' select_'.$this->Pola[$Nazwa]['opcje']['title'].'_'.$level_key.'" name="'.$this->Pola[$Nazwa]['opcje']['title'].'_'.$level_key.'" kategoria_id="'.$level_key.'" title="'.$this->Pola[$Nazwa]['opcje']['title'].'" onchange="javascript:kategoria(\''.$this->Pola[$Nazwa]['opcje']['title'].'_'.$key.'\', \''.$this->Pola[$Nazwa]['opcje']['title'].'_'.$level_key.'\')">';
                    else
                        $select[$key][$level_key] = '<select class="kategoria '.$kategoria_class.' select_'.$this->Pola[$Nazwa]['opcje']['title'].'_'.$level_key.'" name="'.$this->Pola[$Nazwa]['opcje']['title'].'_'.$level_key.'" kategoria_id="'.$level_key.'" style="display: none;" title="'.$this->Pola[$Nazwa]['opcje']['title'].'"  onchange="javascript:kategoria(\''.$this->Pola[$Nazwa]['opcje']['title'].'_'.$key.'\', \''.$this->Pola[$Nazwa]['opcje']['title'].'_'.$level_key.'\')">';
                    $select[$key][$level_key] .= '<option class="option_kategorie_'.$level_key.'" value="0">-- wybierz --</option>';
                    foreach($level_values as $select_key => $select_values){
                        $select[$key][$level_key] .= '<option class="option_kategorie_'.$level_key.'" value="'.$select_values['id'].'">'.$select_values['title'].'</option>';
                    }
                    $select[$key][$level_key] .= '</select>';
                    if( $key == 0 ) $kategoria_class = '';
                }
            }
            $kategoria_class = '';
             foreach($select as $klas_sel=>$sel){  
                 $kategoria_class = $this->Pola[$Nazwa]['opcje']['title'].'_'.$klas_sel.' ';
                 ?>
                <div class="div-categories <?php echo $kategoria_class;?>" <?php echo ($klas_sel == 0 ? 'name="'.$klas_sel.'"' : 'name="'.$klas_sel.'" style=" display: none"'); ?>>
                <?php
                echo ($klas_sel == 0 ? '<h1>Kategoria</h1>' : '<h1>Podkategoria</h1>' );
                 foreach($sel as $sel2){?>
                    <div> <?php echo $sel2; ?> </div>
             <?php } ?>
            </div>
             <?php }
        ?>
        <div>
            <a href="javascript:kategorie_save_hide('<?php echo $this->Pola[$Nazwa]['opcje']['title'];?>')" class="like-button">Zatwierdź</a>
            <a href="javascript:kategorie_hide('<?php echo $this->Pola[$Nazwa]['opcje']['title'];?>')" class="like-button">Anuluj</a>
        </div>
    </div>
<?php } ?>