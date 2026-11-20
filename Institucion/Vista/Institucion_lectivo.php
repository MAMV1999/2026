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
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
            <div>
                <h5 class="mb-1"><b>AÑOS LECTIVOS</b></h5>
                <small class="text-body-secondary">Cada año lectivo pertenece a una institución.</small>
            </div>
            <button type="button" onclick="MostrarFormulario();cargar_instituciones();" class="btn btn-success">Agregar año lectivo</button>
        </div>

        <div class="alert alert-info py-2">
            Al activar un año lectivo, los demás años lectivos de la misma institución quedarán inactivos.
        </div>

        <div class="table-responsive p-2">
            <table class="table" id="myTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>AÑO</th>
                        <th>NOMBRE DEL AÑO LECTIVO</th>
                        <th>INSTITUCIÓN</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="my-3 p-3 bg-body rounded shadow-sm" id="formulario">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
            <div>
                <h5 class="mb-1"><b>DATOS DEL AÑO LECTIVO</b></h5>
                <small class="text-body-secondary">Seleccione la institución y registre el año que se utilizará en la estructura académica.</small>
            </div>
            <button type="button" onclick="MostrarListado();" class="btn btn-secondary btn-sm">Volver al listado</button>
        </div>

        <form id="frm_form" name="frm_form" method="post">
            <input type="hidden" id="id" name="id" class="form-control">

            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="nombre" class="form-label"><b>Año lectivo:</b></label>
                            <input type="text" id="nombre" name="nombre" placeholder="Ej.: 2026" class="form-control" required>
                        </div>
                        <div class="col-md-8">
                            <label for="id_institucion" class="form-label"><b>Institución:</b></label>
                            <select id="id_institucion" name="id_institucion" class="form-control selectpicker" data-live-search="true" required></select>
                        </div>
                        <div class="col-12">
                            <label for="nombre_lectivo" class="form-label"><b>Nombre oficial del año lectivo:</b></label>
                            <input type="text" id="nombre_lectivo" name="nombre_lectivo" placeholder="Ej.: Año de..." class="form-control" maxlength="300" required>
                        </div>
                        <div class="col-12">
                            <label for="observaciones" class="form-label"><b>Observaciones:</b></label>
                            <textarea id="observaciones" name="observaciones" placeholder="Observaciones adicionales" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <button type="button" onclick="MostrarListado();" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar año lectivo</button>
            </div>
        </form>
    </div>
</main>
<?php include "../../General/Include/2_footer.php"; ?>
<script src="Institucion_lectivo.js"></script>
<?php
}
ob_end_flush();
?>
