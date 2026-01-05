var link = "../Controlador/biblioteca_libro.php?op=";
var tabla;

function init() {
    $("#frm_form").on("submit", function (e) {
        guardaryeditar(e);
    });
    MostrarListado();
}

function limpiar() {
    $("#tabla_dinamica tbody").empty();
    agregarFila();
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
    let detalles = [];

    $("#tabla_dinamica tbody tr").each(function () {
        let id = $(this).find("input[name='ids[]']").val() || null;
        let codigo = $(this).find("input[name='codigos[]']").val() || "";
        let nombre = $(this).find("input[name='nombres[]']").val() || "";
        let observaciones = $(this).find("input[name='observaciones[]']").val() || "";

        if (codigo !== "" && nombre !== "") {
            detalles.push({
                id: id,
                codigo: codigo,
                nombre: nombre,
                observaciones: observaciones
            });
        }
    });

    if (detalles.length === 0) {
        alert("Debe agregar al menos un libro antes de guardar.");
        return;
    }

    $.ajax({
        url: link + "guardaryeditar",
        type: "POST",
        data: { detalles: JSON.stringify(detalles) },
        success: function (response) {
            alert(response);
            tabla.ajax.reload();
            MostrarListado();
            limpiar();
        },
        error: function () {
            alert("Error al guardar los registros.");
        }
    });
}

function agregarFila() {
    let fila = `<tr>
        <td><input type="hidden" name="ids[]"></td>
        <td><input type="text" name="codigos[]" class="form-control" required></td>
        <td><input type="text" name="nombres[]" class="form-control" required></td>
        <td><input type="text" name="observaciones[]" class="form-control"></td>
        <td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">ELIMINAR</button></td>
    </tr>`;
    $("#tabla_dinamica tbody").append(fila);
}

function eliminarFila(fila) {
    $(fila).closest("tr").remove();
}

function mostrar(id) {
    $.ajax({
        url: link + "mostrar",
        type: "POST",
        data: { id: id },
        dataType: "json",
        success: function (data) {
            if (data) {
                MostrarFormulario();
                $("#tabla_dinamica tbody").empty();
                let fila = `<tr>
                    <td><input type="hidden" name="ids[]" value="${data.id}"></td>
                    <td><input type="text" name="codigos[]" class="form-control" value="${data.codigo}" required></td>
                    <td><input type="text" name="nombres[]" class="form-control" value="${data.nombre}" required></td>
                    <td><input type="text" name="observaciones[]" class="form-control" value="${data.observaciones || ''}"></td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">Eliminar</button></td>
                </tr>`;
                $("#tabla_dinamica tbody").append(fila);
            } else {
                alert("No se encontraron datos del libro.");
            }
        },
        error: function () {
            alert("Error al obtener los datos del libro.");
        }
    });
}

$(document).ready(function () {
    tabla = $('#myTable').DataTable({
        "ajax": {
            "url": link + "listar",
            "dataSrc": function (json) {
                return json.aaData;
            }
        }
    });
});

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
