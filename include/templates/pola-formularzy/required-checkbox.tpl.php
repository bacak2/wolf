<div style="margin: 8px 0px;">
<div><?php echo $this->Pola[$Nazwa]['opis']; ?></div>
<?php 
foreach($this->Pola[$Nazwa]['opcje']['elementy'] as $KatName=>$KatParams){ ?>
<div class="super-form-span1">
    <div class="super-form-label"><?php echo $KatName;?></div>
    <div class="clear"></div>
  <?php
  foreach($KatParams as $ParamID=>$ParamsVal){ ?>
  <div class="super-form-span2">
    <div class="super-form-div">
      <div style="width:75%;float:left;line-height:21px;">
	<small><?php echo $ParamsVal;?></small>
      </div>
      <div class="super-form-span4">
	<div class="super-form-field<?php echo ($this->FormWyswietl == 'WyswietlDane' ? '_dane' : ''); ?>">
	<?php
        if($this->FormWyswietl == 'WyswietlDane' ){
            echo isset($Wartosc[$ParamID]) ? $Wartosc[$ParamID] : '&nbsp;';
        }else{
	  FormularzSimple::PoleInputText("{$this->MapaNazw[$Nazwa]}[{$ParamID}]", isset($Wartosc[$ParamID]) ? $Wartosc[$ParamID] : '', (isset($this->Pola[$Nazwa]['opcje']['wymagane-puste']) && in_array($ParamID, $this->Pola[$Nazwa]['opcje']['wymagane-puste']) ? " style='background-color: #FFC0C0;'" : ""));
        }
	?>
	</div>
      </div>
    </div>
  </div>
  <?php } ?>
  </div>
<?php } ?>
<div class="clear"></div>
</div>