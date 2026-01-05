var link = "../Controlador/almacen_salida.php?op=";
var tabla;
var tabla2;
var tabla3;

function init() {
    $("#frm_form").on("submit", function (e) {
        guardaryeditar(e);
    });
    MostrarListado();
    actualizarFechaHora();
    setInterval(actualizarFechaHora, 1000);
    fecha();
    hora();
    listar_almacen_comprobante();
    listar_almacen_metodo_pago();
}

function agregarapoderado(id, nombre) {
    event.preventDefault();
    $("#usuario_apoderado_id").val(id);
    $("#usuario_apoderado_nombre").val(nombre);
    $("#listar_buscador_apoderado").modal("hide");
}

function numeracion() {
    $.post(link + "numeracion", function (data) {
        $("#numeracion").val(data);
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

function hora() {
    var now = new Date();
    var hours = ("0" + now.getHours()).slice(-2);
    var minutes = ("0" + now.getMinutes()).slice(-2);
    var seconds = ("0" + now.getSeconds()).slice(-2);
    var currentTime = hours + ":" + minutes + ":" + seconds;
    $("#hora").val(currentTime);
}

$(document).ready(function () {
    tabla = $("#myTable").DataTable({
        ajax: link + "listar",
    });
    tabla2 = $("#myTable2").DataTable({
        ajax: link + "listar_almacen_producto",
    });
    tabla3 = $("#myTable3").DataTable({
        ajax: link + "listar_buscador_apoderado",
    });
});

function limpiar_apoderado() {
    $("#usuario_apoderado_id").val("");
    $("#usuario_apoderado_nombre").val("");
}

function limpiar() {
    $("#almacen_salida_id").val("");

    $("#frm_form input[type='hidden'], #frm_form input[type='text'], #frm_form input[type='number'], #frm_form input[type='date']").val("");
    $("#frm_form select").prop("selectedIndex", 0);
    $("#observaciones").val("");
    $("#tablaProductos tbody").empty();
    $("#total").val("0.00");
    $("#frm_form input, #frm_form select, #frm_form textarea").removeClass("is-invalid is-valid");
}

function MostrarListado() {
    limpiar();
    $("#listado").show();
    $("#formulario").hide();
}

function MostrarFormulario() {
    $("#listado").hide();
    $("#formulario").show();

    // Si es NUEVO, genera numeración y fecha/hora
    if ($("#almacen_salida_id").val() === "") {
        numeracion();
        fecha();
        hora();
    }
}

// NUEVO: MOSTRAR PARA EDITAR
function mostrar(id) {
    $.post(link + "mostrar", { id: id }, function (resp) {
        let data;
        try {
            data = JSON.parse(resp);
        } catch (e) {
            alert("Error al leer respuesta del servidor.");
            return;
        }

        if (data.error) {
            alert(data.error);
            return;
        }

        // Mostrar formulario
        $("#listado").hide();
        $("#formulario").show();

        // Cabecera
        const c = data.cabecera;

        $("#almacen_salida_id").val(c.id);
        $("#usuario_apoderado_id").val(c.usuario_apoderado_id);
        $("#usuario_apoderado_nombre").val(c.nombre_apoderado);

        $("#almacen_comprobante_id").val(c.almacen_comprobante_id);
        $("#numeracion").val(c.numeracion);
        $("#fecha").val(c.fecha);

        $("#almacen_metodo_pago_id").val(c.almacen_metodo_pago_id);
        $("#total").val(parseFloat(c.total).toFixed(2));
        $("#observaciones").val(c.observaciones);

        // Detalle: reconstruir tabla
        $("#tablaProductos tbody").empty();

        data.detalle.forEach(function (d) {
            let tbody = document.querySelector('#tablaProductos tbody');
            let tr = document.createElement('tr');

            tr.innerHTML = `
                <td data-id_producto="${d.almacen_producto_id}">
                    <input type="hidden" name="productos[${d.almacen_producto_id}][almacen_producto_id]" value="${d.almacen_producto_id}" class="form-control">
                    ${d.producto}
                </td>
                <td>
                    <input type="number" class="form-control" name="productos[${d.almacen_producto_id}][stock]" min="1" value="${d.stock}" oninput="calcularTotal()">
                </td>
                <td>
                    <input type="text" class="form-control" name="productos[${d.almacen_producto_id}][precio_unitario]" value="${d.precio_unitario}" oninput="calcularTotal()">
                </td>
                <td>
                    <input type="text" class="form-control" name="productos[${d.almacen_producto_id}][observaciones]" value="${d.observaciones || ''}" placeholder="OBSERVACIONES">
                </td>
                <td>
                    <button class="btn btn-danger btn-sm" onclick="eliminarProducto(this)">ELIMINAR</button>
                </td>
            `;

            tbody.appendChild(tr);
        });

        calcularTotal();

    });
}

function guardaryeditar(e) {
    e.preventDefault();

    const formData = new FormData(document.getElementById("frm_form"));

    $.ajax({
        url: link + "guardaryeditar",
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
            alert("Error al intentar guardar/editar los datos.");
        }
    });
}

function calcularTotal() {
    let total = 0;
    document.querySelectorAll('#tablaProductos tbody tr').forEach(row => {
        const cantidad = parseFloat(row.querySelector('input[name$="[stock]"]').value) || 0;
        const costoUnitario = parseFloat(row.querySelector('input[name$="[precio_unitario]"]').value) || 0;
        total += cantidad * costoUnitario;
    });
    document.getElementById('total').value = total.toFixed(2);
}

function agregardetalle(id_producto, producto, descripcion, stock, precio_venta) {
    event.preventDefault();

    let tbody = document.querySelector('#tablaProductos tbody');

    let productoExistente = Array.from(tbody.querySelectorAll('tr')).some(row => {
        let cell = row.querySelector('td:first-child');
        return cell && cell.dataset.id_producto === id_producto;
    });

    if (productoExistente) {
        alert('El producto ya fue agregado.');
        return;
    }

    let tr = document.createElement('tr');

    tr.innerHTML = `
        <td data-id_producto="${id_producto}">
            <input type="hidden" name="productos[${id_producto}][almacen_producto_id]" value="${id_producto}" class="form-control">
            ${producto}
        </td>
        <td><input type="number" class="form-control" name="productos[${id_producto}][stock]" min="1" max="${stock}" value="1" oninput="calcularTotal()"></td>
        <td><input type="text" class="form-control" name="productos[${id_producto}][precio_unitario]" value="${precio_venta}" oninput="calcularTotal()"></td>
        <td><input type="text" class="form-control" name="productos[${id_producto}][observaciones]" placeholder="OBSERVACIONES"></td>
        <td><button class="btn btn-danger btn-sm" onclick="eliminarProducto(this)">ELIMINAR</button></td>
    `;

    tbody.appendChild(tr);
    calcularTotal();
}

function eliminarProducto(btn) {
    let row = btn.parentElement.parentElement;
    row.remove();
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
                tabla.ajax.reload();
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
                tabla.ajax.reload();
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
