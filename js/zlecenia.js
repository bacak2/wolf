$(document).ready(function(){
    $('[id^=part_id_]').change(function(){
            var str = $(this).attr('id');
            var pattern = /[0-9]+/g;
            var pattern1 = /\[([^\]]+)\]/;
            var matches = str.match(pattern);
            $('#part_kat_number_'+matches).val($(this).find(":selected").text().match(pattern1)[1]);
            //console.log( '#' + $(this).find(":selected").text().match(pattern1)[1]);
        }
    );
});

