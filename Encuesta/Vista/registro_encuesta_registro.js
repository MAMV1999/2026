var link = "../Controlador/registro_encuesta_registro.php?op=";
var tabla;

function init() {
    $("#frm_form").on("submit", function (e) {
        guardar(e);
    });

    MostrarListado();
}

$(document).ready(function () {
    tabla = $("#myTable").DataTable({
        ajax: link + "listar"
    });
});

function MostrarListado() {
    $("#listado").show();
    $("#formulario").hide();

    $("#encuesta_general_id").val("");
    $("#encuesta_alumno_id").val("");
    $("#titulo_encuesta").html("");
    $("#detalle_encuesta").html("");
    $("#contenedor_docentes").html("");
}

function MostrarFormulario() {
    $("#listado").hide();
    $("#formulario").show();
}

function responder(encuesta_general_id, encuesta_alumno_id) {

    $.post(link + "mostrar", {
        encuesta_general_id: encuesta_general_id,
        encuesta_alumno_id: encuesta_alumno_id
    }, function (resp) {

        let data = JSON.parse(resp);

        if (data.estado == false) {
            alert(data.mensaje);
            tabla.ajax.reload();
            return;
        }

        MostrarFormulario();

        $("#encuesta_general_id").val(data.cabecera.encuesta_general_id);
        $("#encuesta_alumno_id").val(data.cabecera.encuesta_alumno_id);

        $("#titulo_encuesta").html(data.cabecera.nombre);

        $("#detalle_encuesta").html(
            "Fecha: " + data.cabecera.fecha_inicio + " hasta " + data.cabecera.fecha_fin +
            " | Calificación: " + data.cabecera.calificacion_menor + " a " + data.cabecera.calificacion_mayor + " estrellas"
        );

        let html = "";

        data.docentes.forEach(function (docente) {

            html += `
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">

                        <h5 class="card-title">
                            ${docente.docente}
                        </h5>

                        <label><b>Calificación</b></label>

                        <div class="estrellas mb-3" data-docente="${docente.encuesta_docente_id}">
            `;

            for (let i = parseInt(data.cabecera.calificacion_menor); i <= parseInt(data.cabecera.calificacion_mayor); i++) {
                html += `
                    <input type="radio"
                           id="estrella_${docente.encuesta_docente_id}_${i}"
                           name="calificacion[${docente.encuesta_docente_id}]"
                           value="${i}"
                           required>

                    <label for="estrella_${docente.encuesta_docente_id}_${i}" title="${i} estrellas">
                        ★
                    </label>
                `;
            }

            html += `
                        </div>

                        <label><b>Comentario</b></label>
                        <textarea 
                            name="comentario[${docente.encuesta_docente_id}]"
                            class="form-control"
                            rows="3"
                            placeholder="Escriba su comentario para este docente"></textarea>

                    </div>
                </div>
            `;
        });

        $("#contenedor_docentes").html(html);
    });
}

function guardar(e) {
    e.preventDefault();

    if (!confirm("¿Desea enviar la encuesta? Después no podrá modificarla.")) {
        return;
    }

    let formData = new FormData(document.getElementById("frm_form"));

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
            alert("Error al intentar guardar la encuesta.");
        }
    });
}

init();