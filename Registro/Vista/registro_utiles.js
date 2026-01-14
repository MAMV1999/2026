var link = "../Controlador/registro_utiles.php?op=";
var tabla;

function init() {
    $("#frm_form").on("submit", function (e) {
        guardaryeditar(e);
    });

    MostrarListado();
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
                    <td style="width:70%;"><input type="text" name="nombres[]" class="form-control" value="${nombre}" required></td>
                    <td style="width:20%;"><input type="text" name="observaciones[]" class="form-control" value="${observaciones}"></td>
                    <td style="width:5%;"><button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">ELIMINAR</button></td>
                </tr>`;
    $("#tabla_dinamica tbody").append(fila);
}

function eliminarFila(btn) {
    $(btn).closest("tr").remove();
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

function guardaryeditar(e) {
    e.preventDefault();

    let id_matricula = $("#id_matricula").val();
    if (!id_matricula) {
        alert("Primero debes seleccionar una matrícula (botón EDITAR).");
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
            if (response.toLowerCase().includes("correctamente")) {
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
