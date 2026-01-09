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
                        <thead>
                            <tr>
                                <th colspan="4" class="" scope="col">MATRICULA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>LECTIVO</td>
                                <td>NIVEL</td>
                                <td>GRADO</td>
                                <td>SECCION</td>
                            </tr>
                            <tr>
                                <td><span id="lectivo"></span></td>
                                <td><span id="nivel"></span></td>
                                <td><span id="grado"></span></td>
                                <td><span id="seccion"></span></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th colspan="4" class="" scope="col">APODERADO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>DOCUMENTO</td>
                                <td>NUMERO DOCUMENTO</td>
                                <td>NOMBRE Y APELLIDO</td>
                                <td>TELÉFONO</td>
                            </tr>
                            <tr>
                                <td><span id="apoderado_tipo_documento"></span></td>
                                <td><span id="apoderado_numerodocumento"></span></td>
                                <td><span id="apoderado_nombreyapellido"></span></td>
                                <td><span id="apoderado_telefono"></span></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th colspan="3" class="" scope="col">ALUMNO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>DOCUMENTO</td>
                                <td>NUMERO DOCUMENTO</td>
                                <td>NOMBRE Y APELLIDO</td>
                            </tr>
                            <tr>
                                <td><span id="alumno_tipo_documento"></span></td>
                                <td><span id="alumno_numerodocumento"></span></td>
                                <td><span id="alumno_nombreyapellido"></span></td>
                            </tr>
                        </tbody>
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
    <script src="Matricula_mensualidad.js"></script>
<?php
}
ob_end_flush();
?>