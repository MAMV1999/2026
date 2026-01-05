var link = "../Controlador/Usuario_apoderado.php?op=";
var tabla;

function init() {
    $("#frm_form").on("submit", function (e) {
        guardaryeditar(e);
    });
    MostrarListado();
    cargar_tipos_apoderados();
    cargar_tipos_documentos();
    actualizarFechaHora();
    setInterval(actualizarFechaHora, 1000);
}

function cargar_tipos_apoderados() {
    $.post(link + "listar_tipos_apoderados_activos", function (r) {
        $("#id_apoderado_tipo").html(r);
        $("#id_apoderado_tipo").selectpicker("refresh");
    });
}

function cargar_tipos_documentos() {
    $.post(link + "listar_tipos_documentos_activos", function (r) {
        $("#id_documento").html(r);
        $("#id_documento").selectpicker("refresh");
    });
}

$(document).ready(function () {
    tabla = $("#myTable").DataTable({
        ajax: link + "listar",
    });
});

function limpiar() {
    cargar_tipos_apoderados();
    cargar_tipos_documentos();
    $("#id").val("");
    $("#id_apoderado_tipo").val("");
    $("#id_documento").val("");
    $("#numerodocumento").val("");
    $("#nombreyapellido").val("");
    $("#telefono").val("");
    $("#id_sexo").val("");
    $("#id_estado_civil").val("");
    $("#usuario").val("");
    $("#clave").val("");
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
            $("#id_apoderado_tipo").val(data.id_apoderado_tipo);
            $("#id_documento").val(data.id_documento);
            $("#numerodocumento").val(data.numerodocumento);
            $("#nombreyapellido").val(data.nombreyapellido);
            $("#telefono").val(data.telefono);
            $("#usuario").val(data.usuario);
            $("#clave").val(data.clave);
            $("#observaciones").val(data.observaciones);

            $("#id_apoderado_tipo").selectpicker("refresh");
            $("#id_documento").selectpicker("refresh");
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
