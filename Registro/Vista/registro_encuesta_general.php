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
        <h5 class="border-bottom pb-2 mb-0"><b>ENCUESTAS - LISTADO</b></h5>

        <div class="p-3">
            <table class="table" id="myTable">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>ENCUESTA</th>
                    <th>FECHAS</th>
                    <th>DÍAS RESTANTES</th>
                    <th>CALIFICACIÓN</th>
                    <th>OPCIONES</th>
                </tr>
            </thead>
                <tbody></tbody>
            </table>
        </div>

        <small class="d-block text-end mt-3">
            <button type="button" onclick="MostrarFormulario(); limpiar(); cargar_docentes(); cargar_alumnos();" class="btn btn-success">
                Agregar
            </button>
        </small>
    </div>

    <div class="my-3 p-3 bg-body rounded shadow-sm" id="formulario">
        <h5 class="border-bottom pb-2 mb-0"><b>ENCUESTAS - FORMULARIO</b></h5>

        <form id="frm_form" name="frm_form" method="post">

            <input type="hidden" id="id" name="id">

            <div class="p-3">
                <label><b>NOMBRE DE LA ENCUESTA</b></label>
                <input type="text" id="nombre" name="nombre" class="form-control" required>
            </div>

            <div class="p-3">
                <table class="table table-borderless">
                    <tr>
                        <td>
                            <label><b>FECHA INICIO</b></label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" required>
                        </td>
                        <td>
                            <label><b>FECHA FIN</b></label>
                            <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" required>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label><b>CALIFICACIÓN MENOR</b></label>
                            <input type="number" id="calificacion_menor" name="calificacion_menor" class="form-control" required>
                        </td>
                        <td>
                            <label><b>CALIFICACIÓN MAYOR</b></label>
                            <input type="number" id="calificacion_mayor" name="calificacion_mayor" class="form-control" required>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="p-3">
                <label><b>DOCENTES</b></label>

                <div class="mb-2">
                    <button type="button" class="btn btn-sm btn-success" onclick="marcarTodosDocentes()">Marcar todos</button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="desmarcarTodosDocentes()">Desmarcar todos</button>
                </div>

                <table class="table table-bordered table-hover" id="tabla_docentes">
                    <thead>
                        <tr>
                            <th style="width: 120px;">SELECCIONAR</th>
                            <th>DOCENTE</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="p-3">
                <label><b>ALUMNOS MATRICULADOS</b></label>

                <div class="mb-2">
                    <button type="button" class="btn btn-sm btn-success" onclick="marcarTodosAlumnos()">Marcar todos los alumnos</button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="desmarcarTodosAlumnos()">Desmarcar todos los alumnos</button>
                </div>

                <div class="accordion" id="accordionAlumnos"></div>
            </div>

            <div class="p-3">
                <label><b>OBSERVACIONES</b></label>
                <textarea id="observaciones" name="observaciones" class="form-control"></textarea>
            </div>

            <div class="p-3">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" onclick="MostrarListado();" class="btn btn-secondary">Cancelar</button>
            </div>

        </form>
    </div>

</main>

<?php include "../../General/Include/2_footer.php"; ?>
<script src="registro_encuesta_general.js"></script>

<?php
}
ob_end_flush();
?>