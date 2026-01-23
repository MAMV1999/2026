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

                <div class="p-3 d-flex gap-2">
                    <button type="button" class="btn btn-info" onclick="agregarFila();">Agregar Fila</button>

                    <!-- Botón para abrir modal (opcional dentro del formulario también) -->
                    <button type="button" class="btn btn-primary" onclick="abrirModalBloque($('#id_matricula').val());">
                        Agregar en Bloque
                    </button>
                </div>

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

                <div class="p-3 d-flex gap-2">
                    <button type="button" class="btn btn-info" onclick="agregarFila();">Agregar Fila</button>
                    <button type="button" class="btn btn-primary" onclick="abrirModalBloque($('#id_matricula').val());">
                        Agregar en Bloque
                    </button>
                </div>

                <div class="p-3">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" onclick="MostrarListado();" class="btn btn-secondary">Cancelar</button>
                </div>
            </form>
        </div>

        <!-- MODAL: AGREGAR EN BLOQUE -->
        <div class="modal fade" id="modalBloque" tabindex="-1" aria-labelledby="modalBloqueLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modalBloqueLabel">Agregar útiles en bloque</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <div class="alert alert-info py-2" id="bloque_info">
                            Pega tu lista: cada línea será un útil distinto.
                        </div>

                        <label class="form-label"><b>Lista (una línea = un útil)</b></label>
                        <textarea id="txt_bloque" class="form-control" rows="10" placeholder="Ejemplo:
Cuaderno A4
Lápiz 2B
Borrador
Regla 30 cm"></textarea>

                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="chk_reemplazar">
                            <label class="form-check-label" for="chk_reemplazar">
                                Reemplazar la lista actual (borra la tabla y agrega solo lo pegado)
                            </label>
                        </div>

                        <small class="text-muted d-block mt-2">
                            Tip: si pegas con viñetas (•) o numeración (1.), el sistema lo limpia automáticamente.
                        </small>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" id="btn_aplicar_bloque">Agregar a la tabla</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /MODAL -->

    </main>

    <?php include "../../General/Include/2_footer.php"; ?>
    <script src="registro_utiles.js"></script>

<?php
}
ob_end_flush();
?>
