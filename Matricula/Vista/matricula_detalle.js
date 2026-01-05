var link = "../Controlador/matricula_detalle.php?op=";
var tabla;

function init() {
    $("#frm_form").on("submit", function (e) {
        guardaryeditar(e);
    });
    MostrarListado();
    cargarSelectores(); // Cargar selectores dinámicos al inicializar
    fecha();
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

$(document).ready(function () {
    tabla = $("#myTable").DataTable({
        ajax: link + "listar",
    });
});

function fecha() {
    var now = new Date();
    var day = ("0" + now.getDate()).slice(-2);
    var month = ("0" + (now.getMonth() + 1)).slice(-2);
    var today = now.getFullYear() + "-" + (month) + "-" + (day);
    $("#pago_fecha").val(today);
}

function limpiar() {
    $("#apoderado_tipo").val("");
    $("#apoderado_documento").val("");
    $("#apoderado_dni").val("");
    $("#apoderado_nombreyapellido").val("");
    $("#apoderado_telefono").val("");
    $("#apoderado_observaciones").val("");

    $("#alumno_documento").val("");
    $("#alumno_sexo").val("");
    $("#alumno_dni").val("");
    $("#alumno_nombreyapellido").val("");
    $("#alumno_nacimiento").val("");
    $("#alumno_telefono").val("");
    $("#alumno_observaciones").val("");

    $("#matricula_id").val("");
    $("#matricula_categoria").val("");
    $("#matricula_observaciones").val("");

    $("#pago_numeracion").val("");
    $("#pago_fecha").val("");
    $("#pago_descripcion").val("");
    $("#pago_monto").val("");
    $("#pago_observaciones").val("");
}

function MostrarListado() {
    limpiar();
    $("#listado").show();
    $("#formulario").hide();
}

// Llamar a esta función al mostrar el formulario
function MostrarFormulario() {
    $("#listado").hide();
    $("#formulario").show();
    cargarSelectores();
    fecha();

    // Cargar la numeración del pago
    cargarSiguienteNumeracionPago();

    setTimeout(function () {
        InformacionDetalle();
    }, 200);
}

// Cargar datos en los selectores dinámicos
function cargarSelectores() {
    cargarApoderadoTipos();
    cargarDocumentos();
    cargarSexos();
    cargarEstadosCiviles();
    cargarMatriculas();
    cargarCategorias();
    cargarMetodosPago();
    cargarApoderadosReferidos();
}

function cargarApoderadosReferidos() {
    $.post(link + "listar_apoderados_referidos_activos", function (r) {
        $("#apoderado_referido").html(r); // ID del nuevo select
    });
}

// Apoderado - Tipos
function cargarApoderadoTipos() {
    $.post(link + "listar_apoderado_tipos_activos", function (r) {
        $("#apoderado_tipo").html(r);
    });
}

// Documentos
function cargarDocumentos() {
    $.post(link + "listar_documentos_activos", function (r) {
        $("#apoderado_documento, #alumno_documento").html(r);
    });
}

// Sexos
function cargarSexos() {
    $.post(link + "listar_sexos_activos", function (r) {
        $("#apoderado_sexo, #alumno_sexo").html(r);
    });
}

// Estados Civiles
function cargarEstadosCiviles() {
    $.post(link + "listar_estados_civiles_activos", function (r) {
        $("#apoderado_estado_civil").html(r);
    });
}

// Matrículas
function cargarMatriculas() {
    $.post(link + "listar_matriculas_activas", function (r) {
        $("#matricula_id").html(r);
    });

    $("#matricula_id").change(function () {
        InformacionDetalle();
        cargarMensualidades();
    });
}

function InformacionDetalle() {
    var selectedOption = $("#matricula_id option:selected");

    // Datos fijos
    var lectivo = selectedOption.data('lectivo') || '';
    var nivel = selectedOption.data('nivel') || '';
    var grado = selectedOption.data('grado') || '';
    var seccion = selectedOption.data('seccion') || '';
    var observaciones = selectedOption.data('observaciones') || '';

    // Fecha dd/mm/yyyy
    var now = new Date();
    var day = ("0" + now.getDate()).slice(-2);
    var month = ("0" + (now.getMonth() + 1)).slice(-2);
    var today = day + "/" + month + "/" + now.getFullYear();

    // Cobros dinámicos (data-cobro-*)
    var cobros = {}; // { slug: valor }
    $.each(selectedOption[0].attributes, function (i, attr) {
        if (attr && attr.name && attr.name.indexOf("data-cobro-") === 0) {
            var key = attr.name.replace("data-cobro-", ""); // slug
            cobros[key] = attr.value; // viene como string
        }
    });

    // Armar bloque de cobros para el detalle
    var cobrosTexto = '';
    var keys = Object.keys(cobros);

    if (keys.length > 0) {
        // Orden opcional por nombre de key
        keys.sort();

        keys.forEach(function (k) {
            // Mostrar un nombre más legible: matricula -> Matricula, pago-libro -> Pago libro
            var label = k.replace(/-/g, ' ');
            label = label.charAt(0).toUpperCase() + label.slice(1);

            cobrosTexto += label + ': S./' + cobros[k] + '\n';
        });
    } else {
        cobrosTexto = 'No hay cobros registrados.\n';
    }

    // Elegir monto principal para #pago_monto
    // Prioridad: cobro "matricula" si existe; si no, el primero.
    var montoPrincipal = '0';
    if (cobros.hasOwnProperty('matricula')) {
        montoPrincipal = cobros['matricula'];
    } else if (keys.length > 0) {
        montoPrincipal = cobros[keys[0]];
    }

    // Texto final
    var info_matricula =
        'MATRICULA ' + lectivo + ' - ' + today + '\n' +
        'NIVEL: ' + nivel + ' - GRADO: ' + grado + ' - SECCION: ' + seccion + '\n\n' +
        cobrosTexto + '\n' +
        'Observaciones: ' + observaciones + '\n';

    // Set inputs
    $("#pago_monto").val(montoPrincipal);
    $("#detalle").val(info_matricula);
    $("#pago_descripcion").val(info_matricula);
}

// Categorías
function cargarCategorias() {
    $.post(link + "listar_categorias_activas", function (r) {
        $("#matricula_categoria").html(r);
    });
}

// Métodos de Pago
function cargarMetodosPago() {
    $.post(link + "listar_metodos_pago_activos", function (r) {
        $("#pago_metodo_id").html(r);
    });
}

function cargarSiguienteNumeracionPago() {
    $.post(link + "obtener_siguiente_numeracion_pago", function (data) {
        $("#pago_numeracion").val(data); // Asignar el valor obtenido al campo
    });
}

// TABLA MENCUlIDADES - INICIO
function cargarMensualidades() {

    var selectedOption = $("#matricula_id option:selected");
    var matricula_id = selectedOption.val();

    const tbody = document.querySelector("#mensualidadTable tbody");

    if (!matricula_id) {
        tbody.innerHTML = "";
        return;
    }

    // Helpers internos (solo existen dentro de esta función)
    function toNumber(value) {
        // Acepta "10", "10.5", "10,5", "S/ 10.50", etc.
        if (value == null) return 0;
        let s = String(value).trim();

        // Quita símbolos y espacios, deja dígitos, coma, punto y signo -
        s = s.replace(/[^\d.,-]/g, "");

        // Si viene con coma decimal, la convertimos a punto
        // Ej: "10,50" -> "10.50"
        // Si viene "1,200.50" o "1.200,50", esto es ambiguo; aquí priorizamos lo más común en ES: coma decimal.
        // Para tu caso escolar normalmente será simple.
        if (s.includes(",") && !s.includes(".")) {
            s = s.replace(",", ".");
        } else if (s.includes(",") && s.includes(".")) {
            // Si existen ambos, asumimos que la coma es separador de miles y la quitamos: "1,200.50" -> "1200.50"
            s = s.replace(/,/g, "");
        }

        const n = parseFloat(s);
        return isNaN(n) ? 0 : n;
    }

    function calcularFila(tr) {
        const mensualidadInput = tr.querySelector('input[name="mensualidad_precio[]"]');
        const mantenimientoInput = tr.querySelector('input[name="mantenimiento_precio[]"]');
        const impresionInput = tr.querySelector('input[name="impresion_precio[]"]');
        const totalInput = tr.querySelector('input[name="total_precio[]"]');

        if (!mensualidadInput || !mantenimientoInput || !impresionInput || !totalInput) return;

        const mensualidad = toNumber(mensualidadInput.value);
        const mantenimiento = toNumber(mantenimientoInput.value);
        const impresion = toNumber(impresionInput.value);

        const total = mensualidad + mantenimiento + impresion;

        // Mantén formato simple (puedes ajustar decimales si quieres)
        totalInput.value = total.toFixed(2);
    }

    function calcularTodo() {
        tbody.querySelectorAll("tr").forEach(calcularFila);
    }

    $.ajax({
        url: link + "listar_mensualidades_activas",
        type: "GET",
        data: { matricula_id: matricula_id },
        success: function (response) {
            tbody.innerHTML = response;

            // 1) Calcula totales al cargar
            calcularTodo();

            // 2) Recalcula al modificar cualquiera de los 3 campos (sin duplicar listeners)
            //    Delegación de eventos: funciona incluso si vuelves a cargar el tbody.
            tbody.oninput = function (e) {
                const t = e.target;
                if (
                    t && t.matches('input[name="mensualidad_precio[]"], input[name="mantenimiento_precio[]"], input[name="impresion_precio[]"]')
                ) {
                    const tr = t.closest("tr");
                    if (tr) calcularFila(tr);
                }
            };
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar las mensualidades: ", error);
        }
    });
}


// TABLA MENCUlIDADES - FIN

function eliminarConValidacion(id) {
    let contraseña = prompt("Ingrese la contraseña para validar la eliminación:");
    if (contraseña !== null && contraseña !== "") {
        $.ajax({
            type: "POST",
            url: link + "eliminar_con_validacion",
            data: { id_matricula_detalle: id, contraseña: contraseña },
            success: function (datos) {
                alert(datos);
                tabla.ajax.reload();
            },
            error: function () {
                alert("Ocurrió un error al intentar eliminar.");
            }
        });
    } else {
        alert("Operación cancelada. No se ingresó contraseña.");
    }
}

function desactivarConValidacion(id) {
    let contraseña = prompt("Ingrese la contraseña para validar la desactivación:");
    if (contraseña !== null && contraseña !== "") {
        $.ajax({
            type: "POST",
            url: link + "desactivar_con_validacion",
            data: { id_matricula_detalle: id, contraseña: contraseña },
            success: function (datos) {
                alert(datos);
                tabla.ajax.reload();
            },
            error: function () {
                alert("Ocurrió un error al intentar desactivar.");
            }
        });
    } else {
        alert("Operación cancelada. No se ingresó contraseña.");
    }
}


function buscarApoderado() {
    var dni = $("#apoderado_dni").val();
    if (!dni) {
        alert("Por favor, ingrese el número de documento.");
        return;
    }

    $.ajax({
        type: "POST",
        url: link + "buscar_apoderado",
        data: { dni: dni },
        success: function (response) {
            var data = JSON.parse(response);
            if (data && data.id) {
                // Rellenar los campos con la información del apoderado
                $("#apoderado_id").val(data.id);
                $("#apoderado_nombreyapellido").val(data.nombreyapellido);
                $("#apoderado_telefono").val(data.telefono);
                $("#apoderado_tipo").val(data.id_apoderado_tipo).change();
                $("#apoderado_documento").val(data.id_documento).change();
                $("#apoderado_sexo").val(data.id_sexo).change();
                $("#apoderado_estado_civil").val(data.id_estado_civil).change();
                $("#apoderado_observaciones").val(data.observaciones);
            } else {
                alert("No se encontró un apoderado con ese número de documento.");
                $("#apoderado_dni").val("");
            }
        },
        error: function () {
            alert("Ocurrió un error al buscar al apoderado.");
        }
    });
}

function buscarAlumno() {
    var dni = $("#alumno_dni").val();
    if (!dni) {
        alert("Por favor, ingrese el número de documento del alumno.");
        return;
    }

    $.ajax({
        type: "POST",
        url: link + "buscar_alumno",
        data: { dni: dni },
        success: function (response) {
            var data = JSON.parse(response);
            if (data && data.id) {
                // Rellenar los campos con la información del alumno
                $("#alumno_id").val(data.id);
                $("#alumno_nombreyapellido").val(data.nombreyapellido);
                $("#alumno_nacimiento").val(data.nacimiento);
                $("#alumno_documento").val(data.id_documento).change();
                $("#alumno_sexo").val(data.id_sexo).change();
                $("#alumno_telefono").val(data.telefono);
                $("#alumno_observaciones").val(data.observaciones);
            } else {
                alert("No se encontró un alumno con ese número de documento.");
            }
        },
        error: function () {
            alert("Ocurrió un error al buscar al alumno.");
        }
    });
}



init();
