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
        <h5 class="border-bottom pb-2 mb-0"><b>CATEGORÍA DE MATRÍCULA - LISTADO</b></h5>
        <div class="p-3">
            <table class="table" id="myTable">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>NOMBRE</th>
                        <th>OPCIONES</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <small class="d-block text-end mt-3">
            <button type="button" onclick="MostrarFormulario();" class="btn btn-success">Agregar</button>
        </small>
    </div>

    <div class="my-3 p-3 bg-body rounded shadow-sm" id="formulario">
        <h5 class="border-bottom pb-2 mb-0"><b>CATEGORÍA DE MATRÍCULA - FORMULARIO</b></h5>
        <form id="frm_form" name="frm_form" method="post">
            <input type="hidden" id="id" name="id" class="form-control">

            <div class="p-3">
                <label for="nombre" class="form-label"><b>NOMBRE:</b></label>
                <div class="input-group">
                    <input type="text" id="nombre" name="nombre" placeholder="Nombre de la categoría de matrícula" class="form-control">
                </div>
            </div>

            <div class="p-3">
                <label for="observaciones" class="form-label"><b>OBSERVACIONES:</b></label>
                <div class="input-group">
                    <input type="text" id="observaciones" name="observaciones" placeholder="Observaciones" class="form-control">
                </div>
            </div>

            <div class="p-3">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" onclick="MostrarListado();" class="btn btn-secondary">Cancelar</button>
            </div>
        </form>
    </div>
</main>
<?php include "../../General/Include/2_footer.php"; ?>
<script src="matricula_categoria.js"></script>
<?php
}
ob_end_flush();
?>
