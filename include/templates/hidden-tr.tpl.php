<?php
if(isset($Element['hidden-tr']) && is_array($Element['hidden-tr']))
    foreach($Element['hidden-tr'] as $HidenTR){
    ?>
        <tr class='hidden-<?php echo $Element[$HideTRClassName]; ?>' style="display: none;">
            <td colspan="<?php echo $Colspan?>">
            <?php
                if(isset($this->include_tpls)){
                    if(isset($this->include_tpls_data) && is_array($this->include_tpls_data))
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
            </td>
        </tr>
    <?php } ?>
    