var link = '../Controlador/usuario_cargo.php?op=';
var tabla;

function init() {
    $("#frm_form").on("submit", function (e) {
        guardaryeditar(e);
    });
    MostrarListado();
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

    const tbody = document.querySelector("#tablaMenus tbody");
    if (tbody) tbody.innerHTML = "";
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

function listar_usuario_menu_activos(callback) {
    $.ajax({
        url: link + "listar_usuario_menu_activos",
        type: "GET",
        success: function (response) {
            const tbody = document.querySelector("#tablaMenus tbody");
            tbody.innerHTML = response;

            if (typeof callback === "function") {
                callback();
            }
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar los menús: ", error);
        }
    });
}

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

            // Cabecera
            if (data && data.cabecera) {
                $("#id").val(data.cabecera.id);
                $("#nombre").val(data.cabecera.nombre);
                $("#observaciones").val(data.cabecera.observaciones);
            }

            // 1) Cargar menús activos
            // 2) Aplicar detalle por id_usuario_menu
            listar_usuario_menu_activos(function () {
                if (!data || !data.detalle) return;

                $("#tablaMenus tbody tr").each(function () {
                    const hidden = $(this).find("input[type='hidden'][name*='[id_usuario_menu]']");
                    const menuId = hidden.val();

                    if (menuId && data.detalle[menuId]) {
                        const ingreso = data.detalle[menuId].ingreso;
                        const obs = data.detalle[menuId].observaciones;

                        // radios ingreso (0/1)
                        const radios = $(this).find("input[type='radio'][name*='[ingreso]']");
                        radios.each(function () {
                            if ($(this).val() == ingreso) {
                                $(this).prop("checked", true);
                            }
                        });

                        // observaciones detalle
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
