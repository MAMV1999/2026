var link = "../Controlador/mensualidad_x_grupo.php?op=";
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
    // Limpia el contenido del acordeón dinámico
    $("#accordionExample").empty();

    // Limpia cualquier campo de texto o inputs en el formulario
    $("#frm_form")[0].reset();

    // Opcionalmente, puedes reiniciar selectores dinámicos o elementos adicionales
    // Si usas librerías como SelectPicker o similares, refresca el estado:
    // $('.selectpicker').selectpicker('refresh');
}


function guardaryeditar(e) {
    e.preventDefault();
    let detalles = [];

    $("#accordionExample .accordion-item").each(function () {
        $(this).find("tbody tr").each(function () {
            let id = $(this).find("input[name^='id-']").val(); // Verifica este selector
            let monto = $(this).find("input[name^='monto-']").val(); // Verifica este selector
            let pagado = $(this).find("input[name^='pagado-']:checked").val(); // Verifica este selector
            let observaciones = $(this).find("input[name^='observaciones-']").val(); // Verifica este selector

            detalles.push({
                id: id,
                monto: monto,
                pagado: pagado,
                observaciones: observaciones
            });
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
        }
    });
}

function mostrar(id_apoderado) {
    $.post(link + "mostrar", { id_apoderado: id_apoderado }, function (data) {
        let detalles = JSON.parse(data);
        limpiar();

        let accordion = $("#accordionExample");
        accordion.empty(); // Limpia el contenido previo.

        // Iterar sobre los alumnos
        detalles.forEach((detalle, index) => {
            let idsmd = detalle.ids_mensualidad_detalle.split(", ");
            let idsMeses = detalle.ids_mes.split(", ");
            let meses = detalle.meses.split(", ");
            let montos = detalle.montos.split(", ");
            let observaciones = detalle.observaciones.split(", ");
            let estadosPago = detalle.estados_pago.split(", ");

            // Asegurarse de que `estadosPago` tenga un valor para cada mes
            estadosPago = estadosPago.map(estado => (estado === "1" || estado === "0") ? estado : "0");

            // Generar el contenido del acordeón
            let filaMeses = idsMeses.map((idMes, i) => {
                let estado = estadosPago[i] || "0"; // Usar "0" como valor predeterminado si el estado no está definido

                return `
                    <tr>
                        <td style="width: 25%; height: auto;">${meses[i]}</td>
                        <td style="width: 25%; height: auto;"><input type="text" class="form-control" name="monto-${idMes}" value="${montos[i]}" /></td>
                        <td style="width: 25%; height: auto;">
                            <div class="form-check form-check-inline">
                                <input type="radio" name="pagado-${index}-${idMes}" value="1" ${estadosPago[i] === "1" ? "checked" : ""} class="form-check-input estado-pago-radio" data-id="${idMes}">
                                <label class="form-check-label">PAGADO</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" name="pagado-${index}-${idMes}" value="0" ${estadosPago[i] === "0" ? "checked" : ""} class="form-check-input estado-pago-radio" data-id="${idMes}">
                                <label class="form-check-label">PENDIENTE</label>
                            </div>
                            <input type="hidden" name="id-${idMes}" value="${idsmd[i]}" />
                        </td>
                        <td style="width: 25%; height: auto;"><input type="text" class="form-control" placeholder="OBSERVACIONES" name="observaciones-${idMes}" value="${observaciones[i]}" /></td>
                    </tr>
                `;
            }).join("");

            let accordionItem = `
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading${index}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${index}" aria-expanded="false" aria-controls="collapse${index}">
                            ${detalle.lectivo} - ${detalle.nivel} - ${detalle.grado} - ${detalle.seccion} - ${detalle.nombre_alumno}
                        </button>
                    </h2>
                    <div id="collapse${index}" class="accordion-collapse collapse" aria-labelledby="heading${index}" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col" colspan="2">INFORMACION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>MATRICULA</th>
                                        <td>${detalle.lectivo} - ${detalle.nivel} - ${detalle.grado} - ${detalle.seccion}</td>
                                    </tr>
                                    <tr>
                                        <th>APODERADO</th>
                                        <td>${detalle.tipo_apoderado} - ${detalle.nombre_apoderado}</td>
                                    </tr>
                                    <tr>
                                        <th>ALUMNO(A)</th>
                                        <td>${detalle.nombre_alumno}</td>
                                    </tr>
                                    <tr>
                                        <th>TELÉFONO</th>
                                        <td>${detalle.telefono}</td>
                                    </tr>
                                    <tr>
                                        <th>CODIGO</th>
                                        <td>${detalle.codigo}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>MES</th>
                                        <th>MONTO</th>
                                        <th>ESTADO</th>
                                        <th>OBSERVACIONES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${filaMeses}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
            accordion.append(accordionItem);
        });

        MostrarFormulario();
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
