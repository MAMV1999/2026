var link = "../Controlador/Institucion_estructura.php?op=";
var tabla;
var estructura = [];

function init() {
    $("#frm_form").on("submit", function (e) {
        guardaryeditar(e);
    });

    MostrarListado();
    cargar_docentes();

    if (typeof actualizarFechaHora === "function") {
        actualizarFechaHora();
        setInterval(actualizarFechaHora, 1000);
    }
}

function refrescarSelectPicker(id) {
    if ($.fn.selectpicker) {
        $(id).selectpicker("refresh");
    }
}

function cargar_docentes() {
    $.post(link + "listar_docentes_activos", function (r) {
        $("#id_usuario_docente").html(r);
        refrescarSelectPicker("#id_usuario_docente");
    });
}

$(document).ready(function () {
    tabla = $("#myTable").DataTable({
        ajax: link + "listar",
    });
});

function estructuraInicial() {
    estructura = [
        {
            id: "",
            nombre: "",
            niveles: [
                {
                    id: "",
                    nombre: "",
                    grados: [
                        {
                            id: "",
                            nombre: "",
                            secciones: [
                                {
                                    id: "",
                                    nombre: ""
                                }
                            ]
                        }
                    ]
                }
            ]
        }
    ];
}

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

    refrescarSelectPicker("#id_usuario_docente");

    estructuraInicial();
    dibujarEstructura();
}

function contarFilasLectivo(lectivo) {
    let total = 0;

    lectivo.niveles.forEach(function (nivel) {
        total += contarFilasNivel(nivel);
    });

    return total > 0 ? total : 1;
}

function contarFilasNivel(nivel) {
    let total = 0;

    nivel.grados.forEach(function (grado) {
        total += contarFilasGrado(grado);
    });

    return total > 0 ? total : 1;
}

function contarFilasGrado(grado) {
    let total = grado.secciones.length;
    return total > 0 ? total : 1;
}

function inputGrupo(valor, placeholder, nivel, i, j, k, l) {
    let identificador = "";

    if (valor == null) {
        valor = "";
    }

    if (nivel == "lectivo") {
        identificador = `data-nivel="${nivel}" data-i="${i}"`;
    }

    if (nivel == "nivel") {
        identificador = `data-nivel="${nivel}" data-i="${i}" data-j="${j}"`;
    }

    if (nivel == "grado") {
        identificador = `data-nivel="${nivel}" data-i="${i}" data-j="${j}" data-k="${k}"`;
    }

    if (nivel == "seccion") {
        identificador = `data-nivel="${nivel}" data-i="${i}" data-j="${j}" data-k="${k}" data-l="${l}"`;
    }

    return `
        <div class="input-group">
            <input type="text" class="form-control input-estructura" placeholder="${placeholder}" value="${valor}" ${identificador}>
            <button class="btn btn-outline-secondary" type="button" onclick="agregarElemento('${nivel}', ${i}, ${j}, ${k}, ${l})">+</button>
            <button class="btn btn-outline-secondary" type="button" onclick="quitarElemento('${nivel}', ${i}, ${j}, ${k}, ${l})">-</button>
        </div>
    `;
}

function dibujarEstructura() {
    let tbody = $("#tbody_estructura");
    tbody.empty();

    estructura.forEach(function (lectivo, i) {
        let lectivoRowspan = contarFilasLectivo(lectivo);
        let lectivoPintado = false;

        lectivo.niveles.forEach(function (nivel, j) {
            let nivelRowspan = contarFilasNivel(nivel);
            let nivelPintado = false;

            nivel.grados.forEach(function (grado, k) {
                let gradoRowspan = contarFilasGrado(grado);
                let gradoPintado = false;

                grado.secciones.forEach(function (seccion, l) {
                    let fila = "<tr>";

                    if (!lectivoPintado) {
                        fila += `<td rowspan="${lectivoRowspan}" style="vertical-align: middle;">${inputGrupo(lectivo.nombre, "NOMBRE DEL LECTIVO", "lectivo", i, 0, 0, 0)}</td>`;
                        lectivoPintado = true;
                    }

                    if (!nivelPintado) {
                        fila += `<td rowspan="${nivelRowspan}" style="vertical-align: middle;">${inputGrupo(nivel.nombre, "NOMBRE DEL NIVEL", "nivel", i, j, 0, 0)}</td>`;
                        nivelPintado = true;
                    }

                    if (!gradoPintado) {
                        fila += `<td rowspan="${gradoRowspan}" style="vertical-align: middle;">${inputGrupo(grado.nombre, "NOMBRE DEL GRADO", "grado", i, j, k, 0)}</td>`;
                        gradoPintado = true;
                    }

                    fila += `<td style="vertical-align: middle;">${inputGrupo(seccion.nombre, "NOMBRE DE LA SECCIÓN", "seccion", i, j, k, l)}</td>`;
                    fila += "</tr>";

                    tbody.append(fila);
                });
            });
        });
    });
}

$(document).on("keyup change", ".input-estructura", function () {
    let nivel = $(this).data("nivel");
    let i = $(this).data("i");
    let j = $(this).data("j");
    let k = $(this).data("k");
    let l = $(this).data("l");
    let valor = $(this).val();

    if (nivel == "lectivo") {
        estructura[i].nombre = valor;
    }

    if (nivel == "nivel") {
        estructura[i].niveles[j].nombre = valor;
    }

    if (nivel == "grado") {
        estructura[i].niveles[j].grados[k].nombre = valor;
    }

    if (nivel == "seccion") {
        estructura[i].niveles[j].grados[k].secciones[l].nombre = valor;
    }
});

function agregarElemento(nivel, i, j, k, l) {
    if (nivel == "lectivo") {
        estructura.splice(i + 1, 0, {
            id: "",
            nombre: "",
            niveles: [
                {
                    id: "",
                    nombre: "",
                    grados: [
                        {
                            id: "",
                            nombre: "",
                            secciones: [
                                {
                                    id: "",
                                    nombre: ""
                                }
                            ]
                        }
                    ]
                }
            ]
        });
    }

    if (nivel == "nivel") {
        estructura[i].niveles.splice(j + 1, 0, {
            id: "",
            nombre: "",
            grados: [
                {
                    id: "",
                    nombre: "",
                    secciones: [
                        {
                            id: "",
                            nombre: ""
                        }
                    ]
                }
            ]
        });
    }

    if (nivel == "grado") {
        estructura[i].niveles[j].grados.splice(k + 1, 0, {
            id: "",
            nombre: "",
            secciones: [
                {
                    id: "",
                    nombre: ""
                }
            ]
        });
    }

    if (nivel == "seccion") {
        estructura[i].niveles[j].grados[k].secciones.splice(l + 1, 0, {
            id: "",
            nombre: ""
        });
    }

    dibujarEstructura();
}

function quitarElemento(nivel, i, j, k, l) {
    if (nivel == "lectivo") {
        estructura.splice(i, 1);

        if (estructura.length == 0) {
            estructuraInicial();
        }
    }

    if (nivel == "nivel") {
        estructura[i].niveles.splice(j, 1);

        if (estructura[i].niveles.length == 0) {
            estructura[i].niveles.push({
                id: "",
                nombre: "",
                grados: [
                    {
                        id: "",
                        nombre: "",
                        secciones: [
                            {
                                id: "",
                                nombre: ""
                            }
                        ]
                    }
                ]
            });
        }
    }

    if (nivel == "grado") {
        estructura[i].niveles[j].grados.splice(k, 1);

        if (estructura[i].niveles[j].grados.length == 0) {
            estructura[i].niveles[j].grados.push({
                id: "",
                nombre: "",
                secciones: [
                    {
                        id: "",
                        nombre: ""
                    }
                ]
            });
        }
    }

    if (nivel == "seccion") {
        estructura[i].niveles[j].grados[k].secciones.splice(l, 1);

        if (estructura[i].niveles[j].grados[k].secciones.length == 0) {
            estructura[i].niveles[j].grados[k].secciones.push({
                id: "",
                nombre: ""
            });
        }
    }

    dibujarEstructura();
}

function guardaryeditar(e) {
    e.preventDefault();

    $.ajax({
        url: link + "guardaryeditar",
        type: "POST",
        data: {
            id: $("#id").val(),
            nombre: $("#nombre").val(),
            id_usuario_docente: $("#id_usuario_docente").val(),
            telefono: $("#telefono").val(),
            correo: $("#correo").val(),
            ruc: $("#ruc").val(),
            razon_social: $("#razon_social").val(),
            direccion: $("#direccion").val(),
            observaciones: $("#observaciones").val(),
            estructura: JSON.stringify(estructura)
        },

        success: function (datos) {
            alert(datos);
            MostrarListado();
            tabla.ajax.reload();
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

            $("#id").val(data.id);
            $("#nombre").val(data.nombre);
            $("#id_usuario_docente").val(data.id_usuario_docente);
            $("#telefono").val(data.telefono);
            $("#correo").val(data.correo);
            $("#ruc").val(data.ruc);
            $("#razon_social").val(data.razon_social);
            $("#direccion").val(data.direccion);
            $("#observaciones").val(data.observaciones);

            refrescarSelectPicker("#id_usuario_docente");

            cargarEstructura(id);
            MostrarFormulario();
        }
    );
}

function cargarEstructura(id) {
    $.post(
        link + "listar_estructura",
        {
            id: id,
        },
        function (data) {
            let registros = JSON.parse(data);

            estructura = [];

            registros.forEach(function (reg) {
                let lectivo = estructura.find(x => x.id == reg.lectivo_id);

                if (!lectivo) {
                    lectivo = {
                        id: reg.lectivo_id,
                        nombre: reg.lectivo_nombre,
                        niveles: []
                    };
                    estructura.push(lectivo);
                }

                let nivel = lectivo.niveles.find(x => x.id == reg.nivel_id);

                if (!nivel) {
                    nivel = {
                        id: reg.nivel_id,
                        nombre: reg.nivel_nombre,
                        grados: []
                    };
                    lectivo.niveles.push(nivel);
                }

                let grado = nivel.grados.find(x => x.id == reg.grado_id);

                if (!grado) {
                    grado = {
                        id: reg.grado_id,
                        nombre: reg.grado_nombre,
                        secciones: []
                    };
                    nivel.grados.push(grado);
                }

                if (reg.seccion_id != null) {
                    grado.secciones.push({
                        id: reg.seccion_id,
                        nombre: reg.seccion_nombre
                    });
                }
            });

            if (estructura.length == 0) {
                estructuraInicial();
            }

            estructura.forEach(function (lectivo) {
                if (lectivo.niveles.length == 0) {
                    lectivo.niveles.push({
                        id: "",
                        nombre: "",
                        grados: [
                            {
                                id: "",
                                nombre: "",
                                secciones: [
                                    {
                                        id: "",
                                        nombre: ""
                                    }
                                ]
                            }
                        ]
                    });
                }

                lectivo.niveles.forEach(function (nivel) {
                    if (nivel.grados.length == 0) {
                        nivel.grados.push({
                            id: "",
                            nombre: "",
                            secciones: [
                                {
                                    id: "",
                                    nombre: ""
                                }
                            ]
                        });
                    }

                    nivel.grados.forEach(function (grado) {
                        if (grado.secciones.length == 0) {
                            grado.secciones.push({
                                id: "",
                                nombre: ""
                            });
                        }
                    });
                });
            });

            dibujarEstructura();
        }
    );
}

function activar(id) {
    let condicion = confirm("¿ACTIVAR?");

    if (condicion === true) {
        $.ajax({
            type: "POST",
            url: link + "activar",
            data: {
                id: id,
            },
            success: function (datos) {
                alert(datos);
                tabla.ajax.reload();
            },
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
            data: {
                id: id,
            },
            success: function (datos) {
                alert(datos);
                tabla.ajax.reload();
            },
        });
    } else {
        alert("CANCELADO");
    }
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