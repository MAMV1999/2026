var link = "../Controlador/Institucion_seccion.php?op=";
var tabla;

function init() {
    $("#frm_form").on("submit", function (e) {
        guardaryeditar(e);
    });
    MostrarListado();
    cargar_grados_activos();
    actualizarFechaHora();
    setInterval(actualizarFechaHora, 1000);
}

function cargar_grados_activos() {
    $.post(link + "listar_grados_activos", function (r) {
        $("#id_institucion_grado").html(r);
        $("#id_institucion_grado").selectpicker("refresh");
    });
}

$(document).ready(function () {
    tabla = $("#myTable").DataTable({
        ajax: link + "listar",
    });
});

function limpiar() {
    cargar_grados_activos();
    $("#id").val("");
    $("#nombre").val("");
    $("#id_institucion_grado").val("");
    $("#observaciones").val("");
}

function MostrarListado() {
    limpiar();
    $("#listado").show();
    $("#formulario").hide();
}

function MostrarFormulario() {
    $("#listado").hide();
    $("#formulario").show();
}

function guardaryeditar(e) {
    e.preventDefault();

    $.ajax({
        url: link + "guardaryeditar",
        type: "POST",
        data: $("#frm_form").serialize(),

        success: function (datos) {
            alert(datos);
            MostrarListado();
            tabla.ajax.reload();
        },
    });
    limpiar();
}

function mostrar(id) {
    $.post(
        link + "mostrar",
        {
            id: id,
        },
        function (data, status) {
            data = JSON.parse(data);
            MostrarFormulario();

            $("#id").val(data.id);
            $("#nombre").val(data.nombre);
            $("#id_institucion_grado").val(data.id_institucion_grado);
            $("#observaciones").val(data.observaciones);
            $('#id_institucion_grado').selectpicker('refresh');
        }
    );
}

function activar(id) {
    let condicion = confirm("¿ACTIVAR?");
    if (condicion === true) {
        $.ajax({
            type: "POST",
            url: link + "activar",
            data: {
                id: id,
            },
            success: function (datos) {
                alert(datos);
                tabla.ajax.reload();
            },
        });
    } else {
        alert("CANCELADO");
    }
}

function desactivar(id) {
    let condicion = confirm("¿DESACTIVAR?");
    if (condicion === true) {
        $.ajax({
            type: "POST",
            url: link + "desactivar",
            data: {
                id: id,
            },
            success: function (datos) {
                alert(datos);
                tabla.ajax.reload();
            },
        });
    } else {
        alert("CANCELADO");
    }
}

init();
