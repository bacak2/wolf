<?php if( isset($aNavi) && is_array($aNavi) ){ ?>
<div class="f_right linkmenu" style="font-weight: bold; margin-right:32px;">
    <?php foreach ($aNavi as $keyNavi => $valueNavi){ ?>
    <div class="f_left">
        <a class="linkemnu" href="<?php echo $valueNavi; ?>"<?php echo ($valueNavi == '#' ? " style='background-color: #FFF;'" : ""); ?>>
            <?php echo $keyNavi; ?>
        </a>&nbsp;</div>
    <?php } ?>
</div>
    <div style="clear:both;"></div>
<?php } ?>
<?php if(isset($aHead)){ ?>
<div style="font-weight: bold; width: 95%; margin: 25px auto 0px auto;"><?php echo $aHead; ?>:</div>
<?php }?>
<table class="lista" style="width: 95%; margin: 15px auto;">
    <tr>
        <?php
            foreach($PolaDwa as $Pole => $Name){
                ?><th<?php echo (is_array($Name) && isset($Name['th_style']) ? " style='{$Name['th_style']}'" : ""); ?>><?php echo (is_array($Name) ? $Name['naglowek'] : $Name); ?></th><?php
            }
        ?>
    </tr>
    <?php
        if(isset($Dane['array']) && is_array($Dane['array'])){
            foreach($Dane['array'] as $KatID => $Element){
            ?>
                <tr>
                    <?php
                        foreach($PolaDwa as $Pole => $Name){
                            ?><td<?php echo (is_array($Name) && isset($Name['style']) ? " style='{$Name['style']}'" : ""); ?>><?php echo (is_array($Name) &&  ( isset($Name['elementy']) || isset($Name['lista']) ) ? (isset($Name['elementy']) ? $Name['elementy'][$Element[$Pole]] : Usefull::GetLI($Element[$Pole], $Name['lista'], (isset($Name['modul']) ? $Name['modul'] : null ) ) ): (isset($Element[$Pole]) ? $Element[$Pole] : '')); ?></td><?php
                        }
                    ?>
                </tr>
            <?php
            }
        }else{ ?>
            <tr>
                    <?php
                        foreach($PolaDwa as $Pole => $Name){
                            ?><td<?php echo (is_array($Name) && isset($Name['style']) ? " style='{$Name['style']}'" : ""); ?>><?php echo (is_array($Name) &&  ( isset($Name['elementy']) || isset($Name['lista']) ) ? (isset($Name['elementy']) ? $Name['elementy'][$Dane[$Pole]] : Usefull::GetLI($Dane[$Pole], $Name['lista'], (isset($Name['modul']) ? $Name['modul'] : null )) ): (isset($Dane[$Pole]) ? $Dane[$Pole] : '')); ?></td><?php
                        }
                    ?>
                </tr>
            <?php
        }
    ?>
</table>
<?php if( isset($Dane['description']) ){ ?>
<div style="width: 95%; margin: 0 auto;">
    <p>Opis: <i><?php echo nl2br(stripslashes($Dane['description'])); ?></i></p>    
</div>
<?php } ?>