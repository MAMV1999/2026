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
            <h5 class="border-bottom pb-2 mb-0"><b>RETIRO DE LIBROS - LISTADO</b></h5>
            <div class="p-3">
                <table class="table table-hover" id="myTable">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>CÓDIGO RETIRO</th>
                            <th>DOCENTE</th>
                            <th>FECHA</th>
                            <th>CANTIDAD DE LIBROS</th>
                            <th>ESTADO</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <small class="d-block text-end mt-3">
                <button type="button" onclick="MostrarFormulario();" class="btn btn-success">AGREGAR</button>
            </small>
        </div>

        <!-- FORMULARIO -->
        <div class="my-3 p-3 bg-body rounded shadow-sm" id="formulario" style="display:none;">
            <h5 class="border-bottom pb-2 mb-0"><b>RETIRO DE LIBROS - FORMULARIO</b></h5>

            <form id="frm_form" name="frm_form" method="post">
                <input type="hidden" id="biblioteca_retiro_id" name="biblioteca_retiro_id">

                <div class="p-3">
                    <label for="usuario_docente_id" class="form-label"><b>DOCENTE / FECHA:</b></label>
                    <div class="input-group">
                        <select id="usuario_docente_id" name="usuario_docente_id" class="form-control" required></select>
                        <input type="date" id="fecha" name="fecha" class="form-control" required>
                    </div>
                </div>

                <div class="p-3">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalLibros">AGREGAR LIBRO</button>

                    <div class="modal fade" id="modalLibros" tabindex="-1" aria-labelledby="modalLibrosLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="modalLibrosLabel">LIBROS DISPONIBLES</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-bordered" style="width:100%;" id="myTable2">
                                        <thead>
                                            <tr>
                                                <th>CÓDIGO</th>
                                                <th>LIBRO</th>
                                                <th>STOCK</th>
                                                <th>ACCIONES</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">SALIR</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br><br>

                    <div class="table-responsive">
                        <table id="tablaLibros" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:15%;">CÓDIGO</th>
                                    <th style="width:35%;">LIBRO</th>
                                    <th style="width:10%;">STOCK</th>
                                    <th style="width:10%;">CANTIDAD</th>
                                    <th style="width:20%;">OBSERVACIONES</th>
                                    <th style="width:10%;">ELIMINAR</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <div class="p-3">
                    <label for="observaciones" class="form-label"><b>OBSERVACIONES:</b></label>
                    <textarea id="observaciones" name="observaciones" class="form-control" style="height:120px;"></textarea>
                </div>

                <div class="p-3">
                    <button type="submit" class="btn btn-primary">GUARDAR</button>
                    <button type="button" onclick="MostrarListado();" class="btn btn-secondary">CANCELAR</button>
                </div>
            </form>
        </div>

        <!-- MODAL RECIBO PDF -->
        <div class="modal fade" id="modalRecibo" tabindex="-1" aria-labelledby="modalReciboLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width:1100px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalReciboLabel"><b>RECIBO DE RETIRO DE LIBROS</b></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <iframe id="iframeRecibo" title="Recibo de retiro de libros" style="width:100%; height:75vh; border:0;"></iframe>
                    </div>
                    <div class="modal-footer">
                        <a href="#" id="btnAbrirRecibo" target="_blank" class="btn btn-primary">ABRIR / DESCARGAR PDF</a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CERRAR</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include "../../General/Include/2_footer.php"; ?>
    <script src="biblioteca_retiro.js"></script>
<?php
}
ob_end_flush();
?>