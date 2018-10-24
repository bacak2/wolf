<div class='filters-and-actions' style="width: 100%;">
    <table class="lista" style="width: 95%; margin: 0 auto;">
        <tr>
            <?php
                foreach($PolaJeden as $Pole => $Name){
                    ?><th><?php echo (is_array($Name) ? $Name['naglowek'] : $Name); ?></th><?php
                }
            ?>
        </tr>
        <tr>
            <?php
                foreach($PolaJeden as $Pole => $Name){
                    ?><td<?php echo (is_array($Name) && isset($Name['style']) ? " style='{$Name['style']}'" : ""); ?>><?php echo (is_array($Name) && isset($Name['elementy']) ? $Name['elementy'][$Dane[$Pole]] : $Dane[$Pole]); ?></td><?php
                }
            ?>
        </tr>
    </table>
    <br /><br />
    <table class="lista" style="width: 95%; margin: 0 auto;">
        <tr>
            <?php
                foreach($PolaDwa as $Pole => $Name){
                    ?><th><?php echo (is_array($Name) ? $Name['naglowek'] : $Name); ?></th><?php
                }
            ?>
        </tr>
        <tr>
            <?php
                foreach($PolaDwa as $Pole => $Name){
                    ?><td<?php echo (is_array($Name) && isset($Name['style']) ? " style='{$Name['style']}'" : ""); ?>><?php echo (is_array($Name) &&  ( isset($Name['elementy']) || isset($Name['lista']) ) ? (isset($Name['elementy']) ? $Name['elementy'][$Dane[$Pole]] : Usefull::GetLI($Dane[$Pole], $Name['lista'], (isset($Name['modul']) ? $Name['modul'] : null )) ): $Dane[$Pole]); ?></td><?php
                }
            ?>
        </tr>
    </table>
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
        foreach($PolaComments as $Pole => $Name){
            ?><div class="comment"><span class="comment-label"><?php echo $Name; ?>:</span><br /><?php echo $Dane[$Pole]; ?></div><?php
        }
    ?>

<?php
}
?>
