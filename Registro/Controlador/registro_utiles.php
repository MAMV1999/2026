<?php
include_once("../Modelo/registro_utiles.php");

$utiles = new Utiles_escolares();

switch ($_GET["op"]) {

    // LISTAR MATRÍCULAS
    case 'listar':
        $rspta = $utiles->listar_matriculas();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $matricula_txt = $reg->lectivo . ' / ' . $reg->nivel . ' / ' . $reg->grado . ' / ' . $reg->seccion;

            $data[] = array(
                "0" => count($data) + 1,
                "1" => $matricula_txt,
                "2" => '<button class="btn btn-warning btn-sm" onclick="mostrar(' . $reg->id . ')">EDITAR</button>'
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

    // MOSTRAR: MATRÍCULA + ÚTILES
    case 'mostrar':
        $id_matricula = isset($_POST["id_matricula"]) ? limpiarcadena($_POST["id_matricula"]) : "";

        $matricula = $utiles->mostrar_matricula($id_matricula);

        $rspta_det = $utiles->listar_utiles_por_matricula($id_matricula);
        $detalles = array();

        while ($d = $rspta_det->fetch_object()) {
            $detalles[] = array(
                "id" => $d->id,
                "nombre" => $d->nombre,
                "observaciones" => $d->observaciones
            );
        }

        echo json_encode(array(
            "matricula" => $matricula,
            "detalles" => $detalles
        ));
        break;

    // GUARDAR / EDITAR MASIVO
    case 'guardaryeditar':
        $id_matricula = isset($_POST["id_matricula"]) ? limpiarcadena($_POST["id_matricula"]) : "";
        $detalles = isset($_POST['detalles']) ? json_decode($_POST['detalles'], true) : [];

        if ($id_matricula == "") {
            echo "Falta seleccionar la matrícula.";
            exit();
        }

        $rspta = $utiles->guardarEditarMasivo($id_matricula, $detalles);
        echo $rspta ? "Útiles guardados/actualizados correctamente" : "Error al guardar/actualizar útiles";
        break;
        
}
?>
