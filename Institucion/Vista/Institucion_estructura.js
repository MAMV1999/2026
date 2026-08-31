var link = "../Controlador/Institucion_estructura.php?op=";
var tabla;

function init() {
    $("#frm_form").on("submit", function (e) {
        guardaryeditar(e);
    });

    MostrarListado();
    cargar_docentes();
    actualizarFechaHora();
    setInterval(actualizarFechaHora, 1000);
}

function refrescarSelectPicker(id) {
    if ($.fn.selectpicker) {
        $("#" + id).selectpicker("refresh");
    }
}

function cargar_docentes() {
    $.post(link + "listar_docentes_activos", function (r) {
        $("#id_usuario_docente").html(r);
        refrescarSelectPicker("id_usuario_docente");
    });
}

$(document).ready(function () {
    tabla = $("#myTable").DataTable({
        ajax: link + "listar",
    });
});

function limpiar() {
    $("#id").val("");
    $("#nombre").val("");
    $("#id_usuario_docente").val("");
    $("#telefono").val("");
    $("#correo").val("");
    $("#ruc").val("");
    $("#razon_social").val("");
    $("#direccion").val("");
    $("#observaciones").val("");
    $("#tbody_estructura").empty();

    cargar_docentes();
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

    $(".estructura-input").each(function () {
        let tipo = $(this).data("tipo");
        let id = $(this).data("id");
        let nombre = $(this).val();
        let nombre_lectivo = "";

        if (tipo == "lectivo") {
            nombre_lectivo = $("#nombre_lectivo_" + id).val();
        }

        detalles.push({
            tipo: tipo,
            id: id,
            nombre: nombre,
            nombre_lectivo: nombre_lectivo
        });
    });

    let formData = $("#frm_form").serializeArray();
    formData.push({
        name: "detalles",
        value: JSON.stringify(detalles)
    });

    $.ajax({
        url: link + "guardaryeditar",
        type: "POST",
        data: formData,

        success: function (datos) {
            let respuesta = JSON.parse(datos);
            alert(respuesta.mensaje);

            if (respuesta.estado) {
                $("#id").val(respuesta.id);
                cargar_estructura(respuesta.id);
                MostrarListado();
                tabla.ajax.reload();
            }
        },
    });
}

function mostrar(id) {
    $.post(
        link + "mostrar",
        {
            id: id,
        },
        function (data, status) {
            data = JSON.parse(data);
            MostrarFormulario();

            $("#id").val(data.id);
            $("#nombre").val(data.nombre);
            $("#id_usuario_docente").val(data.id_usuario_docente);
            $("#telefono").val(data.telefono);
            $("#correo").val(data.correo);
            $("#ruc").val(data.ruc);
            $("#razon_social").val(data.razon_social);
            $("#direccion").val(data.direccion);
            $("#observaciones").val(data.observaciones);

            refrescarSelectPicker("id_usuario_docente");
            cargar_estructura(data.id);
        }
    );
}

function cargar_estructura(id_institucion) {
    $.post(
        link + "listar_estructura",
        {
            id_institucion: id_institucion
        },
        function (data) {
            let detalles = JSON.parse(data);
            pintar_estructura(detalles);
        }
    );
}

function pintar_estructura(detalles) {
    let tbody = $("#tbody_estructura");
    tbody.empty();

    if ($("#id").val() == "") {
        tbody.append(`
            <tr>
                <td colspan="4" class="text-center">
                    Primero guarde la institución para agregar lectivos, niveles, grados y secciones.
                </td>
            </tr>
        `);
        return;
    }

    if (detalles.length == 0) {
        tbody.append(`
            <tr>
                <td colspan="4" class="text-center">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="agregarLectivo()">
                        AGREGAR LECTIVO
                    </button>
                </td>
            </tr>
        `);
        return;
    }

    let lectivos = [];

    detalles.forEach(function (item) {
        let lectivo = lectivos.find(x => x.id == item.lectivo_id);

        if (!lectivo) {
            lectivo = {
                id: item.lectivo_id,
                nombre: item.lectivo_nombre,
                nombre_lectivo: item.lectivo_nombre_lectivo,
                estado: item.lectivo_estado,
                niveles: []
            };
            lectivos.push(lectivo);
        }

        if (item.nivel_id != null) {
            let nivel = lectivo.niveles.find(x => x.id == item.nivel_id);

            if (!nivel) {
                nivel = {
                    id: item.nivel_id,
                    nombre: item.nivel_nombre,
                    estado: item.nivel_estado,
                    grados: []
                };
                lectivo.niveles.push(nivel);
            }

            if (item.grado_id != null) {
                let grado = nivel.grados.find(x => x.id == item.grado_id);

                if (!grado) {
                    grado = {
                        id: item.grado_id,
                        nombre: item.grado_nombre,
                        estado: item.grado_estado,
                        secciones: []
                    };
                    nivel.grados.push(grado);
                }

                if (item.seccion_id != null) {
                    let existeSeccion = grado.secciones.find(x => x.id == item.seccion_id);

                    if (!existeSeccion) {
                        grado.secciones.push({
                            id: item.seccion_id,
                            nombre: item.seccion_nombre,
                            estado: item.seccion_estado
                        });
                    }
                }
            }
        }
    });

    lectivos.forEach(function (lectivo) {
        let lectivoRowspan = calcularRowspanLectivo(lectivo);
        let pintoLectivo = false;

        if (lectivo.niveles.length == 0) {
            tbody.append(`
                <tr>
                    <td style="border: 3px solid #000; vertical-align: middle;" rowspan="1">
                        ${inputLectivo(lectivo)}
                    </td>
                    <td style="border: 3px solid #000; vertical-align: middle;"></td>
                    <td style="border: 3px solid #000; vertical-align: middle;"></td>
                    <td style="border: 3px solid #000; vertical-align: middle;"></td>
                </tr>
            `);
            return;
        }

        lectivo.niveles.forEach(function (nivel) {
            let nivelRowspan = calcularRowspanNivel(nivel);
            let pintoNivel = false;

            if (nivel.grados.length == 0) {
                tbody.append(`
                    <tr>
                        ${!pintoLectivo ? `<td style="border: 3px solid #000; vertical-align: middle;" rowspan="${lectivoRowspan}">${inputLectivo(lectivo)}</td>` : ``}
                        <td style="border: 3px solid #000; vertical-align: middle;" rowspan="1">
                            ${inputNivel(nivel, lectivo)}
                        </td>
                        <td style="border: 3px solid #000; vertical-align: middle;"></td>
                        <td style="border: 3px solid #000; vertical-align: middle;"></td>
                    </tr>
                `);

                pintoLectivo = true;
                return;
            }

            nivel.grados.forEach(function (grado) {
                let gradoRowspan = calcularRowspanGrado(grado);
                let pintoGrado = false;

                if (grado.secciones.length == 0) {
                    tbody.append(`
                        <tr>
                            ${!pintoLectivo ? `<td style="border: 3px solid #000; vertical-align: middle;" rowspan="${lectivoRowspan}">${inputLectivo(lectivo)}</td>` : ``}
                            ${!pintoNivel ? `<td style="border: 3px solid #000; vertical-align: middle;" rowspan="${nivelRowspan}">${inputNivel(nivel, lectivo)}</td>` : ``}
                            <td style="border: 3px solid #000; vertical-align: middle;" rowspan="1">
                                ${inputGrado(grado, nivel)}
                            </td>
                            <td style="border: 3px solid #000; vertical-align: middle;"></td>
                        </tr>
                    `);

                    pintoLectivo = true;
                    pintoNivel = true;
                    return;
                }

                grado.secciones.forEach(function (seccion) {
                    tbody.append(`
                        <tr>
                            ${!pintoLectivo ? `<td style="border: 3px solid #000; vertical-align: middle;" rowspan="${lectivoRowspan}">${inputLectivo(lectivo)}</td>` : ``}
                            ${!pintoNivel ? `<td style="border: 3px solid #000; vertical-align: middle;" rowspan="${nivelRowspan}">${inputNivel(nivel, lectivo)}</td>` : ``}
                            ${!pintoGrado ? `<td style="border: 3px solid #000; vertical-align: middle;" rowspan="${gradoRowspan}">${inputGrado(grado, nivel)}</td>` : ``}
                            <td style="border: 3px solid #000; vertical-align: middle;">
                                ${inputSeccion(seccion, grado)}
                            </td>
                        </tr>
                    `);

                    pintoLectivo = true;
                    pintoNivel = true;
                    pintoGrado = true;
                });
            });
        });
    });
}

function calcularRowspanLectivo(lectivo) {
    let total = 0;

    if (lectivo.niveles.length == 0) {
        return 1;
    }

    lectivo.niveles.forEach(function (nivel) {
        total += calcularRowspanNivel(nivel);
    });

    return total;
}

function calcularRowspanNivel(nivel) {
    let total = 0;

    if (nivel.grados.length == 0) {
        return 1;
    }

    nivel.grados.forEach(function (grado) {
        total += calcularRowspanGrado(grado);
    });

    return total;
}

function calcularRowspanGrado(grado) {
    if (grado.secciones.length == 0) {
        return 1;
    }

    return grado.secciones.length;
}

function inputLectivo(lectivo) {
    return `
        <div class="input-group mb-2">
            <input type="text" 
                   class="form-control estructura-input" 
                   data-tipo="lectivo" 
                   data-id="${lectivo.id}" 
                   value="${lectivo.nombre}">
            ${botonOpciones("lectivo", lectivo.id, lectivo.estado)}
        </div>

        <input type="text" 
               class="form-control" 
               id="nombre_lectivo_${lectivo.id}" 
               value="${lectivo.nombre_lectivo == null ? "" : lectivo.nombre_lectivo}" 
               placeholder="Nombre lectivo">
    `;
}

function inputNivel(nivel, lectivo) {
    return `
        <div class="input-group mb-3">
            <input type="text" 
                   class="form-control estructura-input" 
                   data-tipo="nivel" 
                   data-id="${nivel.id}" 
                   value="${nivel.nombre}">
            ${botonOpciones("nivel", nivel.id, nivel.estado, lectivo.estado)}
        </div>
    `;
}

function inputGrado(grado, nivel) {
    return `
        <div class="input-group mb-3">
            <input type="text" 
                   class="form-control estructura-input" 
                   data-tipo="grado" 
                   data-id="${grado.id}" 
                   value="${grado.nombre}">
            ${botonOpciones("grado", grado.id, grado.estado, nivel.estado)}
        </div>
    `;
}

function inputSeccion(seccion, grado) {
    return `
        <div class="input-group mb-3">
            <input type="text" 
                   class="form-control estructura-input" 
                   data-tipo="seccion" 
                   data-id="${seccion.id}" 
                   value="${seccion.nombre}">
            ${botonOpciones("seccion", seccion.id, seccion.estado, grado.estado)}
        </div>
    `;
}

function botonOpciones(tipo, id, estado, estadoPadre = 1) {
    let textoEstado = estado == 1 ? "DESACTIVAR" : "ACTIVAR";
    let funcionEstado = estado == 1 ? `desactivar('${tipo}', ${id})` : `activar('${tipo}', ${id})`;

    let opcionesAgregar = "";

    if (tipo == "lectivo") {
        opcionesAgregar = `<li><a class="dropdown-item" href="#" onclick="agregarNivel(${id}, ${estado})">AGREGAR NIVEL</a></li>`;
    }

    if (tipo == "nivel") {
        opcionesAgregar = `<li><a class="dropdown-item" href="#" onclick="agregarGrado(${id}, ${estado}, ${estadoPadre})">AGREGAR GRADO</a></li>`;
    }

    if (tipo == "grado") {
        opcionesAgregar = `<li><a class="dropdown-item" href="#" onclick="agregarSeccion(${id}, ${estado}, ${estadoPadre})">AGREGAR SECCIÓN</a></li>`;
    }

    if (tipo == "seccion") {
        opcionesAgregar = ``;
    }

    return `
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            OPC.
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            ${opcionesAgregar}
            <li><a class="dropdown-item" href="#" onclick="${funcionEstado}">${textoEstado}</a></li>
        </ul>
    `;
}

function agregarLectivo() {
    let id_institucion = $("#id").val();

    if (id_institucion == "") {
        alert("Primero guarde la institución");
        return;
    }

    let nombre = prompt("Ingrese el nombre del lectivo");
    if (nombre == null || nombre == "") {
        return;
    }

    let nombre_lectivo = prompt("Ingrese el nombre lectivo");
    if (nombre_lectivo == null) {
        nombre_lectivo = "";
    }

    $.ajax({
        type: "POST",
        url: link + "agregar_lectivo",
        data: {
            id_institucion: id_institucion,
            nombre: nombre,
            nombre_lectivo: nombre_lectivo
        },
        success: function (datos) {
            alert(datos);
            cargar_estructura(id_institucion);
        },
    });
}

function agregarNivel(id_lectivo, estadoLectivo) {
    if (estadoLectivo == 0) {
        alert("No se puede agregar nivel porque el lectivo está desactivado");
        return;
    }

    let nombre = prompt("Ingrese el nombre del nivel");
    if (nombre == null || nombre == "") {
        return;
    }

    $.ajax({
        type: "POST",
        url: link + "agregar_nivel",
        data: {
            id_lectivo: id_lectivo,
            nombre: nombre
        },
        success: function (datos) {
            alert(datos);
            cargar_estructura($("#id").val());
        },
    });
}

function agregarGrado(id_nivel, estadoNivel, estadoLectivo) {
    if (estadoLectivo == 0 || estadoNivel == 0) {
        alert("No se puede agregar grado porque el nivel o lectivo está desactivado");
        return;
    }

    let nombre = prompt("Ingrese el nombre del grado");
    if (nombre == null || nombre == "") {
        return;
    }

    $.ajax({
        type: "POST",
        url: link + "agregar_grado",
        data: {
            id_nivel: id_nivel,
            nombre: nombre
        },
        success: function (datos) {
            alert(datos);
            cargar_estructura($("#id").val());
        },
    });
}

function agregarSeccion(id_grado, estadoGrado, estadoNivel) {
    if (estadoNivel == 0 || estadoGrado == 0) {
        alert("No se puede agregar sección porque el grado o nivel está desactivado");
        return;
    }

    let nombre = prompt("Ingrese el nombre de la sección");
    if (nombre == null || nombre == "") {
        return;
    }

    $.ajax({
        type: "POST",
        url: link + "agregar_seccion",
        data: {
            id_grado: id_grado,
            nombre: nombre
        },
        success: function (datos) {
            alert(datos);
            cargar_estructura($("#id").val());
        },
    });
}

function activar(tipo, id) {
    let condicion = confirm("¿ACTIVAR?");
    if (condicion === true) {
        $.ajax({
            type: "POST",
            url: link + "activar",
            data: {
                tipo: tipo,
                id: id,
            },
            success: function (datos) {
                alert(datos);
                cargar_estructura($("#id").val());
                tabla.ajax.reload();
            },
        });
    } else {
        alert("CANCELADO");
    }
}

function desactivar(tipo, id) {
    let condicion = confirm("¿DESACTIVAR?");
    if (condicion === true) {
        $.ajax({
            type: "POST",
            url: link + "desactivar",
            data: {
                tipo: tipo,
                id: id,
            },
            success: function (datos) {
                alert(datos);
                cargar_estructura($("#id").val());
                tabla.ajax.reload();
            },
        });
    } else {
        alert("CANCELADO");
    }
}

function activarInstitucion(id) {
    activar("institucion", id);
}

function desactivarInstitucion(id) {
    desactivar("institucion", id);
}

function nuevo() {
    limpiar();
    MostrarFormulario();
    pintar_estructura([]);
}

init();