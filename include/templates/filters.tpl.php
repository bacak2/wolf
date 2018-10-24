<?php
    if(count($this->Filtry)){
        FormularzSimple::FormStart("filters", "?modul=$this->ParametrFiltry&akcja=$this->WykonywanaAkcja");
        FormularzSimple::PoleHidden("filters-do-it", 1);
        $Chosen = false;
        for ($i = 0; $i < count($this->Filtry); $i++) {
            if(isset($this->Filtry[$i]['typ']) && $this->Filtry[$i]['typ'] == "lista" && $Chosen == false){
                ?><script type='text/javascript' src='js/plugins/chosen.jquery.js'></script><?php
                $Chosen = true;
            }
            if(isset($this->Filtry[$i]['typ']) && $this->Filtry[$i]['typ'] != 'hidden'){
            ?>
                <div class="filter-box"<?php echo (isset($this->Filtry[$i]['search']) ? " style='padding-top: 9px; margin-right: 0;'" : "") ?>>
            <?php
            }
                if(isset($this->Filtry[$i]['opis']) && $this->Filtry[$i]['opis'] != false ){
                    echo ($this->Filtry[$i]['opis'] === true ? '&nbsp;' : $this->Filtry[$i]['opis'].":<br />");
                }
                if(isset($this->Filtry[$i]['search'])){
                    echo "<nobr>\n";
                }
                if(isset($this->Filtry[$i]['typ']) && $this->Filtry[$i]['typ'] == "lista"){
                    $DomOp = (isset($this->Filtry[$i]['domyslna']) ? $this->Filtry[$i]['domyslna'] : "- wszystkie -");
                    //$Opcje = Usefull::PolaczDwieTablice(array("" => $DomOp), $this->Filtry[$i]['opcje']);
                    FormularzSimple::PoleSelectMulti("filtr_{$i}[]", $this->Filtry[$i]['opcje'], isset($_SESSION['Filtry'][$this->Filtry[$i]['nazwa']]) ? $_SESSION['Filtry'][$this->Filtry[$i]['nazwa']] : null, " onclick='Search()' data-placeholder='$DomOp' class='tabelka chzn-select ".(isset($this->Filtry[$i]['class']) ? $this->Filtry[$i]['class'] : '')."' style='".($this->Filtry[$i]['opis'] != false ? "margin-top: 12px;" : "")."'");
                }elseif(isset($this->Filtry[$i]['typ']) && $this->Filtry[$i]['typ'] == "tekst"){
                    $ValueDefault = "";
                    $IsDefault = false;
                    if(isset($_SESSION['Filtry'][$this->Filtry[$i]['nazwa']])){
                        $ValueDefault = $_SESSION['Filtry'][$this->Filtry[$i]['nazwa']];
                    }else if(isset($this->Filtry[$i]['domyslna'])){
                        $ValueDefault =$this->Filtry[$i]['domyslna'];
                        $IsDefault = true;
                    }
                    $Placeholder = '';
                    if(isset($this->Filtry[$i]['placeholder'])){
                        $Placeholder = 'placeholder="'.$this->Filtry[$i]['placeholder'].'"';
                    }
                    FormularzSimple::PoleInputText("filtr_$i", $ValueDefault, "class='tabelka' $Placeholder style='".($this->Filtry[$i]['opis'] != false ? "margin-top: 12px;" : "")."'");
                }elseif(isset($this->Filtry[$i]['typ']) &&  $this->Filtry[$i]['typ'] == "checkbox" ){
                    $iCheckbox=0;
                    if( isset($this->Filtry[$i]['col'])){
                       echo '<table>';
                    }
                    foreach( $this->Filtry[$i]['opcje'] as $sValue=>$sName ){
/*                        if(is_array($sName)){
                            $sName = implode('/', $sName);
                        }
*/
                        if( isset($this->Filtry[$i]['col']) && !($iCheckbox % $this->Filtry[$i]['col']) ){
                            echo '<tr>';
                            $close = false;
                        }
                        if( in_array($sValue, $_SESSION['Filtry'][$this->Filtry[$i]['nazwa']])){
                            $Wartosc = $sValue;
                        }else{
                            $Wartosc = null;
                        }
                        if(isset($this->Filtry[$i]['class'])){
                            if(is_array($this->Filtry[$i]['class'])){
                                $class = 'class="'.$this->Filtry[$i]['class'][$sValue].'"';
                            }else{
                                $class = 'class="'.$this->Filtry[$i]['class'].'"';
                            }
                        }else{
                            $class = '';
                        }
                        echo "<td $class >";
                            FormularzSimple::PoleCheckbox("filtr_{$i}[]", $sValue,  $Wartosc, $sName, "onclick='Search()' ".(isset($this->Filtry[$i]['dodatki']) ? $this->Filtry[$i]['style'] : '') );
                        echo "</td>";
                        $iCheckbox++;
                        if( isset($this->Filtry[$i]['col']) && !($iCheckbox % $this->Filtry[$i]['col']) && $iCheckbox ){
                            echo '</tr>';
                            $close = true;
                        }
//                        $_SESSION['Filtry'][$this->Filtry[$i]['nazwa']]
                        }
                        if(!$close){
                            $iCheckbox % $this->Filtry[$i]['col'];
                        }
                        if( isset($this->Filtry[$i]['col'])){
                           echo '</table>';
                        }
                    }elseif(isset($this->Filtry[$i]['typ']) && $this->Filtry[$i]['typ'] == "data_oddo"){                      
                        echo "Od: ";
                            FormularzSimple::PoleData("filtr_{$i}[od]", isset($_SESSION['Filtry'][$this->Filtry[$i]['nazwa']]['od']) ? $_SESSION['Filtry'][$this->Filtry[$i]['nazwa']]['od'] : '', null,"style='".($this->Filtry[$i]['opis'] != false ? "margin-top: 12px;" : "")."width:100px;'");
                        echo "<br />Do: ";
                            FormularzSimple::PoleData("filtr_{$i}[do]", isset($_SESSION['Filtry'][$this->Filtry[$i]['nazwa']]['do']) ? $_SESSION['Filtry'][$this->Filtry[$i]['nazwa']]['do'] : '', null, "style='".($this->Filtry[$i]['opis'] != false ? "margin-top: 12px;" : "")."width:100px;'");
                    }
                if(isset($this->Filtry[$i]['search'])){
                    FormularzSimple::PoleSubmit("szukaj", "Szukaj", "class='search-button' style='margin-left: 5px;'");
                    FormularzSimple::PoleSubmit("wyczysc", "Wyczyść", "class='search-button' style='margin-left: 5px;'");
                    echo "</nobr>";
                }
            if(isset($this->Filtry[$i]['typ']) && $this->Filtry[$i]['typ'] != 'hidden'){
            ?></div><?php
            }
            if(isset($this->Filtry[$i]['clear']) && $this->Filtry[$i]['clear']){
                ?><div style="clear: both;"></div><?php
            }
        }
        FormularzSimple::FormEnd();
    }
?>