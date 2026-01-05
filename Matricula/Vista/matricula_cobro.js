var link = '../Controlador/matricula_cobro.php?op=';
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
    tabla = $('#myTable').DataTable({
        "ajax": link + 'listar'
    });
});

function limpiar() {
    $("#id").val("");
    $("#nombre").val("");
    $("#observaciones").val("");
    $("#estado").val("1");
    $("#apertura_no").prop("checked", true);

    const tablaMeses = document.querySelector("#tablaMeses tbody");
    if (tablaMeses) tablaMeses.innerHTML = "";
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

    const formData = new FormData(document.getElementById("frm_form"));
    const id = $("#id").val();

    // Si hay ID -> editar; si no hay -> guardar
    const op = (id && id.trim() !== "") ? "editar" : "guardar";

    $.ajax({
        url: link + op,
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
            alert("Error al intentar guardar/editar los datos.");
        }
    });
}

// Carga meses activos y (opcional) ejecuta callback al finalizar
function listar_matricula_mes_activas(callback) {
    $("#apertura_no").prop("checked", true);

    $.ajax({
        url: link + "listar_matricula_mes_activas",
        type: "GET",
        success: function (response) {
            const tablaMeses = document.querySelector("#tablaMeses tbody");
            tablaMeses.innerHTML = response;

            if (typeof callback === "function") {
                callback();
            }
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar las mensualidades: ", error);
        }
    });
}

// =========================
// NUEVO: mostrar(id) para editar
// =========================
function mostrar(id) {
    $.ajax({
        url: link + "mostrar",
        type: "POST",
        data: { id: id },
        success: function (response) {
            let data = null;

            try {
                data = JSON.parse(response);
            } catch (e) {
                alert("No se pudo leer la respuesta del servidor.");
                return;
            }

            if (data && data.error) {
                alert(data.error);
                return;
            }

            MostrarFormulario();

            // Cargar cabecera en inputs
            if (data && data.cabecera) {
                $("#id").val(data.cabecera.id);
                $("#nombre").val(data.cabecera.nombre);
                $("#observaciones").val(data.cabecera.observaciones);

                if (parseInt(data.cabecera.apertura) === 1) {
                    $("#apertura_si").prop("checked", true);
                } else {
                    $("#apertura_no").prop("checked", true);
                }
            }

            // 1) Cargar tabla de meses
            // 2) Luego aplicar detalle (radios + observaciones) según data.detalle
            listar_matricula_mes_activas(function () {
                if (!data || !data.detalle) return;

                // Recorrer filas y aplicar según matricula_mes_id
                $("#tablaMeses tbody tr").each(function () {
                    const hidden = $(this).find("input[type='hidden'][name*='[matricula_mes_id]']");
                    const mesId = hidden.val();

                    if (mesId && data.detalle[mesId]) {
                        const aplica = data.detalle[mesId].aplica;
                        const obs = data.detalle[mesId].observaciones;

                        // Set radio aplica (0/1)
                        const radios = $(this).find("input[type='radio'][name*='[aplica]']");
                        radios.each(function () {
                            if ($(this).val() == aplica) {
                                $(this).prop("checked", true);
                            }
                        });

                        // Set observaciones detalle
                        $(this).find("input[type='text'][name*='[observaciones]']").val(obs);
                    }
                });
            });
        },
        error: function () {
            alert("Error al intentar mostrar el registro.");
        }
    });
}

function desactivar(id) {
    $.ajax({
        url: link + "desactivar",
        type: "POST",
        data: { id: id },
        success: function (response) {
            alert(response);
            tabla.ajax.reload();
        },
        error: function () {
            alert("Error al intentar desactivar.");
        }
    });
}

function activar(id) {
    $.ajax({
        url: link + "activar",
        type: "POST",
        data: { id: id },
        success: function (response) {
            alert(response);
            tabla.ajax.reload();
        },
        error: function () {
            alert("Error al intentar activar.");
        }
    });
}


init();
