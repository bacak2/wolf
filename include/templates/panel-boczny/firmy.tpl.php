<h1>Ostatnie zmiany</h1>
<ul>
    <?php
        foreach($History as $Companies){
            ?>
            <li>
                <a href="?modul=<?php echo $this->Parametr; ?>&akcja=szczegoly&id=<?php echo $Companies[$this->PoleID]; ?>"><?php echo $Companies[$this->PoleNazwy]; ?></a>
                <br />
                <?php echo UsefullTime::TimeAgo($Companies['updated_at']); ?>
            </li><?php
        }
    ?>
</ul>