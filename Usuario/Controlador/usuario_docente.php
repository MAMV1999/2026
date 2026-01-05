<?php
include_once("../Modelo/usuario_docente.php");

$usuarioDocente = new UsuarioDocente();

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
$estado = isset($_POST["estado"]) ? limpiarcadena($_POST["estado"]) : "1";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($id)) {
            $rspta = $usuarioDocente->guardar(
                $id_documento, $numerodocumento, strtoupper($nombreyapellido), $nacimiento, 
                $id_estado_civil, $id_sexo, $direccion, $telefono, strtoupper($correo), 
                $id_cargo, $id_tipo_contrato, $fechainicio, $fechafin, $sueldo, 
                $cuentabancaria, $cuentainterbancaria, $sunat_ruc, $sunat_usuario, 
                $sunat_contraseña, $usuario, $clave, $observaciones, $estado
            );
            echo $rspta ? "Docente registrado correctamente" : "No se pudo registrar el docente";
        } else {
            $rspta = $usuarioDocente->editar(
                $id, $id_documento, $numerodocumento, strtoupper($nombreyapellido), $nacimiento, 
                $id_estado_civil, $id_sexo, $direccion, $telefono, strtoupper($correo), 
                $id_cargo, $id_tipo_contrato, $fechainicio, $fechafin, $sueldo, 
                $cuentabancaria, $cuentainterbancaria, $sunat_ruc, $sunat_usuario, 
                $sunat_contraseña, $usuario, $clave, $observaciones, $estado
            );
            echo $rspta ? "Docente actualizado correctamente" : "No se pudo actualizar el docente";
        }
        break;

    case 'desactivar':
        $rspta = $usuarioDocente->desactivar($id);
        echo $rspta ? "Docente desactivado correctamente" : "No se pudo desactivar el docente";
        break;

    case 'activar':
        $rspta = $usuarioDocente->activar($id);
        echo $rspta ? "Docente activado correctamente" : "No se pudo activar el docente";
        break;

    case 'mostrar':
        $rspta = $usuarioDocente->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $usuarioDocente->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => count($data) + 1,
                "1" => $reg->tipo_documento . " - " . $reg->numerodocumento,
                "2" => $reg->nombreyapellido,
                "3" => $reg->cargo,
                "4" => '<button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#' . $reg->numerodocumento . '">REPORTE</button>

                        <div class="modal fade" id="' . $reg->numerodocumento . '" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">' . $reg->nombreyapellido . '</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <iframe src="../../Reportes/Vista/usuario_docente.php?id=' . $reg->id . '" type="application/pdf" width="100%" height="600px"></iframe>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">SALIR</button>
                            </div>
                            </div>
                        </div>
                        </div>',
                "5" => ($reg->estado) ? '<button type="button" onclick="mostrar(' . $reg->id . ')" class="btn btn-warning btn-sm">EDITAR</button> <button type="button" onclick="desactivar(' . $reg->id . ')" class="btn btn-danger btn-sm">DESACTIVAR</button>':'<button type="button" onclick="mostrar(' . $reg->id . ')" class="btn btn-warning btn-sm">EDITAR</button> <button type="button" onclick="activar(' . $reg->id . ')" class="btn btn-success btn-sm">ACTIVAR</button>'
            );
        }
        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results);
        break;

    // Listar datos dinámicos para los campos de selección en el formulario
    case 'listar_documentos_activos':
        $rspta = $usuarioDocente->listarTiposDocumentoActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id . '">' . $reg->nombre . '</option>';
        }
        break;

    case 'listar_cargos_activos':
        $rspta = $usuarioDocente->listarCargosActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id . '">' . $reg->nombre . '</option>';
        }
        break;

    case 'listar_estados_civiles_activos':
        $rspta = $usuarioDocente->listarEstadosCivilesActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id . '">' . $reg->nombre . '</option>';
        }
        break;

    case 'listar_tipos_contrato_activos':
        $rspta = $usuarioDocente->listarTiposContratoActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id . '">' . $reg->nombre . '</option>';
        }
        break;

    case 'listar_sexos_activos':
        $rspta = $usuarioDocente->listarSexosActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id . '">' . $reg->nombre . '</option>';
        }
        break;
}
?>
