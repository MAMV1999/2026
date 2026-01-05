var link = "../Controlador/almacen_producto_categoria.php?op=";
var tabla;

function init() {
    $("#frm_form").on("submit", function (e) {
        guardaryeditar(e);
    });
    MostrarListado();
}

function cargarCategorias() {
    $.post(link + "listar_categorias_activas", function (r) {
        $("#categoria_id").html(r);
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

function guardaryeditar(e) {
    e.preventDefault();
    let detalles = [];

    $("#tabla_dinamica tbody tr").each(function () {
        let id = $(this).find("input[name='ids[]']").val();
        let nombre = $(this).find("input[name='nombres[]']").val();
        let categoria_id = $(this).find("select[name='categorias[]']").val();
        let precio_compra = $(this).find("input[name='precios_compra[]']").val();
        let precio_venta = $(this).find("input[name='precios_venta[]']").val();
        let estado = $(this).find("select[name='estados[]']").val();

        detalles.push({
            id: id,
            nombre: nombre,
            categoria_id: categoria_id,
            precio_compra: precio_compra,
            precio_venta: precio_venta,
            estado: estado
        });
    });

    $.ajax({
        url: link + "guardaryeditar",
        type: "POST",
        data: { detalles: JSON.stringify(detalles) },
        success: function (response) {
            alert(response);
            MostrarListado();
            limpiar();
            tabla.ajax.reload();
        },
        error: function () {
            alert("Error al guardar los registros.");
        }
    });
}


function mostrar(categoria_id) {
    $("#tabla_dinamica").html("<p>Cargando datos...</p>"); // Mensaje temporal mientras carga

    // Ejecutar ambas solicitudes AJAX simultáneamente con Promise.all
    Promise.all([
        $.post(link + "listar_categorias_activas"),
        $.post(link + "mostrar", { categoria_id: categoria_id })
    ])
        .then(function ([categoriasHTML, data]) {
            data = JSON.parse(data);

            if (data.length > 0) {
                // Construir la tabla dinámica
                let tablaHTML = `
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style='width: 5%;'>N°</th>
                            <th style='width: 40%;'>NOMBRE</th>
                            <th style='width: 15%;'>CATEGORÍA</th>
                            <th style='width: 10%;'>P. COMPRA</th>
                            <th style='width: 10%;'>P. VENTA</th>
                            <th style='width: 5%;'>STOCK</th>
                            <th style='width: 15%;'>ESTADO</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

                data.forEach((item, index) => {
                    let estadoSelect = `
                    <select class="form-control" name="estados[]">
                        <option value="1" ${item.estados == 1 ? "selected" : ""}>ACTIVADO</option>
                        <option value="0" ${item.estados == 0 ? "selected" : ""}>DESACTIVADO</option>
                    </select>
                `;

                    // Construcción de la fila de la tabla con validaciones
                    tablaHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>
                            <input type="hidden" class="form-control" value="${item.ids}" name="ids[]">
                            <input type="text" class="form-control" value="${item.nombres}" name="nombres[]" required>
                        </td>
                        <td>
                            <select class="form-control" name="categorias[]">
                                ${categoriasHTML.replace(`value="${item.categorias}"`, `value="${item.categorias}" selected`)}
                            </select>
                        </td>
                        <td>
                            <input type="number" class="form-control" value="${parseFloat(item.precios_compra).toFixed(2)}" name="precios_compra[]" min="0" step="0.01" required>
                        </td>
                        <td>
                            <input type="number" class="form-control" value="${parseFloat(item.precios_venta).toFixed(2)}" name="precios_venta[]" min="0" step="0.01" required>
                        </td>
                        <td>${item.stocks}</td>
                        <td>${estadoSelect}</td>
                    </tr>
                `;
                });

                tablaHTML += `
                    </tbody>
                </table>
            `;

                // Insertar la tabla en el contenedor
                $("#tabla_dinamica").html(tablaHTML);
            } else {
                $("#tabla_dinamica").html("<p>No se encontraron datos para esta categoría.</p>");
            }

            // Mostrar el formulario
            MostrarFormulario();
        })
        .catch(function (error) {
            console.error("Error al cargar los datos:", error);
            $("#tabla_dinamica").html("<p>Error al cargar los datos. Intenta nuevamente.</p>");
        });
}

function limpiar() {
    // Limpiar los valores de la tabla dinámica
    $("#tabla_dinamica tbody").empty();

    // Resetear cualquier mensaje de error
    $(".is-invalid").removeClass("is-invalid");

    // Restablecer el formulario
    $("#frm_form")[0].reset();

    // Opcional: resetear select de categorías y estados
    $("select[name='categorias[]']").each(function() {
        $(this).val($(this).find("option:first").val()); // Seleccionar la primera opción
    });

    $("select[name='estados[]']").each(function() {
        $(this).val("1"); // Por defecto en "ACTIVADO"
    });
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

init();
