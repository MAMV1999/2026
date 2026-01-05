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
            <h5 class="border-bottom pb-2 mb-0"><b>MATRICULA DETALLE - LISTADO</b></h5>
            <div class="p-3">
                <table class="table" id="myTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>MATRICULA</th>
                            <th>APODERADO</th>
                            <th>ALUMNO</th>
                            <th>ESTADO</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="my-3 p-3 bg-body rounded shadow-sm" id="formulario">
            <h5 class="border-bottom pb-2 mb-0"><b>MATRICULA DETALLE - FORMULARIO</b></h5>
            <form id="frm_form" name="frm_form" method="post">
                <input type="hidden" id="id" name="id" placeholder="id" class="form-control">

                <div class="p-3">
                    <label for="apoderado_dni" class="form-label"><b>MATRICULA:</b></label>
                    <div class="input-group">
                        <select id="id_matricula" name="id_matricula" class="form-control selectpicker" data-live-search="true"></select>
                        <select id="id_matricula_categoria" name="id_matricula_categoria" class="form-control selectpicker" data-live-search="true"></select>
                    </div>
                </div>

                <div class="p-3">
                    <label for="id_usuario_apoderado" class="form-label"><b>APODERADO:</b></label>
                    <select id="id_usuario_apoderado" name="id_usuario_apoderado" class="form-control selectpicker" data-live-search="true"></select>
                </div>

                <div class="p-3">
                    <label for="id_usuario_alumno" class="form-label"><b>ALUMNO:</b></label>
                    <select id="id_usuario_alumno" name="id_usuario_alumno" class="form-control selectpicker" data-live-search="true"></select>
                </div>

                <div class="p-3">
                    <label for="id_usuario_apoderado_referido" class="form-label"><b>APODERADO REFERIDO:</b></label>
                    <select id="id_usuario_apoderado_referido" name="id_usuario_apoderado_referido" class="form-control selectpicker" data-live-search="true"></select>
                </div>

                <div class="p-3">
                    <label for="descripcion" class="form-label"><b>DESCRIPCION:</b></label>
                    <textarea id="descripcion" name="descripcion" placeholder="Descripcion" class="form-control" style="height: 250px;"></textarea>
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
    <script src="matricula_editar.js"></script>
<?php
}
ob_end_flush();
?>