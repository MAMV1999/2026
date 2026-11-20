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

    <div class="my-3 p-3 bg-body rounded shadow-sm">
        <div class="border-bottom pb-2 mb-3">
            <h5 class="mb-1"><b>ESTRUCTURA ACADÉMICA</b></h5>
            <small class="text-body-secondary">Se muestra automáticamente la estructura del año lectivo activo. También puede seleccionar otro lectivo activo.</small>
        </div>

        <div class="row g-3 align-items-end">
            <div class="col-md-8">
                <label for="id_institucion_lectivo" class="form-label"><b>Año lectivo:</b></label>
                <select id="id_institucion_lectivo" class="form-select"></select>
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-primary w-100" onclick="cargar_estructura();">Cargar estructura</button>
            </div>
        </div>
    </div>

    <div class="my-3 p-3 bg-body rounded shadow-sm" id="contenedor_estructura">
        <div class="alert alert-info mb-0" id="mensaje_inicial">
            Cargando año lectivo activo y estructura académica...
        </div>

        <div id="estructura" style="display:none;">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                <div>
                    <h5 class="mb-1"><b>NIVELES, GRADOS Y SECCIONES</b></h5>
                    <small class="text-body-secondary">Use los botones de cada bloque para agregar o editar la estructura.</small>
                </div>
                <button type="button" class="btn btn-success btn-sm" onclick="nuevo_nivel();">Agregar nivel</button>
            </div>

            <div id="lista_niveles"></div>
        </div>
    </div>

    <!-- MODAL NIVEL -->
    <div class="modal fade" id="modalNivel" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="frm_nivel">
                    <div class="modal-header">
                        <h5 class="modal-title"><b>NIVEL</b></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="nivel_id" name="id">
                        <input type="hidden" id="nivel_id_lectivo" name="id_institucion_lectivo">
                        <div class="mb-3">
                            <label for="nivel_nombre" class="form-label"><b>Nombre del nivel:</b></label>
                            <input type="text" class="form-control" id="nivel_nombre" name="nombre" placeholder="Ej.: INICIAL, PRIMARIA, SECUNDARIA" required>
                        </div>
                        <div class="mb-3">
                            <label for="nivel_observaciones" class="form-label"><b>Observaciones:</b></label>
                            <textarea class="form-control" id="nivel_observaciones" name="observaciones" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar nivel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL GRADO -->
    <div class="modal fade" id="modalGrado" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="frm_grado">
                    <div class="modal-header">
                        <h5 class="modal-title"><b>GRADO</b></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="grado_id" name="id">
                        <input type="hidden" id="grado_id_nivel" name="id_institucion_nivel">
                        <div class="mb-3">
                            <label for="grado_nombre" class="form-label"><b>Nombre del grado:</b></label>
                            <input type="text" class="form-control" id="grado_nombre" name="nombre" placeholder="Ej.: 1 GRADO" required>
                        </div>
                        <div class="mb-3">
                            <label for="grado_observaciones" class="form-label"><b>Observaciones:</b></label>
                            <textarea class="form-control" id="grado_observaciones" name="observaciones" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar grado</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL SECCION -->
    <div class="modal fade" id="modalSeccion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="frm_seccion">
                    <div class="modal-header">
                        <h5 class="modal-title"><b>SECCIÓN</b></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="seccion_id" name="id">
                        <input type="hidden" id="seccion_id_grado" name="id_institucion_grado">
                        <div class="mb-3">
                            <label for="seccion_nombre" class="form-label"><b>Nombre de la sección:</b></label>
                            <input type="text" class="form-control" id="seccion_nombre" name="nombre" placeholder="Ej.: A, B, C" required>
                        </div>
                        <div class="mb-3">
                            <label for="seccion_observaciones" class="form-label"><b>Observaciones:</b></label>
                            <textarea class="form-control" id="seccion_observaciones" name="observaciones" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar sección</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<?php include "../../General/Include/2_footer.php"; ?>
<script src="Institucion_estructura.js"></script>
<?php
}
ob_end_flush();
?>
