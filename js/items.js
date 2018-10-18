function DeleteItem(id){
    var item_name = $("#delete_"+id).attr('title');
    var package = $("#delete_"+id).attr('package');
    if(window.confirm("Na pewno usunąć: "+item_name+"?")){
        location.href="?modul=zlecenia&akcja=szczegoly&id="+package+"&delid="+id;
    }else{
        void(0);
    }
}

function AcceptPackage(id){
    if(window.confirm("Na pewno zaakceptować zlecenie?")){
        location.href = "?modul=zlecenia&akcja=szczegoly&id="+id+"&act=accept";
    }else{
        void(0);
    }
}

function ReadyPackage(id){
    if(window.confirm("Na pewno chcesz oznaczyć jako gotowe?")){
        location.href = "?modul=zlecenia&akcja=szczegoly&id="+id+"&act=ready";
    }else{
        void(0);
    }
}

function SendPackage(id){
    txt = "Na pewno wysłać zlecenie?";
    if(window.confirm(txt)){
        location.href = "?modul=zlecenia&akcja=szczegoly&id="+id+"&act=send";
    }else{
        void(0);
    }
}