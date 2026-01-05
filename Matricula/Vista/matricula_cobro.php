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
            <h5 class="border-bottom pb-2 mb-0"><b>COBRO - LISTADO</b></h5>
            <div class="p-3">
                <table class="table" id="myTable">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>NOMBRE</th>
                            <th>APERTURA</th>
                            <th>OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <small class="d-block text-end mt-3">
                <button type="button" onclick="MostrarFormulario();listar_matricula_mes_activas();" class="btn btn-success">Agregar</button>
            </small>
        </div>

        <div class="my-3 p-3 bg-body rounded shadow-sm" id="formulario" style="display:none;">
            <h5 class="border-bottom pb-2 mb-0"><b>COBRO - FORMULARIO</b></h5>
            <form id="frm_form" name="frm_form" method="post">
                <input type="hidden" id="id" name="id" class="form-control">

                <div class="p-3">
                    <label for="nombre" class="form-label"><b>NOMBRE:</b></label>
                    <div class="input-group">
                        <input type="text" id="nombre" name="nombre" placeholder="Nombre del cobro" class="form-control">
                    </div>
                </div>

                <div class="p-3">
                    <label for="apertura" class="form-label"><b>APERTURA:</b></label>
                    <div class="input-group">
                        <input style="width: 30px; height: 30px;" type="radio" name="apertura" id="apertura_si" value="1"> SI
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <input style="width: 30px; height: 30px;" type="radio" name="apertura" id="apertura_no" value="0"> NO
                    </div>
                </div>

                <div class="p-3">
                    <label class="form-label"><b>MESES:</b></label>
                    <table class="table table-bordered" id="tablaMeses">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">MES</th>
                                <th scope="col">SI / NO</th>
                                <th scope="col">OBSERVACIONES</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="p-3">
                    <label for="observaciones" class="form-label"><b>OBSERVACIONES:</b></label>
                    <div class="input-group">
                        <textarea name="observaciones" id="observaciones" placeholder="Observaciones" class="form-control"></textarea>
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
    <script src="matricula_cobro.js"></script>
<?php
}
ob_end_flush();
?>
