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
            <h5 class="border-bottom pb-2 mb-0"><b>PAGOS DE MATRÍCULA - LISTADO</b></h5>
            <div class="p-3">
                <table class="table table-hover" id="myTable">
                    <thead>
                        <tr>
                            <th>NUMERACION</th>
                            <th>FECHA</th>
                            <th>APODERADO</th>
                            <th>ALUMNO</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="my-3 p-3 bg-body rounded shadow-sm" id="formulario">
            <h5 class="border-bottom pb-2 mb-0"><b>PAGOS DE MATRÍCULA - FORMULARIO</b></h5>
            <form id="frm_form" name="frm_form" method="post">
                <input type="hidden" id="id" name="id" placeholder="id" class="form-control">

                <div class="p-3">
                    <label for="id_matricula_detalle" class="form-label"><b>DETALLE DE MATRÍCULA:</b></label>
                    <div class="input-group">
                        <select id="id_matricula_detalle" name="id_matricula_detalle" class="form-control selectpicker" data-live-search="true"></select>
                    </div>
                </div>

                <div class="p-3">
                    <label for="apoderado_dni" class="form-label"><b>FECHA / NUMERACION:</b></label>
                    <div class="input-group">
                        <input type="date" id="fecha" name="fecha" class="form-control">
                        <input type="text" id="numeracion" name="numeracion" placeholder="Numeración" class="form-control">
                    </div>
                </div>

                <div class="p-3">
                    <label for="descripcion" class="form-label"><b>DESCRIPCIÓN:</b></label>
                    <div class="input-group">
                        <textarea style="height: 250px;" id="descripcion" name="descripcion" placeholder="Descripción" class="form-control"></textarea>
                    </div>
                </div>

                <div class="p-3">
                    <label for="apoderado_dni" class="form-label"><b>MONTO / METODO:</b></label>
                    <div class="input-group">
                    <input type="number" step="0.01" id="monto" name="monto" placeholder="Monto" class="form-control">
                    <select id="id_matricula_metodo_pago" name="id_matricula_metodo_pago" class="form-control selectpicker" data-live-search="true"></select>
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
    <script src="Matricula_pago.js"></script>
<?php
}
ob_end_flush();
?>