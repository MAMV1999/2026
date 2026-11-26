var link = "../Controlador/registro_utiles_detalle.php?op=";
var tabla;

function init() {
    $("#frm_form").on("submit", function (e) {
        guardaryeditar(e);
    });

    $("#utiles_list").on("click", ".stock-menos, .stock-mas", function () {
        var grupo = $(this).closest(".input-group");
        var campo = grupo.find(".stock-cantidad");
        var stock = Math.max(0, parseInt(campo.val(), 10) || 0);
        stock = Math.max(0, stock + ($(this).hasClass("stock-mas") ? 1 : -1));
        campo.val(stock).trigger("input");
    });

    $("#utiles_list").on("input change", ".stock-cantidad", function () {
        var campo = $(this);
        var stock = Math.max(0, parseInt(campo.val(), 10) || 0);
        if (campo.val() !== "" && Number(campo.val()) !== stock) {
            campo.val(stock);
        }
        campo.closest(".input-group").find(".stock-menos").prop("disabled", stock === 0);
    });

    MostrarListado();
}

$(document).ready(function () {
    tabla = $("#myTable").DataTable({
        ajax: link + "listar",
    });
});

function limpiar() {
    $("#matricula_detalle_id").val("");
    $("#lectivo").text("");
    $("#nivel").text("");
    $("#grado").text("");
    $("#seccion").text("");
    $("#apoderado").text("");
    $("#alumno").text("");
    $("#categoria_matricula").text("");

    $("#utiles_list").html("");
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
        link + "mostrar",
        { id: id },
        function (data) {
            data = JSON.parse(data);

            if (data.error) {
                alert(data.error);
                return;
            }

            // Info
            $("#lectivo").text(data.lectivo || "N/A");
            $("#nivel").text(data.nivel || "N/A");
            $("#grado").text(data.grado || "N/A");
            $("#seccion").text(data.seccion || "N/A");
            $("#apoderado").text(data.apoderado || "N/A");
            $("#categoria_matricula").text(data.categoria_matricula || "N/A");
            $("#alumno").text(data.alumno || "N/A");

            $("#matricula_detalle_id").val(data.id_matricula_detalle);

            // Útiles
            var utiles = data.utiles || [];
            var detalles = data.detalles || {};
            var html = "";
            var numero = 1;

            if (utiles.length === 0) {
                html += '<tr><td colspan="5" class="text-center"><b>NO HAY ÚTILES REGISTRADOS PARA ESTA MATRÍCULA</b></td></tr>';
                $("#utiles_list").html(html);
                MostrarFormulario();
                return;
            }

            utiles.forEach(function (util) {
                var detalle = detalles[util.id] || { stock: 0, observaciones: "" };

                html += "<tr>";
                html += "<td>" + numero + "</td>";
                html += "<td>" + (util.nombre_util || "") + "</td>";

                var stock = Math.max(0, parseInt(detalle.stock, 10) || 0);

                html += '<td style="width:180px;">' +
                        '<div class="input-group">' +
                        '<button type="button" class="btn btn-outline-secondary stock-menos" aria-label="Restar cantidad"' + (stock === 0 ? ' disabled' : '') + '>-</button>' +
                        '<input type="number" min="0" step="1" name="utiles[' + util.id + '][stock]" value="' + stock + '" class="form-control text-center stock-cantidad" placeholder="CANT." />' +
                        '<button type="button" class="btn btn-outline-secondary stock-mas" aria-label="Sumar cantidad">+</button>' +
                        '</div>' +
                        "</td>";

                html += '<td>' +
                        '<input type="text" name="utiles[' + util.id + '][observaciones]" value="' + (detalle.observaciones || "") + '" class="form-control" placeholder="OBSERVACIONES" />' +
                        "</td>";

                html += "</tr>";
                numero++;
            });

            $("#utiles_list").html(html);

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
