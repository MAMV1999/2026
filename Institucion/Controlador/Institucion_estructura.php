<?php
include_once("../Modelo/Institucion_estructura.php");

$institucionEstructura = new Institucion_estructura();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";

function limpiarEstructura($estructura)
{
    $nuevo = array();

    foreach ($estructura as $lectivo) {
        $lectivoNuevo = array(
            "id" => isset($lectivo["id"]) ? limpiarcadena($lectivo["id"]) : "",
            "nombre" => isset($lectivo["nombre"]) ? limpiarcadena(strtoupper($lectivo["nombre"])) : "",
            "niveles" => array()
        );

        if (isset($lectivo["niveles"])) {
            foreach ($lectivo["niveles"] as $nivel) {
                $nivelNuevo = array(
                    "id" => isset($nivel["id"]) ? limpiarcadena($nivel["id"]) : "",
                    "nombre" => isset($nivel["nombre"]) ? limpiarcadena(strtoupper($nivel["nombre"])) : "",
                    "grados" => array()
                );

                if (isset($nivel["grados"])) {
                    foreach ($nivel["grados"] as $grado) {
                        $gradoNuevo = array(
                            "id" => isset($grado["id"]) ? limpiarcadena($grado["id"]) : "",
                            "nombre" => isset($grado["nombre"]) ? limpiarcadena(strtoupper($grado["nombre"])) : "",
                            "secciones" => array()
                        );

                        if (isset($grado["secciones"])) {
                            foreach ($grado["secciones"] as $seccion) {
                                $gradoNuevo["secciones"][] = array(
                                    "id" => isset($seccion["id"]) ? limpiarcadena($seccion["id"]) : "",
                                    "nombre" => isset($seccion["nombre"]) ? limpiarcadena(strtoupper($seccion["nombre"])) : ""
                                );
                            }
                        }

                        $nivelNuevo["grados"][] = $gradoNuevo;
                    }
                }

                $lectivoNuevo["niveles"][] = $nivelNuevo;
            }
        }

        $nuevo[] = $lectivoNuevo;
    }

    return $nuevo;
}

switch ($_GET["op"]) {
    case 'guardaryeditar':
        $institucion = array(
            "id" => isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "",
            "nombre" => isset($_POST["nombre"]) ? limpiarcadena(strtoupper($_POST["nombre"])) : "",
            "id_usuario_docente" => isset($_POST["id_usuario_docente"]) ? limpiarcadena($_POST["id_usuario_docente"]) : "",
            "telefono" => isset($_POST["telefono"]) ? limpiarcadena($_POST["telefono"]) : "",
            "correo" => isset($_POST["correo"]) ? limpiarcadena($_POST["correo"]) : "",
            "ruc" => isset($_POST["ruc"]) ? limpiarcadena($_POST["ruc"]) : "",
            "razon_social" => isset($_POST["razon_social"]) ? limpiarcadena(strtoupper($_POST["razon_social"])) : "",
            "direccion" => isset($_POST["direccion"]) ? limpiarcadena(strtoupper($_POST["direccion"])) : "",
            "observaciones" => isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : ""
        );

        $estructura = isset($_POST['estructura']) ? json_decode($_POST['estructura'], true) : array();
        $estructura = limpiarEstructura($estructura);

        $rspta = $institucionEstructura->guardarEditarMasivo($institucion, $estructura);

        echo $rspta ? "Institución y estructura guardadas correctamente" : "No se pudo guardar la institución y estructura";
        break;

    case 'listar':
        $rspta = $institucionEstructura->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => 'N° '.$reg->id,
                "1" => $reg->nombre,
                "2" => $reg->director,
                "3" => $reg->ruc,
                "4" => $reg->telefono,
                "5" => ($reg->estado) ?
                    '<button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->id . ')">EDITAR</button> <button class="btn btn-danger btn-sm" onclick="desactivar(' . $reg->id . ')">DESACTIVAR</button>' :
                    '<button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->id . ')">EDITAR</button> <button class="btn btn-primary btn-sm" onclick="activar(' . $reg->id . ')">ACTIVAR</button>'
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

    case 'mostrar':
        $rspta = $institucionEstructura->mostrar($id);
        echo json_encode($rspta);
        break;

    case 'listar_estructura':
        $rspta = $institucionEstructura->listar_estructura($id);

        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = $reg;
        }

        echo json_encode($data);
        break;

    case 'desactivar':
        $rspta = $institucionEstructura->desactivar($id);
        echo $rspta ? "Institución desactivada correctamente" : "No se pudo desactivar la institución";
        break;

    case 'activar':
        $rspta = $institucionEstructura->activar($id);
        echo $rspta ? "Institución activada correctamente" : "No se pudo activar la institución";
        break;

    case 'listar_docentes_activos':
        $rspta = $institucionEstructura->listarDocentesActivos();

        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id . '>' . $reg->nombreyapellido . ' - ' . $reg->cargo . '</option>';
        }
        break;
}
?>