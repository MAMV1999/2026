var link = "../Controlador/matricula_mes.php?op=";
var tabla;

function init() {
    // Configuración del formulario
    $("#frm_form").on("submit", function (e) {
        guardaryeditar(e);
    });
    MostrarListado();
    cargarInstitucionesLectivas();
    actualizarFechaHora();
    setInterval(actualizarFechaHora, 1000);
}

// Funciones para cargar datos dinámicos en los selects
function cargarInstitucionesLectivas() {
    $.post(link + "listar_instituciones_lectivas_activas", function (r) {
        $("#institucion_lectivo_id").html(r);
    });
}

$(document).ready(function () {
    // Inicialización de DataTable
    tabla = $('#myTable').DataTable({
        "ajax": {
            "url": link + "listar"
        }
    });
});

// Función para limpiar el formulario
function limpiar() {
    cargarInstitucionesLectivas();

    $("#id").val("");
    $("#institucion_lectivo_id").val("");
    $("#nombre").val("");
    $("#fecha_vencimiento").val("");
    $("#mora").val("");
    $("#observaciones").val("");
    $("#estado").val("1");
}

// Función para mostrar el listado y ocultar el formulario
function MostrarListado() {
    limpiar();
    $("#listado").show();
    $("#formulario").hide();
}

// Función para mostrar el formulario y ocultar el listado
function MostrarFormulario() {
    $("#listado").hide();
    $("#formulario").show();
}

// Función para guardar o editar un mes
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

// Función para mostrar un mes específico
function mostrar(id) {
    $.post(link + "mostrar", { id: id }, function (data, status) {
        data = JSON.parse(data);
        MostrarFormulario();

        $("#id").val(data.id);
        $("#institucion_lectivo_id").val(data.institucion_lectivo_id);
        $("#nombre").val(data.nombre);
        $("#fecha_vencimiento").val(data.fecha_vencimiento);
        $("#mora").val(data.mora);
        $("#observaciones").val(data.observaciones);
        $("#estado").val(data.estado);

        // Refrescar selects para que se muestren los valores seleccionados
        $("#institucion_lectivo_id").selectpicker("refresh");
    });
}

// Función para activar
function activar(id) {
    let condicion = confirm("¿ACTIVAR?");
    if (condicion === true) {
        $.post(link + "activar", { id: id }, function (datos) {
            alert(datos);
            tabla.ajax.reload();
        });
    }
}

// Función para desactivar
function desactivar(id) {
    let condicion = confirm("¿DESACTIVAR?");
    if (condicion === true) {
        $.post(link + "desactivar", { id: id }, function (datos) {
            alert(datos);
            tabla.ajax.reload();
        });
    }
}

init();
