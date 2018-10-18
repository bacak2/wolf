<?php
    $NumerySeryjne = new ModulNumerySeryjne($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
    $typy = $NumerySeryjne->GetItemTypes();
?>
<table class="lista" style="width: 90%; margin: 0 auto;">
        <tr>
            <th>Lp</th>
            <th>Interwencje</th>
            <th>Data zdarzenia</th>
            <th>Opis</th>
        </tr>
        <?php
            $idx = 1;
            foreach($Items as $Item){
        ?>
        <tr>
            <td><?php echo $idx; ?></td>
            <td><?php echo $typy[$Item['type']].($Item['type'] == "SetupItem" && $Item['no_warranty'] == 1 ? " bez gwarancji" : ""); ?></td>
            <td><?php echo UsefullTime::PolskiDzien(date("D, d.m.Y", strtotime($Item['happened_at']))); ?></td>
            <td><?php echo $Item['description']; ?></td>
        </tr>
        <?php
            $idx++;
            }
        ?>
    </table>
