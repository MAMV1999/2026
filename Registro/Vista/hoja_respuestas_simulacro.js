var link = "../Controlador/hoja_respuestas_simulacro.php?op=";
var tabla;

function init() {
    MostrarListado();
    actualizarFechaHora();
    setInterval(actualizarFechaHora, 1000);
}

$(document).ready(function () {
    tabla = $("#myTable").DataTable({
        ajax: link + "listar",
    });
});

function verPdf(id_seccion) {
    let formato_default = `ARITMÉTICA-1,5
RAZONAMIENTO MATEMÁTICO-6,10
FÍSICA / QUÍMICA-11,15
GEOMETRÍA-16,20
ALGEBRA-21,25
COMUNICACIÓN INTEGRAL-26,30
RAZONAMIENTO VERBAL-31,35
PLAN LECTOR-36,40
HISTORIA-41,45
BIOLOGÍA Y ANATOMÍA-46,50
CÍVICA Y GEOGRAFÍA-51,55`;

    $("#visor_pdf_" + id_seccion).html(`
        <div class="mb-3">
            <label class="form-label"><b>Ingrese la distribución de cursos y preguntas</b></label>
            <textarea 
                class="form-control" 
                id="estructura_preguntas_${id_seccion}" 
                rows="12">${formato_default}</textarea>
        </div>

        <div class="mb-3">
            <button type="button" 
                    class="btn btn-primary" 
                    onclick="generarPdf(${id_seccion})">
                GENERAR PDF
            </button>
        </div>

        <div id="iframe_pdf_${id_seccion}"></div>
    `);

    $("#pdf_" + id_seccion).modal("show");
}

function generarPdf(id_seccion) {
    let estructura = $("#estructura_preguntas_" + id_seccion).val();

    if (estructura.trim() === "") {
        alert("Ingrese la distribución de cursos y preguntas.");
        return;
    }

    let lineas = estructura.split("\n");
    let valido = true;

    lineas.forEach(function (linea) {
        linea = linea.trim();

        if (linea !== "") {
            let partes = linea.split("-");

            if (partes.length < 2) {
                valido = false;
            } else {
                let rango = partes[partes.length - 1].split(",");

                if (rango.length !== 2) {
                    valido = false;
                } else {
                    let inicio = parseInt(rango[0]);
                    let fin = parseInt(rango[1]);

                    if (isNaN(inicio) || isNaN(fin) || inicio <= 0 || fin <= 0 || inicio > fin) {
                        valido = false;
                    }
                }
            }
        }
    });

    if (!valido) {
        alert("Formato incorrecto. Use este formato: CURSO-1,5");
        return;
    }

    let estructura_url = encodeURIComponent(estructura);

    $("#iframe_pdf_" + id_seccion).html(`
        <iframe 
            src="../../Reportes/Vista/hoja_respuestas_simulacro.php?id_seccion=${id_seccion}&estructura=${estructura_url}" 
            type="application/pdf" 
            width="100%" 
            height="600px">
        </iframe>
    `);
}

function limpiar() {
}

function MostrarListado() {
    $("#listado").show();
}

init();