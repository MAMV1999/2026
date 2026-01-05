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
        <h5 class="border-bottom pb-2 mb-0"><b>INSTITUCIONES LECTIVAS - LISTADO</b></h5>
        <div class="p-3">
            <table class="table" id="myTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>NOMBRE</th>
                        <th>NOMBRE LECTIVO</th>
                        <th>INSTITUCIÓN</th>
                        <th>ESTADO</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <small class="d-block text-end mt-3">
            <button type="button" onclick="MostrarFormulario();cargar_instituciones();" class="btn btn-success">Agregar</button>
        </small>
    </div>

    <div class="my-3 p-3 bg-body rounded shadow-sm" id="formulario">
        <h5 class="border-bottom pb-2 mb-0"><b>INSTITUCIONES LECTIVAS - FORMULARIO</b></h5>
        <form id="frm_form" name="frm_form" method="post">
            <input type="hidden" id="id" name="id" placeholder="id" class="form-control">

            <div class="p-3">
                <label for="nombre" class="form-label"><b>NOMBRE:</b></label>
                <div class="input-group">
                    <input type="text" id="nombre" name="nombre" placeholder="Nombre de la Institución Lectiva" class="form-control">
                </div>
            </div>

            <div class="p-3">
                <label for="nombre_lectivo" class="form-label"><b>NOMBRE LECTIVO:</b></label>
                <div class="input-group">
                    <input type="text" id="nombre_lectivo" name="nombre_lectivo" placeholder="Nombre Lectivo" class="form-control" maxlength="300">
                </div>
            </div>

            <div class="p-3">
                <label for="id_institucion" class="form-label"><b>INSTITUCIÓN:</b></label>
                <div class="input-group">
                    <select id="id_institucion" name="id_institucion" class="form-control selectpicker" data-live-search="true"></select>
                </div>
            </div>

            <div class="p-3">
                <label for="observaciones" class="form-label"><b>OBSERVACIONES:</b></label>
                <div class="input-group">
                    <textarea id="observaciones" name="observaciones" placeholder="Observaciones" class="form-control"></textarea>
                </div>
            </div>

            <div class="p-3">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" onclick="MostrarListado();" class="btn btn-secondary">Cancelar</button>
            </div>
        </form>
    </div>
    <!-- CUERPO_FIN -->

</main>
<?php include "../../General/Include/2_footer.php"; ?>
<script src="Institucion_lectivo.js"></script>
<?php
}
ob_end_flush();
?>
