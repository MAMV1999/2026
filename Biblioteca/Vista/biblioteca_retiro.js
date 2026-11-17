var link = "../Controlador/biblioteca_retiro.php?op=";
var tabla;
var tabla2;

function init() {
    $("#frm_form").on("submit", function (e) {
        guardaryeditar(e);
    });

    listar_docentes();
    MostrarListado();
    actualizarFechaHora();
    setInterval(actualizarFechaHora, 1000);
}

function fecha() {
    var now = new Date();
    var day = ("0" + now.getDate()).slice(-2);
    var month = ("0" + (now.getMonth() + 1)).slice(-2);
    var today = now.getFullYear() + "-" + month + "-" + day;
    $("#fecha").val(today);
}

function listar_docentes() {
    $.post(link + "listar_docentes", function (r) {
        $("#usuario_docente_id").html(r);
    });
}

$(document).ready(function () {
    tabla = $("#myTable").DataTable({
        ajax: link + "listar",
        order: [[3, "desc"]]
    });

    tabla2 = $("#myTable2").DataTable({
        ajax: link + "listar_libros"
    });
});

function limpiar() {
    $("#biblioteca_retiro_id").val("");
    $("#usuario_docente_id").prop("selectedIndex", 0);
    $("#fecha").val("");
    $("#observaciones").val("");
    $("#tablaLibros tbody").empty();
}

function MostrarListado() {
    limpiar();
    $("#listado").show();
    $("#formulario").hide();
}

function MostrarFormulario() {
    $("#listado").hide();
    $("#formulario").show();

    if ($("#biblioteca_retiro_id").val() === "") {
        fecha();
    }
}

function agregardetalle(id_libro) {
    if (window.event) {
        window.event.preventDefault();
    }

    let existe = false;
    $("#tablaLibros tbody tr").each(function () {
        if (String($(this).data("id_libro")) === String(id_libro)) {
            existe = true;
        }
    });

    if (existe) {
        alert("El libro ya fue agregado.");
        return;
    }

    $.post(link + "mostrar_libro", { id: id_libro }, function (resp) {
        let libro;
        try {
            libro = JSON.parse(resp);
        } catch (e) {
            alert("Error al obtener los datos del libro.");
            return;
        }

        if (!libro || parseInt(libro.stock) <= 0) {
            alert("El libro no tiene stock disponible.");
            return;
        }

        agregarFilaLibro(libro.id, libro.codigo, libro.nombre, libro.stock, 1, "");
    });
}

function agregarFilaLibro(id, codigo, nombre, stock, cantidad, observaciones) {
    let tr = `
        <tr data-id_libro="${id}">
            <td>
                ${codigo}
                <input type="hidden" name="libros[${id}][biblioteca_libro_id]" value="${id}">
            </td>
            <td>${nombre}</td>
            <td>${stock}</td>
            <td><input type="number" class="form-control" name="libros[${id}][cantidad]" min="1" max="${stock}" value="${cantidad}" required></td>
            <td><input type="text" class="form-control" name="libros[${id}][observaciones]" value="${observaciones || ''}"></td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarLibro(this)">ELIMINAR</button></td>
        </tr>`;

    $("#tablaLibros tbody").append(tr);
}

function eliminarLibro(btn) {
    $(btn).closest("tr").remove();
}

function guardaryeditar(e) {
    e.preventDefault();

    if ($("#tablaLibros tbody tr").length === 0) {
        alert("Debe agregar al menos un libro al retiro.");
        return;
    }

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
                tabla2.ajax.reload();
            }
        },
        error: function () {
            alert("Error al guardar el retiro.");
        }
    });
}

function mostrar(id) {
    $.post(link + "mostrar", { id: id }, function (resp) {
        let data;
        try {
            data = JSON.parse(resp);
        } catch (e) {
            alert("Error al leer la respuesta del servidor.");
            return;
        }

        if (data.error) {
            alert(data.error);
            return;
        }

        if (parseInt(data.cabecera.devuelto) === 1 || parseInt(data.cabecera.estado) !== 1) {
            alert("Solo se pueden editar retiros pendientes y activos.");
            return;
        }

        $("#listado").hide();
        $("#formulario").show();

        $("#biblioteca_retiro_id").val(data.cabecera.id);
        $("#usuario_docente_id").val(data.cabecera.usuario_docente_id);
        $("#fecha").val(data.cabecera.fecha);
        $("#observaciones").val(data.cabecera.observaciones || "");
        $("#tablaLibros tbody").empty();

        data.detalle.forEach(function (d) {
            let stockParaEditar = parseInt(d.stock_actual) + parseInt(d.cantidad);
            agregarFilaLibro(
                d.biblioteca_libro_id,
                d.codigo,
                d.libro,
                stockParaEditar,
                d.cantidad,
                d.observaciones || ""
            );
        });
    });
}

function devolver(id) {
    if (confirm("¿CONFIRMA QUE EL DOCENTE DEVOLVIÓ TODOS LOS LIBROS DE ESTE RETIRO? EL STOCK SERÁ RESTITUIDO.")) {
        $.post(link + "devolver", { id: id }, function (datos) {
            alert(datos);
            tabla.ajax.reload();
            tabla2.ajax.reload();
        });
    }
}

function verRecibo(id) {
    var urlRecibo = "../../Reportes/Vista/biblioteca_retiro_recibo.php?id=" + encodeURIComponent(id);
    $("#iframeRecibo").attr("src", urlRecibo);
    $("#btnAbrirRecibo").attr("href", urlRecibo);

    var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalRecibo"));
    modal.show();
}

function desactivar(id) {
    if (confirm("¿ANULAR ESTE RETIRO? SI AÚN ESTÁ PENDIENTE, LOS LIBROS SERÁN DEVUELTOS AL STOCK.")) {
        $.post(link + "desactivar", { id: id }, function (datos) {
            alert(datos);
            tabla.ajax.reload();
            tabla2.ajax.reload();
        });
    }
}

function activar(id) {
    if (confirm("¿ACTIVAR ESTE RETIRO? SI NO FUE DEVUELTO, EL STOCK SERÁ DESCONTADO NUEVAMENTE.")) {
        $.post(link + "activar", { id: id }, function (datos) {
            alert(datos);
            tabla.ajax.reload();
            tabla2.ajax.reload();
        });
    }
}

init();