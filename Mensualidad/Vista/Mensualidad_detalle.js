var link = "../Controlador/Mensualidad_detalle.php?op=";
var tabla;

function init() {
    $("#frm_form").on("submit", function (e) {
        guardaryeditar(e);
    });
    MostrarListado();
    cargar_meses();
    cargar_matricula_detalles();
    actualizarFechaHora();
    setInterval(actualizarFechaHora, 1000);
}

function cargar_meses() {
    $.post(link + "listar_meses_activos", function (r) {
        $("#matricula_mes_id").html(r);
        $("#matricula_mes_id").selectpicker("refresh");
    });
}

function cargar_matricula_detalles() {
    $.post(link + "listar_matricula_detalles_activos", function (r) {
        $("#id_matricula_detalle").html(r);
        $("#id_matricula_detalle").selectpicker("refresh");
    });
}

$(document).ready(function () {
    tabla = $("#myTable").DataTable({
        ajax: link + "listar",
    });
});

function limpiar() {
    // Limpiar campos generales
    $("#id").val("");
    $("#lectivo").text("");
    $("#nivel").text("");
    $("#grado").text("");
    $("#seccion").text("");

    // Limpiar datos del apoderado
    $("#apoderado_tipo_documento").text("");
    $("#apoderado_numerodocumento").text("");
    $("#apoderado_nombreyapellido").text("");
    $("#apoderado_telefono").text("");

    // Limpiar datos del alumno
    $("#alumno_tipo_documento").text("");
    $("#alumno_numerodocumento").text("");
    $("#alumno_nombreyapellido").text("");

    // Limpiar detalles relacionados
    $("#detallesRelacionados").html("");

    // Reiniciar selectores dinámicos (meses y detalles de matrícula)
    cargar_meses();
    cargar_matricula_detalles();
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

    $("#detallesRelacionados tr").each(function () {
        let id = $(this).find("input[name^='id']").val();
        let monto = $(this).find("input[name^='monto']").val();
        let observaciones = $(this).find("input[name^='observaciones']").val();
        let pagado = $(this).find("input[name^='pagado']:checked").val();

        detalles.push({
            id: id,
            monto: monto,
            observaciones: observaciones,
            pagado: pagado
        });
    });

    $.ajax({
        url: link + "guardaryeditar",
        type: "POST",
        data: { detalles: JSON.stringify(detalles) },
        success: function (response) {
            alert(response);
            MostrarListado();
            tabla.ajax.reload();
        }
    });
}

function mostrar(id) {
    limpiar(); // Limpia antes de mostrar el nuevo formulario

    $.post(
        link + "mostrar",
        { id: id },
        function (data, status) {
            data = JSON.parse(data);

            if (data) {
                MostrarFormulario();
                $("#id").val(data.general.id_matricula_detalle);

                $("#lectivo").text(data.general.lectivo);
                $("#nivel").text(data.general.nivel);
                $("#grado").text(data.general.grado);
                $("#seccion").text(data.general.seccion);

                // Llenar datos del apoderado
                $("#apoderado_tipo_documento").text(data.general.apoderado.tipo_documento);
                $("#apoderado_numerodocumento").text(data.general.apoderado.numerodocumento);
                $("#apoderado_nombreyapellido").text(data.general.apoderado.nombreyapellido);
                $("#apoderado_telefono").text(data.general.apoderado.telefono);

                // Llenar datos del alumno
                $("#alumno_tipo_documento").text(data.general.alumno.tipo_documento);
                $("#alumno_numerodocumento").text(data.general.alumno.numerodocumento);
                $("#alumno_nombreyapellido").text(data.general.alumno.nombreyapellido);

                // Llenar detalles relacionados
                let detalles = "";
                data.detalles.forEach((item, index) => {
                    detalles += `
                        <tr>
                            <td style='width: 5%;'>${item.matricula_mes_id}<input type="hidden" readonly class="form-control" name="id${index}" id="id${index}" value="${item.id}"></td>
                            <td style='width: 15%;'>${item.mes}</td>
                            <td style='width: 15%;'>${item.fecha_vencimiento}</td>
                            <td style='width: 15%;'><input type="text" class="form-control" name="monto${index}" id="monto${index}" value="${item.monto}"></td>
                            <td style='width: 20%;'>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="pagado${index}" value="1" ${item.pagado == 1 ? "checked" : ""}>
                                    <label class="form-check-label">PAGADO</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="pagado${index}" value="0" ${item.pagado == 0 ? "checked" : ""}>
                                    <label class="form-check-label">PENDIENTE</label>
                                </div>
                            </td>
                            <td style='width: 30%;'>
                                <input type="text" class="form-control" placeholder="OBSERVACIONES" name="observaciones${index}" id="observaciones${index}" value="${item.observaciones}">
                            </td>
                        </tr>
                    `;
                });

                $("#detallesRelacionados").html(detalles);
            }
        }
    );
}

init();
