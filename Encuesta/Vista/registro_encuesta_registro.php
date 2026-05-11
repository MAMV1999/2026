<?php
ob_start();
session_start();

if (!isset($_SESSION['nombre'])) {
    header("Location: ../../Inicio/Controlador/Acceso.php?op=salir");
} else {
?>

<?php include "../../General/Include/1_header.php"; ?>

<style>
    .estrellas {
        display: inline-block;
        direction: rtl;
        unicode-bidi: bidi-override;
    }

    .estrellas input[type="radio"] {
        display: none;
    }

    .estrellas label {
        font-size: 35px;
        color: #ccc;
        cursor: pointer;
        margin-right: 5px;
    }

    .estrellas label:hover,
    .estrellas label:hover ~ label,
    .estrellas input[type="radio"]:checked ~ label {
        color: #ffc107;
    }
</style>

<main class="container">

    <?php include "../../General/Include/3_body.php"; ?>

    <div class="my-3 p-3 bg-body rounded shadow-sm" id="listado">

        <h5 class="border-bottom pb-2 mb-0">
            <b>ENCUESTAS DISPONIBLES</b>
        </h5>

        <div class="p-3">
            <table class="table" id="myTable">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>ENCUESTA</th>
                        <th>FECHAS</th>
                        <th>CALIFICACIÓN</th>
                        <th>OPCIÓN</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>

    <div class="my-3 p-3 bg-body rounded shadow-sm" id="formulario">

        <h5 class="border-bottom pb-2 mb-0">
            <b id="titulo_encuesta"></b>
        </h5>

        <p class="mt-2 text-muted" id="detalle_encuesta"></p>

        <form id="frm_form" name="frm_form" method="post">

            <input type="hidden" id="encuesta_general_id" name="encuesta_general_id">
            <input type="hidden" id="encuesta_alumno_id" name="encuesta_alumno_id">

            <div class="alert alert-info">
                Califique a cada docente marcando las estrellas y escriba un comentario, RECUERDA QUE TODO COMENTARIO ES ANONIMO.
            </div>

            <div id="contenedor_docentes"></div>

            <div class="p-3">
                <button type="submit" class="btn btn-primary">
                    Enviar encuesta
                </button>

                <button type="button" onclick="MostrarListado();" class="btn btn-secondary">
                    Cancelar
                </button>
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