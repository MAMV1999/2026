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

        <!-- LISTADO DE DOCENTES -->
        <div class="my-3 p-3 bg-body rounded shadow-sm" id="listado">
            <h5 class="border-bottom pb-2 mb-0"><b>DOCENTES - LISTADO</b></h5>
            <div class="p-3">
                <table class="table" id="myTable">
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
                <button type="button" onclick="MostrarFormulario();" class="btn btn-success">AGREGAR</button>
            </small>
        </div>

        <!-- FORMULARIO DE DOCENTE -->
        <div class="my-3 p-3 bg-body rounded shadow-sm" id="formulario">
            <h5 class="border-bottom pb-2 mb-0"><b>DOCENTE - FORMULARIO</b></h5>
            <form id="frm_form" name="frm_form" method="post">
                <input type="hidden" id="id" name="id" class="form-control">

                <!-- Pestañas para organización de campos -->
                <div class="p-3">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation"><button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal-tab-pane" type="button" role="tab" aria-controls="personal-tab-pane" aria-selected="true">DATOS PERSONALES</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" id="ubicacion-tab" data-bs-toggle="tab" data-bs-target="#ubicacion-tab-pane" type="button" role="tab" aria-controls="ubicacion-tab-pane" aria-selected="false">UBICACION Y CONTACTO</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" id="laboral-tab" data-bs-toggle="tab" data-bs-target="#laboral-tab-pane" type="button" role="tab" aria-controls="laboral-tab-pane" aria-selected="false">INFORMACIÓN LABORAL</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" id="usuario-tab" data-bs-toggle="tab" data-bs-target="#usuario-tab-pane" type="button" role="tab" aria-controls="usuario-tab-pane" aria-selected="false">DATOS DE USUARIO</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" id="financiero-tab" data-bs-toggle="tab" data-bs-target="#financiero-tab-pane" type="button" role="tab" aria-controls="financiero-tab-pane" aria-selected="false">DATOS FINANCIEROS</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" id="sunat-tab" data-bs-toggle="tab" data-bs-target="#sunat-tab-pane" type="button" role="tab" aria-controls="sunat-tab-pane" aria-selected="false">DATOS SUNAT</button></li>
                    </ul>

                    <div class="tab-content" id="myTabContent">
                        <!-- TAB: DATOS PERSONALES -->
                        <div class="tab-pane fade show active" id="personal-tab-pane" role="tabpanel" aria-labelledby="personal-tab">

                            <div class="p-3">
                                <label for="id_documento" class="form-label"><b>DOCUMENTO:</b></label>
                                <div class="input-group">
                                    <select id="id_documento" name="id_documento" class="form-control" data-live-search="true"></select>
                                    <input type="text" id="numerodocumento" name="numerodocumento" class="form-control" placeholder="Número de Documento">
                                </div>
                            </div>

                            <div class="p-3">
                                <label for="nombreyapellido" class="form-label"><b>NOMBRE Y APELLIDO:</b></label>
                                <input type="text" id="nombreyapellido" name="nombreyapellido" class="form-control" placeholder="Nombre y Apellido">
                            </div>

                            <div class="p-3">
                                <label for="nacimiento" class="form-label"><b>FECHA DE NACIMIENTO:</b></label>
                                <input type="date" id="nacimiento" name="nacimiento" class="form-control">
                            </div>

                            <div class="p-3">
                                <label for="id_documento" class="form-label"><b>ESTADO CIVIL / SEXO:</b></label>
                                <div class="input-group">
                                    <select id="id_estado_civil" name="id_estado_civil" class="form-control" data-live-search="true"></select>
                                    <select id="id_sexo" name="id_sexo" class="form-control" data-live-search="true"></select>
                                </div>
                            </div>
                        </div>

                        <!-- TAB: DATOS PERSONALES -->
                        <div class="tab-pane fade" id="ubicacion-tab-pane" role="tabpanel" aria-labelledby="ubicacion-tab">
                            <div class="p-3">
                                <label for="direccion" class="form-label"><b>DIRECCIÓN:</b></label>
                                <input type="text" id="direccion" name="direccion" class="form-control" placeholder="Dirección">
                            </div>

                            <div class="p-3">
                                <label for="correo" class="form-label"><b>CORREO ELECTRÓNICO:</b></label>
                                <input type="email" id="correo" name="correo" class="form-control" placeholder="Correo Electrónico">
                            </div>

                            <div class="p-3">
                                <label for="telefono" class="form-label"><b>TELÉFONO:</b></label>
                                <input type="text" id="telefono" name="telefono" class="form-control" placeholder="Teléfono">
                            </div>
                        </div>

                        <!-- TAB: INFORMACIÓN LABORAL -->
                        <div class="tab-pane fade" id="laboral-tab-pane" role="tabpanel" aria-labelledby="laboral-tab">
                            <div class="p-3">
                                <label for="id_cargo" class="form-label"><b>CARGO:</b></label>
                                <select id="id_cargo" name="id_cargo" class="form-control" data-live-search="true"></select>
                            </div>

                            <div class="p-3">
                                <label for="id_tipo_contrato" class="form-label"><b>TIPO DE CONTRATO:</b></label>
                                <select id="id_tipo_contrato" name="id_tipo_contrato" class="form-control" data-live-search="true"></select>
                            </div>

                            <div class="p-3">
                                <label for="id_documento" class="form-label"><b>FECHA INICIO / FIN:</b></label>
                                <div class="input-group">
                                    <input type="date" id="fechainicio" name="fechainicio" class="form-control">
                                    <input type="date" id="fechafin" name="fechafin" class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- TAB: DATOS DE USUARIO -->
                        <div class="tab-pane fade" id="usuario-tab-pane" role="tabpanel" aria-labelledby="usuario-tab">
                            <div class="p-3">
                                <label for="id_documento" class="form-label"><b>USUARIO / CLAVE:</b></label>
                                <div class="input-group">
                                    <input type="text" id="usuario" name="usuario" class="form-control" placeholder="Usuario">
                                    <input type="text" id="clave" name="clave" class="form-control" placeholder="Clave">
                                </div>
                            </div>
                        </div>

                        <!-- TAB: DATOS FINANCIEROS -->
                        <div class="tab-pane fade" id="financiero-tab-pane" role="tabpanel" aria-labelledby="financiero-tab">
                            <div class="p-3">
                                <label for="sueldo" class="form-label"><b>SUELDO:</b></label>
                                <input type="text" id="sueldo" name="sueldo" class="form-control" placeholder="Sueldo">
                            </div>

                            <div class="p-3">
                                <label for="cuentabancaria" class="form-label"><b>CUENTA BANCARIA:</b></label>
                                <input type="text" id="cuentabancaria" name="cuentabancaria" class="form-control" placeholder="Cuenta Bancaria">
                            </div>

                            <div class="p-3">
                                <label for="cuentainterbancaria" class="form-label"><b>CUENTA INTERBANCARIA:</b></label>
                                <input type="text" id="cuentainterbancaria" name="cuentainterbancaria" class="form-control" placeholder="Cuenta Interbancaria">
                            </div>
                        </div>

                        <!-- TAB: DATOS SUNAT -->
                        <div class="tab-pane fade" id="sunat-tab-pane" role="tabpanel" aria-labelledby="sunat-tab">
                            <div class="p-3">
                                <label for="sunat_ruc" class="form-label"><b>RUC:</b></label>
                                <input type="text" id="sunat_ruc" name="sunat_ruc" class="form-control" placeholder="RUC">
                            </div>

                            <div class="p-3">
                                <label for="sunat_usuario" class="form-label"><b>USUARIO SUNAT:</b></label>
                                <input type="text" id="sunat_usuario" name="sunat_usuario" class="form-control" placeholder="Usuario">
                            </div>

                            <div class="p-3">
                                <label for="sunat_contraseña" class="form-label"><b>CONTRASEÑA SUNAT:</b></label>
                                <input type="password" id="sunat_contraseña" name="sunat_contraseña" class="form-control" placeholder="Contraseña">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BOTONES DE ACCIÓN -->
                <div class="p-3">
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