var link = "../Controlador/Institucion.php?op=";
var tabla;

function init() {
    $("#frm_form").on("submit", function (e) {
        guardaryeditar(e);
    });
    MostrarListado();
    cargar_docentes();
    actualizarFechaHora();
    setInterval(actualizarFechaHora, 1000);
}

function cargar_docentes() {
    $.post(link + "listar_docentes_activos", function (r) {
        $("#id_usuario_docente").html(r);
        $("#id_usuario_docente").selectpicker("refresh");
    });
}

$(document).ready(function () {
    tabla = $("#myTable").DataTable({
        ajax: link + "listar",
    });
});

function limpiar() {
    cargar_docentes();
    $("#id").val("");
    $("#nombre").val("");
    $("#id_usuario_docente").val("");
    $("#telefono").val("");
    $("#correo").val("");
    $("#ruc").val("");
    $("#razon_social").val("");
    $("#direccion").val("");
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
            $("#id_usuario_docente").val(data.id_usuario_docente);
            $("#telefono").val(data.telefono);
            $("#correo").val(data.correo);
            $("#ruc").val(data.ruc);
            $("#razon_social").val(data.razon_social);
            $("#direccion").val(data.direccion);
            $("#observaciones").val(data.observaciones);
            $('#id_usuario_docente').selectpicker('refresh');
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
