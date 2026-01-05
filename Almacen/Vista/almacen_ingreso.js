var link = "../Controlador/almacen_ingreso.php?op=";
var tabla;
var tabla2;

function init() {
    $("#frm_form").on("submit", function (e) {
        guardaryeditar(e);
    });
    MostrarListado();
    actualizarFechaHora();
    setInterval(actualizarFechaHora, 1000);
    fecha();
    listar_usuario_apoderado();
    listar_almacen_comprobante();
    listar_almacen_metodo_pago();
}

function numeracion() {
    $.post(link + "numeracion", function (data) {
        $("#numeracion").val(data); // Asignar el valor obtenido al campo
    });
}

function listar_usuario_apoderado() {
    $.post(link + "listar_usuario_apoderado", function (r) {
        $("#usuario_apoderado_id").html(r);
    });
}

function listar_almacen_comprobante() {
    $.post(link + "listar_almacen_comprobante", function (r) {
        $("#almacen_comprobante_id").html(r);
    });
}

function listar_almacen_metodo_pago() {
    $.post(link + "listar_almacen_metodo_pago", function (r) {
        $("#almacen_metodo_pago_id").html(r);
    });
}

function fecha() {
    var now = new Date();
    var day = ("0" + now.getDate()).slice(-2);
    var month = ("0" + (now.getMonth() + 1)).slice(-2);
    var today = now.getFullYear() + "-" + (month) + "-" + (day);
    $("#fecha").val(today);
}

$(document).ready(function () {
    tabla = $("#myTable").DataTable({
        ajax: link + "listar",
    });
    tabla2 = $("#myTable2").DataTable({
        ajax: link + "listar_almacen_producto",
    });
});

function limpiar() {

}

function MostrarListado() {
    limpiar();
    $("#listado").show();
    $("#formulario").hide();
}

function MostrarFormulario() {
    $("#listado").hide();
    $("#formulario").show();
    numeracion();
}

function guardaryeditar(e) {
    e.preventDefault();

    // Obtener los datos del formulario y los productos automáticamente
    const formData = new FormData(document.getElementById("frm_form"));

    // Enviar la solicitud AJAX
    $.ajax({
        url: link + "guardar",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            alert(response);
            if (response.includes("correctamente")) {
                MostrarListado();
                tabla.ajax.reload();
            }
        },
        error: function () {
            alert("Error al intentar guardar los datos.");
        }
    });
}

function calcularTotal() {
    let total = 0; // Inicializar el total
    document.querySelectorAll('#tablaProductos tbody tr').forEach(row => {
        const cantidad = parseFloat(row.querySelector('input[name$="[stock]"]').value) || 0; // Leer cantidad
        const costoUnitario = parseFloat(row.querySelector('input[name$="[precio_unitario]"]').value) || 0; // Leer costo unitario
        total += cantidad * costoUnitario; // Sumar al total
    });
    document.getElementById('total').value = total.toFixed(2); // Asignar el total al input con 2 decimales
}


function agregardetalle(id_producto, producto, descripcion, stock, precio_compra) {
    event.preventDefault();

    // Seleccionar el tbody de la tabla con id específico
    let tbody = document.querySelector('#tablaProductos tbody');

    // Verificar si el producto ya está en la tabla
    let productoExistente = Array.from(tbody.querySelectorAll('tr')).some(row => {
        let cell = row.querySelector('td:first-child'); // Primera celda contiene el nombre del producto
        return cell && cell.dataset.id_producto === id_producto; // Comparar id_producto
    });

    if (productoExistente) {
        alert('El producto ya fue agregado.'); // Mostrar alerta o manejar el caso
        return; // Salir de la función para no duplicar el producto
    }

    // Crear una nueva fila
    let tr = document.createElement('tr');

    // Crear celdas con los datos del producto
    tr.innerHTML = `
        <td data-id_producto="${id_producto}"><input type="hidden" name="productos[${id_producto}][almacen_producto_id]" value="${id_producto}" class="form-control">${producto}</td>
        <td><input type="number" class="form-control" name="productos[${id_producto}][stock]" min="1" value="1" oninput="calcularTotal()"></td>
        <td><input type="text" class="form-control" name="productos[${id_producto}][precio_unitario]" value="${precio_compra}" oninput="calcularTotal()"></td>
        <td><input type="text" class="form-control" name="productos[${id_producto}][observaciones]" placeholder="OBSERVACIONES"></td>
        <td><button class="btn btn-danger btn-sm" onclick="eliminarProducto(this)">ELIMINAR</button></td>
    `;

    // Agregar la nueva fila al final de la tabla
    tbody.appendChild(tr);

    // Recalcular el total después de agregar
    calcularTotal();
}


function eliminarProducto(btn) {
    // Eliminar la fila asociada al botón
    let row = btn.parentElement.parentElement; // Fila actual
    row.remove();

    // Recalcular el total después de eliminar
    calcularTotal();
}

function activar(id) {
    let condicion = confirm("¿ACTIVAR?");
    if (condicion === true) {
        $.ajax({
            type: "POST",
            url: link + "activar",
            data: { id: id },
            success: function (response) {
                alert(response);
                tabla.ajax.reload(); // Recargar la tabla
            },
            error: function () {
                alert("Error al intentar activar el registro.");
            }
        });
    } else {
        alert("CANCELADO");
    }
}

function desactivar(id) {
    let condicion = confirm("¿DESACTIVAR?");
    if (condicion === true) {
        $.ajax({
            type: "POST",
            url: link + "desactivar",
            data: { id: id },
            success: function (response) {
                alert(response);
                tabla.ajax.reload(); // Recargar la tabla
            },
            error: function () {
                alert("Error al intentar desactivar el registro.");
            }
        });
    } else {
        alert("CANCELADO");
    }
}

init();
