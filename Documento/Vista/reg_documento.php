<?php
ob_start();
session_start();

if (!isset($_SESSION['nombre'])) {
    header("Location: ../../Inicio/Controlador/Acceso.php?op=salir");
} else {
?>
    <?php include "../../General/Include/1_header.php"; ?>
    <main class="container">
        <!-- TITULO -->
        <?php include "../../General/Include/3_body.php"; ?>

        <!-- CUERPO_INICIO -->
        <div class="my-3 p-3 bg-body rounded shadow-sm" id="listado">
            <h5 class="border-bottom pb-2 mb-0"><b>DETALLE DE DOCUMENTOS - LISTADO</b></h5>
            <div class="p-3">
                <table class="table table-hover" id="myTable">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>MATRICULA</th>
                            <th>APODERADO</th>
                            <th>ALUMNO</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="my-3 p-3 bg-body rounded shadow-sm" id="formulario" style="display: none;">
            <h5 class="border-bottom pb-2 mb-0"><b>DETALLE DE DOCUMENTOS - FORMULARIO</b></h5>

            <!-- Información adicional -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td>LECTIVO / NIVEL / GRADO</td>
                            <td><span id="lectivo"></span> / <span id="nivel"></span> / <span id="grado"></span></td>
                        </tr>
                        <tr>
                            <td>SECCIÓN</td>
                            <td><span id="seccion"></span></td>
                        </tr>
                        <tr>
                            <td>APODERADO</td>
                            <td><span id="apoderado"></span></td>
                        </tr>
                        <tr>
                            <td>ALUMNO</td>
                            <td><span id="alumno"></span></td>
                        </tr>
                        <tr>
                            <td>CATEGORÍA</td>
                            <td><span id="categoria_matricula"></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <form id="frm_form" name="frm_form" method="post">
                <!-- Campos ocultos -->
                <input type="hidden" id="id_matricula_detalle" name="matricula_detalle_id">

                <!-- Tabla de documentos -->
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nº</th>
                                <th>DETALLE</th>
                                <th>NOMBRE</th>
                                <th>***</th>
                                <th>ENTREGADO (SI / NO)</th>
                                <th>OBSERVACIONES</th>
                            </tr>
                        </thead>
                        <tbody id="documento_list">
                            <!-- Aquí se cargarán los documentos dinámicamente -->
                        </tbody>
                    </table>
                </div>

                <!-- Botones de acción -->
                <div class="p-3">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" onclick="MostrarListado();" class="btn btn-secondary">Cancelar</button>
                </div>
            </form>
        </div>


        <!-- CUERPO_FIN -->

    </main>
    <?php include "../../General/Include/2_footer.php"; ?>
    <script src="reg_documento.js"></script>
<?php
}
ob_end_flush();
?>