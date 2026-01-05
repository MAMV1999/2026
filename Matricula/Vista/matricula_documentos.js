var link = "../Controlador/matricula_documentos.php?op=";
var tabla;

function init() {
    // Configuración del formulario
    $("#frm_form").on("submit", function (e) {
        guardaryeditar(e);
    });
    MostrarListado();
    cargar_responsables();
    actualizarFechaHora();
    setInterval(actualizarFechaHora, 1000);
}

// Funciones para cargar datos dinámicos en los selects
function cargar_responsables() {
    $.post(link + "listar_responsables_activos", function (r) {
        $("#id_matricula_documentos_responsable").html(r);
    });
}

$(document).ready(function () {
    // Inicialización de DataTable
    tabla = $('#myTable').DataTable({
        "ajax": {
            "url": link + "listar",
            "dataSrc": function (json) {
                console.log(json); // Verifica la estructura de la respuesta aquí
                return json.aaData; // Asegúrate de que 'aaData' esté correctamente formado en el controlador
            }
        }
    });
});

// Función para limpiar el formulario
function limpiar() {
    cargar_responsables();

    $("#id").val("");
    $("#id_matricula_documentos_responsable").val("");
    $("#nombre").val("");
    $("input[name='obligatorio'][value='0']").prop("checked", true); // Por defecto en "No"
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

// Función para guardar o editar un documento de matrícula
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

// Función para mostrar un documento específico
function mostrar(id) {
    $.post(link + "mostrar", { id: id }, function (data, status) {
        data = JSON.parse(data);
        MostrarFormulario();

        $("#id").val(data.id);
        $("#id_matricula_documentos_responsable").val(data.id_matricula_documentos_responsable);
        $("#nombre").val(data.nombre);
        $("input[name='obligatorio'][value='" + data.obligatorio + "']").prop("checked", true); // Selecciona Sí o No según el valor
        $("#observaciones").val(data.observaciones);

        // Refrescar selects para que se muestren los valores seleccionados
        $("#id_matricula_documentos_responsable").selectpicker("refresh");
    });
}

// Función para activar un documento
function activar(id) {
    let condicion = confirm("¿ACTIVAR?");
    if (condicion === true) {
        $.post(link + "activar", { id: id }, function (datos) {
            alert(datos);
            tabla.ajax.reload();
        });
    }
}

// Función para desactivar un documento
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
