$(document).ready(function(){
	$("table.lista tr").on("click", function(e){
		if($(this).hasClass("picked")){
			$(this).css("background-color", $(this).attr('default-color'));
			$(this).removeClass("picked");
			$("#basket-picked-order").val("");
		}else{
			UnsetAllRows();
			$(this).addClass("picked");
			$(this).attr('default-color', $(this).css("background-color"));
			$(this).css("background-color", "#FFFFAA");
			var order_id = $("td span.idk", this).html();
			$("#basket-picked-order").val(order_id);
		}
	});

	function UnsetAllRows(){
		$("table.lista tr").each(function(){
			$(this).css("background-color", $(this).attr('default-color'));
			$(this).removeClass("picked");
		})
	}
});