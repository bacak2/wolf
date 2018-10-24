<?php
if($this->FormWyswietl != 'WyswietlDane' && isset($this->Pola[$Nazwa]['opcje']['script'])){
    foreach($this->Pola[$Nazwa]['opcje']['script'] as $script){
    ?>
    <script type='text/javascript' src='js/<?php echo $script; ?>'></script>
<?php 
    }
}

  $attr = $stan = '';
  if(isset($this->Pola[$Nazwa]['opcje']['admin']) && $this->Pola[$Nazwa]['opcje']['admin'] == true ){
    $stan = '';
  }else{
    if(isset($this->Pola[$Nazwa]['opcje']['stan']) && is_array($this->Pola[$Nazwa]['opcje']['stan'])){
      foreach($this->Pola[$Nazwa]['opcje']['stan'] as $KeyStan=>$ValStan){
	$stan .= $KeyStan.'="'.$ValStan.'" ';
      }
    }
  }
  if(isset($this->Pola[$Nazwa]['opcje']['atrybuty']) && is_array($this->Pola[$Nazwa]['opcje']['atrybuty']) ){
    foreach($this->Pola[$Nazwa]['opcje']['atrybuty'] as $KeyStan=>$ValStan){
	$attr .= $KeyStan.'="'.$ValStan.'" ';
      }
  }
  
  foreach($this->Pola[$Nazwa]['opcje']['pola'] as $Key => $Label){
    ?>
      <div class="super-form-span1">
	<div class="super-form-span4" style="height:27px;">
	  <small><?php 
            if(is_array($Label)){
                   echo $Label['title'];
                }else{
                    echo $Label;
                }
            ?></small>
	</div>
	<div style="width:75%;float:left;line-height:21px;">
	  <?php if($Label != '&nbsp;' || (is_array($Label) && $Label['title'] != '&nbsp;')){ ?>
	    <div class="super-form-field">
	  <?php
            if($this->FormWyswietl == 'WyswietlDane'){
                if(is_array($Label) && isset($Label['elementy'])){
                   echo $Label['elementy'][$Wartosc[$Key]];
                }else{
                    echo $Wartosc[$Key];
                }
            }else{
                if(is_array($Label) && isset($Label['elementy'])){
                   FormularzSimple::PoleSelect("{$this->MapaNazw[$Nazwa]}[$Key]}", $Label['elementy'], $Wartosc[$Key], $attr.$stan.' class="chzn-select" '.(isset($this->Pola[$Nazwa]['opcje']['wymagane-puste']) && in_array($Key, $this->Pola[$Nazwa]['opcje']['wymagane-puste']) ? " style='background-color: #FFC0C0;'" : ""), $Key);
                }else{
                    FormularzSimple::PoleInputText("{$this->MapaNazw[$Nazwa]}[$Key]}", $Wartosc[$Key], $attr.$stan.(isset($this->Pola[$Nazwa]['opcje']['wymagane-puste']) && in_array($Key, $this->Pola[$Nazwa]['opcje']['wymagane-puste']) ? " style='background-color: #FFC0C0;'" : ""), $Key);
                }
	      
            }
	  ?>
	    </div>
	  <?php
	    }else{ ?>
	      <div style="height:26px;">&nbsp;</div>
	    <?php 
	    }
	  ?>
	</div>
      </div>
      <div class="clear"></div>
    <?php
  }
?>