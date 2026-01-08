var link = "../Controlador/matricula_editar.php?op=";
var tabla;

// ====== CONTROL DE EDICIÓN + PENDIENTES PARA SELECTS (SOLUCIÓN DEFINITIVA) ======
var editando = false;
var pending = {
    matricula_id: null,
    matricula_categoria_id: null,
    referido_id: null
};

function refreshPicker(selector) {
    // Si usas bootstrap-select (selectpicker), refresca; si no, no hace nada
    if ($.fn.selectpicker) {
        try { $(selector).selectpicker("refresh"); } catch (e) {}
    }
}
// ==============================================================================

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
        error: function () {
            alert("Ocurrió un error al guardar/editar.");
        }
    });
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
    // IMPORTANTE: limpiar IDs para que "NUEVO" no quede en modo editar
    $("#matricula_detalle_id").val("");
    $("#apoderado_id").val("");
    $("#alumno_id").val("");

    // Reset modo edición y pendientes
    editando = false;
    pending.matricula_id = null;
    pending.matricula_categoria_id = null;
    pending.referido_id = null;

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
    $("#apoderado_referido").val("");
    $("#detalle").val("");
    $("#matricula_observaciones").val("");

    $("#pago_numeracion").val("");
    $("#pago_fecha").val("");
    $("#pago_descripcion").val("");
    $("#pago_monto").val("");
    $("#pago_metodo_id").val("");
    $("#pago_observaciones").val("");

    refreshPicker("#matricula_id");
    refreshPicker("#matricula_categoria");
    refreshPicker("#apoderado_referido");
    refreshPicker("#apoderado_tipo");
    refreshPicker("#apoderado_documento");
    refreshPicker("#alumno_documento");
    refreshPicker("#alumno_sexo");
    refreshPicker("#pago_metodo_id");

    const tbody = document.querySelector("#mensualidadTable tbody");
    if (tbody) tbody.innerHTML = "";
}

function MostrarListado() {
    limpiar();
    $("#listado").show();
    $("#formulario").hide();
}

function MostrarFormulario() {
    $("#listado").hide();
    $("#formulario").show();
    cargarSelectores();
    fecha();

    // Solo si es nuevo (si no hay id, es nuevo)
    if (!$("#matricula_detalle_id").val()) {
        cargarSiguienteNumeracionPago();
    }

    // Para "nuevo" sí conviene armar detalle automático cuando ya haya matrícula seleccionada
    setTimeout(function () {
        // Solo autogenerar si es NUEVO (y no estamos editando)
        if (!$("#matricula_detalle_id").val() && !editando) {
            InformacionDetalle();
        }
    }, 200);
}

// ======================
// SELECTORES
// ======================

function cargarSelectores() {
    cargarApoderadoTipos();
    cargarDocumentos();
    cargarSexos();
    cargarMatriculas();
    cargarCategorias();
    cargarMetodosPago();
    cargarApoderadosReferidos();
}

function cargarApoderadosReferidos() {
    $.post(link + "listar_apoderados_referidos_activos", function (r) {
        $("#apoderado_referido").html(r);

        // aplicar pendiente si viene de edición
        if (pending.referido_id !== null) {
            $("#apoderado_referido").val(pending.referido_id);
        }
        refreshPicker("#apoderado_referido");
    });
}

function cargarApoderadoTipos() {
    $.post(link + "listar_apoderado_tipos_activos", function (r) {
        $("#apoderado_tipo").html(r);
        refreshPicker("#apoderado_tipo");
    });
}

function cargarDocumentos() {
    $.post(link + "listar_documentos_activos", function (r) {
        $("#apoderado_documento, #alumno_documento").html(r);
        refreshPicker("#apoderado_documento");
        refreshPicker("#alumno_documento");
    });
}

function cargarSexos() {
    $.post(link + "listar_sexos_activos", function (r) {
        $("#alumno_sexo").html(r);
        refreshPicker("#alumno_sexo");
    });
}

function cargarMatriculas() {
    // Evitar duplicar handlers cada vez que llamas cargarMatriculas()
    $("#matricula_id").off("change");

    $.post(link + "listar_matriculas_activas", function (r) {
        $("#matricula_id").html(r);

        // aplicar pendiente si viene de edición (aquí es donde antes fallaba)
        if (pending.matricula_id !== null) {
            $("#matricula_id").val(pending.matricula_id);
        }
        refreshPicker("#matricula_id");
    });

    $("#matricula_id").on("change", function () {
        // Solo autogenera detalle y carga mensualidades cuando es NUEVO.
        if (!$("#matricula_detalle_id").val() && !editando) {
            InformacionDetalle();
            cargarMensualidades();
        }
    });
}

function cargarCategorias() {
    $.post(link + "listar_categorias_activas", function (r) {
        $("#matricula_categoria").html(r);

        // aplicar pendiente si viene de edición
        if (pending.matricula_categoria_id !== null) {
            $("#matricula_categoria").val(pending.matricula_categoria_id);
        }
        refreshPicker("#matricula_categoria");
    });
}

function cargarMetodosPago() {
    $.post(link + "listar_metodos_pago_activos", function (r) {
        $("#pago_metodo_id").html(r);
        refreshPicker("#pago_metodo_id");
    });
}

function cargarSiguienteNumeracionPago() {
    $.post(link + "obtener_siguiente_numeracion_pago", function (data) {
        $("#pago_numeracion").val(data);
    });
}

// ======================
// DETALLE AUTOMÁTICO (NUEVO)
// ======================

function InformacionDetalle() {
    var selectedOption = $("#matricula_id option:selected");
    if (!selectedOption || selectedOption.length === 0) return;

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
    var cobros = {};
    $.each(selectedOption[0].attributes, function (i, attr) {
        if (attr && attr.name && attr.name.indexOf("data-cobro-") === 0) {
            var key = attr.name.replace("data-cobro-", "");
            cobros[key] = attr.value;
        }
    });

    var cobrosTexto = '';
    var keys = Object.keys(cobros);

    if (keys.length > 0) {
        keys.sort();
        keys.forEach(function (k) {
            var label = k.replace(/-/g, ' ');
            label = label.charAt(0).toUpperCase() + label.slice(1);
            cobrosTexto += label + ': S./' + cobros[k] + '\n';
        });
    } else {
        cobrosTexto = 'No hay cobros registrados.\n';
    }

    // monto principal
    var montoPrincipal = '0';
    if (cobros.hasOwnProperty('matricula')) {
        montoPrincipal = cobros['matricula'];
    } else if (keys.length > 0) {
        montoPrincipal = cobros[keys[0]];
    }

    var info_matricula =
        'MATRICULA ' + lectivo + ' - ' + today + '\n' +
        'NIVEL: ' + nivel + ' - GRADO: ' + grado + ' - SECCION: ' + seccion + '\n\n' +
        cobrosTexto + '\n' +
        'Observaciones: ' + observaciones + '\n';

    $("#pago_monto").val(montoPrincipal);
    $("#detalle").val(info_matricula);
    $("#pago_descripcion").val(info_matricula);
}

// ======================
// MENSUALIDADES (NUEVO)
// ======================

function cargarMensualidades() {
    var selectedOption = $("#matricula_id option:selected");
    var matricula_id = selectedOption.val();

    const tbody = document.querySelector("#mensualidadTable tbody");
    if (!tbody) return;

    if (!matricula_id) {
        tbody.innerHTML = "";
        return;
    }

    function toNumber(value) {
        if (value == null) return 0;
        let s = String(value).trim();
        s = s.replace(/[^\d.,-]/g, "");

        if (s.includes(",") && !s.includes(".")) {
            s = s.replace(",", ".");
        } else if (s.includes(",") && s.includes(".")) {
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

            calcularTodo();

            // Delegación de eventos
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
        error: function () {
            console.error("Error al cargar las mensualidades.");
        }
    });
}

// ======================
// ELIMINAR / DESACTIVAR
// ======================

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

// ======================
// BUSCAR APODERADO / ALUMNO
// ======================

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
                $("#apoderado_id").val(data.id);
                $("#apoderado_nombreyapellido").val(data.nombreyapellido);
                $("#apoderado_telefono").val(data.telefono);
                $("#apoderado_tipo").val(data.id_apoderado_tipo);
                $("#apoderado_documento").val(data.id_documento);
                $("#apoderado_observaciones").val(data.observaciones);

                refreshPicker("#apoderado_tipo");
                refreshPicker("#apoderado_documento");
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
                $("#alumno_id").val(data.id);
                $("#alumno_nombreyapellido").val(data.nombreyapellido);
                $("#alumno_nacimiento").val(data.nacimiento);
                $("#alumno_documento").val(data.id_documento);
                $("#alumno_sexo").val(data.id_sexo);
                $("#alumno_telefono").val(data.telefono);
                $("#alumno_observaciones").val(data.observaciones);

                refreshPicker("#alumno_documento");
                refreshPicker("#alumno_sexo");
            } else {
                alert("No se encontró un alumno con ese número de documento.");
            }
        },
        error: function () {
            alert("Ocurrió un error al buscar al alumno.");
        }
    });
}

// ======================
// MOSTRAR (EDITAR)
// ======================

function mostrar(id) {
    // 1) Marcar modo edición y pasar a formulario
    editando = true;

    // Reset pendientes y setear el id
    pending.matricula_id = null;
    pending.matricula_categoria_id = null;
    pending.referido_id = null;

    $("#matricula_detalle_id").val(id);
    MostrarFormulario();

    // 2) Pedir data al servidor
    $.ajax({
        url: link + "mostrar",
        type: "POST",
        dataType: "json",
        data: { id_matricula_detalle: id }
    })
        .done(function (resp) {

            if (resp.error) {
                alert(resp.error);
                editando = false;
                return;
            }

            const c = resp.cabecera;

            // ID principal real
            $("#matricula_detalle_id").val(c.matricula_detalle_id);

            // PENDIENTES para selects cargados por ajax (se aplican en cargarMatriculas/cargarCategorias/cargarApoderadosReferidos)
            pending.matricula_id = c.matricula_id;
            pending.matricula_categoria_id = c.matricula_categoria_id;
            pending.referido_id = c.referido_id;

            // Forzar una recarga de selects para aplicar pendientes sí o sí
            cargarMatriculas();
            cargarCategorias();
            cargarApoderadosReferidos();

            // Textos
            $("#detalle").val(c.detalle);
            $("#matricula_observaciones").val(c.matricula_observaciones);

            // Apoderado
            $("#apoderado_id").val(c.apoderado_id);
            $("#apoderado_dni").val(c.apoderado_dni);
            $("#apoderado_nombreyapellido").val(c.apoderado_nombre);
            $("#apoderado_telefono").val(c.apoderado_telefono);
            $("#apoderado_tipo").val(c.apoderado_tipo);
            $("#apoderado_documento").val(c.apoderado_documento);
            $("#apoderado_observaciones").val(c.apoderado_observaciones);

            refreshPicker("#apoderado_tipo");
            refreshPicker("#apoderado_documento");

            // Alumno
            $("#alumno_id").val(c.alumno_id);
            $("#alumno_dni").val(c.alumno_dni);
            $("#alumno_nombreyapellido").val(c.alumno_nombre);
            $("#alumno_nacimiento").val(c.alumno_nacimiento);
            $("#alumno_sexo").val(c.alumno_sexo);
            $("#alumno_documento").val(c.alumno_documento);
            $("#alumno_telefono").val(c.alumno_telefono);
            $("#alumno_observaciones").val(c.alumno_observaciones);

            refreshPicker("#alumno_sexo");
            refreshPicker("#alumno_documento");

            // Pago
            $("#pago_numeracion").val(c.pago_numeracion);
            $("#pago_fecha").val(c.pago_fecha);
            $("#pago_descripcion").val(c.pago_descripcion);
            $("#pago_monto").val(c.pago_monto);
            $("#pago_metodo_id").val(c.pago_metodo_id);
            $("#pago_observaciones").val(c.pago_observaciones);

            refreshPicker("#pago_metodo_id");

            // Mensualidades
            const tbody = document.querySelector("#mensualidadTable tbody");
            if (tbody) tbody.innerHTML = resp.mensualidad_html || "";

            // 3) Terminar modo edición (muy importante)
            editando = false;

        })
        .fail(function (xhr) {
            console.error("ERROR mostrar():", xhr.responseText);
            alert("No se pudo cargar el registro. Revisa consola (F12) -> Console.");
            editando = false;
        });
}

init();
