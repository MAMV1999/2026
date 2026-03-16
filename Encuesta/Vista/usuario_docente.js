var link = "../Controlador/usuario_docente.php?op=";
var tabla;

function init() {
    // Configuración del formulario
    $("#frm_form").on("submit", function (e) {
        guardaryeditar(e);
    });
    MostrarListado();
    cargar_documentos();
    cargar_cargos();
    cargar_estados_civiles();
    cargar_sexos();
    cargar_tipos_contrato();
    actualizarFechaHora();
    setInterval(actualizarFechaHora, 1000);
}

// Funciones para cargar datos dinámicos en los selects
function cargar_documentos() {
    $.post(link + "listar_documentos_activos", function (r) {
        $("#id_documento").html(r);
    });
}

function cargar_cargos() {
    $.post(link + "listar_cargos_activos", function (r) {
        $("#id_cargo").html(r);
    });
}

function cargar_estados_civiles() {
    $.post(link + "listar_estados_civiles_activos", function (r) {
        $("#id_estado_civil").html(r);
    });
}

function cargar_sexos() {
    $.post(link + "listar_sexos_activos", function (r) {
        $("#id_sexo").html(r);
    });
}

function cargar_tipos_contrato() {
    $.post(link + "listar_tipos_contrato_activos", function (r) {
        $("#id_tipo_contrato").html(r);
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
    cargar_documentos();
    cargar_cargos();
    cargar_estados_civiles();
    cargar_sexos();
    cargar_tipos_contrato();

    $("#id").val("");
    $("#id_documento").val("");
    $("#numerodocumento").val("");
    $("#nombreyapellido").val("");
    $("#id_cargo").val("");
    $("#id_tipo_contrato").val("");
    $("#nacimiento").val("");
    $("#id_estado_civil").val("");
    $("#direccion").val("");
    $("#telefono").val("");
    $("#correo").val("");
    $("#fechainicio").val("");
    $("#fechafin").val("");
    $("#sueldo").val("");
    $("#cuentabancaria").val("");
    $("#cuentainterbancaria").val("");
    $("#sunat_ruc").val("");
    $("#sunat_usuario").val("");
    $("#sunat_contraseña").val("");
    $("#usuario").val("");
    $("#clave").val("");
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

// Función para guardar o editar un docente
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

// Función para mostrar un docente específico
function mostrar(id) {
    $.post(link + "mostrar", { id: id }, function (data, status) {
        data = JSON.parse(data);
        MostrarFormulario();

        $("#id").val(data.id);
        $("#id_documento").val(data.id_documento);
        $("#numerodocumento").val(data.numerodocumento);
        $("#nombreyapellido").val(data.nombreyapellido);
        $("#nacimiento").val(data.nacimiento);
        $("#id_estado_civil").val(data.id_estado_civil);
        $("#id_sexo").val(data.id_sexo);
        $("#direccion").val(data.direccion);
        $("#telefono").val(data.telefono);
        $("#correo").val(data.correo);
        $("#id_cargo").val(data.id_cargo);
        $("#id_tipo_contrato").val(data.id_tipo_contrato);
        $("#fechainicio").val(data.fechainicio);
        $("#fechafin").val(data.fechafin);
        $("#sueldo").val(data.sueldo);
        $("#cuentabancaria").val(data.cuentabancaria);
        $("#cuentainterbancaria").val(data.cuentainterbancaria);
        $("#sunat_ruc").val(data.sunat_ruc);
        $("#sunat_usuario").val(data.sunat_usuario);
        $("#sunat_contraseña").val(data.sunat_contraseña);
        $("#usuario").val(data.usuario);
        $("#clave").val(data.clave);
        $("#observaciones").val(data.observaciones);

        // Refrescar selects para que se muestren los valores seleccionados
        $("#id_documento").selectpicker("refresh");
        $("#id_cargo").selectpicker("refresh");
        $("#id_estado_civil").selectpicker("refresh");
        $("#id_sexo").selectpicker("refresh");
        $("#id_tipo_contrato").selectpicker("refresh");
    });
}

// Función para activar un docente
function activar(id) {
    let condicion = confirm("¿ACTIVAR?");
    if (condicion === true) {
        $.post(link + "activar", { id: id }, function (datos) {
            alert(datos);
            tabla.ajax.reload();
        });
    }
}

// Función para desactivar un docente
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
