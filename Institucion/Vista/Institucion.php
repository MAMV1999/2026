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
                <h5 class="mb-1"><b>INSTITUCIONES</b></h5>
                <small class="text-body-secondary">Datos generales de la institución educativa.</small>
            </div>
            <button type="button" onclick="MostrarFormulario();cargar_docentes();" class="btn btn-success">Agregar institución</button>
        </div>

        <div class="table-responsive p-2">
            <table class="table" id="myTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>NOMBRE</th>
                        <th>DIRECTOR</th>
                        <th>RUC - RAZÓN SOCIAL</th>
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
                <h5 class="mb-1"><b>DATOS DE LA INSTITUCIÓN</b></h5>
                <small class="text-body-secondary">Complete la información principal y de contacto.</small>
            </div>
            <button type="button" onclick="MostrarListado();" class="btn btn-secondary btn-sm">Volver al listado</button>
        </div>

        <form id="frm_form" name="frm_form" method="post">
            <input type="hidden" id="id" name="id" class="form-control">

            <div class="card mb-3">
                <div class="card-header"><b>Información principal</b></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label for="nombre" class="form-label"><b>Nombre de la institución:</b></label>
                            <input type="text" id="nombre" name="nombre" placeholder="Ej.: I.E.P. Nombre de la institución" class="form-control" required>
                        </div>
                        <div class="col-md-5">
                            <label for="id_usuario_docente" class="form-label"><b>Director:</b></label>
                            <select id="id_usuario_docente" name="id_usuario_docente" class="form-control selectpicker" data-live-search="true" required></select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header"><b>Contacto y datos fiscales</b></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="telefono" class="form-label"><b>Teléfono:</b></label>
                            <input type="text" id="telefono" name="telefono" placeholder="Teléfono" class="form-control">
                        </div>
                        <div class="col-md-8">
                            <label for="correo" class="form-label"><b>Correo:</b></label>
                            <input type="email" id="correo" name="correo" placeholder="Correo institucional" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="ruc" class="form-label"><b>RUC:</b></label>
                            <input type="text" id="ruc" name="ruc" placeholder="RUC" class="form-control" maxlength="11" required>
                        </div>
                        <div class="col-md-8">
                            <label for="razon_social" class="form-label"><b>Razón social:</b></label>
                            <input type="text" id="razon_social" name="razon_social" placeholder="Razón social" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label for="direccion" class="form-label"><b>Dirección:</b></label>
                            <input type="text" id="direccion" name="direccion" placeholder="Dirección completa" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header"><b>Observaciones</b></div>
                <div class="card-body">
                    <textarea id="observaciones" name="observaciones" placeholder="Observaciones adicionales" class="form-control" rows="3"></textarea>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <button type="button" onclick="MostrarListado();" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar institución</button>
            </div>
        </form>
    </div>
</main>
<?php include "../../General/Include/2_footer.php"; ?>
<script src="Institucion.js"></script>
<?php
}
ob_end_flush();
?>
