<?php
include_once("../Modelo/Institucion_estructura.php");

$institucionEstructura = new Institucion_estructura();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarcadena($_POST["nombre"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";

switch ($_GET["op"]) {
    case 'listar_lectivos_activos':
        $rspta = $institucionEstructura->listarLectivosActivos();
        echo '<option value="">SELECCIONE UN AÑO LECTIVO</option>';
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id . '">' . $reg->nombre . '</option>';
        }
        break;

    case 'listar_estructura':
        $id_institucion_lectivo = isset($_POST["id_institucion_lectivo"]) ? limpiarcadena($_POST["id_institucion_lectivo"]) : "";
        $rspta = $institucionEstructura->listarEstructura($id_institucion_lectivo);
        $data = array();
        while ($reg = $rspta->fetch_assoc()) {
            $data[] = $reg;
        }
        echo json_encode($data);
        break;

    case 'guardar_nivel':
        $id_institucion_lectivo = isset($_POST["id_institucion_lectivo"]) ? limpiarcadena($_POST["id_institucion_lectivo"]) : "";
        if (empty($id)) {
            $rspta = $institucionEstructura->guardarNivel(strtoupper($nombre), $id_institucion_lectivo, $observaciones);
            echo $rspta ? "Nivel registrado correctamente" : "No se pudo registrar el nivel";
        } else {
            $rspta = $institucionEstructura->editarNivel($id, strtoupper($nombre), $id_institucion_lectivo, $observaciones);
            echo $rspta ? "Nivel actualizado correctamente" : "No se pudo actualizar el nivel";
        }
        break;

    case 'mostrar_nivel':
        echo json_encode($institucionEstructura->mostrarNivel($id));
        break;

    case 'guardar_grado':
        $id_institucion_nivel = isset($_POST["id_institucion_nivel"]) ? limpiarcadena($_POST["id_institucion_nivel"]) : "";
        if (empty($id)) {
            $rspta = $institucionEstructura->guardarGrado(strtoupper($nombre), $id_institucion_nivel, $observaciones);
            echo $rspta ? "Grado registrado correctamente" : "No se pudo registrar el grado";
        } else {
            $rspta = $institucionEstructura->editarGrado($id, strtoupper($nombre), $id_institucion_nivel, $observaciones);
            echo $rspta ? "Grado actualizado correctamente" : "No se pudo actualizar el grado";
        }
        break;

    case 'mostrar_grado':
        echo json_encode($institucionEstructura->mostrarGrado($id));
        break;

    case 'guardar_seccion':
        $id_institucion_grado = isset($_POST["id_institucion_grado"]) ? limpiarcadena($_POST["id_institucion_grado"]) : "";
        if (empty($id)) {
            $rspta = $institucionEstructura->guardarSeccion(strtoupper($nombre), $id_institucion_grado, $observaciones);
            echo $rspta ? "Sección registrada correctamente" : "No se pudo registrar la sección";
        } else {
            $rspta = $institucionEstructura->editarSeccion($id, strtoupper($nombre), $id_institucion_grado, $observaciones);
            echo $rspta ? "Sección actualizada correctamente" : "No se pudo actualizar la sección";
        }
        break;

    case 'mostrar_seccion':
        echo json_encode($institucionEstructura->mostrarSeccion($id));
        break;

    case 'cambiar_estado':
        $tipo = isset($_POST["tipo"]) ? limpiarcadena($_POST["tipo"]) : "";
        $estado = isset($_POST["estado"]) ? limpiarcadena($_POST["estado"]) : "";
        $rspta = $institucionEstructura->cambiarEstado($tipo, $id, $estado);
        echo $rspta ? "Estado actualizado correctamente" : "No se pudo actualizar el estado";
        break;
}
?>
