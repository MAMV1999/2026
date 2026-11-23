var link = "../Controlador/registro_encuestas_detalle.php?op=";
var tabla;

function init() {
    MostrarListado();
}

$(document).ready(function () {
    tabla = $("#myTable").DataTable({
        ajax: link + "listar"
    });
});

function limpiar() {
    $("#nombre_encuesta").text("");
    $("#fechas_encuesta").text("");
    $("#rango_calificacion").text("");
    $("#titulo_encuesta").text("DETALLE DE RESPUESTAS");
    $("#contenedor_respuestas").html("");
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

            limpiar();

            var cabecera = data.cabecera;
            var respuestas = data.respuestas || [];

            $("#titulo_encuesta").text("RESPUESTAS DE LA ENCUESTA");
            $("#nombre_encuesta").text(cabecera.nombre || "");
            $("#fechas_encuesta").text((cabecera.fecha_inicio || "") + " - " + (cabecera.fecha_fin || ""));
            $("#rango_calificacion").text(cabecera.calificacion_menor + " a " + cabecera.calificacion_mayor + " estrellas");

            if (respuestas.length === 0) {
                $("#contenedor_respuestas").html(
                    '<div class="alert alert-warning text-center"><b>ESTA ENCUESTA TODAVÍA NO TIENE RESPUESTAS REGISTRADAS.</b></div>'
                );
                MostrarFormulario();
                return;
            }

            var participantes = {};

            respuestas.forEach(function (respuesta) {
                var clave = respuesta.encuesta_alumno_id;

                if (!participantes[clave]) {
                    participantes[clave] = {
                        alumno: respuesta.alumno || "ALUMNO NO IDENTIFICADO",
                        nivel: respuesta.nivel || "",
                        grado: respuesta.grado || "",
                        seccion: respuesta.seccion || "",
                        fecha: respuesta.fecha_respuesta,
                        respuestas: []
                    };
                }

                participantes[clave].respuestas.push(respuesta);
            });

            var html = "";
            Object.keys(participantes).forEach(function (clave) {
                var participante = participantes[clave];

                html += '<div class="card mb-3 shadow-sm">';
                html += '  <div class="card-header d-flex justify-content-between align-items-center">';
                html += '      <div>';
                html += '          <b>ALUMNO: ' + escaparHtml(participante.alumno) + '</b>';
                var datosMatricula = [];
                if (participante.nivel) datosMatricula.push("Nivel: " + escaparHtml(participante.nivel));
                if (participante.grado) datosMatricula.push("Grado: " + escaparHtml(participante.grado));
                if (participante.seccion) datosMatricula.push("Sección: " + escaparHtml(participante.seccion));
                if (datosMatricula.length > 0) {
                    html += '          <div class="small text-muted mt-1">' + datosMatricula.join(" | ") + '</div>';
                }
                html += '      </div>';
                html += '      <span class="text-muted">Fecha: ' + escaparHtml(participante.fecha || "-") + '</span>';
                html += '  </div>';
                html += '  <div class="card-body">';
                html += '      <div class="table-responsive">';
                html += '          <table class="table table-bordered table-hover">';
                html += '              <thead><tr><th style="width: 60px;">N°</th><th>DOCENTE</th><th style="width: 160px;">CALIFICACIÓN</th><th>COMENTARIO</th></tr></thead>';
                html += '              <tbody>';

                participante.respuestas.forEach(function (respuesta, index) {
                    html += '<tr>';
                    html += '  <td class="text-center">' + (index + 1) + '</td>';
                    html += '  <td>' + escaparHtml(respuesta.docente || "") + '</td>';
                    html += '  <td class="text-center"><span class="text-warning">' + estrellas(respuesta.numero_calificacion) + '</span> (' + respuesta.numero_calificacion + ')</td>';
                    html += '  <td>' + (respuesta.comentario ? escaparHtml(respuesta.comentario).replace(/\n/g, "<br>") : '<span class="text-muted">Sin comentario</span>') + '</td>';
                    html += '</tr>';
                });

                html += '              </tbody>';
                html += '          </table>';
                html += '      </div>';
                html += '  </div>';
                html += '</div>';

            });

            $("#contenedor_respuestas").html(html);
            MostrarFormulario();
        }
    ).fail(function () {
        alert("Error al obtener las respuestas de la encuesta.");
    });
}

function estrellas(cantidad) {
    var total = parseInt(cantidad, 10) || 0;
    var html = "";

    for (var i = 0; i < total; i++) {
        html += "★";
    }

    return html;
}

function escaparHtml(texto) {
    return String(texto)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/\"/g, "&quot;")
        .replace(/'/g, "&#039;");
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
