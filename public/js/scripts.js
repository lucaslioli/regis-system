
function delete_resource(OBJ){

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
            $("#tr-"+id).remove();
        }
    });
    return false;
}

function findFirstLast(docNum, first = 1){
    markers = $("#content-"+docNum+" mark");
    if(markers.length == 0)
        return;

    markers.removeClass('focus');

    if(first)
        mark = $("#content-"+docNum+" mark").first();
    else
        mark = $("#content-"+docNum+" mark").last();

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

    $("#btnPrev-"+docNum).attr("onclick","findPrevMarker(\""+docNum+"\", "+(i-1)+")");
    $("#btnNext-"+docNum).attr("onclick","findNextMarker(\""+docNum+"\", "+(i)+")");
}
