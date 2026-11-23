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
            <h5 class="border-bottom pb-2 mb-0"><b>ENCUESTAS - RESPUESTAS REGISTRADAS</b></h5>

            <div class="p-3">
                <table class="table table-hover" id="myTable">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>ENCUESTA</th>
                            <th>FECHAS</th>
                            <th>CALIFICACIÓN</th>
                            <th>ALUMNOS QUE RESPONDIERON</th>
                            <th>TOTAL DE RESPUESTAS</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="my-3 p-3 bg-body rounded shadow-sm" id="formulario" style="display: none;">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                <h5 class="mb-0"><b id="titulo_encuesta">DETALLE DE RESPUESTAS</b></h5>
                <button type="button" onclick="MostrarListado();" class="btn btn-secondary btn-sm">VOLVER</button>
            </div>

            <div class="table-responsive mb-3">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td style="width: 25%;"><b>ENCUESTA</b></td>
                            <td><span id="nombre_encuesta"></span></td>
                        </tr>
                        <tr>
                            <td><b>FECHAS</b></td>
                            <td><span id="fechas_encuesta"></span></td>
                        </tr>
                        <tr>
                            <td><b>RANGO DE CALIFICACIÓN</b></td>
                            <td><span id="rango_calificacion"></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="alert alert-info">
                Se muestra el nombre del alumno que respondió la encuesta, junto con sus datos de nivel, grado y sección cuando estén registrados.
            </div>

            <div id="contenedor_respuestas"></div>
        </div>
    </main>

    <?php include "../../General/Include/2_footer.php"; ?>
    <script src="registro_encuestas_detalle.js"></script>
<?php
}
ob_end_flush();
?>
