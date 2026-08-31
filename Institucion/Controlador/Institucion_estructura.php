<?php
include_once("../Modelo/Institucion_estructura.php");

$institucionEstructura = new Institucion_estructura();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarcadena($_POST["nombre"]) : "";
$id_usuario_docente = isset($_POST["id_usuario_docente"]) ? limpiarcadena($_POST["id_usuario_docente"]) : "";
$telefono = isset($_POST["telefono"]) ? limpiarcadena($_POST["telefono"]) : "";
$correo = isset($_POST["correo"]) ? limpiarcadena($_POST["correo"]) : "";
$ruc = isset($_POST["ruc"]) ? limpiarcadena($_POST["ruc"]) : "";
$razon_social = isset($_POST["razon_social"]) ? limpiarcadena($_POST["razon_social"]) : "";
$direccion = isset($_POST["direccion"]) ? limpiarcadena($_POST["direccion"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        $institucion = array(
            "id" => $id,
            "nombre" => strtoupper($nombre),
            "id_usuario_docente" => $id_usuario_docente,
            "telefono" => $telefono,
            "correo" => $correo,
            "ruc" => $ruc,
            "razon_social" => strtoupper($razon_social),
            "direccion" => strtoupper($direccion),
            "observaciones" => $observaciones
        );

        $detalles = isset($_POST['detalles']) ? json_decode($_POST['detalles'], true) : [];

        $rspta = $institucionEstructura->guardarEditarMasivo($institucion, $detalles);

        if ($rspta) {
            echo json_encode(array(
                "estado" => true,
                "mensaje" => "Institución y estructura guardadas correctamente",
                "id" => $rspta
            ));
        } else {
            echo json_encode(array(
                "estado" => false,
                "mensaje" => "No se pudo guardar la institución y estructura",
                "id" => ""
            ));
        }
        break;

    case 'listar':
        $rspta = $institucionEstructura->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => 'N° ' . $reg->id,
                "1" => $reg->nombre,
                "2" => $reg->director,
                "3" => $reg->ruc,
                "4" => ($reg->estado) ?
                    '<button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->id . ')">EDITAR</button> <button class="btn btn-danger btn-sm" onclick="desactivarInstitucion(' . $reg->id . ')">DESACTIVAR</button>' :
                    '<button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->id . ')">EDITAR</button> <button class="btn btn-primary btn-sm" onclick="activarInstitucion(' . $reg->id . ')">ACTIVAR</button>'
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
        $id_institucion = isset($_POST["id_institucion"]) ? limpiarcadena($_POST["id_institucion"]) : "";

        $rspta = $institucionEstructura->listarEstructura($id_institucion);
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = $reg;
        }

        echo json_encode($data);
        break;

    case 'agregar_lectivo':
        $id_institucion = isset($_POST["id_institucion"]) ? limpiarcadena($_POST["id_institucion"]) : "";
        $nombre_lectivo = isset($_POST["nombre_lectivo"]) ? limpiarcadena($_POST["nombre_lectivo"]) : "";

        $rspta = $institucionEstructura->agregarLectivo($id_institucion, strtoupper($nombre), strtoupper($nombre_lectivo));
        echo $rspta ? "Lectivo agregado correctamente" : "No se pudo agregar el lectivo";
        break;

    case 'agregar_nivel':
        $id_lectivo = isset($_POST["id_lectivo"]) ? limpiarcadena($_POST["id_lectivo"]) : "";

        $rspta = $institucionEstructura->agregarNivel($id_lectivo, strtoupper($nombre));
        echo $rspta ? "Nivel agregado correctamente" : "No se pudo agregar el nivel";
        break;

    case 'agregar_grado':
        $id_nivel = isset($_POST["id_nivel"]) ? limpiarcadena($_POST["id_nivel"]) : "";

        $rspta = $institucionEstructura->agregarGrado($id_nivel, strtoupper($nombre));
        echo $rspta ? "Grado agregado correctamente" : "No se pudo agregar el grado";
        break;

    case 'agregar_seccion':
        $id_grado = isset($_POST["id_grado"]) ? limpiarcadena($_POST["id_grado"]) : "";

        $rspta = $institucionEstructura->agregarSeccion($id_grado, strtoupper($nombre));
        echo $rspta ? "Sección agregada correctamente" : "No se pudo agregar la sección";
        break;

    case 'activar':
        $tipo = isset($_POST["tipo"]) ? limpiarcadena($_POST["tipo"]) : "";
        $rspta = $institucionEstructura->cambiarEstado($tipo, $id, 1);
        echo $rspta ? "Registro activado correctamente" : "No se pudo activar el registro";
        break;

    case 'desactivar':
        $tipo = isset($_POST["tipo"]) ? limpiarcadena($_POST["tipo"]) : "";
        $rspta = $institucionEstructura->cambiarEstado($tipo, $id, 0);
        echo $rspta ? "Registro desactivado correctamente" : "No se pudo desactivar el registro";
        break;

    case 'listar_docentes_activos':
        $rspta = $institucionEstructura->listarDocentesActivos();

        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id . '>' . $reg->nombreyapellido . ' - ' . $reg->cargo . '</option>';
        }
        break;
}
?>