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
            <h5 class="border-bottom pb-2 mb-0"><b>DOCENTES - LISTADO</b></h5>
            <div class="p-3">
                <table class="table table-hover" id="myTable">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>DOCUMENTO</th>
                            <th>NOMBRE Y APELLIDO</th>
                            <th>CARGO</th>
                            <th>INFORMACION</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <small class="d-block text-end mt-3">
                <button type="button" onclick="editarTodo();" class="btn btn-primary">EDITAR TODO</button>
                <button type="button" onclick="MostrarFormulario();" class="btn btn-success">AGREGAR</button>
            </small>
        </div>

        <!-- FORMULARIO MASIVO -->
        <div class="my-3 p-3 bg-body rounded shadow-sm" id="formulario">
            <h5 class="border-bottom pb-2 mb-0"><b>DOCENTES - FORMULARIO MASIVO</b></h5>

            <form id="frm_form" name="frm_form" method="post">
                <div class="p-3">
                    <button type="button" class="btn btn-info" onclick="agregarFila();">AGREGAR FILA</button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-hover" id="tabla_dinamica">
                        <thead>
                            <tr>
                                <th style="min-width: 60px;">ACCION</th>
                                <th style="min-width: 100px;">DOC.</th>
                                <th style="min-width: 130px;">N° DOC.</th>
                                <th style="min-width: 400px;">NOMBRE Y APELLIDO</th>
                                <th style="min-width: 150px;">NACIMIENTO</th>
                                <th style="min-width: 130px;">ESTADO CIVIL</th>
                                <th style="min-width: 150px;">SEXO</th>
                                <th style="min-width: 400px;">DIRECCION</th>
                                <th style="min-width: 130px;">TELEFONO</th>
                                <th style="min-width: 300px;">CORREO</th>
                                <th style="min-width: 150px;">CARGO</th>
                                <th style="min-width: 180px;">TIPO CONTRATO</th>
                                <th style="min-width: 160px;">F. INICIO</th>
                                <th style="min-width: 160px;">F. FIN</th>
                                <th style="min-width: 140px;">SUELDO</th>
                                <th style="min-width: 200px;">CTA BANCARIA</th>
                                <th style="min-width: 220px;">CTA INTERBANCARIA</th>
                                <th style="min-width: 150px;">RUC</th>
                                <th style="min-width: 200px;">USUARIO SUNAT</th>
                                <th style="min-width: 200px;">CLAVE SUNAT</th>
                                <th style="min-width: 180px;">USUARIO</th>
                                <th style="min-width: 180px;">CLAVE</th>
                                <th style="min-width: 250px;">OBSERVACIONES</th>
                                <th style="min-width: 130px;">ESTADO</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">GUARDAR</button>
                    <button type="button" onclick="MostrarListado();" class="btn btn-secondary">CANCELAR</button>
                </div>
            </form>
        </div>
    </main>
    <?php include "../../General/Include/2_footer.php"; ?>
    <script src="usuario_docente.js"></script>
<?php
}
ob_end_flush();
?>