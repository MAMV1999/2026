<?php
ob_start();
session_start();

if (!isset($_SESSION['docente_id']) || !isset($_SESSION['nombre'])) {
    header("Location ../../Inicio/Controlador/Acceso.php?op=salir");
    exit();
}
?>
<?php include "../../General/Include/1_header.php"; ?>
<main class="container">
    <?php include "../../General/Include/3_body.php"; ?>

    <div class="my-3 p-3 bg-body rounded shadow-sm" id="formulario">
        <h5 class="border-bottom pb-2 mb-0"><b><?php echo $_SESSION['nombre']; ?> (PERFIL)</b></h5>

        <form id="frmPerfil" name="frmPerfil" method="post">
            <input type="hidden" id="id" name="id" value="<?php echo $_SESSION['docente_id']; ?>">

            <div class="p-3 row g-4">
                <div class="col-md-6">
                    <label for="id_documento" class="form-label"><b>TIPO DE DOCUMENTO</b></label>
                    <select id="id_documento" name="id_documento" class="form-control selectpicker" data-live-search="true"></select>
                </div>
                <div class="col-md-6">
                    <label for="numerodocumento" class="form-label"><b>NÚMERO DE DOCUMENTO</b></label>
                    <input type="text" id="numerodocumento" name="numerodocumento" placeholder="Número de Documento" class="form-control">
                </div>
                <div class="col-12">
                    <label for="nombreyapellido" class="form-label"><b>NOMBRE Y APELLIDO</b></label>
                    <input type="text" id="nombreyapellido" name="nombreyapellido" placeholder="Nombre y Apellido" class="form-control">
                </div>
                <div class="col-4">
                    <label for="nacimiento" class="form-label"><b>FECHA DE NACIMIENTO</b></label>
                    <input type="date" id="nacimiento" name="nacimiento" class="form-control">
                </div>
                <div class="col-4">
                    <label for="id_estado_civil" class="form-label"><b>ESTADO CIVIL</b></label>
                    <select id="id_estado_civil" name="id_estado_civil" class="form-control selectpicker" data-live-search="true"></select>
                </div>
                <div class="col-4">
                    <label for="id_sexo" class="form-label"><b>SEXO</b></label>
                    <select id="id_sexo" name="id_sexo" class="form-control selectpicker" data-live-search="true"></select>
                </div>
                <div class="col-md-4">
                    <label for="telefono" class="form-label"><b>TELÉFONO</b></label>
                    <input type="text" id="telefono" name="telefono" placeholder="Teléfono" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="correo" class="form-label"><b>CORREO</b></label>
                    <input type="email" id="correo" name="correo" placeholder="Correo" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="direccion" class="form-label"><b>DIRECCIÓN</b></label>
                    <input type="text" id="direccion" name="direccion" placeholder="Dirección" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="id_cargo" class="form-label"><b>CARGO</b></label>
                    <select id="id_cargo" name="id_cargo" class="form-control selectpicker" data-live-search="true"></select>
                </div>
                <div class="col-md-6">
                    <label for="id_tipo_contrato" class="form-label"><b>TIPO DE CONTRATO</b></label>
                    <select id="id_tipo_contrato" name="id_tipo_contrato" class="form-control selectpicker" data-live-search="true"></select>
                </div>
                <div class="col-md-6">
                    <label for="fechainicio" class="form-label"><b>FECHA INICIO</b></label>
                    <input type="date" id="fechainicio" name="fechainicio" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="fechafin" class="form-label"><b>FECHA FIN</b></label>
                    <input type="date" id="fechafin" name="fechafin" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="cuentabancaria" class="form-label"><b>CUENTA BANCARIA</b></label>
                    <input type="text" id="cuentabancaria" name="cuentabancaria" placeholder="Cuenta Bancaria" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="cuentainterbancaria" class="form-label"><b>CUENTA INTERBANCARIA</b></label>
                    <input type="text" id="cuentainterbancaria" name="cuentainterbancaria" placeholder="Cuenta Interbancaria" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="sunat_ruc" class="form-label"><b>RUC (SUNAT)</b></label>
                    <input type="text" id="sunat_ruc" name="sunat_ruc" placeholder="RUC" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="sunat_usuario" class="form-label"><b>USUARIO SUNAT</b></label>
                    <input type="text" id="sunat_usuario" name="sunat_usuario" placeholder="Usuario SUNAT" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="sunat_contraseña" class="form-label"><b>CONTRASEÑA SUNAT</b></label>
                    <input type="text" id="sunat_contraseña" name="sunat_contraseña" placeholder="Contraseña SUNAT" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="usuario" class="form-label"><b>USUARIO</b></label>
                    <input type="text" id="usuario" name="usuario" placeholder="Usuario" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="clave" class="form-label"><b>CLAVE</b></label>
                    <input type="text" id="clave" name="clave" placeholder="Clave" class="form-control">
                </div>
            </div>

            <div class="p-3">
                <label for="observaciones" class="form-label"><b>OBSERVACIONES</b></label>
                <div class="input-group">
                    <textarea id="observaciones" name="observaciones" placeholder="Observaciones" class="form-control"></textarea>
                </div>
            </div>

            <div class="p-3">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" onclick="$(location).attr('href', 'Escritorio.php');" class="btn btn-secondary">Cancelar</button>
            </div>
        </form>
    </div>
</main>

<?php include "../../General/Include/2_footer.php"; ?>
<script src="perfil.js"></script>
<?php
ob_end_flush();
?>