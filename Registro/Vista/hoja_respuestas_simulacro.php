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
                <b>REPORTE DE SIMULACRO - LISTADO</b>
            </h5>

            <div class="p-3">
                <table class="table table-hover" id="myTable">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>NIVEL</th>
                            <th>GRADO</th>
                            <th>SECCIÓN</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>

                    <tbody></tbody>
                </table>
            </div>
        </div>
    </main>

    <?php include "../../General/Include/2_footer.php"; ?>
    <script src="hoja_respuestas_simulacro.js"></script>
<?php
}
ob_end_flush();
?>