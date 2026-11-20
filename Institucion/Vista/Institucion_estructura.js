var link = "../Controlador/Institucion_estructura.php?op=";
var modalNivel;
var modalGrado;
var modalSeccion;

function init() {
    cargar_lectivos();
    $("#id_institucion_lectivo").on("change", cargar_estructura);

    $("#frm_nivel").on("submit", function (e) {
        guardar_nivel(e);
    });

    $("#frm_grado").on("submit", function (e) {
        guardar_grado(e);
    });

    $("#frm_seccion").on("submit", function (e) {
        guardar_seccion(e);
    });

    modalNivel = new bootstrap.Modal(document.getElementById("modalNivel"));
    modalGrado = new bootstrap.Modal(document.getElementById("modalGrado"));
    modalSeccion = new bootstrap.Modal(document.getElementById("modalSeccion"));

    actualizarFechaHora();
    setInterval(actualizarFechaHora, 1000);
}

function cargar_lectivos() {
    $.ajax({
        url: link + "listar_lectivos_activos",
        type: "POST",
        success: function (r) {
            let selector = $("#id_institucion_lectivo");
            selector.html(r);
            let primero = selector.find("option[value!='']").first().val();
            if (primero) {
                selector.val(primero);
                cargar_estructura();
            } else {
                $("#mensaje_inicial").text("No hay años lectivos activos. Active uno desde Años lectivos.").show();
                $("#estructura").hide();
            }
        },
        error: function () {
            $("#mensaje_inicial").text("No se pudo cargar la lista de años lectivos. Revise la conexión.").show();
        }
    });
}

function cargar_estructura() {
    let id_lectivo = $("#id_institucion_lectivo").val();
    if (!id_lectivo) {
        $("#mensaje_inicial").text("Seleccione un año lectivo activo.").show();
        $("#estructura").hide();
        return;
    }
    $.ajax({
        url: link + "listar_estructura",
        type: "POST",
        data: { id_institucion_lectivo: id_lectivo },
        dataType: "json",
        success: function (respuesta) {
            pintar_estructura(respuesta);
            $("#mensaje_inicial").hide();
            $("#estructura").show();
        },
        error: function () {
            $("#mensaje_inicial").text("No se pudo cargar la estructura académica. Revise la conexión.").show();
            $("#estructura").hide();
        }
    });
}

function pintar_estructura(detalles) {
    let contenedor = $("#lista_niveles");
    contenedor.empty();

    if (!Array.isArray(detalles) || detalles.length === 0) {
        contenedor.html(`
            <div class="alert alert-light border text-center mb-0">
                Este año lectivo todavía no tiene niveles registrados.<br>
                Use el botón <b>Agregar nivel</b> para comenzar.
            </div>
        `);
        return;
    }

    let niveles = [];

    detalles.forEach(function (item) {
        let nivel = niveles.find(x => x.id == item.nivel_id);

        if (!nivel) {
            nivel = {
                id: item.nivel_id,
                nombre: item.nivel_nombre,
                observaciones: item.nivel_observaciones || "",
                estado: item.nivel_estado,
                grados: []
            };
            niveles.push(nivel);
        }

        if (item.grado_id != null) {
            let grado = nivel.grados.find(x => x.id == item.grado_id);

            if (!grado) {
                grado = {
                    id: item.grado_id,
                    nombre: item.grado_nombre,
                    observaciones: item.grado_observaciones || "",
                    estado: item.grado_estado,
                    secciones: []
                };
                nivel.grados.push(grado);
            }

            if (item.seccion_id != null) {
                let existe = grado.secciones.find(x => x.id == item.seccion_id);
                if (!existe) {
                    grado.secciones.push({
                        id: item.seccion_id,
                        nombre: item.seccion_nombre,
                        observaciones: item.seccion_observaciones || "",
                        estado: item.seccion_estado
                    });
                }
            }
        }
    });

    niveles.forEach(function (nivel) {
        let idNivel = Number(nivel.id);
        let estadoNivel = nivel.estado == 1
            ? `<button type="button" class="btn btn-outline-danger btn-sm" onclick="cambiar_estado('nivel', ${idNivel}, 0)">Desactivar</button>`
            : `<button type="button" class="btn btn-outline-primary btn-sm" onclick="cambiar_estado('nivel', ${idNivel}, 1)">Activar</button>`;

        let gradosHtml = "";

        if (nivel.grados.length === 0) {
            gradosHtml = `
                <tr>
                    <td colspan="4" class="text-center text-body-secondary py-3">
                        No hay grados registrados en este nivel.
                    </td>
                </tr>
            `;
        } else {
            nivel.grados.forEach(function (grado, indiceGrado) {
                let idGrado = Number(grado.id);
                let collapseId = `secciones_grado_${idGrado}`;
                let estadoGrado = grado.estado == 1
                    ? `<span class="badge bg-success ms-2">ACTIVO</span>`
                    : `<span class="badge bg-secondary ms-2">INACTIVO</span>`;
                let accionGrado = grado.estado == 1
                    ? `<button type="button" class="btn btn-outline-danger btn-sm" onclick="cambiar_estado('grado', ${idGrado}, 0)">Desactivar</button>`
                    : `<button type="button" class="btn btn-outline-primary btn-sm" onclick="cambiar_estado('grado', ${idGrado}, 1)">Activar</button>`;

                let seccionesHtml = "";
                if (grado.secciones.length === 0) {
                    seccionesHtml = `
                        <tr>
                            <td colspan="4" class="text-center text-body-secondary py-3">
                                Este grado todavía no tiene secciones registradas.
                            </td>
                        </tr>
                    `;
                } else {
                    grado.secciones.forEach(function (seccion, indiceSeccion) {
                        let idSeccion = Number(seccion.id);
                        let estadoSeccion = seccion.estado == 1
                            ? `<span class="badge bg-success">ACTIVA</span>`
                            : `<span class="badge bg-secondary">INACTIVA</span>`;
                        let accionSeccion = seccion.estado == 1
                            ? `<button type="button" class="btn btn-outline-danger btn-sm" onclick="cambiar_estado('seccion', ${idSeccion}, 0)">Desactivar</button>`
                            : `<button type="button" class="btn btn-outline-primary btn-sm" onclick="cambiar_estado('seccion', ${idSeccion}, 1)">Activar</button>`;

                        seccionesHtml += `
                            <tr>
                                <th scope="row" class="text-center">${indiceSeccion + 1}</th>
                                <td>${escapeHtml(seccion.nombre)}</td>
                                <td class="text-center">${estadoSeccion}</td>
                                <td class="text-center">
                                    <div class="d-flex flex-wrap justify-content-center gap-1">
                                        <button type="button" class="btn btn-warning btn-sm" onclick="editar_seccion(${idSeccion})">Editar</button>
                                        ${accionSeccion}
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                }

                gradosHtml += `
                    <tr>
                        <th scope="row" class="text-center">${indiceGrado + 1}</th>
                        <td>
                            <b>${escapeHtml(grado.nombre)}</b>
                            ${estadoGrado}
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                data-bs-toggle="collapse" data-bs-target="#${collapseId}"
                                aria-expanded="false" aria-controls="${collapseId}">
                                ${grado.secciones.length} <span aria-hidden="true">⌄</span>
                            </button>
                        </td>
                        <td class="text-center">
                            <div class="d-flex flex-wrap justify-content-center gap-1">
                                <button type="button" class="btn btn-warning btn-sm" onclick="editar_grado(${idGrado})">Editar</button>
                                ${accionGrado}
                                <button type="button" class="btn btn-success btn-sm" onclick="nueva_seccion(${idGrado})">+ Sección</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="p-0 border-0">
                            <div class="collapse" id="${collapseId}">
                                <div class="p-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <b>SECCIONES DE ${escapeHtml(grado.nombre)}</b>
                                        <span class="text-body-secondary small">${grado.secciones.length} registro(s)</span>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width: 8%;">N.º</th>
                                                    <th>SECCIÓN</th>
                                                    <th class="text-center" style="width: 18%;">ESTADO</th>
                                                    <th class="text-center" style="width: 30%;">ACCIÓN</th>
                                                </tr>
                                            </thead>
                                            <tbody>${seccionesHtml}</tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }

        contenedor.append(`
            <div class="card mb-3 shadow-sm">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <b>NIVEL: ${escapeHtml(nivel.nombre)}</b>
                        ${nivel.estado == 1 ? '<span class="badge bg-success ms-2">ACTIVO</span>' : '<span class="badge bg-secondary ms-2">INACTIVO</span>'}
                    </div>
                    <div class="d-flex flex-wrap gap-1">
                        <button type="button" class="btn btn-warning btn-sm" onclick="editar_nivel(${idNivel})">Editar nivel</button>
                        ${estadoNivel}
                        <button type="button" class="btn btn-success btn-sm" onclick="nuevo_grado(${idNivel})">+ Agregar grado</button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 8%;">#</th>
                                    <th>DESCRIPCIÓN</th>
                                    <th class="text-center" style="width: 18%;">N.º DE SECCIONES</th>
                                    <th class="text-center" style="width: 34%;">ACCIÓN</th>
                                </tr>
                            </thead>
                            <tbody>${gradosHtml}</tbody>
                        </table>
                    </div>
                </div>
            </div>
        `);
    });
}

function nuevo_nivel() {
    let id_lectivo = $("#id_institucion_lectivo").val();
    if (!id_lectivo) {
        alert("Seleccione un año lectivo");
        return;
    }

    $("#frm_nivel")[0].reset();
    $("#nivel_id").val("");
    $("#nivel_id_lectivo").val(id_lectivo);
    modalNivel.show();
}

function editar_nivel(id) {
    $.post(link + "mostrar_nivel", { id: id }, function (data) {
        let item = JSON.parse(data);
        $("#nivel_id").val(item.id);
        $("#nivel_id_lectivo").val(item.id_institucion_lectivo);
        $("#nivel_nombre").val(item.nombre);
        $("#nivel_observaciones").val(item.observaciones);
        modalNivel.show();
    });
}

function nuevo_grado(id_nivel) {
    $("#frm_grado")[0].reset();
    $("#grado_id").val("");
    $("#grado_id_nivel").val(id_nivel);
    modalGrado.show();
}

function editar_grado(id) {
    $.post(link + "mostrar_grado", { id: id }, function (data) {
        let item = JSON.parse(data);
        $("#grado_id").val(item.id);
        $("#grado_id_nivel").val(item.id_institucion_nivel);
        $("#grado_nombre").val(item.nombre);
        $("#grado_observaciones").val(item.observaciones);
        modalGrado.show();
    });
}

function nueva_seccion(id_grado) {
    $("#frm_seccion")[0].reset();
    $("#seccion_id").val("");
    $("#seccion_id_grado").val(id_grado);
    modalSeccion.show();
}

function editar_seccion(id) {
    $.post(link + "mostrar_seccion", { id: id }, function (data) {
        let item = JSON.parse(data);
        $("#seccion_id").val(item.id);
        $("#seccion_id_grado").val(item.id_institucion_grado);
        $("#seccion_nombre").val(item.nombre);
        $("#seccion_observaciones").val(item.observaciones);
        modalSeccion.show();
    });
}

function guardar_nivel(e) {
    e.preventDefault();
    $.ajax({
        url: link + "guardar_nivel",
        type: "POST",
        data: $("#frm_nivel").serialize(),
        success: function (datos) {
            alert(datos);
            modalNivel.hide();
            cargar_estructura();
        }
    });
}

function guardar_grado(e) {
    e.preventDefault();
    $.ajax({
        url: link + "guardar_grado",
        type: "POST",
        data: $("#frm_grado").serialize(),
        success: function (datos) {
            alert(datos);
            modalGrado.hide();
            cargar_estructura();
        }
    });
}

function guardar_seccion(e) {
    e.preventDefault();
    $.ajax({
        url: link + "guardar_seccion",
        type: "POST",
        data: $("#frm_seccion").serialize(),
        success: function (datos) {
            alert(datos);
            modalSeccion.hide();
            cargar_estructura();
        }
    });
}

function cambiar_estado(tipo, id, estado) {
    let accion = estado == 1 ? "ACTIVAR" : "DESACTIVAR";
    if (!confirm("¿" + accion + " este registro?")) {
        return;
    }

    $.post(link + "cambiar_estado", { tipo: tipo, id: id, estado: estado }, function (datos) {
        alert(datos);
        cargar_estructura();
    });
}

function escapeHtml(texto) {
    return $("<div>").text(texto == null ? "" : texto).html();
}

init();
