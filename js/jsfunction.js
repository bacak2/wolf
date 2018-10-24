function ValueChange(idPola,newValue){
	document.getElementById(idPola).value = newValue;
	document.formularz.submit();
}

function ValueChangeNoSubmit(idPola,newValue){
	document.getElementById(idPola).value = newValue;
}

function NewWindow(mypage,myname,w,h,scroll){
	LeftPosition = (screen.width - w)/2;
	TopPosition = (screen.height - h)/2;
	settings = 'height='+h+',width='+w+',top='+TopPosition+',left='+LeftPosition+',scrollbars='+scroll+',resizable'
	window.open(mypage,myname,settings);
}

function Close(){
	window.close();
}

function ChangeAction(Form, Pole, Value){
	$("#"+Pole).val(Value);
	$("#"+Form).submit();
}

function Popup(page){
	NewWindow(page, "Popup", 800, 600, "yes");
}

function ShowPopup(){
	$('#offtop').css("visibility", "visible");
	$('#popup_bg').css("visibility", "visible");
	$('#popup').css("visibility", "visible");
}

function ClosePopup(){
	$('#offtop').css("visibility", "hidden");
	$('#popup_bg').css("visibility", "hidden");
	$('#popup').css("visibility", "hidden");
}

function AutomaticClose(ReturnObj){
	$(ReturnObj).css('background-image', '');
	setTimeout(KomunikatyClose, 5000);
}

function KomunikatyClose(){
	$('#Komunikaty').css('display','none');
	$('#Komunikaty2').css('display','none');
}

function SubmitForm(){
    
}

var url_base = '';
var url_fullPath = '';

function get_html(setting){
	var params = '';
	var appers = '';

    $.each(setting['params'], function(key, value) {
        params = params + appers + key + '=' + value;
        appers = '&';
    });
    ajax_action(setting, params)
    
}

function save_form(setting){
    var params = '';
    params = setting['params'];
    ajax_action(setting, params)
}

function ajax_action(setting, params){
   $.ajax({ type: setting['type'],
             url: url_base + setting['action'],
             data: params,
             success: function(html)
             {
                if(setting['return_object_id'])
                {
                    $(setting['return_object_id']).html(html);
                }
                if(setting['do_after']){
                    eval(setting['do_after']);
                }
             }
          })
}

function PrzeladujForm(){
	document.formularz.action = '';
	document.formularz.target = '';
	ValueChange("OpcjaFormularza", "przeladuj");
}

function ShowLoading(){
    $("#loading").html("<img src='images/ajax-loader.gif' />");
    $("#loading").css("display", "");
}

function CloseLoading(){
    $("#loading").css("display", "none");
}

function get_popup_content(setting){
	var params = '';
	var appers = '';

    $.each(setting['params'], function(key, value) {
        params = params + appers + key + '=' + value;
        appers = '&';
    });

    $.ajax({ type: setting['type'],
             url: url_base + setting['action'],
             data: params,
             success: function(html)
             {
             	var width = (setting['width'] ? setting['width'] : 700);
               $('#popup').html(html);
               $("#popup").css('background-color', '#FFF');
				$("#popup").css('width', width+'px');
				pozx = (screen.width/2) - (width/2);
				pozy = $('body').scrollTop()+50;
				ShowPopup(pozx,pozy);
             }
          })
}

function get_popup_content_post(setting){
	var params = '';
	var appers = '';
    params = setting['params'];
    $.ajax({ type: setting['type'],
             url: url_base + setting['action'],
             data: params,
             success: function(html)
             {
               var width = (setting['width'] ? setting['width'] : 700);
               $('#popup').html(html);
               $("#popup").css('background-color', '#FFF');
				$("#popup").css('width', width+'px');
				pozx = (screen.width/2) - (width/2);
				pozy = $('body').scrollTop()+50;
				ShowPopup(pozx,pozy);
             }
          })
}

function get_content(setting){
	var params = '';
	var appers = '';

    $.each(setting['params'], function(key, value) {
        params = params + appers + key + '=' + value;
        appers = '&';
    });

    $.ajax({ type: setting['type'],
             url: url_base + setting['action'],
             data: params,
             success: function(html)
             {
                if(setting['return_object_id'])
                {
                    $(setting['return_object_id']).html(html);
                }
                if(setting['after_get_content']){
                    eval(setting['after_get_content']);
                }
             }
          })
}

function get_value(setting){
   var params = '';
	var appers = '';

    $.each(setting['params'], function(key, value) {
        params = params + appers + key + '=' + value;
        appers = '&';
    });

    $.ajax({ type: setting['type'],
             url: url_base + setting['action'],
             data: params,
             success: function(html)
             {
                if(setting['return_object_id'])
                {
                    $(setting['return_object_id']).val(html);
                }
                ClosePopup();
             }
          })
}

function save_and_add_to_select(setting){
    var params = '';
    $("#popup").html("<img src='images/ajax-loader-big.gif' />");
    $("#popup").css("width", "auto");
    params = setting['params'];
    $.ajax({ type: setting['type'],
             url: url_base + setting['action'],
             data: params,
             success: function(html)
             	{
                	dane = html.split("::");
                	if(dane[0] == "add"){
                            var select = $("#"+setting['add_select_id']);
                            AddOptionToSelect(select, dane[1], dane[2], true);
                            if(setting['function_after']){
                                eval(setting['function_after']);
                            }
                            ClosePopup();
                	}else{
               			$('#popup').html(html);
               			$("#popup").css('background-color', '#FFF');
                                $("#popup").css('width', '900px');
                                pozx = (screen.width/2) - (900/2);
                                pozy = $('body').scrollTop()+50;
                                ShowPopup(pozx,pozy);
                	}
                }
          })
}

function AddOptionToSelect(select, val, text, selected){
    selectOptions = select.attr('options');
    selectOptions[selectOptions.length] = new Option(text, val);
    if(selected){
        select.val(val);
    }
}

function get_select_options(setting){
    var params = '';
    $("#popup").html("<img src='images/ajax-loader-big.gif' />");
    $("#popup").css("width", "auto");
    params = setting['params'];
    $.ajax({ type: setting['type'],
             url: url_base + setting['action'],
             data: params,
             success: function(html)
             	{
                	dane = html.split("-$-");
                        var select = $("#"+setting['select_id']);
                        $("#"+setting['select_id']+" option").remove();
                        $.each(dane, function(index, value){
                            valse = value.split("::");
                            AddOptionToSelect(select, valse[0], valse[1], false);
                        });
                }
          })
}

function add_row(setting){
	var params = '';
	var appers = '';

    $.each(setting['params'], function(key, value) {
        params = params + appers + key + '=' + value;
        appers = '&';
    });

    add_row_method(params, setting);
}

function save_form_and_add_row(setting){
    params = setting['params'];
    add_row_method(params, setting);
}

function add_row_method(params, setting){
    $.ajax({ type: setting['type'],
             url: url_base + setting['action'],
             data: params,
             success: function(html)
             {
                CloseLoading();
                if(setting['return_object_id'])
                {
                    $(setting['return_object_id']+" tr:last").after(html);
                }
                if(setting['after_get_content']){
                    eval(setting['after_get_content']);
                }
             }
          })
}

function SessionTime(){
   get_content({'params': {},
                'type'  : 'POST',
                'action': 'js/session-time.php',
                'return_type' : 'html',
                'return_object_id' : "#session_time"
        });
}

function ChangeSelectToInput(name){
    dodaj = $("#"+name+"-select").val();
    if(dodaj == "last"){
        $("#"+name+"-select-box").css('display', 'none');
        $("#"+name+"-input").css('display', 'block');
    }
}

function ChangeInputToSelect(name){
    $("#"+name+"-select").val("0");
    $("#"+name+"-select-box").css('display', 'block');
    $("#"+name+"-input").css('display', 'none');
}

function loadingContainer(div_task)
{
        var offset = div_task.offset();
        top_corner = offset.top+20;
        left_corner = offset.left+50;
	$("#div_ajax").css("top", top_corner+'px');
        $("#div_ajax").css("left", left_corner+'px');
        $("#div_ajax").html('<div class="in_container" id="loading">loading...</div>');
        $("#div_ajax").css("display", 'block');
}

function DoKoszyka(part_id, basket_id){
    if(basket_id == 0){
        var res = $.ajax({
                url: "include/classes/ajax/check-basket.php",
                data: {},
                async: false
        }).responseText;
        basket_id = res;
    }
    if(basket_id > 0 || window.confirm("Nie wybrano zamówienia w koszyku. Czy chcesz utworzyć nowe zamówienie i dodać do niego wybraną część?")){
        $.ajax({ type: "POST",
             url: 'include/classes/ajax/add-to-basket.php',
             data: {basket_id : basket_id, part_id : part_id},
             success: function(html)
             {
                create("default", { title: html});
             }
          })
    }
}

function kategorie_show(kategoria){
    $("#select_"+kategoria).show();
}
function kategorie_hide(kategoria){
    $("#select_"+kategoria).hide();
}

function kategorie_save_hide(kategoria){
     $('input#'+kategoria).val( $('input#temp_'+kategoria).val() );
     show_text = new Array();
     var count = 0;
     $("#select_"+kategoria+" select").each(function(){
         if(($(this).css('display') == "inline" || $(this).css('display') == "inline-block") && $(this).val() > 0){
            show_text[count] = $("option:selected", this).text();
            count++;
         }
     })
     if(count > 0){
         var zmien = count-1;
         show_text[zmien] = "<strong>"+show_text[zmien]+"</strong>";
     }
     $('div.'+kategoria+'_name').html(show_text.join(" » "));
     $("#select_"+kategoria).hide();
}

function kategoria(kategoria, kategoria_id){
    kat_name = $("div."+kategoria).attr('name');
    kat_title = $("select."+kategoria).attr('title');
    kat_id = $("select.select_"+kategoria_id).val();
    kat_name = parseInt(kat_name,10)+1;
    $('select.'+kat_title+'_'+kat_name).val(0);
    $('.'+kat_title+'_'+kat_name).hide();
    if(kat_id > 0){
        $('div.'+kat_title+'_'+kat_name).show();
        $('select.select_'+kat_title+'_'+kat_id).show();
    }
    $('input#temp_'+kat_title).val(kat_id);
    $('input#temp_title_'+kat_title).val($("select.select_"+kategoria_id+" option:selected").text());
    $(".div-categories").hide();
    $(".div-categories select").each(function(){
        if($(this).css('display') == "inline" || $(this).css('display') == "inline-block"){
            $(this).parent().parent().show();
        }
    })
   
}

function Search(){
    $("#filters").submit();
}

function create( template, vars, opts ){
    return $container.notify("create", template, vars, opts);
}

function PulpitButton(location){
    window.location = location;
}




function RemoveBox( id, status, type){
    var res = $.ajax({
            url: "include/classes/ajax/remove-box-from-pulpit.php",
            type: "POST",
            data: {id : id, status: status, type : type},
            async: false
    }).responseText;
    if( res == 'true' ){
	var locationObj = window.location;
	window.location = locationObj;
    }
 }
    $(function(){
        $container = $("#container").notify();
      
    });
    
$(document).ready(function(){


    $(".showek").mouseover(function(){
        $(this).attr("back-color", $(this).css("background-color"));
        $(this).css("background-color", "#FFFFD0");
    });
    
    $(".showek").mouseout(function(){
        $(this).css("background-color", $(this).attr("back-color"));
    });
    
    $(".showek").click(function(event){
        var clicked_target = event.target.nodeName;
        if(clicked_target != "IMG" && clicked_target != "A"){
            var hrefik = $(this).find("a[rel='information']:first").attr("href");
            if(hrefik != undefined){
                window.location.href = hrefik;
            }
        }
    });
    
    $(".wloskie").on('change',function(){
        var myValues = $(".wloskie").chosen().val();
        if(myValues){
            $(".polskie").prop('disabled', true).trigger("chosen:updated");
        }else{
            $(".polskie").prop('disabled', false).trigger("chosen:updated");
        }

    });
    
    $(".polskie").on('change', function(){
        var myValues = $(".polskie").chosen().val();
        if(myValues){
            $(".wloskie").prop('disabled', true).trigger("chosen:updated");
        }else{
            $(".wloskie").prop('disabled', false).trigger("chosen:updated");
        }
    });
    if( $(".wloskie").is() ){
        var myValues = $(".wloskie").chosen().val();
        if(myValues){
            $(".polskie").prop('disabled', true).trigger("chosen:updated");
        }else{
            var myValues = $(".polskie").chosen().val();            
            if(myValues){
                $(".wloskie").prop('disabled', true).trigger("chosen:updated");
            }
        }
    };
    
        var datapic_opt = {
            changeMonth: true,
            changeYear: true,
            buttonImage: 'images/calendar_add.gif',
            showOn: "button"
        };
        $(".datapicker").datepicker(datapic_opt);
        $(".datapicker_today").each(function(){
            if($(this).attr('min')){
                datapic_opt['maxDate'] = parseInt($(this).attr('max'))*(-1);
                datapic_opt['minDate'] = parseInt($(this).attr('min'))*(-1);
            }else{
                datapic_opt['maxDate'] = parseInt($(this).attr('max'))*(-1);
            }
            $(this).datepicker(datapic_opt);
        });
        
        
    if( $(".chzn-select").length > 0 )
        $(".chzn-select").chosen({no_results_text: "Brak pasuj\u0105cych wyników", default_text: "Wybierz"});

   // Notice the use of the each() method to acquire access to each elements attributes
   $('.tooltip_trigger').each(function(){
      div_name = $(this).attr('tooltip');
      $(this).qtip({
         content: $(div_name).html(),
         style: 'light',
         position: {
              corner: {
                 tooltip: "rightTop", // Use the corner...
                 target: "leftBottom" // ...and opposite corner
              },
              adjust: {
                  screen: true
              }
         },
         hide: {
            fixed: true, // Make it fixed so it can be hovered over
            delay: 300            
         }
      });
   });

   $('.simple_tooltip').each(function(){
      $(this).qtip({
         content: $(this).attr('comments'),
         style: {name : 'dark'},
         position: {
              corner: {
                 tooltip: "centerBottom", // Use the corner...
                 target: "centerTop" // ...and opposite corner
              },
              adjust: {
                  screen: true
              }
         },
         hide: {
            fixed: true // Make it fixed so it can be hovered over
         }
      });
   });

  $(".delTR").on("click", function(){
      var rem_id = $(this).attr("remove-id");
      $("#tr_"+rem_id).remove();
  });
   
})

