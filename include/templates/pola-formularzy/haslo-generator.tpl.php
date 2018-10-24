<script type="text/javascript" src="js/generator-hasla.js"></script>
<div class="super-form-span4">
    <div class="super-form-label">
        <label for="<?php echo $this->MapaNazw[$Nazwa]; ?>">Hasło</label>
    </div><div class="clear"></div>
    <div class="super-form-div">
        <div class="super-form-field">
            <?php FormularzSimple::PolePassword("{$this->MapaNazw[$Nazwa]}", $Wartosc, "id='haslo' class='pass'$AtrybutyDodatkowe") ?>
        </div>
    </div>        
</div>
<div class="super-form-span4">
    <div class="super-form-label">
        <label for="<?php echo $this->MapaNazw[$Nazwa]; ?>">Potwierdzenie hasła</label>
    </div><div class="clear"></div>
    <div class="super-form-div">
        <div class="super-form-field">
            <?php FormularzSimple::PolePassword("reply_pass", (isset($_POST['reply_pass']) ? $_POST['reply_pass'] : ""), "id='reply_haslo' class='pass'$AtrybutyDodatkowe") ?>
        </div>
    </div>
</div>
<div class="super-form-span4">
    <div class="super-form-fild" style="width: 100px;margin: 0 auto 8px auto;">
        <a href="javascript:genrePass(10,4)" class="like-button">Generuj hasło</a>
    </div>
</div>
<div class="super-form-span4">
    <div class="super-form-label">
        <label>Wygenerowane hasło</label>
    </div><div class="clear"></div>
    <div class="super-form-div">    
        <div class="super-form-field">
            <div id="wygenerowane-haslo" style="margin: 4px 2px;">&nbsp;</div>
        </div>
    </div>
</div>
<div class="clear"></div>

            
