var link = "../Controlador/mensualidad_x_mes.php?op=";
var tabla;

function init() {
    $("#frm_form").on("submit", function (e) {
        guardaryeditar(e);
    });
    MostrarListado();
    actualizarFechaHora();
    setInterval(actualizarFechaHora, 1000);
}

$(document).ready(function () {
    tabla = $("#myTable").DataTable({
        ajax: link + "listar",
    });
});

function limpiar() {
    $("#formulario-detalles tbody").empty();
}

function guardaryeditar(e) {
    e.preventDefault();
    let detalles = [];

    // Recorre cada fila de la tabla para recopilar los datos
    $("#formulario-detalles tbody tr").each(function () {
        let id = $(this).find("input[name^='id']").val();
        let monto = $(this).find("input[name^='monto']").val();
        let pagado = $(this).find("input[name^='pagado']").is(":checked") ? 1 : 0; // Valor del checkbox

        detalles.push({
            id: id,
            monto: monto,
            pagado: pagado
        });
    });

    // Envío de datos al servidor
    $.ajax({
        url: link + "guardaryeditar",
        type: "POST",
        data: { detalles: JSON.stringify(detalles) },
        success: function (response) {
            alert(response);
            MostrarListado();
            limpiar(); // Limpia el formulario y otros elementos.
            tabla.ajax.reload();
        },
        error: function () {
            alert("Error al guardar los registros.");
        }
    });
}

function mostrar(id_mensualidad_mes) {
    $.post(link + "mostrar", { id_mensualidad_mes: id_mensualidad_mes }, function (data) {
        let detalles = JSON.parse(data); // Convierte los datos JSON en un arreglo de objetos.
        limpiar(); // Limpia el formulario y otros elementos.

        let tbody = $("#formulario-detalles tbody");
        tbody.empty(); // Limpia los datos previos.

        // Itera sobre los datos y genera filas en el formulario.
        detalles.forEach((detalle, index) => {
            let fila = `
                    <tr>
                        <td>${detalle.mensualidad_nombre} ${detalle.institucion_lectivo_nombre} - ${detalle.grado_nombre}</td>
                        <td>${detalle.apoderado_nombre}</td>
                        <td>${detalle.apoderado_telefono}</td>
                        <td>${detalle.alumno_nombre}</td>
                        <td>${detalle.alumno_numero_documento}</td>
                        <td><input type="text" class="form-control" name="monto${index}" value="${detalle.monto}" style="width: 90px; height: auto;"></td>
                        <td>
                            <input type="hidden" name="id${index}" value="${detalle.mensualidad_detalle_id}">
                            <input type="checkbox" name="pagado${index}" ${detalle.pagado == 1 ? "checked" : ""} style="width: 30px; height: 30px;">
                        </td>
                    </tr>
            `;
            tbody.append(fila);
        });

        MostrarFormulario(); // Muestra el formulario con los datos cargados.
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
