<?php if( isset($PrzypomnienieOSzkoleniu) && !empty($PrzypomnienieOSzkoleniu) ){ ?>
<div style="width: 90%; margin: 20px 40px;">
<?php
    foreach($PrzypomnienieOSzkoleniu as $Komunikat)
            Usefull::$Komunikat['type']($Komunikat['text'], (isset($Komunikat['bold']) ? $Komunikat['bold'] : false) ); 
    ?>
</div>
<?php
}
?>
<table border='0' cellspacing='0' cellpadding='12' style="width: 90%; margin: 20px 40px;">
    <tr>
        <td style="width:35%; vertical-align: top;">
            <?php if($Role != 'administrator'){ ?>
            <div id="co_chcesz_zrobic">
                <div class="co_chcesz_zrobic">co chcesz zrobić</div>
                <?php if($Role != 'top'){ ?>
                    <a class="like-button" href="?modul=items&akcja=dodaj_element<?php echo $ZlId;?>"><div>pierwsze<br />uruchomienie</div></a>
                    <?php if($Role != 'serwisant'){ ?>
                            <a class="like-button" href="?modul=items&akcja=dodaj_element<?php echo $ZlId;?>"><div>naprawa<br />gwarancyjna</div></a>
                    <?php } ?>		
                <?php } ?>
                    <a class="like-button" href="?modul=czesci"><div>kupić<br />części</div></a>
            <div style="clear: both;"></div>
            </div>
            <?php }else{ ?>
                <b>Wybierz dział:</b><br /><br />
                <table border='0' cellspacing='0' cellpadding='8'>
                        <?php
                                foreach($this->Modules as $parametr => $nazwa){
                        ?>
                        <tr class="heand menu_hover" onmouseover="" onclick="javascript:PulpitButton('<?php echo("?modul=$parametr"); ?>')">
                                <td width='35'><img src='images/button_in.gif' alt='' height='23' width='23' border='0'></td>
                                <td><a href='<?php echo("?modul=$parametr"); ?>' class='se'><?php echo $nazwa; ?></a></td>
<!--                    <td align='right' valign='middle' width='43'><img src='images/button_out.gif' alt='' height='28' width='46' border='0'></td> -->
                        </tr>
                        <?php
                                }
                        ?>
                        <tr>
                                <td width='35'><img src='images/button_in.gif' alt='' height='23' width='23' border='0'></td>
                                <td><a href='public/Pomoc.pdf' class='se' target="_blank">Instrukcja obsługi</a></td>
                        </tr>
                </table>
            <?php } ?>
        </td>
        <td style="width: 32%; vertical-align: top;">
            <?php if($this->Uzytkownik->FirmaTop($this->UserID)) { ?>
                    <?php
                        if($Opoznione != false){
                    ?>
                    <b>Opóźnione zamówienia: (<?php echo ($Opoznione == false ? "0" : count($Opoznione)) ?>)</b><br /><br />
                    <?php
                            foreach($Opoznione as $Paczka){
                    ?>
                        <div class="komunikat_pulpit <?php echo ($Paczka['role'] == 1 ? 'top' : '')?>">
                            <div  class="f_right pulpit_remove_box" onClick="RemoveBox(<?php echo $Paczka['id']; ?>, <?php echo $Paczka['status']; ?>,'zamowienia')">X</div>
                            <a href="?modul=zamowieniauzytkownicy&akcja=szczegoly&id=<?php echo $Paczka['id']; ?>">[<?php echo $Paczka['id']; ?>] <?php echo $Paczka['title']; ?></a><br />
                            <?php echo $Paczka['simplename']; ?><br />
                            <span style="color: #FF0000; font-weight: bold;">Wysłano: <?php echo UsefullTime::TimeAgo($Paczka['sent_at']); ?></span><br />
                            <span style="color: #999999; font-size: 10px;">Realizuje: <?php echo $Paczka['top_name']; ?></span>
                        </div>
                    <?php
                            }
                            ?><br /><?php
                        }
                    ?>
                    <?php
                        if($Oczekujace != false){
                    ?>
                    <b>Najnowsze oczekujące zamówienia: (<?php echo ($Oczekujace == false ? "0" : count($Oczekujace)) ?>)</b><br /><br />
                    <?php
                            foreach($Oczekujace as $Paczka){
                    ?>
                        <div class="komunikat_pulpit <?php echo ($Paczka['role'] == 1 ? 'top' : '')?>">
                            <div  class="f_right pulpit_remove_box" onClick="RemoveBox(<?php echo $Paczka['id']; ?>, <?php echo $Paczka['status']; ?>,'zamowienia')">X</div>
                            <a href="?modul=zamowieniauzytkownicy&akcja=szczegoly&id=<?php echo $Paczka['id']; ?>">[<?php echo $Paczka['id']; ?>] <?php echo $Paczka['title']; ?></a><br />
                            <?php echo $Paczka['simplename']; ?><br />
                            <span style="color: #FF0000; font-weight: bold;">Wysłano: <?php echo UsefullTime::TimeAgo($Paczka['sent_at']); ?></span><br />
                            <span style="color: #999999; font-size: 10px;">Realizuje: <?php echo $Paczka['top_name']; ?></span>
                        </div>
                    <?php
                            }
                        }
                    ?>
                    <?php
                        if($Zaakceptowane != false){
                    ?>
                    <b>Najnowsze zrealizowane zamówienia: (<?php echo ($Zaakceptowane == false ? "0" : count($Zaakceptowane)) ?>)</b><br /><br />
                    <?php
                            foreach($Zaakceptowane as $Paczka){
                    ?>
                        <div class="komunikat_pulpit accepted">
                            <div  class="f_right pulpit_remove_box" onClick="RemoveBox(<?php echo $Paczka['id']; ?>, <?php echo $Paczka['status']; ?>,'zamowienia')">X</div>
                            <a href="?modul=zamowieniauzytkownicy&akcja=szczegoly&id=<?php echo $Paczka['id']; ?>">[<?php echo $Paczka['id']; ?>] <?php echo $Paczka['title']; ?></a><br />
                            <?php echo $Paczka['simplename']; ?><br />
                            <span style="color: #FF0000; font-weight: bold;">Zrealizowano: <?php echo UsefullTime::TimeAgo($Paczka['finalized_at']); ?></span><br />
                            <span style="color: #999999; font-size: 10px;">Realizuje: <?php echo $Paczka['top_name']; ?></span>
                        </div>
                    <?php
                            }
                        }
                    ?>
            <?php }else{ ?>
                <?php
                    if($Opoznione != false){
                ?>
                <b>Opóźnione zlecenia: (<?php echo ($Opoznione == false ? "0" : count($Opoznione)) ?>)</b><br /><br />
                <?php
                    foreach($Opoznione as $Paczka){
                ?>
                    <div class="komunikat_pulpit">
                         <div  class="f_right pulpit_remove_box" onClick="RemoveBox(<?php echo $Paczka['id']; ?>, <?php echo $Paczka['status']; ?>,'zlecenia')">X</div>
                        <a href="?modul=zlecenia&akcja=szczegoly&id=<?php echo $Paczka['id']; ?>">[<?php echo $Paczka['id']; ?>] <?php echo $Paczka['title']; ?></a><br />
                        <?php echo $Paczka['simplename']; ?><br />
                        <span style="color: #FF0000; font-weight: bold;">Wysłano: <?php echo UsefullTime::TimeAgo($Paczka['sent_at']); ?></span>
                    </div>
                <?php
                        }
                        ?><br /><?php
                    }
                ?>
                <?php
                    if($Oczekujace != false){
                ?>
                <b>Najnowsze oczekujące zlecenia: (<?php echo ($Oczekujace == false ? "0" : count($Oczekujace)) ?>)</b><br /><br />
                <?php
                        foreach($Oczekujace as $Paczka){
                ?>
                    <div class="komunikat_pulpit waiting">
                        <div  class="f_right pulpit_remove_box" onClick="RemoveBox(<?php echo $Paczka['id']; ?>, <?php echo $Paczka['status']; ?>,'zlecenia')">X</div>
                        <a href="?modul=zlecenia&akcja=szczegoly&id=<?php echo $Paczka['id']; ?>">[<?php echo $Paczka['id']; ?>] <?php echo $Paczka['title']; ?></a><br />
                        <?php echo $Paczka['simplename']; ?><br />
                        <span style="color: #FF0000; font-weight: bold;">Wysłano: <?php echo UsefullTime::TimeAgo($Paczka['sent_at']); ?></span>
                    </div>
                <?php
                        }
                        ?><br /><?php
                    }
                ?>
                <?php
                    if($Odrzucone != false){
                ?>
                <b>Ostatnio odrzucone zlecenia: (<?php echo ($Odrzucone == false ? "0" : count($Odrzucone)) ?>)</b><br /><br />
                <?php
                        foreach($Odrzucone as $Paczka){
                ?>
                    <div class="komunikat_pulpit reject">
                        <div  class="f_right pulpit_remove_box" onClick="RemoveBox(<?php echo $Paczka['id']; ?>, <?php echo $Paczka['status']; ?>,'zlecenia')">X</div>                    
                        <a href="?modul=zlecenia&akcja=szczegoly&id=<?php echo $Paczka['id']; ?>">[<?php echo $Paczka['id']; ?>] <?php echo $Paczka['title']; ?></a><br />
                        <?php echo $Paczka['simplename']; ?><br />
                        <span style="color: #e20707; font-weight: bold;">Wysłano: <?php echo UsefullTime::TimeAgo($Paczka['sent_at']); ?></span> 
                    </div>
                <?php
                        }
                        ?><br /><?php
                    }
                ?>
                <?php
                    if($Zaakceptowane != false){
                ?>
                <b>Ostatnio zaakceptowane zlecenia: (<?php echo ($Zaakceptowane == false ? "0" : count($Zaakceptowane)) ?>)</b><br /><br />
                <?php
                        foreach($Zaakceptowane as $Paczka){
                ?>
                                <div class="komunikat_pulpit accepted">
                                    <div  class="f_right pulpit_remove_box" onClick="RemoveBox(<?php echo $Paczka['id']; ?>, <?php echo $Paczka['status']; ?>,'zlecenia')">X</div>
                                    <a href="?modul=zlecenia&akcja=szczegoly&id=<?php echo $Paczka['id']; ?>">[<?php echo $Paczka['id']; ?>] <?php echo $Paczka['title']; ?></a><br />
                                    <?php echo $Paczka['simplename']; ?><br />
                                    <span style="color: #FF0000; font-weight: bold;">Wysłano: <?php echo UsefullTime::TimeAgo($Paczka['sent_at']); ?></span>
                                </div>
                <?php
                        }
                    }
                ?>
                <?php } ?>
        </td>
        <td style="width: 32%; vertical-align: top;">
            <?php
                if($Komentarze){
            ?>
            <b>Ostatnio komentowane elementy: (<?php echo ($Komentarze == false ? "0" : count($Komentarze)) ?>)</b><br /><br />
            <?php
                    foreach($Komentarze as $Paczka){
            ?>
                <div class="komunikat_pulpit pulpit_2">
                    <div  class="f_right pulpit_remove_box" onClick="RemoveBox(<?php echo $Paczka['komentarz_id']; ?>, -1, 'komentarze')">X</div> 
                    <a href="?modul=items&akcja=szczegoly&id=<?php echo $Paczka['item_id']; ?>">[<?php echo $Paczka['id']; ?>] <?php echo $Paczka['title']; ?></a><br />
                    <?php echo $Paczka['simplename']; ?> | <?php echo $Paczka['client_name']; ?><br />
                    <span style="color: #FF0000; font-weight: bold;">Wysłano: <?php echo UsefullTime::PolskiDzien(date("D, d.m.Y H:i:s", strtotime($Paczka['sent_at'])));; ?></span>
                </div>
            <?php
                    }
                    ?><br /><?php
                }
            ?>
            <?php
                if($OpoznioneZamowienia != false){
            ?>
            <b>Opóźnione zamówienia: (<?php echo ($OpoznioneZamowienia == false ? "0" : count($OpoznioneZamowienia)) ?>)</b><br /><br />
            <?php
                    foreach($OpoznioneZamowienia as $Paczka){
            ?>
                <div class="komunikat_pulpit <?php echo ($Paczka['role'] == 1 ? 'top' : '')?>">
                    <div  class="f_right pulpit_remove_box" onClick="RemoveBox(<?php echo $Paczka['id']; ?>, <?php echo $Paczka['status']; ?>,'zamowienia')">X</div>
                    <a href="?modul=zamowieniauzytkownicy&akcja=szczegoly&id=<?php echo $Paczka['id']; ?>">[<?php echo $Paczka['id']; ?>] <?php echo $Paczka['title']; ?></a><br />
                    <?php echo $Paczka['simplename']; ?><br />
                    <span style="color: #FF0000; font-weight: bold;">Wysłano: <?php echo UsefullTime::TimeAgo($Paczka['sent_at']); ?></span><br />
                    <span style="color: #999999; font-size: 10px;">Realizuje: <?php echo $Paczka['top_name']; ?></span>
                </div>
            <?php
                    }
                    ?><br /><?php
                }
            ?>
            <?php
                if($OczekujaceZamowienia != false){
            ?>
            <b>Najnowsze oczekujące zamówienia: (<?php echo ($OczekujaceZamowienia == false ? "0" : count($OczekujaceZamowienia)) ?>)</b><br /><br />
            <?php
                    foreach($OczekujaceZamowienia as $Paczka){
            ?>
                <div class="komunikat_pulpit <?php echo ($Paczka['role'] == 1 ? 'top' : '')?>">
                    <div  class="f_right pulpit_remove_box" onClick="RemoveBox(<?php echo $Paczka['id']; ?>, <?php echo $Paczka['status']; ?>,'zamowienia')">X</div>
                    <a href="?modul=zamowieniauzytkownicy&akcja=szczegoly&id=<?php echo $Paczka['id']; ?>">[<?php echo $Paczka['id']; ?>] <?php echo $Paczka['title']; ?></a><br />
                    <?php echo $Paczka['simplename']; ?><br />
                    <span style="color: #FF0000; font-weight: bold;">Wysłano: <?php echo UsefullTime::TimeAgo($Paczka['sent_at']); ?></span><br />
                    <span style="color: #999999; font-size: 10px;">Realizuje: <?php echo $Paczka['top_name']; ?></span>
                </div>
            <?php
                    }
                }
            ?>
            <?php
                if($ZrealizowaneZamowienia != false){
            ?>
            <b>Najnowsze zrealizowane zamówienia: (<?php echo ($ZrealizowaneZamowienia == false ? "0" : count($ZrealizowaneZamowienia)) ?>)</b><br /><br />
            <?php
                    foreach($ZrealizowaneZamowienia as $Paczka){
            ?>
                <div class="komunikat_pulpit accepted">
                    <div  class="f_right pulpit_remove_box" onClick="RemoveBox(<?php echo $Paczka['id']; ?>, <?php echo $Paczka['status']; ?>,'zamowienia')">X</div>
                    <a href="?modul=zamowieniauzytkownicy&akcja=szczegoly&id=<?php echo $Paczka['id']; ?>">[<?php echo $Paczka['id']; ?>] <?php echo $Paczka['title']; ?></a><br />
                    <?php echo $Paczka['simplename']; ?><br />
                    <span style="color: #FF0000; font-weight: bold;">Zrealizowano: <?php echo UsefullTime::TimeAgo($Paczka['finalized_at']); ?></span><br />
                    <span style="color: #999999; font-size: 10px;">Realizuje: <?php echo $Paczka['top_name']; ?></span>
                </div>
            <?php
                    }
                }
            ?>
            <?php if($this->Uzytkownik->FirmaTop($this->UserID) || $this->Uzytkownik->IsAdmin() ) { ?>
                    <?php
                        if($WDrodze != false){
                    ?>
                    <b>Zamówienia w dordze: (<?php echo ($WDrodze == false ? "0" : count($WDrodze)) ?>)</b><br /><br />
                    <?php
                            foreach($WDrodze as $Paczka){
                    ?>
                        <div class="komunikat_pulpit en-route">
                            <div  class="f_right pulpit_remove_box" onClick="RemoveBox(<?php echo $Paczka['id']; ?>, <?php echo $Paczka['status']; ?>,'zamowienia')">X</div>
                            <a href="?modul=zamowieniauzytkownicy&akcja=szczegoly&id=<?php echo $Paczka['id']; ?>">[<?php echo $Paczka['id']; ?>] <?php echo $Paczka['title']; ?></a><br />
                            <?php echo $Paczka['simplename']; ?><br />
                            <span style="color: #FF0000; font-weight: bold;">Wysłano: <?php echo UsefullTime::TimeAgo($Paczka['sent_at']); ?></span><br />
                            <span style="color: #999999; font-size: 10px;">Realizuje: <?php echo $Paczka['top_name']; ?></span>
                        </div>
                    <?php
                            }
                            ?><br /><?php
                        }
            }
            ?>
                            
            
        </td>
    </tr>
</table>