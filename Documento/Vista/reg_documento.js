var link = "../Controlador/reg_documento.php?op=";
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
    // Limpia los campos de texto e inputs ocultos
    $("#id").val("");
    $("#matricula_detalle_id").val("");
    $("#lectivo").text("");
    $("#nivel").text("");
    $("#grado").text("");
    $("#seccion").text("");
    $("#apoderado").text("");
    $("#alumno").text("");
    $("#categoria_matricula").text("");

    // Limpia la tabla de documentos
    $("#documento_list").html("");
}

function guardaryeditar(e) {
    e.preventDefault();

    var formData = $("#frm_form").serialize();

    $.ajax({
        url: link + "guardar",
        type: "POST",
        data: formData,
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



function mostrar(id) {
    $.post(
        link + "mostrar", // Llama al controlador con la acción 'mostrar'
        { id: id }, // Parámetro enviado al servidor
        function (data, status) {
            data = JSON.parse(data); // Asegúrate de que los datos sean un JSON válido

            console.log(data); // Debug: Verifica los datos recibidos

            // Mostrar información personal
            $("#lectivo").text(data.lectivo || "N/A");
            $("#nivel").text(data.nivel || "N/A");
            $("#grado").text(data.grado || "N/A");
            $("#seccion").text(data.seccion || "N/A");
            $("#apoderado").text(data.apoderado || "N/A");
            $("#categoria_matricula").text(data.categoria_matricula || "N/A");
            $("#alumno").text(data.alumno || "N/A");
            $("#id_matricula_detalle").val(data.id_matricula_detalle); // Ya existe en tu función `mostrar`


            // Mostrar documentos
            var documentos = data.documentos;
            var detalles = data.detalles || {}; // Garantizar que detalles no sea null
            var html = '';
            var numero = 1;

            documentos.forEach(function (documento) {
                var detalle = detalles[documento.id] || { entregado: 0, observaciones: "" };

                html += '<tr>';
                html += '<td>' + numero + '</td>'; // Número de fila
                html += '<td>' + documento.iniciales_responsable + '</td>'; // Iniciales del responsable
                html += '<td>' + documento.nombre_documento + '</td>'; // Nombre del documento
                html += '<td>' + documento.obligatorio_marcado + '</td>'; // Indicador de obligatorio
                html += '<td>';
                html += '<label class="custom-radio">';
                html += '<input type="radio" name="documentos[' + documento.id + '][entregado]" value="1"' + (detalle.entregado == 1 ? ' checked' : '') + '> SÍ</label>';
                html += '&nbsp;';
                html += '<label class="custom-radio">';
                html += '<input type="radio" name="documentos[' + documento.id + '][entregado]" value="0"' + (detalle.entregado == 0 ? ' checked' : '') + '> NO</label>';
                html += '</td>';
                html += '<td><input type="text" name="documentos[' + documento.id + '][observaciones]" value="' + (detalle.observaciones || '') + '" class="form-control" placeholder="OBSERVACIONES"></td>';
                html += '</tr>';

                numero++;
            });

            $("#documento_list").html(html);

            // Mostrar el formulario
            MostrarFormulario();
        }
    ).fail(function () {
        alert("Error al obtener los datos del servidor.");
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
