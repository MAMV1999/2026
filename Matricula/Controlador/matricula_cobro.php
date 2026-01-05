<?php
include_once("../Modelo/matricula_cobro.php");

$matriculaCobro = new MatriculaCobro();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarcadena($_POST["nombre"]) : "";
$apertura = isset($_POST["apertura"]) ? limpiarcadena($_POST["apertura"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarcadena($_POST["observaciones"]) : "";
$estado = isset($_POST["estado"]) ? limpiarcadena($_POST["estado"]) : "1";

switch ($_GET["op"]) {

    case 'guardar':
        $nombre = $_POST['nombre'] ?? null;
        $apertura = $_POST['apertura'] ?? null;
        $observaciones = $_POST['observaciones'] ?? null;
        $meses = $_POST['meses'] ?? [];

        if ($nombre && !empty($meses)) {
            $resultado = $matriculaCobro->guardar($nombre, $apertura, $observaciones, $meses);
            echo $resultado ? "Cobro registrado correctamente" : "Error al guardar el registro";
        } else {
            echo "Datos incompletos. Verifique e intente nuevamente.";
        }
        break;

    // =========================
    // NUEVO: EDITAR
    // =========================
    case 'editar':
        $id = $_POST['id'] ?? null;
        $nombre = $_POST['nombre'] ?? null;
        $apertura = $_POST['apertura'] ?? null;
        $observaciones = $_POST['observaciones'] ?? null;
        $meses = $_POST['meses'] ?? [];

        if ($id && $nombre && !empty($meses)) {
            $resultado = $matriculaCobro->editar($id, $nombre, $apertura, $observaciones, $meses);
            echo $resultado ? "Cobro actualizado correctamente" : "Error al actualizar el registro";
        } else {
            echo "Datos incompletos. Verifique e intente nuevamente.";
        }
        break;

    // =========================
    // NUEVO: MOSTRAR (para cargar formulario al editar)
    // =========================
    case 'mostrar':
        $id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";

        if (empty($id)) {
            echo json_encode(array("error" => "ID vacío"));
            break;
        }

        $data = $matriculaCobro->mostrar($id);
        echo json_encode($data);
        break;

    case 'listar':
        $rspta = $matriculaCobro->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => count($data) + 1,
                "1" => $reg->nombre,
                "2" => ($reg->apertura) == 1 ? 'Sí' : 'No',
                "3" => ($reg->estado) ?
                    '<button type="button" onclick="mostrar(' . $reg->id . ')" class="btn btn-warning btn-sm">EDITAR</button> <button type="button" onclick="desactivar(' . $reg->id . ')" class="btn btn-danger btn-sm">DESACTIVAR</button>'
                    :
                    '<button type="button" onclick="mostrar(' . $reg->id . ')" class="btn btn-warning btn-sm">EDITAR</button> <button type="button" onclick="activar(' . $reg->id . ')" class="btn btn-success btn-sm">ACTIVAR</button>',
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

    case 'listar_matricula_mes_activas':
        $rspta = $matriculaCobro->listar_matricula_mes_activas();
        $cont = 1;
        $rows = "";
        while ($reg = $rspta->fetch_object()) {
            $rows .= "
                <tr>
                    <th scope='row'>{$cont}</th>
                    <td><input type='hidden' id='meses[{$cont}][matricula_mes_id]' name='meses[{$cont}][matricula_mes_id]' value='{$reg->id}'> {$reg->nombre_con_institucion}</td>
                    <td>
                        <input style='width: 30px; height: 30px;' type='radio' id='meses[{$cont}][aplica]' name='meses[{$cont}][aplica]' value='0' class='form-check-input' checked> NO
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <input style='width: 30px; height: 30px;' type='radio' id='meses[{$cont}][aplica]' name='meses[{$cont}][aplica]' value='1' class='form-check-input'> SÍ
                    </td>
                    <td>
                        <input type='text' id='meses[{$cont}][observaciones]' name='meses[{$cont}][observaciones]' placeholder='Observaciones' class='form-control'>
                    </td>
                </tr>";
            $cont++;
        }
        echo $rows;
        break;

    case 'desactivar':
        $id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";

        if (empty($id)) {
            echo "ID vacío";
            break;
        }

        $resultado = $matriculaCobro->desactivar($id);
        echo $resultado ? "Cobro desactivado correctamente" : "Error al desactivar";
        break;

    case 'activar':
        $id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";

        if (empty($id)) {
            echo "ID vacío";
            break;
        }

        $resultado = $matriculaCobro->activar($id);
        echo $resultado ? "Cobro activado correctamente" : "Error al activar";
        break;
}
