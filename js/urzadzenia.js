$(".DelRequiredCheckbox").on("click", function(){
    var rem_id = $(this).attr("remove-id");
    $("#tr_"+rem_id).remove();
});

