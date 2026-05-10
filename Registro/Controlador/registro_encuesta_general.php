<?php
include_once("../Modelo/registro_encuesta_general.php");

$registro = new RegistroEncuesta();

$id = isset($_POST["id"]) ? limpiarcadena($_POST["id"]) : "";

switch ($_GET["op"]) {

    case 'guardar':

        $id = $_POST["id"] ?? "";
        $nombre = $_POST["nombre"] ?? "";
        $fecha_inicio = $_POST["fecha_inicio"] ?? "";
        $fecha_fin = $_POST["fecha_fin"] ?? "";
        $calificacion_menor = $_POST["calificacion_menor"] ?? "";
        $calificacion_mayor = $_POST["calificacion_mayor"] ?? "";
        $observaciones = $_POST["observaciones"] ?? "";

        $docentes = $_POST["docentes"] ?? [];
        $alumnos = $_POST["alumnos"] ?? [];

        if ($nombre != "" && $fecha_inicio != "" && $fecha_fin != "" && !empty($docentes) && !empty($alumnos)) {

            if (empty($id)) {
                $rspta = $registro->guardar(
                    $nombre,
                    $fecha_inicio,
                    $fecha_fin,
                    $calificacion_menor,
                    $calificacion_mayor,
                    $observaciones,
                    $docentes,
                    $alumnos
                );

                echo $rspta ? "Encuesta registrada correctamente" : "Error al guardar la encuesta";

            } else {
                $rspta = $registro->editar(
                    $id,
                    $nombre,
                    $fecha_inicio,
                    $fecha_fin,
                    $calificacion_menor,
                    $calificacion_mayor,
                    $observaciones,
                    $docentes,
                    $alumnos
                );

                echo $rspta ? "Encuesta actualizada correctamente" : "Error al actualizar la encuesta";
            }

        } else {
            echo "Datos incompletos. Seleccione docentes y alumnos.";
        }

        break;

    case 'listar':

        $rspta = $registro->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {

            if ($reg->dias_restantes > 0) {
                $estado_fecha = "Faltan " . $reg->dias_restantes . " días";
            } elseif ($reg->dias_restantes == 0) {
                $estado_fecha = "Termina hoy";
            } else {
                $estado_fecha = "Finalizó hace " . abs($reg->dias_restantes) . " días";
            }
        
            $data[] = array(
                "0" => count($data) + 1,
                "1" => $reg->nombre,
                "2" => $reg->fecha_inicio . " - " . $reg->fecha_fin,
                "3" => $estado_fecha,
                "4" => $reg->calificacion_menor . " - " . $reg->calificacion_mayor,
                "5" => ($reg->estado) ?
                    '<button class="btn btn-warning btn-sm" onclick="mostrar('.$reg->id.')">EDITAR</button>
                     <button class="btn btn-danger btn-sm" onclick="desactivar('.$reg->id.')">DESACTIVAR</button>'
                    :
                    '<button class="btn btn-warning btn-sm" onclick="mostrar('.$reg->id.')">EDITAR</button>
                     <button class="btn btn-primary btn-sm" onclick="activar('.$reg->id.')">ACTIVAR</button>'
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

        $cabecera = $registro->mostrar($id);

        $docentesSeleccionados = array();
        $rsptaDoc = $registro->listarDocentesSeleccionados($id);

        while ($reg = $rsptaDoc->fetch_object()) {
            $docentesSeleccionados[] = $reg->usuario_docente_id;
        }

        $alumnosSeleccionados = array();
        $rsptaAlu = $registro->listarAlumnosSeleccionados($id);

        while ($reg = $rsptaAlu->fetch_object()) {
            $alumnosSeleccionados[] = $reg->matricula_detalle_id;
        }

        echo json_encode(array(
            "cabecera" => $cabecera,
            "docentes" => $docentesSeleccionados,
            "alumnos" => $alumnosSeleccionados
        ));

        break;

    case 'listar_docentes':

        $rspta = $registro->listarDocentes();

        while ($reg = $rspta->fetch_object()) {
            echo '
                <tr>
                    <td>
                        <input type="checkbox" name="docentes[]" value="'.$reg->id.'" class="check_docente">
                    </td>
                    <td>'.$reg->nombreyapellido.'</td>
                </tr>
            ';
        }

        break;

    case 'listar_alumnos':

        $rspta = $registro->listarAlumnos();

        $grupos = array();

        while ($reg = $rspta->fetch_object()) {

            $nombreGrupo = $reg->nivel . " - " . $reg->grado;
            $gradoKey = md5($nombreGrupo);

            if (!isset($grupos[$gradoKey])) {
                $grupos[$gradoKey] = array(
                    "titulo" => $nombreGrupo,
                    "alumnos" => array()
                );
            }

            $grupos[$gradoKey]["alumnos"][] = array(
                "id" => $reg->matricula_detalle_id,
                "seccion" => $reg->seccion,
                "alumno" => $reg->alumno,
                "nivel" => $reg->nivel,
                "grado" => $reg->grado
            );
        }

        $contadorAccordion = 1;

        foreach ($grupos as $gradoKey => $grupo) {

            $collapseId = "collapse_" . $gradoKey;
            $headingId = "heading_" . $gradoKey;

            $show = ($contadorAccordion == 1) ? "show" : "";
            $collapsed = ($contadorAccordion == 1) ? "" : "collapsed";
            $ariaExpanded = ($contadorAccordion == 1) ? "true" : "false";

            echo '
            <div class="accordion-item">
                <h2 class="accordion-header" id="'.$headingId.'">
                    <button class="accordion-button '.$collapsed.'" type="button" data-bs-toggle="collapse" data-bs-target="#'.$collapseId.'" aria-expanded="'.$ariaExpanded.'" aria-controls="'.$collapseId.'">
                        <b>'.$grupo["titulo"].'</b> &nbsp; <span class="badge bg-primary">'.count($grupo["alumnos"]).' alumnos</span>
                    </button>
                </h2>

                <div id="'.$collapseId.'" class="accordion-collapse collapse '.$show.'" aria-labelledby="'.$headingId.'" data-bs-parent="#accordionAlumnos">
                    <div class="accordion-body">

                        <div class="mb-2 text-end">
                            <button type="button" class="btn btn-sm btn-success" onclick="marcarGrado(\''.$gradoKey.'\')">
                                Marcar grado
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="desmarcarGrado(\''.$gradoKey.'\')">
                                Desmarcar grado
                            </button>
                        </div>

                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">N°</th>
                                    <th style="width: 80px;">SEL.</th>
                                    <th>SECCIÓN</th>
                                    <th>ALUMNO</th>
                                </tr>
                            </thead>
                            <tbody>
            ';

            $cont = 1;

            foreach ($grupo["alumnos"] as $alumno) {
                echo '
                    <tr>
                        <td>'.$cont.'</td>
                        <td>
                            <input type="checkbox" name="alumnos[]" value="'.$alumno["id"].'" class="check_alumno grado_'.$gradoKey.'">
                        </td>
                        <td>'.$alumno["seccion"].'</td>
                        <td>'.$alumno["alumno"].'</td>
                    </tr>
                ';

                $cont++;
            }

            echo '
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            ';

            $contadorAccordion++;
        }

        break;

    case 'activar':

        $rspta = $registro->activar($id);
        echo $rspta ? "Encuesta activada correctamente" : "No se pudo activar la encuesta";

        break;

    case 'desactivar':

        $rspta = $registro->desactivar($id);
        echo $rspta ? "Encuesta desactivada correctamente" : "No se pudo desactivar la encuesta";

        break;
}