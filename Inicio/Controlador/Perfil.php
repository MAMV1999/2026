<?php
include_once("../Modelo/Perfil.php");
session_start();

$perfil = new Perfil();

// Campos (usuario_docente)
$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$id_documento = isset($_POST["id_documento"]) ? limpiarcadena($_POST["id_documento"]) : "";
$numerodocumento = isset($_POST["numerodocumento"]) ? limpiarcadena($_POST["numerodocumento"]) : "";
$nombreyapellido = isset($_POST["nombreyapellido"]) ? limpiarcadena($_POST["nombreyapellido"]) : "";
$nacimiento = isset($_POST["nacimiento"]) ? limpiarcadena($_POST["nacimiento"]) : "";
$id_estado_civil = isset($_POST["id_estado_civil"]) ? limpiarcadena($_POST["id_estado_civil"]) : "";
$id_sexo = isset($_POST["id_sexo"]) ? limpiarcadena($_POST["id_sexo"]) : "";
$direccion = isset($_POST["direccion"]) ? limpiarcadena($_POST["direccion"]) : "";
$telefono = isset($_POST["telefono"]) ? limpiarcadena($_POST["telefono"]) : "";
$correo = isset($_POST["correo"]) ? limpiarcadena($_POST["correo"]) : "";
$id_cargo = isset($_POST["id_cargo"]) ? limpiarcadena($_POST["id_cargo"]) : "";
$id_tipo_contrato = isset($_POST["id_tipo_contrato"]) ? limpiarcadena($_POST["id_tipo_contrato"]) : "";
$fechainicio = isset($_POST["fechainicio"]) ? limpiarcadena($_POST["fechainicio"]) : "";
$fechafin = isset($_POST["fechafin"]) ? limpiarcadena($_POST["fechafin"]) : "";
$sueldo = isset($_POST["sueldo"]) ? limpiarcadena($_POST["sueldo"]) : "";
$cuentabancaria = isset($_POST["cuentabancaria"]) ? limpiarcadena($_POST["cuentabancaria"]) : "";
$cuentainterbancaria = isset($_POST["cuentainterbancaria"]) ? limpiarcadena($_POST["cuentainterbancaria"]) : "";
$sunat_ruc = isset($_POST["sunat_ruc"]) ? limpiarcadena($_POST["sunat_ruc"]) : "";
$sunat_usuario = isset($_POST["sunat_usuario"]) ? limpiarcadena($_POST["sunat_usuario"]) : "";
$sunat_contraseña = isset($_POST["sunat_contraseña"]) ? limpiarcadena($_POST["sunat_contraseña"]) : "";
$usuario = isset($_POST["usuario"]) ? limpiarcadena($_POST["usuario"]) : "";
$clave = isset($_POST["clave"]) ? limpiarcadena($_POST["clave"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";

switch ($_GET["op"]) {

    case 'guardaryeditar':
        if (empty($id)) {
            echo "Operación no permitida: no se puede registrar desde Perfil";
        } else {
            $rspta = $perfil->editar(
                $id,
                $id_documento,
                $numerodocumento,
                strtoupper($nombreyapellido),
                $nacimiento,
                $id_estado_civil,
                $id_sexo,
                $direccion,
                $telefono,
                strtolower($correo),
                $id_cargo,
                $id_tipo_contrato,
                $fechainicio,
                $fechafin,
                $sueldo,
                $cuentabancaria,
                $cuentainterbancaria,
                $sunat_ruc,
                $sunat_usuario,
                $sunat_contraseña,
                strtolower($usuario),
                $clave,
                $observaciones
            );
            echo $rspta ? "Perfil actualizado correctamente" : "No se pudo actualizar el perfil";
        }
        break;

    case 'mostrar':
        // SI NO VIENE EL ID POR POST, TOMARLO DESDE SESIÓN
        if (empty($id)) {
            $id = isset($_SESSION['docente_id']) ? $_SESSION['docente_id'] : "";
        }

        if (empty($id)) {
            echo json_encode(null);
            break;
        }

        $rspta = $perfil->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar_tipos_documentos_activos':
        $rspta = $perfil->listarTiposDocumentosActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id . '>' . $reg->nombre . '</option>';
        }
        break;

    case 'listar_estados_civiles_activos':
        $rspta = $perfil->listarEstadosCivilesActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id . '>' . $reg->nombre . '</option>';
        }
        break;

    case 'listar_sexos_activos':
        $rspta = $perfil->listarSexosActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id . '>' . $reg->nombre . '</option>';
        }
        break;

    case 'listar_cargos_activos':
        $rspta = $perfil->listarCargosActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id . '>' . $reg->nombre . '</option>';
        }
        break;

    case 'listar_tipos_contrato_activos':
        $rspta = $perfil->listarTiposContratoActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id . '>' . $reg->nombre . '</option>';
        }
        break;
}
?>
