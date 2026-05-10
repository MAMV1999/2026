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
}

function MostrarFormulario() {
    $("#listado").hide();
    $("#formulario").show();
}

function responder(id) {
    $.post(link + "mostrar", { id: id }, function (resp) {

        let data = JSON.parse(resp);

        if (data.status === "error") {
            alert(data.message);
            return;
        }

        MostrarFormulario();

        $("#encuesta_id").val(data.cabecera.id);
        $("#encuesta_alumno_id").val(data.cabecera.encuesta_alumno_id);
        $("#titulo_encuesta").html(data.cabecera.nombre);
        $("#rango_calificacion").html(
            "Calificación permitida: " +
            data.cabecera.calificacion_menor +
            " a " +
            data.cabecera.calificacion_mayor +
            " estrellas"
        );

        let html = "";

        data.docentes.forEach(function (docente, index) {

            html += `
                <div class="card mb-3">
                    <div class="card-header">
                        <b>${index + 1}. ${docente.docente}</b>
                    </div>

                    <div class="card-body">
                        <label><b>Calificación</b></label>
                        <div class="mb-3">
            `;

            for (let i = parseInt(data.cabecera.calificacion_menor); i <= parseInt(data.cabecera.calificacion_mayor); i++) {

                let checked = "";

                if (parseInt(docente.numero_calificacion) === i) {
                    checked = "checked";
                }

                html += `
                    <label class="me-3">
                        <input type="radio" 
                               name="calificacion[${docente.encuesta_docente_id}]" 
                               value="${i}" 
                               ${checked}
                               required>
                        ${i} ⭐
                    </label>
                `;
            }

            let comentario = docente.comentario ?? "";

            html += `
                        </div>

                        <label><b>Comentario</b></label>
                        <textarea 
                            name="comentario[${docente.encuesta_docente_id}]" 
                            class="form-control"
                            rows="3"
                            placeholder="Escriba su comentario sobre el docente">${comentario}</textarea>
                    </div>
                </div>
            `;
        });

        $("#contenedor_docentes").html(html);
    });
}

function guardar(e) {
    e.preventDefault();

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
            alert("Error al guardar la encuesta.");
        }
    });
}

init();