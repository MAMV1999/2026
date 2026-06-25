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

        <div class="my-3 p-3 bg-body rounded shadow-sm" id="listado">
            <h5 class="border-bottom pb-2 mb-0">
                <b>DETALLE DE MENSUALIDADES POR GRADO - LISTADO</b>
            </h5>

            <div class="p-3">
                <table class="table table-hover" id="myTable">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>AÑO LECTIVO</th>
                            <th>NIVEL</th>
                            <th>GRADO</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>

                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="my-3 p-3 bg-body rounded shadow-sm" id="formulario">
            <h5 class="border-bottom pb-2 mb-0">
                <b>DETALLE DE MENSUALIDADES POR GRADO - FORMULARIO</b>
            </h5>

            <form id="frm_form" name="frm_form" method="post">
                <br>

                <div id="accordionExample" class="accordion">
                    <!-- Aquí se insertarán dinámicamente los alumnos del grado -->
                </div>

                <div class="p-3">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" onclick="MostrarListado();" class="btn btn-secondary">Cancelar</button>
                </div>
            </form>
        </div>
    </main>

    <?php include "../../General/Include/2_footer.php"; ?>
    <script src="mensualidad_x_grado.js"></script>
<?php
}
ob_end_flush();
?>