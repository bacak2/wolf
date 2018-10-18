<h1>Ostatnio aktywni</h1>
<ul>
    <?php
        foreach($History as $Companies){
            ?>
            <li>
                <a href="?modul=<?php echo $this->Parametr; ?>&akcja=szczegoly&id=<?php echo $Companies[$this->PoleID]; ?>"><?php echo $Companies['imie_nazwisko']; ?></a>
                <br />
                <?php echo UsefullTime::TimeAgo($Companies['last_sign_in_at']); ?>
            </li><?php
        }
    ?>
</ul>