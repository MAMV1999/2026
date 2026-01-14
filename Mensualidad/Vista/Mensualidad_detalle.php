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
            <h5 class="border-bottom pb-2 mb-0"><b>DETALLE DE MENSUALIDADES - LISTADO</b></h5>
            <div class="p-3">
                <table class="table" id="myTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>MATRICULA</th>
                            <th>APODERADO</th>
                            <th>ALUMNO</th>
                            <th>CODIGO</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="my-3 p-3 bg-body rounded shadow-sm" id="formulario">
            <h5 class="border-bottom pb-2 mb-0"><b>DETALLE DE MENSUALIDADES - FORMULARIO</b></h5>
            <form id="frm_form" name="frm_form" method="post">
                <input type="hidden" id="id" name="id" placeholder="id" class="form-control">

                <div class="p-3">
                    <label for="detallesRelacionados" class="form-label"><b>DETALLES GENERALES</b></label>
                    <table class="table table-bordered table-hover">

                        <tr class="fw-bold table-secondary"><td colspan="2">INSTITUCION</td></tr>

                        <tr>        <td>LECTIVO</td>                <td><span id="lectivo"></td>                        </tr>
                        <tr>        <td>MATRICULA</td>                <td><span id="matricula"></td>                    </tr>

                        <tr class="fw-bold table-secondary"><td colspan="2">APODERADO</td></tr>

                        <tr>        <td>DOCUMENTO</td>              <td><span id="apoderado_tipo_documento"></td>       </tr>
                        <tr>        <td>NOMBRE</td>                 <td><span id="apoderado_nombreyapellido"></td>      </tr>
                        <tr>        <td>TELÉFONO</td>               <td><span id="apoderado_telefono"></td>             </tr>

                        <tr class="fw-bold table-secondary"><td colspan="2">ALUMNO</td></tr>

                        <tr>        <td>DOCUMENTO</td>              <td><span id="alumno_tipo_documento"></td>          </tr>
                        <tr>        <td>NOMBRE</td>                 <td><span id="alumno_nombreyapellido"></td>         </tr>
                    </table>
                </div>

                <div class="p-3">
                    <label for="detallesRelacionados" class="form-label"><b>DETALLES RELACIONADOS</b></label>
                    <div class="input-group">
                        <table class="table table-bordered table-hover">
                            <thead class="table-secondary">
                                <tr>
                                    <th>N°</th>
                                    <th>MES</th>
                                    <th>VENCIMIENTO</th>
                                    <th>MONTO</th>
                                    <th>PAGADO</th>
                                    <th>OBSERVACIONES</th>
                                </tr>
                            </thead>
                            <tbody id="detallesRelacionados"></tbody>
                        </table>
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
    <script src="Mensualidad_detalle.js"></script>
<?php
}
ob_end_flush();
?>