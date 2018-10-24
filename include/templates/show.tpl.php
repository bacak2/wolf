<?php if(isset($Name) && is_array($Name) && !empty($Name)) { ?>
    <div class="szczegoly-name"><?php echo $Name['key']; ?>: <span><?php echo $Name['value']; ?></span></div>
<?php }
if(isset($Name_multi) && is_array($Name_multi) && !empty($Name_multi)) { 
    foreach($Name_multi as $Name ){ ?>
            <div class="szczegoly-name"><?php echo $Name['key']; ?> <span><?php echo $Name['value']; ?></span></div>
    <?php } 
}

if(isset($Komunikat) && !empty($Komunikat)){
    Usefull::$Komunikat['type']($Komunikat['text'], (isset($Komunikat['bold']) ? $Komunikat['bold'] : false) );
} 
?>
<div <?php echo (isset($Simply) && $Simply == true ? "" : "class='filters-and-actions'"); ?> style="width: 100%; margin: 10px auto 20px auto;">
    <?php if(isset($Zdjecie) && !empty($Zdjecie)) { ?>
        <div style="width: <?php echo (isset($Simply) && $Simply == true ? ((isset($_GET['act']) && !in_array($_GET['act'], array("setup", "repair", "service"))) ? "65%" : "93%") : "95%"); ?>; margin: 0 auto;">
            <div style="float: left;width: <?php echo $Zdjecie['width']; ?>px;">
                <img src='<?php echo $Zdjecie['src']; ?>' />
            </div>
            <div style="float:left;width: 70%">
        <?php }?>
    <table class="lista" style="width: <?php echo (isset($Simply) && $Simply == true ? ((isset($_GET['act']) && !in_array($_GET['act'], array("setup", "repair", "service"))) ? "65%" : "93%") : "95%"); ?>; margin: 0 auto;">
        <tr>
            <?php
                foreach($PolaJeden as $Pole => $Name){
                    ?><th><?php echo (is_array($Name) ? $Name['naglowek'] : $Name); ?></th><?php
                }
            ?>
        </tr>
        <tr<?php echo ( (isset($Dane['no_warranty']['check']) && $Dane['no_warranty']['check'] == 1) ? " style='background-color: #fcc4c4;'" : ""); ?>> 
            <?php
                foreach($PolaJeden as $Pole => $Name){
                    ?><td<?php echo (is_array($Name) && isset($Name['style']) ? " style='{$Name['style']}'" : ""); ?>>
                        <?php
                        if(isset($Name['color']) && isset($Name['color'][$Dane[$Pole]])){
                            echo "<span class='{$Name['color'][$Dane[$Pole]]}'>";
                        }
                        echo (is_array($Name) && isset($Name['elementy']) ? $Name['elementy'][$Dane[$Pole]] : $Dane[$Pole]);
                        if(isset($Name['color']) && isset($Name['color'][$Dane[$Pole]])){
                            echo "</span>\n";
                        }
                        ?>
                    </td><?php
                }
            ?>
        </tr>
    </table>
    <?php if(isset($Zdjecie) && !empty($Zdjecie)) { ?>
        </div>
        <div style='clear:both;'></div>
    <?php } ?>
    <br /><br />
    <div style="width: 95%; margin: 0 auto;">
        <?php
        if(isset($PolaDwa) && !empty($PolaDwa) && $this->bShowPolaDwa){
            foreach($PolaDwa as $Pole => $Name){
                if(is_array($Name) && isset($Name['type']) && $Name['type'] == "zamiennik"){
                    $this->ShowZamiennik($Dane[$Pole], $Name);
                }else if(is_array($Name) && isset($Name['type']) && $Name['type'] == "urzadzenia"){
                    $this->ShowUrzadzenia($Dane[$Pole], $Name);
                }else{
                ?>
                <p><?php echo (is_array($Name) ? $Name['naglowek'] : $Name); ?>: <?php echo (is_array($Name) && $Name['long'] ? "<br />" : ""); ?><i><?php echo (is_array($Name) && isset($Name['elementy']) ? $Name['elementy'][$Dane[$Pole]] : $Dane[$Pole]); ?></i></p>
                <?php
                }
            }
        }
        ?>
    </div>
    <?php
        if(isset($this->include_tpls)){
            if(isset($this->include_tpls_data))
                extract($this->include_tpls_data);
            if(is_array($this->include_tpls)){
                foreach($this->include_tpls as $include_tpl){
                    if(file_exists(SCIEZKA_SZABLONOW.$include_tpl))
                        include(SCIEZKA_SZABLONOW.$include_tpl);
                }
            }elseif(is_string($this->include_tpls)){
                if(file_exists(SCIEZKA_SZABLONOW.$this->include_tpls))
                    include(SCIEZKA_SZABLONOW.$this->include_tpls);
            }else{
                if(file_exists($this->include_tpls))
                    include($this->include_tpls);
            }
        }
    ?>
    
</div>
<div style='clear:both;'></div>
<?php 
if(isset($CommentsMultipleDivs) && $CommentsMultipleDivs){
        foreach($PolaComments as $DivComment){
            ?><div class="comment"><?php
            foreach($DivComment as $Pole => $Name){
                ?><span class="comment-label"><?php echo $Name; ?>:</span><br /><?php echo $Dane[$Pole]; ?><br /><br /><?php
            }
            ?></div>
<?php
        }
}else{ ?>

    <?php
        if(isset($PolaComments) && is_array($PolaComments))
            foreach($PolaComments as $Pole => $Name){
                ?><div class="comment"><span class="comment-label"><?php echo $Name; ?>:</span><br /><?php echo $Dane[$Pole]; ?></div><?php
            }
    ?>

<?php
}
?>