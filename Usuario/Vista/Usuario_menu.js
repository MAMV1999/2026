var link = "../Controlador/Usuario_menu.php?op=";
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
        let nombre = $(this).find("input[name='nombres[]']").val() || "";
        let icono = $(this).find("input[name='iconos[]']").val() || "";
        let ruta = $(this).find("input[name='rutas[]']").val() || "";
        let observaciones = $(this).find("input[name='observaciones[]']").val() || "";

        if (nombre !== "") {
            detalles.push({
                id: id,
                nombre: nombre,
                icono: icono,
                ruta: ruta,
                observaciones: observaciones
            });
        }
    });

    if (detalles.length === 0) {
        alert("Debe agregar al menos un menú antes de guardar.");
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
        <td><input type="text" name="nombres[]" class="form-control" required></td>
        <td><input type="text" name="iconos[]" class="form-control"></td>
        <td><input type="text" name="rutas[]" class="form-control"></td>
        <td><input type="text" name="observaciones[]" class="form-control"></td>
        <td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">Eliminar</button></td>
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
                    <td><input type="text" name="nombres[]" class="form-control" value="${data.nombre || ''}" required></td>
                    <td><input type="text" name="iconos[]" class="form-control" value="${data.icono || ''}"></td>
                    <td><input type="text" name="rutas[]" class="form-control" value="${data.ruta || ''}"></td>
                    <td><input type="text" name="observaciones[]" class="form-control" value="${data.observaciones || ''}"></td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">ELIMINAR</button></td>
                </tr>`;

                $("#tabla_dinamica tbody").append(fila);

            } else {
                alert("No se encontraron datos del menú.");
            }
        },
        error: function () {
            alert("Error al obtener los datos del menú.");
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

function editarTodo() {
    MostrarFormulario();
    $("#tabla_dinamica tbody").empty();

    $.ajax({
        url: link + "listar_todos",
        type: "GET",
        dataType: "json",
        success: function (rows) {
            if (!rows || rows.length === 0) {
                alert("No hay registros para editar.");
                agregarFila();
                return;
            }

            let html = "";
            for (let i = 0; i < rows.length; i++) {
                let r = rows[i];

                html += `<tr>
                    <td><input type="hidden" name="ids[]" value="${r.id}"></td>
                    <td><input type="text" name="nombres[]" class="form-control" value="${r.nombre || ''}" required></td>
                    <td><input type="text" name="iconos[]" class="form-control" value="${r.icono || ''}"></td>
                    <td><input type="text" name="rutas[]" class="form-control" value="${r.ruta || ''}"></td>
                    <td><input type="text" name="observaciones[]" class="form-control" value="${r.observaciones || ''}"></td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">Eliminar</button>
                    </td>
                </tr>`;
            }

            $("#tabla_dinamica tbody").html(html);
        },
        error: function () {
            alert("Error al cargar los registros para editar todo.");
            agregarFila();
        }
    });
}


init();
