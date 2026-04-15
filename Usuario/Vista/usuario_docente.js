var link = "../Controlador/usuario_docente.php?op=";
var tabla;

var documentos = [];
var cargos = [];
var estadosCiviles = [];
var sexos = [];
var tiposContrato = [];

function init() {
    $("#frm_form").on("submit", function (e) {
        guardaryeditar(e);
    });

    cargarCombos(function () {
        MostrarListado();
    });
}

function cargarCombos(callback) {
    $.when(
        $.post(link + "listar_documentos_activos", function (r) {
            documentos = JSON.parse(r);
        }),
        $.post(link + "listar_cargos_activos", function (r) {
            cargos = JSON.parse(r);
        }),
        $.post(link + "listar_estados_civiles_activos", function (r) {
            estadosCiviles = JSON.parse(r);
        }),
        $.post(link + "listar_sexos_activos", function (r) {
            sexos = JSON.parse(r);
        }),
        $.post(link + "listar_tipos_contrato_activos", function (r) {
            tiposContrato = JSON.parse(r);
        })
    ).done(function () {
        if (callback) callback();
    });
}

function generarOpciones(lista, seleccionado = "") {
    let html = `<option value="">SELECCIONAR</option>`;
    for (let i = 0; i < lista.length; i++) {
        let sel = (String(lista[i].id) === String(seleccionado)) ? "selected" : "";
        html += `<option value="${lista[i].id}" ${sel}>${lista[i].nombre}</option>`;
    }
    return html;
}

function limpiar() {
    $("#tabla_dinamica tbody").empty();
    agregarFila();
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

    let detalles = [];

    $("#tabla_dinamica tbody tr").each(function () {
        let fila = $(this);

        let detalle = {
            id: fila.find("input[name='ids[]']").val() || null,
            id_documento: fila.find("select[name='id_documentos[]']").val() || "",
            numerodocumento: fila.find("input[name='numerodocumentos[]']").val() || "",
            nombreyapellido: fila.find("input[name='nombreyapellidos[]']").val() || "",
            nacimiento: fila.find("input[name='nacimientos[]']").val() || "",
            id_estado_civil: fila.find("select[name='id_estados_civiles[]']").val() || "",
            id_sexo: fila.find("select[name='id_sexos[]']").val() || "",
            direccion: fila.find("input[name='direcciones[]']").val() || "",
            telefono: fila.find("input[name='telefonos[]']").val() || "",
            correo: fila.find("input[name='correos[]']").val() || "",
            id_cargo: fila.find("select[name='id_cargos[]']").val() || "",
            id_tipo_contrato: fila.find("select[name='id_tipos_contrato[]']").val() || "",
            fechainicio: fila.find("input[name='fechainicios[]']").val() || "",
            fechafin: fila.find("input[name='fechafins[]']").val() || "",
            sueldo: fila.find("input[name='sueldos[]']").val() || "",
            cuentabancaria: fila.find("input[name='cuentabancarias[]']").val() || "",
            cuentainterbancaria: fila.find("input[name='cuentainterbancarias[]']").val() || "",
            sunat_ruc: fila.find("input[name='sunat_rucs[]']").val() || "",
            sunat_usuario: fila.find("input[name='sunat_usuarios[]']").val() || "",
            sunat_contraseña: fila.find("input[name='sunat_contraseñas[]']").val() || "",
            usuario: fila.find("input[name='usuarios[]']").val() || "",
            clave: fila.find("input[name='claves[]']").val() || "",
            observaciones: fila.find("input[name='observaciones[]']").val() || "",
            estado: fila.find("select[name='estados[]']").val() || "1"
        };

        if (detalle.numerodocumento !== "" && detalle.nombreyapellido !== "") {
            detalles.push(detalle);
        }
    });

    if (detalles.length === 0) {
        alert("Debe agregar al menos un docente válido.");
        return;
    }

    $.ajax({
        url: link + "guardaryeditar",
        type: "POST",
        data: { detalles: JSON.stringify(detalles) },
        success: function (response) {
            alert(response);
            tabla.ajax.reload();
            MostrarListado();
            limpiar();
        },
        error: function () {
            alert("Error al guardar los registros.");
        }
    });
}

function agregarFila(data = null) {
    let fila = `
        <tr>
            <td>
                <input type="hidden" name="ids[]" value="${data ? data.id || '' : ''}">
                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">X</button>
            </td>
            <td>
                <select name="id_documentos[]" class="form-control">
                    ${generarOpciones(documentos, data ? data.id_documento : "")}
                </select>
            </td>
            <td><input type="text" name="numerodocumentos[]" class="form-control" value="${data ? data.numerodocumento || '' : ''}"></td>
            <td><input type="text" name="nombreyapellidos[]" class="form-control" value="${data ? data.nombreyapellido || '' : ''}"></td>
            <td><input type="date" name="nacimientos[]" class="form-control" value="${data ? data.nacimiento || '' : ''}"></td>
            <td>
                <select name="id_estados_civiles[]" class="form-control">
                    ${generarOpciones(estadosCiviles, data ? data.id_estado_civil : "")}
                </select>
            </td>
            <td>
                <select name="id_sexos[]" class="form-control">
                    ${generarOpciones(sexos, data ? data.id_sexo : "")}
                </select>
            </td>
            <td><input type="text" name="direcciones[]" class="form-control" value="${data ? data.direccion || '' : ''}"></td>
            <td><input type="text" name="telefonos[]" class="form-control" value="${data ? data.telefono || '' : ''}"></td>
            <td><input type="email" name="correos[]" class="form-control" value="${data ? data.correo || '' : ''}"></td>
            <td>
                <select name="id_cargos[]" class="form-control">
                    ${generarOpciones(cargos, data ? data.id_cargo : "")}
                </select>
            </td>
            <td>
                <select name="id_tipos_contrato[]" class="form-control">
                    ${generarOpciones(tiposContrato, data ? data.id_tipo_contrato : "")}
                </select>
            </td>
            <td><input type="date" name="fechainicios[]" class="form-control" value="${data ? data.fechainicio || '' : ''}"></td>
            <td><input type="date" name="fechafins[]" class="form-control" value="${data ? data.fechafin || '' : ''}"></td>
            <td><input type="text" name="sueldos[]" class="form-control" value="${data ? data.sueldo || '' : ''}"></td>
            <td><input type="text" name="cuentabancarias[]" class="form-control" value="${data ? data.cuentabancaria || '' : ''}"></td>
            <td><input type="text" name="cuentainterbancarias[]" class="form-control" value="${data ? data.cuentainterbancaria || '' : ''}"></td>
            <td><input type="text" name="sunat_rucs[]" class="form-control" value="${data ? data.sunat_ruc || '' : ''}"></td>
            <td><input type="text" name="sunat_usuarios[]" class="form-control" value="${data ? data.sunat_usuario || '' : ''}"></td>
            <td><input type="text" name="sunat_contraseñas[]" class="form-control" value="${data ? data.sunat_contraseña || '' : ''}"></td>
            <td><input type="text" name="usuarios[]" class="form-control" value="${data ? data.usuario || '' : ''}"></td>
            <td><input type="text" name="claves[]" class="form-control" value="${data ? data.clave || '' : ''}"></td>
            <td><input type="text" name="observaciones[]" class="form-control" value="${data ? data.observaciones || '' : ''}"></td>
            <td>
                <select name="estados[]" class="form-control">
                    <option value="1" ${(data && String(data.estado) === "1") ? "selected" : ""}>ACTIVO</option>
                    <option value="0" ${(data && String(data.estado) === "0") ? "selected" : ""}>INACTIVO</option>
                </select>
            </td>
        </tr>
    `;

    $("#tabla_dinamica tbody").append(fila);
}

function eliminarFila(btn) {
    $(btn).closest("tr").remove();

    if ($("#tabla_dinamica tbody tr").length === 0) {
        agregarFila();
    }
}

function mostrar(id) {
    $.ajax({
        url: link + "mostrar",
        type: "POST",
        data: { id: id },
        dataType: "json",
        success: function (data) {
            if (data) {
                MostrarFormulario();
                $("#tabla_dinamica tbody").empty();
                agregarFila(data);
            } else {
                alert("No se encontraron datos del docente.");
            }
        },
        error: function () {
            alert("Error al obtener los datos del docente.");
        }
    });
}

function editarTodo() {
    MostrarFormulario();
    $("#tabla_dinamica tbody").empty();

    $.ajax({
        url: link + "listar_todos",
        type: "GET",
        dataType: "json",
        success: function (rows) {
            if (!rows || rows.length === 0) {
                alert("No hay registros para editar.");
                agregarFila();
                return;
            }

            for (let i = 0; i < rows.length; i++) {
                agregarFila(rows[i]);
            }
        },
        error: function () {
            alert("Error al cargar los registros para editar todo.");
            agregarFila();
        }
    });
}

$(document).ready(function () {
    tabla = $('#myTable').DataTable({
        "ajax": {
            "url": link + "listar",
            "dataSrc": function (json) {
                return json.aaData;
            }
        }
    });
});

function activar(id) {
    if (confirm("¿ACTIVAR?")) {
        $.post(link + "activar", { id: id }, function (datos) {
            alert(datos);
            tabla.ajax.reload();
        });
    }
}

function desactivar(id) {
    if (confirm("¿DESACTIVAR?")) {
        $.post(link + "desactivar", { id: id }, function (datos) {
            alert(datos);
            tabla.ajax.reload();
        });
    }
}

init();