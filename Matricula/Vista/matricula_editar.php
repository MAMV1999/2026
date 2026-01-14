<?php
ob_start();
session_start();

if (!isset($_SESSION['nombre'])) {
    header("Location: ../../Inicio/Controlador/Acceso.php?op=salir");
} else {
?>
    <?php include "../../General/Include/1_header.php"; ?>
    <main class="container">
        <!-- TÍTULO -->
        <?php include "../../General/Include/3_body.php"; ?>

        <!-- CUERPO_INICIO -->
        <div class="my-3 p-3 bg-body rounded shadow-sm" id="listado">
            <h5 class="border-bottom pb-2 mb-0"><b>MATRÍCULA DETALLE - LISTADO</b></h5>
            <div class="p-3">
                <table class="table table-hover" id="myTable">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>GRADO</th>
                            <th>ALUMNO</th>
                            <th>APODERADO</th>
                            <th>TIPO</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <small class="d-block text-end mt-3">
                <br>
            </small>
        </div>

        <div class="my-3 p-3 bg-body rounded shadow-sm" id="formulario">
            <h5 class="border-bottom pb-2 mb-0"><b>MATRÍCULA DETALLE - FORMULARIO</b></h5>
            <form id="frm_form" name="frm_form" method="post">
                <input type="hidden" id="matricula_detalle_id" name="matricula_detalle_id">
                <div class="p-3">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation"><button class="nav-link active" id="matricula-tab" data-bs-toggle="tab" data-bs-target="#matricula-tab-pane" type="button" role="tab" aria-controls="matricula-tab-pane" aria-selected="true">MATRÍCULA</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" id="alumno-tab" data-bs-toggle="tab" data-bs-target="#alumno-tab-pane" type="button" role="tab"  aria-controls="alumno-tab-pane" aria-selected="false">ALUMNO</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" id="apoderado-tab" data-bs-toggle="tab" data-bs-target="#apoderado-tab-pane" type="button" role="tab" aria-controls="apoderado-tab-pane" aria-selected="false">APODERADO</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" id="pago-tab" data-bs-toggle="tab" data-bs-target="#pago-tab-pane" type="button" role="tab" aria-controls="pago-tab-pane" aria-selected="false">MATRICULA</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" id="mensualidad-tab" data-bs-toggle="tab" data-bs-target="#mensualidad-tab-pane" type="button" role="tab" aria-controls="mensualidad-tab-pane" aria-selected="false">MENSUALIDAD</button></li>
                    </ul>

                    <div class="tab-content" id="myTabContent">

                        <!-- MATRÍCULA -->
                        <div class="tab-pane fade show active" id="matricula-tab-pane" role="tabpanel" aria-labelledby="matricula-tab">
                            <div class="p-3">
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th style="width: 50%;" scope="col">MATRICULA</th>
                                            <th style="width: 50%;" scope="col">CATEGORIA</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="input-group">
                                                    <select id="matricula_id" name="matricula_id" onmousedown="cargarMatriculas();" class="form-control" data-live-search="true"></select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <select id="matricula_categoria" name="matricula_categoria" class="form-control" data-live-search="true"></select>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3">
                                <label for="apoderado_referido" class="form-label"><b>REFERIDO:</b></label>
                                <select id="apoderado_referido" name="apoderado_referido" class="form-control" data-live-search="true"></select>
                            </div>
                            <div class="p-3">
                                <label for="detalle" class="form-label"><b>DETALLE:</b></label>
                                <textarea style="height: 250px;" name="detalle" id="detalle" class="form-control" placeholder="Observaciones - (Opcional)"></textarea>
                            </div>
                            <div class="p-3">
                                <label for="matricula_observaciones" class="form-label"><b>OBSERVACIONES:</b></label>
                                <textarea name="matricula_observaciones" id="matricula_observaciones" class="form-control" placeholder="Observaciones - (Opcional)"></textarea>
                            </div>
                        </div>

                        <!-- ALUMNO -->
                        <div class="tab-pane fade" id="alumno-tab-pane" role="tabpanel" aria-labelledby="alumno-tab">
                            <input type="hidden" id="alumno_id" name="alumno_id" class="form-control" placeholder="alumno_id">
                            <div class="p-3">
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th style="width: 50%;" scope="col">DOCUMENTO</th>
                                            <th style="width: 50%;" scope="col">NUMERO DE DOCUMENTO</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="input-group">
                                                    <select id="alumno_documento" name="alumno_documento" class="form-control" data-live-search="true"></select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="number" id="alumno_dni" name="alumno_dni" class="form-control" placeholder="DNI del alumno" required>
                                                    <button type="button" class="btn btn-primary" onclick="buscarAlumno()">BUSCAR</button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3">
                                <label for="alumno_nombreyapellido" class="form-label"><b>NOMBRE Y APELLIDO</b></label>
                                <input type="text" id="alumno_nombreyapellido" name="alumno_nombreyapellido" class="form-control" placeholder="Nombre y Apellido" required>
                            </div>
                            <div class="p-3">
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th style="width: 50%;" scope="col">FECHA DE NACIMIENTO</th>
                                            <th style="width: 50%;" scope="col">SEXO</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="input-group">
                                                    <input type="date" id="alumno_nacimiento" name="alumno_nacimiento" class="form-control" required>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <select id="alumno_sexo" name="alumno_sexo" class="form-control" data-live-search="true"></select>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3">
                                <label for="alumno_observaciones" class="form-label"><b>OBSERVACIONES</b></label>
                                <textarea id="alumno_observaciones" name="alumno_observaciones" class="form-control" placeholder="Observaciones - (Opcional)"></textarea>
                            </div>
                        </div>

                        <!-- APODERADO -->
                        <div class="tab-pane fade" id="apoderado-tab-pane" role="tabpanel" aria-labelledby="apoderado-tab">
                            <input type="hidden" id="apoderado_id" name="apoderado_id" class="form-control" placeholder="apoderado_id">
                            <div class="p-3">
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th style="width: 30.3%;" scope="col">PARENTESCO</th>
                                            <th style="width: 30.3%;" scope="col">DOCUMENTO</th>
                                            <th style="width: 30.3%;" scope="col">NUMERO DOCUMENTO</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="input-group">
                                                    <select id="apoderado_tipo" name="apoderado_tipo" class="form-control" data-live-search="true"></select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <select id="apoderado_documento" name="apoderado_documento" class="form-control" data-live-search="true"></select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="number" id="apoderado_dni" name="apoderado_dni" class="form-control" placeholder="Número de Documento" required>
                                                    <button type="button" class="btn btn-primary" onclick="buscarApoderado()">BUSCAR</button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3">
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th style="width: 60.6%;" scope="col">NOMBRE Y APELLIDO</th>
                                            <th style="width: 30.3%;" scope="col">TELÉFONO</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="input-group">
                                                    <input type="text" id="apoderado_nombreyapellido" name="apoderado_nombreyapellido" class="form-control" placeholder="Nombre y Apellido" required>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="text" id="apoderado_telefono" name="apoderado_telefono" class="form-control" placeholder="Teléfono" required>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3">
                                <label for="apoderado_observaciones" class="form-label"><b>OBSERVACIONES</b></label>
                                <textarea id="apoderado_observaciones" name="apoderado_observaciones" class="form-control" placeholder="Observaciones - (Opcional)"></textarea>
                            </div>
                        </div>

                        <!-- PAGO -->
                        <div class="tab-pane fade" id="pago-tab-pane" role="tabpanel" aria-labelledby="pago-tab">
                            <div class="p-3">
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th style="width: 50%;" scope="col">FECHA</th>
                                            <th style="width: 50%;" scope="col">NUMERACION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="input-group">
                                                    <input type="date" id="pago_fecha" name="pago_fecha" class="form-control" required>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="text" id="pago_numeracion" name="pago_numeracion" class="form-control" placeholder="Numeración" readonly required>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3">
                                <label for="pago_descripcion" class="form-label"><b>DESCRIPCIÓN:</b></label>
                                <textarea style="height: 250px;" id="pago_descripcion" name="pago_descripcion" class="form-control" placeholder="Descripción - (Opcional)"></textarea>
                            </div>
                            <div class="p-3">
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th style="width: 50%;" scope="col">MONTO</th>
                                            <th style="width: 50%;" scope="col">METODO</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="input-group">
                                                    <input type="text" id="pago_monto" name="pago_monto" class="form-control" placeholder="Monto" required>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <select id="pago_metodo_id" name="pago_metodo_id" class="form-control" data-live-search="true"></select>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3">
                                <label for="pago_observaciones" class="form-label"><b>OBSERVACIONES:</b></label>
                                <textarea id="pago_observaciones" name="pago_observaciones" class="form-control" placeholder="Observaciones - (Opcional)"></textarea>
                            </div>
                        </div>

                        <!-- MENSUALIDAD -->
                        <div class="tab-pane fade" id="mensualidad-tab-pane" role="tabpanel" aria-labelledby="mensualidad-tab">
                            <div class="p-3">
                                <table class="table table-bordered table-hover" id="mensualidadTable">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">MES</th>
                                            <th scope="col" class="d-none">MENSUALIDAD</th>
                                            <th scope="col" class="d-none">MANTENIMIENTO</th>
                                            <th scope="col" class="d-none">IMPRESION</th>
                                            <th scope="col">TOTAL</th>
                                            <th scope="col">OBSERVACIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Las filas se llenarán dinámicamente -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="p-3">
                    <button type="submit" class="btn btn-primary">GUARDAR</button>
                    <button type="button" onclick="MostrarListado();" class="btn btn-secondary">CANCELAR</button>
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