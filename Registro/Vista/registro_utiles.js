var link = "../Controlador/registro_utiles.php?op=";
var tabla;

function init() {
    $("#frm_form").on("submit", function (e) {
        guardaryeditar(e);
    });

    MostrarListado();

    // Botón del modal: Agregar en bloque
    $("#btn_aplicar_bloque").on("click", function () {
        aplicarBloqueATabla();
    });
}

$(document).ready(function () {
    tabla = $("#myTable").DataTable({
        ajax: link + "listar",
    });
});

function limpiar() {
    $("#id_matricula").val("");
    $("#titulo_matricula").html("MATRÍCULAS - FORMULARIO");
    $("#tabla_dinamica tbody").empty();

    // limpiar modal
    $("#txt_bloque").val("");
    $("#chk_reemplazar").prop("checked", false);
    $("#bloque_info").html("");
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

function agregarFila(id = "", nombre = "", observaciones = "") {
    let fila = `<tr>
                    <td style="width:5%;"><input type="hidden" name="ids[]" value="${id}"></td>
                    <td style="width:70%;"><input type="text" name="nombres[]" class="form-control" value="${escapeHtml(nombre)}" required></td>
                    <td style="width:20%;"><input type="text" name="observaciones[]" class="form-control" value="${escapeHtml(observaciones)}"></td>
                    <td style="width:5%;"><button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">ELIMINAR</button></td>
                </tr>`;
    $("#tabla_dinamica tbody").append(fila);
}

function eliminarFila(btn) {
    $(btn).closest("tr").remove();
}

// Escape básico para evitar romper HTML al pegar texto con caracteres especiales
function escapeHtml(text) {
    if (text === null || text === undefined) return "";
    return String(text)
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}

// Quitar viñetas / numeración comunes al inicio (•, -, *, 1., 1), etc.)
function normalizarLinea(linea) {
    if (!linea) return "";
    let x = String(linea).trim();

    // elimina BOM o caracteres raros iniciales
    x = x.replace(/^\uFEFF/, "");

    // elimina viñetas / numeración
    // ejemplos: "• Lapiz", "- Lapiz", "* Lapiz", "1. Lapiz", "1) Lapiz"
    x = x.replace(/^(\•|\-|\*|\—|\–)\s+/, "");
    x = x.replace(/^\d+\s*[\.\)]\s+/, "");

    return x.trim();
}

// CARGAR FORMULARIO POR MATRÍCULA
function mostrar(id_matricula) {
    $.ajax({
        url: link + "mostrar",
        type: "POST",
        data: { id_matricula: id_matricula },
        dataType: "json",
        success: function (resp) {
            if (!resp || !resp.matricula) {
                alert("No se pudo obtener la matrícula (verifica estados en 1).");
                return;
            }

            // Setear matrícula seleccionada
            $("#id_matricula").val(resp.matricula.id);

            // Título con datos
            let titulo = resp.matricula.lectivo + " / " + resp.matricula.nivel + " / " + resp.matricula.grado + " / " + resp.matricula.seccion;
            $("#titulo_matricula").html("ÚTILES ESCOLARES - " + titulo);

            // Mostrar formulario y cargar filas
            MostrarFormulario();
            $("#tabla_dinamica tbody").empty();

            if (resp.detalles && resp.detalles.length > 0) {
                resp.detalles.forEach(function (d) {
                    agregarFila(d.id, d.nombre, d.observaciones || "");
                });
            } else {
                // si no hay registros, dejamos una fila lista
                agregarFila();
            }
        },
        error: function () {
            alert("Error al obtener datos de la matrícula.");
        }
    });
}

/**
 * ABRIR MODAL "BLOQUE"
 * - Carga la matrícula (igual que EDITAR)
 * - Abre el modal para pegar lista
 */
function abrirModalBloque(id_matricula) {
    // Cargar formulario de esa matrícula
    mostrar(id_matricula);

    // Reset modal
    $("#txt_bloque").val("");
    $("#chk_reemplazar").prop("checked", false);
    $("#bloque_info").html("Pega tu lista: cada línea será un útil distinto.");

    // Abrir modal (Bootstrap 5)
    var modalEl = document.getElementById("modalBloque");
    var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
}

/**
 * Toma el textarea, separa por saltos de línea y crea filas.
 * Si "Reemplazar" está activo, borra la tabla actual y vuelve a cargar solo lo pegado.
 */
function aplicarBloqueATabla() {
    let id_matricula = $("#id_matricula").val();
    if (!id_matricula) {
        alert("Primero selecciona una matrícula (EDITAR o BLOQUE).");
        return;
    }

    let texto = $("#txt_bloque").val() || "";
    // soporta saltos \r\n (Windows) y \n (Linux)
    let lineas = texto.split(/\r?\n/).map(normalizarLinea).filter(l => l !== "");

    if (lineas.length === 0) {
        alert("No hay ítems válidos. Pega al menos una línea con texto.");
        return;
    }

    let reemplazar = $("#chk_reemplazar").is(":checked");

    if (reemplazar) {
        $("#tabla_dinamica tbody").empty();
    } else {
        // Si la tabla solo tenía la fila vacía inicial (sin nombre), la limpiamos para no duplicar
        let filas = $("#tabla_dinamica tbody tr");
        if (filas.length === 1) {
            let nombre0 = $(filas[0]).find("input[name='nombres[]']").val() || "";
            let obs0 = $(filas[0]).find("input[name='observaciones[]']").val() || "";
            let id0 = $(filas[0]).find("input[name='ids[]']").val() || "";
            if (id0 === "" && nombre0.trim() === "" && obs0.trim() === "") {
                $("#tabla_dinamica tbody").empty();
            }
        }
    }

    // Agregar líneas como filas
    lineas.forEach(function (nombre) {
        agregarFila("", nombre, "");
    });

    // Cerrar modal
    var modalEl = document.getElementById("modalBloque");
    var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.hide();

    // limpiar textarea para próximo uso
    $("#txt_bloque").val("");

    alert("Se agregaron " + lineas.length + " ítems a la tabla.");
}

function guardaryeditar(e) {
    e.preventDefault();

    let id_matricula = $("#id_matricula").val();
    if (!id_matricula) {
        alert("Primero debes seleccionar una matrícula (botón EDITAR o BLOQUE).");
        return;
    }

    let detalles = [];

    $("#tabla_dinamica tbody tr").each(function () {
        let id = $(this).find("input[name='ids[]']").val() || null;
        let nombre = $(this).find("input[name='nombres[]']").val() || "";
        let observaciones = $(this).find("input[name='observaciones[]']").val() || "";

        if (nombre.trim() !== "") {
            detalles.push({
                id: id,
                nombre: nombre,
                observaciones: observaciones
            });
        }
    });

    if (detalles.length === 0) {
        alert("Debes agregar al menos un útil antes de guardar.");
        return;
    }

    $.ajax({
        url: link + "guardaryeditar",
        type: "POST",
        data: {
            id_matricula: id_matricula,
            detalles: JSON.stringify(detalles)
        },
        success: function (response) {
            alert(response);
            if (String(response).toLowerCase().includes("correctamente")) {
                tabla.ajax.reload();
                MostrarListado();
            }
        },
        error: function () {
            alert("Error al guardar los útiles.");
        }
    });
}

init();
