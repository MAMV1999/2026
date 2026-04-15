<?php
include_once("../Modelo/usuario_docente.php");

$usuarioDocente = new UsuarioDocente();
$detalles = isset($_POST['detalles']) ? json_decode($_POST['detalles'], true) : [];

switch ($_GET["op"]) {

    case 'guardaryeditar':
        $rspta = $usuarioDocente->guardarEditarMasivo($detalles);
        echo $rspta ? "Registros actualizados correctamente" : "Error al actualizar los registros";
        break;

    case 'desactivar':
        $id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
        $rspta = $usuarioDocente->desactivar($id);
        echo $rspta ? "Docente desactivado correctamente" : "No se pudo desactivar el docente";
        break;

    case 'activar':
        $id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
        $rspta = $usuarioDocente->activar($id);
        echo $rspta ? "Docente activado correctamente" : "No se pudo activar el docente";
        break;

    case 'mostrar':
        $id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
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

                        <div class="modal fade" id="' . $reg->numerodocumento . '" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5">' . $reg->nombreyapellido . '</h1>
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
                "5" => ($reg->estado)
                    ? '<button type="button" onclick="mostrar(' . $reg->id . ')" class="btn btn-warning btn-sm">EDITAR</button> 
                       <button type="button" onclick="desactivar(' . $reg->id . ')" class="btn btn-danger btn-sm">DESACTIVAR</button>'
                    : '<button type="button" onclick="mostrar(' . $reg->id . ')" class="btn btn-warning btn-sm">EDITAR</button> 
                       <button type="button" onclick="activar(' . $reg->id . ')" class="btn btn-success btn-sm">ACTIVAR</button>'
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

    case 'listar_todos':
        $rspta = $usuarioDocente->listarTodos();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "id" => $reg->id,
                "id_documento" => $reg->id_documento,
                "numerodocumento" => $reg->numerodocumento,
                "nombreyapellido" => $reg->nombreyapellido,
                "nacimiento" => $reg->nacimiento,
                "id_estado_civil" => $reg->id_estado_civil,
                "id_sexo" => $reg->id_sexo,
                "direccion" => $reg->direccion,
                "telefono" => $reg->telefono,
                "correo" => $reg->correo,
                "id_cargo" => $reg->id_cargo,
                "id_tipo_contrato" => $reg->id_tipo_contrato,
                "fechainicio" => $reg->fechainicio,
                "fechafin" => $reg->fechafin,
                "sueldo" => $reg->sueldo,
                "cuentabancaria" => $reg->cuentabancaria,
                "cuentainterbancaria" => $reg->cuentainterbancaria,
                "sunat_ruc" => $reg->sunat_ruc,
                "sunat_usuario" => $reg->sunat_usuario,
                "sunat_contraseña" => $reg->sunat_contraseña,
                "usuario" => $reg->usuario,
                "clave" => $reg->clave,
                "observaciones" => $reg->observaciones,
                "estado" => $reg->estado
            );
        }

        echo json_encode($data);
        break;

    case 'listar_documentos_activos':
        $rspta = $usuarioDocente->listarTiposDocumentoActivos();
        $data = array();
        while ($reg = $rspta->fetch_object()) {
            $data[] = array("id" => $reg->id, "nombre" => $reg->nombre);
        }
        echo json_encode($data);
        break;

    case 'listar_cargos_activos':
        $rspta = $usuarioDocente->listarCargosActivos();
        $data = array();
        while ($reg = $rspta->fetch_object()) {
            $data[] = array("id" => $reg->id, "nombre" => $reg->nombre);
        }
        echo json_encode($data);
        break;

    case 'listar_estados_civiles_activos':
        $rspta = $usuarioDocente->listarEstadosCivilesActivos();
        $data = array();
        while ($reg = $rspta->fetch_object()) {
            $data[] = array("id" => $reg->id, "nombre" => $reg->nombre);
        }
        echo json_encode($data);
        break;

    case 'listar_tipos_contrato_activos':
        $rspta = $usuarioDocente->listarTiposContratoActivos();
        $data = array();
        while ($reg = $rspta->fetch_object()) {
            $data[] = array("id" => $reg->id, "nombre" => $reg->nombre);
        }
        echo json_encode($data);
        break;

    case 'listar_sexos_activos':
        $rspta = $usuarioDocente->listarSexosActivos();
        $data = array();
        while ($reg = $rspta->fetch_object()) {
            $data[] = array("id" => $reg->id, "nombre" => $reg->nombre);
        }
        echo json_encode($data);
        break;
}
?>