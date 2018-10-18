<script type="text/javascript">
            $(document).ready(function(){
            $("#lista tr.hidden").hide();
            $("#lista tr.unhidden").click(function(){
//                console.log($(this).attr('hidden-class'));
                $(this).next("tr."+$(this).attr('hidden-class')).toggle();
            });
        }); 
</script>