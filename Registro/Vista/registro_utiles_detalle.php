<?php
ob_start();
session_start();

if (!isset($_SESSION['nombre'])) {
    header("Location: ../../Inicio/Controlador/Acceso.php?op=salir");
} else {
?>
    <?php include "../../General/Include/1_header.php"; ?>
    <main class="container">
        <?php include "../../General/Include/3_body.php"; ?>

        <!-- LISTADO -->
        <div class="my-3 p-3 bg-body rounded shadow-sm" id="listado">
            <h5 class="border-bottom pb-2 mb-0"><b>DETALLE DE ÚTILES - LISTADO</b></h5>
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

        <!-- FORMULARIO -->
        <div class="my-3 p-3 bg-body rounded shadow-sm" id="formulario" style="display: none;">
            <h5 class="border-bottom pb-2 mb-0"><b>DETALLE DE ÚTILES - FORMULARIO</b></h5>

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
                <!-- Campo oculto -->
                <input type="hidden" id="matricula_detalle_id" name="matricula_detalle_id">

                <!-- Tabla de útiles -->
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nº</th>
                                <th>ÚTIL</th>
                                <th>STOCK / CANT.</th>
                                <th>OBSERVACIONES</th>
                            </tr>
                        </thead>
                        <tbody id="utiles_list">
                            <!-- Aquí se cargan los útiles dinámicamente -->
                        </tbody>
                    </table>
                </div>

                <!-- Botones -->
                <div class="p-3">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" onclick="MostrarListado();" class="btn btn-secondary">Cancelar</button>
                </div>
            </form>
        </div>

    </main>
    <?php include "../../General/Include/2_footer.php"; ?>
    <script src="registro_utiles_detalle.js"></script>
<?php
}
ob_end_flush();
?>
