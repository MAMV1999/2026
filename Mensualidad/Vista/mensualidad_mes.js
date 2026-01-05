var link = "../Controlador/mensualidad_mes.php?op=";
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
        $("#id_institucion_lectivo").html(r);
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
    $("#id_institucion_lectivo").val("");
    $("#nombre").val("");
    $("#descripcion").val("");
    $("#fechavencimiento").val(""); // Limpia el campo de fecha de vencimiento
    $("#observaciones").val("");
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

// Función para guardar o editar un mes de mensualidad
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

// Función para mostrar un mes de mensualidad específico
function mostrar(id) {
    $.post(link + "mostrar", { id: id }, function (data, status) {
        data = JSON.parse(data);
        MostrarFormulario();

        $("#id").val(data.id);
        $("#id_institucion_lectivo").val(data.id_institucion_lectivo);
        $("#nombre").val(data.nombre);
        $("#descripcion").val(data.descripcion);
        $("#fechavencimiento").val(data.fechavencimiento);
        $("#observaciones").val(data.observaciones);

        // Refrescar selects para que se muestren los valores seleccionados
        $("#id_institucion_lectivo").selectpicker("refresh");
    });
}

// Función para activar un mes de mensualidad
function activar(id) {
    let condicion = confirm("¿ACTIVAR?");
    if (condicion === true) {
        $.post(link + "activar", { id: id }, function (datos) {
            alert(datos);
            tabla.ajax.reload();
        });
    }
}

// Función para desactivar un mes de mensualidad
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
