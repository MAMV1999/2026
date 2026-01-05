var link = '../Controlador/mensualidad_mes.php?op=';
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
    $("#descripcion").val("");
    $("#observaciones").val("");
    $("#estado").val("1");
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

    $.ajax({
        url: link + "guardaryeditar",
        type: "POST",
        data: $("#frm_form").serialize(),

        success: function (datos) {
            alert(datos);
            MostrarListado();
            tabla.ajax.reload();
        }
    });
    limpiar();
}

function mostrar(id) {
    $.post(link + "mostrar", { id: id },
        function (data, status) {
            data = JSON.parse(data);
            MostrarFormulario();

            $("#id").val(data.id);
            $("#nombre").val(data.nombre);
            $("#descripcion").val(data.descripcion);
            $("#observaciones").val(data.observaciones);
            $("#estado").val(data.estado);
        }
    );
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
            }
        });
    } else {
        alert("CANCELADO");
    }
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
            }
        });
    } else {
        alert("CANCELADO");
    }
}

init();
