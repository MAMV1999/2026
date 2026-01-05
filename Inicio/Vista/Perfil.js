var link = "../Controlador/Perfil.php?op=";

function safeSelectpickerRefresh(selector) {
    // Si bootstrap-select no está cargado, NO romper el script
    if ($.fn.selectpicker) {
        $(selector).selectpicker("refresh");
    }
}

function init() {
    $("#frmPerfil").on("submit", function (e) {
        guardaryeditar(e);
    });

    // Cargar combos (sin romper) y luego mostrar perfil
    $.when(
        cargar_tipos_documentos(),
        cargar_estados_civiles(),
        cargar_sexos(),
        cargar_cargos(),
        cargar_tipos_contrato()
    ).always(function () {
        // ALWAYS: aunque falle un combo, igual intenta cargar el perfil
        mostrarPerfil();
    });

    if (typeof actualizarFechaHora === "function") {
        actualizarFechaHora();
        setInterval(actualizarFechaHora, 1000);
    }
}

function cargar_tipos_documentos() {
    return $.post(link + "listar_tipos_documentos_activos")
        .done(function (r) {
            $("#id_documento").html(r);
            safeSelectpickerRefresh("#id_documento");
        });
}

function cargar_estados_civiles() {
    return $.post(link + "listar_estados_civiles_activos")
        .done(function (r) {
            $("#id_estado_civil").html(r);
            safeSelectpickerRefresh("#id_estado_civil");
        });
}

function cargar_sexos() {
    return $.post(link + "listar_sexos_activos")
        .done(function (r) {
            $("#id_sexo").html(r);
            safeSelectpickerRefresh("#id_sexo");
        });
}

function cargar_cargos() {
    return $.post(link + "listar_cargos_activos")
        .done(function (r) {
            $("#id_cargo").html(r);
            safeSelectpickerRefresh("#id_cargo");
        });
}

function cargar_tipos_contrato() {
    return $.post(link + "listar_tipos_contrato_activos")
        .done(function (r) {
            $("#id_tipo_contrato").html(r);
            safeSelectpickerRefresh("#id_tipo_contrato");
        });
}

function guardaryeditar(e) {
    e.preventDefault();

    $.ajax({
        url: link + "guardaryeditar",
        type: "POST",
        data: $("#frmPerfil").serialize(),
        success: function (datos) {
            alert(datos);
            mostrarPerfil(); // recarga datos desde BD y confirma que guardó
        },
        error: function (xhr) {
            console.log("Error guardando:", xhr.responseText);
            alert("Ocurrió un error al guardar.");
        }
    });
}

function mostrarPerfil() {
    // OJO: en tu backend ya lo tomas desde sesión si no viene ID.
    // Igual lo mando, pero no dependas de esto.
    var id = $("#id").val();

    $.post(link + "mostrar", { id: id })
        .done(function (resp) {
            var data = null;

            try {
                data = JSON.parse(resp);
            } catch (e) {
                console.log("RESPUESTA NO JSON:", resp);
                alert("El backend no devolvió JSON válido en mostrar(). Revisa consola (F12).");
                return;
            }

            if (!data) {
                alert("No se encontraron datos para este docente.");
                return;
            }

            $("#id_documento").val(data.id_documento);
            $("#numerodocumento").val(data.numerodocumento);
            $("#nombreyapellido").val(data.nombreyapellido);
            $("#nacimiento").val(data.nacimiento);

            $("#id_estado_civil").val(data.id_estado_civil);
            $("#id_sexo").val(data.id_sexo);

            $("#direccion").val(data.direccion);
            $("#telefono").val(data.telefono);
            $("#correo").val(data.correo);

            $("#id_cargo").val(data.id_cargo);
            $("#id_tipo_contrato").val(data.id_tipo_contrato);

            $("#fechainicio").val(data.fechainicio);
            $("#fechafin").val(data.fechafin);

            $("#sueldo").val(data.sueldo);
            $("#cuentabancaria").val(data.cuentabancaria);
            $("#cuentainterbancaria").val(data.cuentainterbancaria);

            $("#sunat_ruc").val(data.sunat_ruc);
            $("#sunat_usuario").val(data.sunat_usuario);

            // En JSON tu campo viene como sunat_contrase\u00f1a (sunat_contraseña)
            // O sea: la propiedad real es sunat_contraseña
            $("#sunat_contraseña").val(data["sunat_contraseña"]);

            $("#usuario").val(data.usuario);
            $("#clave").val(data.clave);
            $("#observaciones").val(data.observaciones);

            safeSelectpickerRefresh("#id_documento");
            safeSelectpickerRefresh("#id_estado_civil");
            safeSelectpickerRefresh("#id_sexo");
            safeSelectpickerRefresh("#id_cargo");
            safeSelectpickerRefresh("#id_tipo_contrato");
        })
        .fail(function (xhr) {
            console.log("Error en mostrar():", xhr.responseText);
            alert("Error al obtener datos del perfil. Revisa consola (F12).");
        });
}

$(document).ready(function () {
    init();
});
