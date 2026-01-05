$("#frmAcceso").on('submit', function(e) {
    e.preventDefault();
    var usuario = $("#usuario").val();
    var clave = $("#clave").val();

    $.post("../Controlador/Acceso.php?op=verificar", {"usuario": usuario, "clave": clave}, function(response) {
        var data = JSON.parse(response);
        if (data.status == "success") {
            $("#usuario").val("");
            $("#clave").val("");
            $(location).attr("href", "Escritorio.php");
        } else {
            $("#usuario").val("");
            $("#clave").val("");
            alert("Algo anda mal: " + data.message);
        }
    });
});
