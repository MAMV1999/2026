<?php
include_once("../Modelo/biblioteca_retiro.php");

$bibliotecaRetiro = new BibliotecaRetiro();

switch ($_GET["op"]) {
    case 'guardaryeditar':
        $biblioteca_retiro_id = $_POST['biblioteca_retiro_id'] ?? "";
        $usuario_docente_id = $_POST['usuario_docente_id'] ?? null;
        $fecha = $_POST['fecha'] ?? null;
        $observaciones = $_POST['observaciones'] ?? '';
        $libros = $_POST['libros'] ?? [];

        if ($usuario_docente_id && $fecha && !empty($libros)) {
            if (empty($biblioteca_retiro_id)) {
                $resultado = $bibliotecaRetiro->guardar($usuario_docente_id, $fecha, $observaciones, $libros);
            } else {
                $resultado = $bibliotecaRetiro->editar($biblioteca_retiro_id, $usuario_docente_id, $fecha, $observaciones, $libros);
            }
            echo $resultado['msg'];
        } else {
            echo "Datos incompletos. Verifique docente, fecha y libros.";
        }
        break;

    case 'mostrar':
        $id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";

        if (empty($id)) {
            echo json_encode(["cabecera" => null, "detalle" => [], "error" => "ID vacío"]);
            break;
        }

        $cabecera = $bibliotecaRetiro->mostrar($id);
        $detalle = [];
        $rsDetalle = $bibliotecaRetiro->listar_detalle($id);

        while ($reg = $rsDetalle->fetch_object()) {
            $detalle[] = $reg;
        }

        echo json_encode(["cabecera" => $cabecera, "detalle" => $detalle, "error" => null]);
        break;

    case 'listar':
        $rspta = $bibliotecaRetiro->listar();
        $data = [];

        while ($reg = $rspta->fetch_object()) {
            $acciones = '<button class="btn btn-info btn-sm" onclick="verRecibo(' . $reg->id . ')">RECIBO</button> ';

            if ($reg->estado == 1 && $reg->devuelto == 0) {
                $acciones .= '<button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->id . ')">EDITAR</button> '
                    . '<button class="btn btn-success btn-sm" onclick="devolver(' . $reg->id . ')">DEVOLVIÓ</button> '
                    . '<button class="btn btn-danger btn-sm" onclick="desactivar(' . $reg->id . ')">ANULAR</button>';
            } elseif ($reg->estado == 1 && $reg->devuelto == 1) {
                $acciones .= '<span class="badge bg-success">DEVUELTO</span>';
            } else {
                $acciones .= '<button class="btn btn-success btn-sm" onclick="activar(' . $reg->id . ')">ACTIVAR</button>';
            }

            $data[] = array(
                "0" => count($data) + 1,
                "1" => $reg->codigo_retiro,
                "2" => $reg->docente,
                "3" => $reg->fecha,
                "4" => $reg->cantidad_libros,
                "5" => $reg->estado_texto,
                "6" => $acciones
            );
        }

        echo json_encode(array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        ));
        break;

    case 'listar_docentes':
        $rspta = $bibliotecaRetiro->listar_docentes();
        echo '<option value="">SELECCIONAR DOCENTE</option>';
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id . '">' . $reg->nombreyapellido . '</option>';
        }
        break;

    case 'listar_libros':
        $rspta = $bibliotecaRetiro->listar_libros();
        $data = [];

        while ($reg = $rspta->fetch_object()) {
            $boton = ((int)$reg->stock > 0)
                ? '<button class="btn btn-warning btn-sm" onclick="agregardetalle(' . $reg->id . ')">AGREGAR</button>'
                : '<button class="btn btn-secondary btn-sm" disabled>SIN STOCK</button>';

            $data[] = array(
                "0" => $reg->codigo,
                "1" => $reg->nombre,
                "2" => $reg->stock,
                "3" => $boton
            );
        }

        echo json_encode(array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        ));
        break;

    case 'mostrar_libro':
        $id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";
        $rspta = $bibliotecaRetiro->mostrar_libro($id);
        echo json_encode($rspta);
        break;

    case 'devolver':
        $id = $_POST['id'] ?? null;
        if ($id) {
            $resultado = $bibliotecaRetiro->devolver($id);
            echo $resultado['msg'];
        } else {
            echo "ID no proporcionado.";
        }
        break;

    case 'desactivar':
        $id = $_POST['id'] ?? null;
        if ($id) {
            $resultado = $bibliotecaRetiro->desactivar($id);
            echo $resultado['msg'];
        } else {
            echo "ID no proporcionado.";
        }
        break;

    case 'activar':
        $id = $_POST['id'] ?? null;
        if ($id) {
            $resultado = $bibliotecaRetiro->activar($id);
            echo $resultado['msg'];
        } else {
            echo "ID no proporcionado.";
        }
        break;
}
?>