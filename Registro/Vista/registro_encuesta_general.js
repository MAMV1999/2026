var link = "../Controlador/registro_encuesta_general.php?op=";
var tabla;

function init() {
    $("#frm_form").on("submit", function (e) {
        guardaryeditar(e);
    });

    MostrarListado();
    cargar_docentes();
    cargar_alumnos();
}

$(document).ready(function () {
    tabla = $("#myTable").DataTable({
        ajax: link + "listar"
    });
});

function limpiar() {
    $("#id").val("");
    $("#nombre").val("");
    $("#fecha_inicio").val("");
    $("#fecha_fin").val("");
    $("#calificacion_menor").val("");
    $("#calificacion_mayor").val("");
    $("#observaciones").val("");

    $(".check_docente").prop("checked", false);
    $(".check_alumno").prop("checked", false);

    $("#btn_guardar").html("Guardar");
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

function cargar_docentes(callback) {
    $.post(link + "listar_docentes", function (r) {
        $("#tabla_docentes tbody").html(r);

        if (typeof callback === "function") {
            callback();
        }
    });
}

function cargar_alumnos(callback) {
    $.post(link + "listar_alumnos", function (r) {
        $("#accordionAlumnos").html(r);

        if (typeof callback === "function") {
            callback();
        }
    });
}

function guardaryeditar(e) {
    e.preventDefault();

    let id = $("#id").val();

    if (id == "") {
        if (!confirm("Está creando una NUEVA encuesta. ¿Desea continuar?")) {
            return;
        }
    } else {
        if (!confirm("Está EDITANDO la encuesta seleccionada. ¿Desea guardar los cambios?")) {
            return;
        }
    }

    let formData = new FormData(document.getElementById("frm_form"));

    $.ajax({
        url: link + "guardar",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            alert(response);

            if (response.includes("correctamente")) {
                MostrarListado();
                tabla.ajax.reload();
            }
        },
        error: function () {
            alert("Error al intentar guardar los datos.");
        }
    });
}

function mostrar(id) {
    $.post(link + "mostrar", { id: id }, function (resp) {

        let data = JSON.parse(resp);

        MostrarFormulario();

        $("#id").val(data.cabecera.id);
        $("#nombre").val(data.cabecera.nombre);
        $("#fecha_inicio").val(data.cabecera.fecha_inicio);
        $("#fecha_fin").val(data.cabecera.fecha_fin);
        $("#calificacion_menor").val(data.cabecera.calificacion_menor);
        $("#calificacion_mayor").val(data.cabecera.calificacion_mayor);
        $("#observaciones").val(data.cabecera.observaciones);

        $("#btn_guardar").html("Actualizar");

        cargar_docentes(function () {
            $(".check_docente").prop("checked", false);

            data.docentes.forEach(function (idDocente) {
                $("input[name='docentes[]'][value='" + idDocente + "']").prop("checked", true);
            });
        });

        cargar_alumnos(function () {
            $(".check_alumno").prop("checked", false);

            data.alumnos.forEach(function (idAlumno) {
                $("input[name='alumnos[]'][value='" + idAlumno + "']").prop("checked", true);
            });
        });
    });
}

function marcarGrado(grado) {
    $(".grado_" + grado).prop("checked", true);
}

function desmarcarGrado(grado) {
    $(".grado_" + grado).prop("checked", false);
}

function marcarTodosDocentes() {
    $(".check_docente").prop("checked", true);
}

function desmarcarTodosDocentes() {
    $(".check_docente").prop("checked", false);
}

function marcarTodosAlumnos() {
    $(".check_alumno").prop("checked", true);
}

function desmarcarTodosAlumnos() {
    $(".check_alumno").prop("checked", false);
}

function activar(id) {
    if (confirm("¿ACTIVAR?")) {
        $.post(link + "activar", { id: id }, function (datos) {
            alert(datos);
            tabla.ajax.reload();
        });
    }
}

function desactivar(id) {
    if (confirm("¿DESACTIVAR?")) {
        $.post(link + "desactivar", { id: id }, function (datos) {
            alert(datos);
            tabla.ajax.reload();
        });
    }
}

init();