<?php
ob_start();
session_start();

if (!isset($_SESSION['nombre'])) {
    header("Location: ../../Inicio/Controlador/Acceso.php?op=salir");
} else {
?>
    <?php include "../../General/Include/1_header.php"; ?>
    <main class="container">
        <!-- TÍTULO -->
        <?php include "../../General/Include/3_body.php"; ?>

        <!-- LISTADO DE DOCUMENTOS DE MATRÍCULA -->
        <div class="my-3 p-3 bg-body rounded shadow-sm" id="listado">
            <h5 class="border-bottom pb-2 mb-0"><b>DOCUMENTOS DE MATRÍCULA - LISTADO</b></h5>
            <div class="p-3">
                <table class="table" id="myTable">
                    <thead>
                        <tr>
                            <th>RESPONSABLE</th>
                            <th>NOMBRE DEL DOCUMENTO</th>
                            <th>OBLIGATORIO</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <small class="d-block text-end mt-3">
                <button type="button" onclick="MostrarFormulario();" class="btn btn-success">AGREGAR</button>
            </small>
        </div>

        <!-- FORMULARIO DE DOCUMENTO DE MATRÍCULA -->
        <div class="my-3 p-3 bg-body rounded shadow-sm" id="formulario">
            <h5 class="border-bottom pb-2 mb-0"><b>DOCUMENTO DE MATRÍCULA - FORMULARIO</b></h5>
            <form id="frm_form" name="frm_form" method="post">
                <input type="hidden" id="id" name="id" class="form-control">

                <!-- Pestañas para organización de campos -->
                <div class="p-3">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="documento-tab" data-bs-toggle="tab" data-bs-target="#documento-tab-pane" type="button" role="tab" aria-controls="documento-tab-pane" aria-selected="true">DATOS DEL DOCUMENTO</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="myTabContent">
                        <!-- TAB: DATOS DEL DOCUMENTO -->
                        <div class="tab-pane fade show active" id="documento-tab-pane" role="tabpanel" aria-labelledby="documento-tab">
                            <div class="p-3">
                                <label for="id_matricula_documentos_responsable" class="form-label"><b>RESPONSABLE:</b></label>
                                <select id="id_matricula_documentos_responsable" name="id_matricula_documentos_responsable" class="form-control" data-live-search="true"></select>
                            </div>

                            <div class="p-3">
                                <label for="nombre" class="form-label"><b>NOMBRE DEL DOCUMENTO:</b></label>
                                <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre del Documento">
                            </div>

                            <div class="p-3">
                                <label class="form-label"><b>OBLIGATORIO:</b></label>
                                <div>
                                    <input type="radio" id="obligatorio_si" name="obligatorio" value="1">
                                    <label for="obligatorio_si">Sí</label>
                                    <input type="radio" id="obligatorio_no" name="obligatorio" value="0" checked>
                                    <label for="obligatorio_no">No</label>
                                </div>
                            </div>

                            <div class="p-3">
                                <label for="observaciones" class="form-label"><b>OBSERVACIONES:</b></label>
                                <textarea id="observaciones" name="observaciones" class="form-control" placeholder="Observaciones"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BOTONES DE ACCIÓN -->
                <div class="p-3">
                    <button type="submit" class="btn btn-primary">GUARDAR</button>
                    <button type="button" onclick="MostrarListado();" class="btn btn-secondary">CANCELAR</button>
                </div>
            </form>
        </div>
    </main>
    <?php include "../../General/Include/2_footer.php"; ?>
    <script src="matricula_documentos.js"></script>
<?php
}
ob_end_flush();
?>
