
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
