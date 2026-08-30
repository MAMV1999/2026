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
            <h5 class="border-bottom pb-2 mb-0"><b>INSTITUCIONES - LISTADO</b></h5>
            <div class="p-3">
                <table class="table" id="myTable">
                    <thead>
                        <tr>
                            <th>ID INSTITUCIÓN</th>
                            <th>INSTITUCIÓN</th>
                            <th>DIRECTOR</th>
                            <th>RUC</th>
                            <th>TELÉFONO</th>
                            <th>ESTADO</th>
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
            <h5 class="border-bottom pb-2 mb-0"><b>INSTITUCIONES - FORMULARIO</b></h5>
            <form id="frm_form" name="frm_form" method="post">
                <input type="hidden" id="id" name="id" placeholder="id" class="form-control">

                <div class="p-3">
                    <label for="nombre" class="form-label"><b>NOMBRE:</b></label>
                    <div class="input-group">
                        <input type="text" id="nombre" name="nombre" placeholder="Nombre de la Institución" class="form-control">
                    </div>
                </div>

                <div class="p-3">
                    <label for="id_usuario_docente" class="form-label"><b>DIRECTOR:</b></label>
                    <div class="input-group">
                        <select id="id_usuario_docente" name="id_usuario_docente" class="form-control selectpicker" data-live-search="true"></select>
                    </div>
                </div>

                <div class="p-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th colspan="4" class="text-center">ESTRUCTURA</th>
                            </tr>
                            <tr>
                                <th class="text-center">LECTIVO</th>
                                <th class="text-center">NIVEL</th>
                                <th class="text-center">GRADO</th>
                                <th class="text-center">SECCIÓN</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_estructura">
                        </tbody>
                    </table>
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
    <script src="Institucion_estructura.js"></script>
<?php
}
ob_end_flush();
?>