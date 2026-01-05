var link = "../Controlador/matricula_editar.php?op=";
var tabla;

function init() {
    $("#frm_form").on("submit", function (e) {
        guardaryeditar(e);
    });
    MostrarListado();
    cargar_matriculas();
    cargar_categorias_matricula();
    cargar_apoderados();
    cargar_alumnos();
    cargar_apoderados_referidos();
    actualizarFechaHora();
    setInterval(actualizarFechaHora, 1000);
}

function cargar_matriculas() {
    $.post(link + "listar_matriculas_activas", function (r) {
        $("#id_matricula").html(r);
        $("#id_matricula").selectpicker("refresh");
    });
}

function cargar_categorias_matricula() {
    $.post(link + "listar_categorias_matricula_activas", function (r) {
        $("#id_matricula_categoria").html(r);
        $("#id_matricula_categoria").selectpicker("refresh");
    });
}

function cargar_apoderados() {
    $.post(link + "listar_apoderados_activos", function (r) {
        $("#id_usuario_apoderado").html(r);
        $("#id_usuario_apoderado").selectpicker("refresh");
    });
}

function cargar_alumnos() {
    $.post(link + "listar_alumnos_activos", function (r) {
        $("#id_usuario_alumno").html(r);
        $("#id_usuario_alumno").selectpicker("refresh");
    });
}

function cargar_apoderados_referidos() {
    $.post(link + "listar_apoderados_referidos_activos", function (r) {
        $("#id_usuario_apoderado_referido").html(r);
        $("#id_usuario_apoderado_referido").selectpicker("refresh");
    });
}

$(document).ready(function () {
    tabla = $("#myTable").DataTable({
        ajax: link + "listar",
    });
});

function limpiar() {
    cargar_matriculas();
    cargar_categorias_matricula();
    cargar_apoderados();
    cargar_alumnos();
    cargar_apoderados_referidos();
    $("#id").val("");
    $("#id_matricula").val("");
    $("#id_matricula_categoria").val("");
    $("#id_usuario_apoderado").val("");
    $("#id_usuario_alumno").val("");
    $("#id_usuario_apoderado_referido").val("");
    $("#descripcion").val("");
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
            $("#id_matricula").val(data.id_matricula);
            $("#id_matricula_categoria").val(data.id_matricula_categoria);
            $("#id_usuario_apoderado").val(data.id_usuario_apoderado);
            $("#id_usuario_alumno").val(data.id_usuario_alumno);
            $("#id_usuario_apoderado_referido").val(data.id_usuario_apoderado_referido);
            $("#descripcion").val(data.descripcion);
            $("#observaciones").val(data.observaciones);

            $("#id_matricula").selectpicker("refresh");
            $("#id_matricula_categoria").selectpicker("refresh");
            $("#id_usuario_apoderado").selectpicker("refresh");
            $("#id_usuario_alumno").selectpicker("refresh");
            $("#id_usuario_apoderado_referido").selectpicker("refresh");
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
