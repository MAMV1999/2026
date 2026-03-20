var link = "../Controlador/Usuario_alumno.php?op=";
var tabla;

function init() {
    $("#frm_form").on("submit", function (e) {
        guardaryeditar(e);
    });
    MostrarListado();
    cargar_apoderados();
    cargar_tipos_documentos();
    cargar_sexos();
    actualizarFechaHora();
    setInterval(actualizarFechaHora, 1000);
}

function cargar_apoderados() {
    $.post(link + "listar_apoderados_activos", function (r) {
        $("#id_apoderado").html(r);
        $("#id_apoderado").selectpicker("refresh");
    });
}

function cargar_tipos_documentos() {
    $.post(link + "listar_tipos_documentos_activos", function (r) {
        $("#id_documento").html(r);
        $("#id_documento").selectpicker("refresh");
    });
}

function cargar_sexos() {
    $.post(link + "listar_sexos_activos", function (r) {
        $("#id_sexo").html(r);
        $("#id_sexo").selectpicker("refresh");
    });
}

$(document).ready(function () {
    tabla = $("#myTable").DataTable({
        ajax: link + "listar",
    });
});

function limpiar() {
    cargar_apoderados();
    cargar_tipos_documentos();
    cargar_sexos();
    $("#id").val("");
    $("#id_apoderado").val("");
    $("#id_documento").val("");
    $("#numerodocumento").val("");
    $("#nombreyapellido").val("");
    $("#id_sexo").val("");
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
            $("#id_apoderado").val(data.id_apoderado);
            $("#id_documento").val(data.id_documento);
            $("#numerodocumento").val(data.numerodocumento);
            $("#nombreyapellido").val(data.nombreyapellido);
            $("#id_sexo").val(data.id_sexo);
            $("#usuario").val(data.usuario);
            $("#clave").val(data.clave);
            $("#observaciones").val(data.observaciones);

            $("#id_apoderado").selectpicker("refresh");
            $("#id_documento").selectpicker("refresh");
            $("#id_sexo").selectpicker("refresh");
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
