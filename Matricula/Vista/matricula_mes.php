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

        <!-- LISTADO -->
        <div class="my-3 p-3 bg-body rounded shadow-sm" id="listado">
            <h5 class="border-bottom pb-2 mb-0"><b>MES DE MATRÍCULA - LISTADO</b></h5>
            <div class="p-3">
                <table class="table" id="myTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>INSTITUCIÓN LECTIVA</th>
                            <th>NOMBRE</th>
                            <th>FECHA VENCIMIENTO</th>
                            <th>MORA</th>
                            <th>ESTADO</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <small class="d-block text-end mt-3">
                <button type="button" onclick="MostrarFormulario();cargarInstitucionesLectivas();" class="btn btn-success">Agregar</button>
            </small>
        </div>

        <!-- FORMULARIO -->
        <div class="my-3 p-3 bg-body rounded shadow-sm" id="formulario">
            <h5 class="border-bottom pb-2 mb-0"><b>MES DE MATRÍCULA - FORMULARIO</b></h5>

            <form id="frm_form" name="frm_form" method="post">
                <input type="hidden" id="id" name="id" placeholder="id" class="form-control">

                <div class="p-3">
                    <label for="institucion_lectivo_id" class="form-label"><b>INSTITUCIÓN LECTIVA:</b></label>
                    <div class="input-group">
                        <select id="institucion_lectivo_id" name="institucion_lectivo_id" class="form-control selectpicker" data-live-search="true"></select>
                    </div>
                </div>

                <div class="p-3">
                    <label for="nombre" class="form-label"><b>NOMBRE:</b></label>
                    <div class="input-group">
                        <input type="text" id="nombre" name="nombre" placeholder="Nombre del Mes" class="form-control">
                    </div>
                </div>

                <div class="p-3">
                    <label for="fecha_vencimiento" class="form-label"><b>FECHA DE VENCIMIENTO:</b></label>
                    <div class="input-group">
                        <input type="date" id="fecha_vencimiento" name="fecha_vencimiento" class="form-control">
                    </div>
                </div>

                <div class="p-3">
                    <label for="mora" class="form-label"><b>MORA:</b></label>
                    <div class="input-group">
                        <input type="number" step="0.01" id="mora" name="mora" placeholder="0.00" class="form-control">
                    </div>
                </div>

                <div class="p-3">
                    <label for="observaciones" class="form-label"><b>OBSERVACIONES:</b></label>
                    <div class="input-group">
                        <textarea id="observaciones" name="observaciones" placeholder="Observaciones" class="form-control"></textarea>
                    </div>
                </div>

                <div class="p-3">
                    <input type="hidden" id="estado" name="estado" value="1">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" onclick="MostrarListado();" class="btn btn-secondary">Cancelar</button>
                </div>
            </form>
        </div>

    </main>
    <?php include "../../General/Include/2_footer.php"; ?>
    <script src="matricula_mes.js"></script>
<?php
}
ob_end_flush();
?>
