
function delete_resource(OBJ, deleteAllFromTable = ""){

    if(!confirm("Do you really want to do this?")) {
        return false;
    }

    var id = $(OBJ).data("id");
    var token = $("meta[name='csrf-token']").attr("content");

    $.ajax({
        url: OBJ.href,
        type: 'DELETE',
        data: {
            _token: token,
            id: id
        },
        success: function (response){
            $("#response").removeClass("alert alert-danger");
            $("#response").addClass("alert alert-success");
            $("#response").html(response);

            if(deleteAllFromTable != ""){
                removedMessage = "<tr><td colspan='100%' class='text-center'><i class='fas fa-ban'></i> All deleted</td></tr>";
                $("#"+deleteAllFromTable+" tbody").html(removedMessage);
            } else
                $("#tr-"+id).remove();
        },
        error: function(error){
            $("#response").removeClass("alert alert-success");
            $("#response").addClass("alert alert-danger");
            $("#response").html(error.responseText);
        }
    });
    return false;
}

function findFirstLast(docNum, first = 1){
    markers = $("#content-"+docNum+" mark");
    if(markers.length == 0)
        return;

    markers.removeClass('focus');

    if(first){
        mark = $("#content-"+docNum+" mark").first();
        $("#current-mark-"+docNum).html(1);
    }else{
        mark = $("#content-"+docNum+" mark").last();
        $("#current-mark-"+docNum).html(markers.length);
    }

    mark.addClass('focus');

    $("#content-"+docNum).scrollTop(0);
    posT = mark.position().top;
    $("#content-"+docNum).scrollTop(posT-50);

    if(first){
        $("#btnPrev-"+docNum).attr("onclick","findPrevMarker(\""+docNum+"\", 0)");
        $("#btnNext-"+docNum).attr("onclick","findNextMarker(\""+docNum+"\", 1)");

    }else{
        $("#btnPrev-"+docNum).attr("onclick","findPrevMarker(\""+docNum+"\", "+(markers.length-1)+")");
        $("#btnNext-"+docNum).attr("onclick","findNextMarker(\""+docNum+"\", "+(markers.length-1)+")");
    }
}

function findNextMarker(docNum, i=0){
    markers = $("#content-"+docNum+" mark");
    if(markers.length == 0 || (i != 0 && (i == markers.length)))
        return;

    markers.removeClass('focus');

    mark = $("#content-"+docNum+" mark:eq("+i+")");

    mark.addClass('focus');

    $("#content-"+docNum).scrollTop(0);
    posT = mark.position().top;
    $("#content-"+docNum).scrollTop(posT-50);

    curr = parseInt($("#current-mark-"+docNum).html());
    $("#current-mark-"+docNum).html((curr + 1));

    $("#btnPrev-"+docNum).attr("onclick","findPrevMarker(\""+docNum+"\", "+(i)+")");
    $("#btnNext-"+docNum).attr("onclick","findNextMarker(\""+docNum+"\", "+(i+1)+")");
}

function findPrevMarker(docNum, i=0){
    markers = $("#content-"+docNum+" mark");
    if(i == 0 || markers.length == 0)
        return;

    markers.removeClass('focus');

    mark = $("#content-"+docNum+" mark:eq("+(i-1)+")");

    mark.addClass('focus');

    $("#content-"+docNum).scrollTop(0);
    posT = mark.position().top;
    $("#content-"+docNum).scrollTop(posT-50);

    curr = parseInt($("#current-mark-"+docNum).html());
    $("#current-mark-"+docNum).html((curr - 1));

    $("#btnPrev-"+docNum).attr("onclick","findPrevMarker(\""+docNum+"\", "+(i-1)+")");
    $("#btnNext-"+docNum).attr("onclick","findNextMarker(\""+docNum+"\", "+(i)+")");
}
