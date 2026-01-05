var link = "../Controlador/Matricula.php?op=";
var tabla;

function init() {
    $("#frm_form").on("submit", function (e) {
        guardaryeditar(e);
    });
    MostrarListado();
    cargar_secciones();
    cargar_docentes();
    actualizarFechaHora();
    setInterval(actualizarFechaHora, 1000);
}

function cargar_secciones() {
    $.post(link + "listar_secciones_activas", function (r) {
        $("#id_institucion_seccion").html(r);
        $("#id_institucion_seccion").selectpicker("refresh");
    });
}

function cargar_docentes() {
    $.post(link + "listar_docentes_activos", function (r) {
        $("#id_usuario_docente").html(r);
        $("#id_usuario_docente").selectpicker("refresh");
    });
}

$(document).ready(function () {
    tabla = $("#myTable").DataTable({
        ajax: link + "listar",
    });
});

function limpiar() {
    cargar_secciones();
    cargar_docentes();
    $("#id").val("");
    $("#aforo").val("");
    $("#observaciones").val("");

    $("#accordionExample tbody").html("");
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
            alert("Error al intentar guardar los datos.");
        }
    });
}

// AGREGAR: tabla vacía
function cargarCobrosActivos() {
    $.ajax({
        url: link + "listar_matricula_cobro_activos",
        type: "GET",
        success: function (response) {
            const tablaPagos = document.querySelector("#accordionExample tbody");
            tablaPagos.innerHTML = response;
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar cargarCobrosActivos(): ", error);
        }
    });
}

function mostrar(id) {
    $.post(link + "mostrar", { id: id }, function (resp) {

        resp = JSON.parse(resp);

        if (resp.error) {
            alert(resp.error);
            return;
        }

        MostrarFormulario();
        const data = resp.cabecera;

        $("#id").val(data.id);
        $("#id_institucion_seccion").val(data.id_institucion_seccion);
        $("#id_usuario_docente").val(data.id_usuario_docente);
        $("#aforo").val(data.aforo);
        $("#observaciones").val(data.observaciones);

        // PINTAR DETALLE (pagos)
        document.querySelector("#accordionExample tbody").innerHTML = resp.detalle_html;

        $("#id_institucion_seccion").selectpicker("refresh");
        $("#id_usuario_docente").selectpicker("refresh");

    });
}


function activar(id) {
    let condicion = confirm("¿ACTIVAR?");
    if (condicion === true) {
        $.ajax({
            type: "POST",
            url: link + "activar",
            data: { id: id },
            success: function (datos) {
                alert(datos);
                tabla.ajax.reload();
                cargar_secciones();
                cargar_docentes();
            },
        });
    } else { alert("CANCELADO"); }
}

function desactivar(id) {
    let condicion = confirm("¿DESACTIVAR?");
    if (condicion === true) {
        $.ajax({
            type: "POST",
            url: link + "desactivar",
            data: { id: id },
            success: function (datos) {
                alert(datos);
                tabla.ajax.reload();
                cargar_secciones();
                cargar_docentes();
            },
        });
    } else { alert("CANCELADO"); }
}

function eliminar(id) {
    let condicion = confirm("¿ELIMINAR?");
    if (condicion === true) {
        $.ajax({
            type: "POST",
            url: link + "eliminar",
            data: { id: id },
            success: function (datos) {
                alert(datos);
                tabla.ajax.reload();
                cargar_secciones();
                cargar_docentes();
            },
        });
    } else { alert("CANCELADO"); }
}

init();
