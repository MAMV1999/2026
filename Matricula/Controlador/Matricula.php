<?php
include_once("../Modelo/Matricula.php");

$matricula = new Matricula();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$id_institucion_seccion = isset($_POST["id_institucion_seccion"]) ? limpiarcadena($_POST["id_institucion_seccion"]) : "";
$id_usuario_docente = isset($_POST["id_usuario_docente"]) ? limpiarcadena($_POST["id_usuario_docente"]) : "";
$aforo = isset($_POST["aforo"]) ? limpiarcadena($_POST["aforo"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";

switch ($_GET["op"]) {

    // Guardar o Editar
    case 'guardar':
        $id = $_POST['id'] ?? "";
        $id_institucion_seccion = $_POST['id_institucion_seccion'] ?? null;
        $id_usuario_docente = $_POST['id_usuario_docente'] ?? null;
        $aforo = $_POST['aforo'] ?? null;
        $observaciones = $_POST['observaciones'] ?? null;
        $montos = $_POST['montos'] ?? [];

        if ($id_institucion_seccion && $id_usuario_docente && $aforo && !empty($montos)) {

            if (empty($id)) {
                $resultado = $matricula->guardar($id_institucion_seccion, $id_usuario_docente, $aforo, $observaciones, $montos);
                echo $resultado ? "Matrícula registrada correctamente" : "Error al guardar el registro";
            } else {
                $resultado = $matricula->editar($id, $id_institucion_seccion, $id_usuario_docente, $aforo, $observaciones, $montos);
                echo $resultado ? "Matrícula actualizada correctamente" : "Error al actualizar el registro";
            }
        } else {
            echo "Datos incompletos. Verifique e intente nuevamente.";
        }
        break;

    // LISTAR
    case 'listar':
        $rspta = $matricula->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => count($data) + 1,
                "1" => $reg->nombre_lectivo . ' / ' . $reg->nombre_nivel . ' / ' . $reg->nombre_grado,
                "2" => $reg->docente_nombre,
                "3" => $reg->aforo . ' ALUMNOS',
                "4" => ($reg->estado) ?
                    '
                    <button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->id . ');">EDITAR</button>
                    <button class="btn btn-danger btn-sm" onclick="desactivar(' . $reg->id . ')">DESACTIVAR</button>
                ' : '
                    <button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->id . ');">EDITAR</button>
                    <button class="btn btn-primary btn-sm" onclick="activar(' . $reg->id . ')">ACTIVAR</button>
                '
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

    // MOSTRAR (para editar) - CABECERA + DETALLE EN UNA SOLA RESPUESTA
    case 'mostrar':

        $id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";

        if (empty($id)) {
            echo json_encode([
                "cabecera" => null,
                "detalle_html" => "",
                "error" => "ID vacío"
            ]);
            break;
        }

        // 1) CABECERA
        $cabecera = $matricula->mostrar($id);

        // 2) DETALLE (cobros + montos)
        $rspta = $matricula->listar_matricula_cobro_activos_con_montos($id);

        $cont = 1;
        $rows = "";

        while ($reg = $rspta->fetch_object()) {

            $monto_val = htmlspecialchars($reg->monto, ENT_QUOTES, 'UTF-8');
            $obs_val   = htmlspecialchars($reg->monto_observaciones, ENT_QUOTES, 'UTF-8');

            $rows .= "
            <tr>
                <th scope='row'>{$cont}.</th>
                <td>
                    <input type='hidden' name='montos[{$reg->id}][matricula_cobro_id]' value='{$reg->id}'>
                    {$reg->nombre}
                </td>
                <td><input type='number' name='montos[{$reg->id}][monto]' placeholder='Monto' required class='form-control' value='{$monto_val}'></td>
                <td><input type='text' name='montos[{$reg->id}][observaciones]' placeholder='Observaciones' class='form-control' value='{$obs_val}'></td>
            </tr>";
            $cont++;
        }
        
        echo json_encode([
            "cabecera" => $cabecera,
            "detalle_html" => $rows
        ]);

        break;


    case 'listar_secciones_activas':
        $rspta = $matricula->listarSeccionesActivas();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id_seccion . '>' . $reg->nombre_lectivo . ' - ' . $reg->nombre_nivel . ' - ' . $reg->nombre_grado . ' - ' . $reg->nombre_seccion . '</option>';
        }
        break;

    case 'listar_docentes_activos':
        $rspta = $matricula->listarDocentesActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->id_docente . '>' . $reg->nombre_docente . ' - ' . $reg->nombre_cargo . '</option>';
        }
        break;

    // Para AGREGAR (montos vacíos)
    case 'listar_matricula_cobro_activos':
        $rspta = $matricula->listar_matricula_cobro_activos();
        $cont = 1;
        $rows = "";
        while ($reg = $rspta->fetch_object()) {
            $rows .= "
                <tr>
                    <th scope='row'>{$cont}.</th>
                    <td>
                        <input type='hidden' id='matricula_cobro_id_{$reg->id}' name='montos[{$reg->id}][matricula_cobro_id]' class='form-control' value='{$reg->id}'>
                        {$reg->nombre}
                    </td>
                    <td><input type='number' id='monto_{$reg->id}' name='montos[{$reg->id}][monto]' placeholder='Monto' required class='form-control'></td>
                    <td><input type='text' id='observacion_{$reg->id}' name='montos[{$reg->id}][observaciones]' placeholder='Observaciones' class='form-control'></td>
                </tr>";
            $cont++;
        }
        echo $rows;
        break;

    case 'activar':
        $rspta = $matricula->activar($id);
        echo $rspta ? "Matrícula activada correctamente" : "No se pudo activar la matrícula";
        break;

    case 'desactivar':
        $rspta = $matricula->desactivar($id);
        echo $rspta ? "Matrícula desactivada correctamente" : "No se pudo desactivar la matrícula";
        break;

    case 'eliminar':
        $rspta = $matricula->eliminar($id);
        echo $rspta ? "Matrícula eliminada correctamente" : "No se pudo eliminar la matrícula";
        break;
}
