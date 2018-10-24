<?php if($this->FormWyswietl == 'WyswietlDane'){ ?>
    <?php foreach($this->Pola[$Nazwa]['opcje']['kategorie_czesci'] as $KategoriaCzesciID=>$KategoriaCzesci){
        if( isset($Wartosc[$KategoriaCzesciID]) && file_exists($this->Pola[$Nazwa]['opcje']['KatalogDanych'].$KategoriaCzesciID.'/'.$Wartosc[$KategoriaCzesciID]['arrangement_of_parts_files_name'])
            && is_file($this->Pola[$Nazwa]['opcje']['KatalogDanych'].$KategoriaCzesciID.'/'.$Wartosc[$KategoriaCzesciID]['arrangement_of_parts_files_name'])
        ){ ?>
            <div class="super-form-label">
                <?php echo $KategoriaCzesci; ?>
            </div>
            <div class="super-form-span1">
                    <div class="getfile">
                    <a href="<?php echo $this->Pola[$Nazwa]['opcje']['KatalogDanych'].$KategoriaCzesciID.'/'.$Wartosc[$KategoriaCzesciID]['arrangement_of_parts_files_name']; ?>"
                       target="_blank"><?php echo $Wartosc[$KategoriaCzesciID]['arrangement_of_parts_files_orginal_name']; ?></a>
                    </div>
            </div>
        <?php }
    } ?>
<?php }else{ ?>
    <?php foreach($this->Pola[$Nazwa]['opcje']['kategorie_czesci'] as $KategoriaCzesciID=>$KategoriaCzesci){ ?>
        <div class="super-form-label">
            <?php echo $KategoriaCzesci; ?>
        </div>
        <div class="super-form-span1">
        <?php 
//        var_dump($this->Pola[$Nazwa]['opcje']['KatalogDanych'].$KategoriaCzesciID.'/'.$Wartosc[$KategoriaCzesciID]['arrangement_of_parts_files_name']);
                if(  isset($Wartosc[$KategoriaCzesciID]) && file_exists($this->Pola[$Nazwa]['opcje']['KatalogDanych'].$KategoriaCzesciID.'/'.$Wartosc[$KategoriaCzesciID]['arrangement_of_parts_files_name'])
                        && is_file($this->Pola[$Nazwa]['opcje']['KatalogDanych'].$KategoriaCzesciID.'/'.$Wartosc[$KategoriaCzesciID]['arrangement_of_parts_files_name'])
                        ){ ?>
                    <div class="getfile">
                    <a href="<?php echo $this->Pola[$Nazwa]['opcje']['KatalogDanych'].$KategoriaCzesciID.'/'.$Wartosc[$KategoriaCzesciID]['arrangement_of_parts_files_name']; ?>"
                       target="_blank"><?php echo $Wartosc[$KategoriaCzesciID]['arrangement_of_parts_files_orginal_name']; ?></a>
                    </div>
                    <div class="delfile">
                        <a href='?modul=<?php echo $_GET['modul']; ?>&akcja=<?php echo $_GET['akcja']; ?>&id=<?php echo $_GET['id']; ?>&plik=<?php echo $Wartosc[$KategoriaCzesciID]['arrangement_of_parts_files_hash']; ?>&usun_plik=1&kategoria_czesci=<?php echo $KategoriaCzesciID; ?>' onclick='return confirm("Plik zostanie bezpowrotnie utracony. Kontynuować kasowanie?");'><img src='images/bin_empty.gif' style='display: inline; vertical-align: middle;'> Kasuj plik</a>
                    </div>
                <?php }else{ ?>
                    <div  class='f_left padding_8'>
                        <?php echo FormularzSimple::PoleFile($this->MapaNazw[$Nazwa]."[{$KategoriaCzesciID}]"); ?>
                        <?php echo FormularzSimple::PoleHidden($this->MapaNazw[$Nazwa]."[{$KategoriaCzesciID}][KatID]", $KategoriaCzesciID); ?>
                    </div>
                    <div class='f_left'>
                        <input type='button' value='Dodaj plik' onclick='ValueChange("OpcjaFormularza","dodaj_kategorieczesci");return false;'>
                    </div>
                    <div style='clear:both;'></div>
                <?php }
            ?>
        </div>
    <?php } ?>
<?php } ?>