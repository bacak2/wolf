<?php
if (isset($ID)){
    echo "<div style='width: 50%; float: right; text-align: right; display: inline;'>";
    if ($IDPoprzedniego = $this->IDPoprzedniego($ID)) {
        echo("<a href='?modul=$this->Parametr&amp;akcja=$this->WykonywanaAkcja&amp;id=$IDPoprzedniego'><img src='images/buttons/prev-button.png' title='Poprzedni' alt='Poprzedni' onmouseover='this.src=\"images/buttons/prev-button-hover.png\"' onmouseout='this.src=\"images/buttons/prev-button.png\"'></a>");
    }
    else {
        echo("<img src='images/buttons/prev-button-grey.png' title='Poprzedni' alt='Poprzedni'>");
    }
    echo("&nbsp;&nbsp;<a href='$this->LinkPowrotu'><img src='images/buttons/back-button.png' title='Powrót do listy' alt='Powrót do listy' onmouseover='this.src=\"images/buttons/back-button-hover.png\"' onmouseout='this.src=\"images/buttons/back-button.png\"'></a>&nbsp;&nbsp;");
    if ($IDNastepnego = $this->IDNastepnego($ID)) {
        echo("<a href='?modul=$this->Parametr&amp;akcja=$this->WykonywanaAkcja&amp;id=$IDNastepnego'><img src='images/buttons/next-button.png' title='Następny' alt='Następny' onmouseover='this.src=\"images/buttons/next-button-hover.png\"' onmouseout='this.src=\"images/buttons/next-button.png\"'></a>");
    }
    else {
        echo("<img src='images/buttons/next-button-grey.png' title='Następny' alt='Następny'>");
    }
    echo "</div>";
}
?>