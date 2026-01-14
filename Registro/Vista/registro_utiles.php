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
            <h5 class="border-bottom pb-2 mb-0"><b>MATRÍCULAS - LISTADO</b></h5>
            <div class="p-3">
                <table class="table table-hover" id="myTable">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>MATRÍCULA</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <small class="d-block text-end mt-3"><br></small>
        </div>

        <!-- FORMULARIO -->
        <div class="my-3 p-3 bg-body rounded shadow-sm" id="formulario" style="display:none;">
            <h5 class="border-bottom pb-2 mb-0"><b id="titulo_matricula">MATRÍCULAS - FORMULARIO</b></h5>

            <div class="p-2">
                <span class="badge bg-secondary" id="matricula_texto"></span>
            </div>

            <form id="frm_form" name="frm_form" method="post">
                <input type="hidden" name="id_matricula" id="id_matricula">

                <div class="p-3"><button type="button" class="btn btn-info" onclick="agregarFila();">Agregar Fila</button></div>

                <table class="table table-bordered" id="tabla_dinamica">
                    <thead>
                        <tr>
                            <th style="width: 5%;">N°</th>
                            <th style="width: 70%;">NOMBRE</th>
                            <th style="width: 20%;">OBSERVACIONES</th>
                            <th style="width: 5%;">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <div class="p-3"><button type="button" class="btn btn-info" onclick="agregarFila();">Agregar Fila</button></div>

                <div class="p-3">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" onclick="MostrarListado();" class="btn btn-secondary">Cancelar</button>
                </div>
            </form>
        </div>
    </main>

    <?php include "../../General/Include/2_footer.php"; ?>
    <script src="registro_utiles.js"></script>

<?php
}
ob_end_flush();
?>