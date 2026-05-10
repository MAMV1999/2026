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
        <h5 class="border-bottom pb-2 mb-0"><b>ENCUESTAS DISPONIBLES</b></h5>

        <div class="p-3">
            <table class="table" id="myTable">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>ENCUESTA</th>
                        <th>FECHAS</th>
                        <th>RANGO</th>
                        <th>ESTADO</th>
                        <th>OPCIÓN</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="my-3 p-3 bg-body rounded shadow-sm" id="formulario">
        <h5 class="border-bottom pb-2 mb-0">
            <b>RESPONDER ENCUESTA</b>
        </h5>

        <form id="frm_form" name="frm_form" method="post">

            <input type="hidden" id="encuesta_id" name="encuesta_id">
            <input type="hidden" id="encuesta_alumno_id" name="encuesta_alumno_id">

            <div class="p-3">
                <h5 id="titulo_encuesta"></h5>
                <p id="rango_calificacion" class="text-muted"></p>
            </div>

            <div class="p-3" id="contenedor_docentes">
            </div>

            <div class="p-3">
                <button type="submit" class="btn btn-primary">Enviar encuesta</button>
                <button type="button" onclick="MostrarListado();" class="btn btn-secondary">Cancelar</button>
            </div>

        </form>
    </div>

</main>

<?php include "../../General/Include/2_footer.php"; ?>
<script src="registro_encuesta_registro.js"></script>

<?php
}
ob_end_flush();
?>