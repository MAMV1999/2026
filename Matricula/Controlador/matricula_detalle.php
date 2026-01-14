<?php
include_once("../Modelo/matricula_detalle.php");

$matriculaDetalle = new MatriculaDetalle();

// Variables recibidas desde el formulario
$apoderado_id = isset($_POST["apoderado_id"]) ? limpiarcadena($_POST["apoderado_id"]) : "";
$apoderado_dni = isset($_POST["apoderado_dni"]) ? limpiarcadena($_POST["apoderado_dni"]) : "";
$apoderado_nombreyapellido = isset($_POST["apoderado_nombreyapellido"]) ? limpiarcadena($_POST["apoderado_nombreyapellido"]) : "";
$apoderado_telefono = isset($_POST["apoderado_telefono"]) ? limpiarcadena($_POST["apoderado_telefono"]) : "";
$apoderado_tipo = isset($_POST["apoderado_tipo"]) ? limpiarcadena($_POST["apoderado_tipo"]) : "";
$apoderado_documento = isset($_POST["apoderado_documento"]) ? limpiarcadena($_POST["apoderado_documento"]) : "";
$apoderado_usuario = isset($_POST["apoderado_usuario"]) ? limpiarcadena($_POST["apoderado_usuario"]) : "";
$apoderado_clave = isset($_POST["apoderado_clave"]) ? limpiarcadena($_POST["apoderado_clave"]) : "";
$apoderado_observaciones = isset($_POST["apoderado_observaciones"]) ? limpiarcadena($_POST["apoderado_observaciones"]) : "";

$alumno_id = isset($_POST["alumno_id"]) ? limpiarcadena($_POST["alumno_id"]) : "";
$alumno_dni = isset($_POST["alumno_dni"]) ? limpiarcadena($_POST["alumno_dni"]) : "";
$alumno_nombreyapellido = isset($_POST["alumno_nombreyapellido"]) ? limpiarcadena($_POST["alumno_nombreyapellido"]) : "";
$alumno_nacimiento = isset($_POST["alumno_nacimiento"]) ? limpiarcadena($_POST["alumno_nacimiento"]) : "";
$alumno_sexo = isset($_POST["alumno_sexo"]) ? limpiarcadena($_POST["alumno_sexo"]) : "";
$alumno_documento = isset($_POST["alumno_documento"]) ? limpiarcadena($_POST["alumno_documento"]) : "";
$alumno_telefono = isset($_POST["alumno_telefono"]) ? limpiarcadena($_POST["alumno_telefono"]) : "";
$alumno_usuario = isset($_POST["alumno_usuario"]) ? limpiarcadena($_POST["alumno_usuario"]) : "";
$alumno_clave = isset($_POST["alumno_clave"]) ? limpiarcadena($_POST["alumno_clave"]) : "";
$alumno_observaciones = isset($_POST["alumno_observaciones"]) ? limpiarcadena($_POST["alumno_observaciones"]) : "";

$detalle = isset($_POST["detalle"]) ? limpiarcadena($_POST["detalle"]) : "";
$matricula_id = isset($_POST["matricula_id"]) ? limpiarcadena($_POST["matricula_id"]) : "";
$matricula_categoria = isset($_POST["matricula_categoria"]) ? limpiarcadena($_POST["matricula_categoria"]) : "";
$referido_id = isset($_POST["apoderado_referido"]) ? limpiarcadena($_POST["apoderado_referido"]) : "0";
$matricula_observaciones = isset($_POST["matricula_observaciones"]) ? limpiarcadena($_POST["matricula_observaciones"]) : "";

$pago_numeracion = isset($_POST["pago_numeracion"]) ? limpiarcadena($_POST["pago_numeracion"]) : "";
$pago_fecha = isset($_POST["pago_fecha"]) ? limpiarcadena($_POST["pago_fecha"]) : "";
$pago_descripcion = isset($_POST["pago_descripcion"]) ? limpiarcadena($_POST["pago_descripcion"]) : "";
$pago_monto = isset($_POST["pago_monto"]) ? limpiarcadena($_POST["pago_monto"]) : "";
$pago_metodo_id = isset($_POST["pago_metodo_id"]) ? limpiarcadena($_POST["pago_metodo_id"]) : "";
$pago_observaciones = isset($_POST["pago_observaciones"]) ? limpiarcadena($_POST["pago_observaciones"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        $rspta = $matriculaDetalle->guardar(
            $apoderado_dni,
            $apoderado_nombreyapellido,
            $apoderado_telefono,
            $apoderado_tipo,
            $apoderado_documento,
            $apoderado_observaciones,
            $alumno_dni,
            $alumno_nombreyapellido,
            $alumno_nacimiento,
            $alumno_sexo,
            $alumno_documento,
            $alumno_telefono,
            $alumno_observaciones,
            $detalle,
            $matricula_id,
            $matricula_categoria,
            $referido_id,
            $matricula_observaciones,
            $pago_numeracion,
            $pago_fecha,
            $pago_descripcion,
            $pago_monto,
            $pago_metodo_id,
            $pago_observaciones,
            $_POST["mensualidad_id"],
            $_POST["total_precio"],
            $apoderado_id,
            $alumno_id
        );
        echo $rspta ? "Matrícula registrada correctamente" : "No se pudo registrar la matrícula";
        break;

    case 'eliminar_con_validacion':
        $id_matricula_detalle = isset($_POST["id_matricula_detalle"]) ? limpiarcadena($_POST["id_matricula_detalle"]) : "";
        $contraseña = isset($_POST["contraseña"]) ? limpiarcadena($_POST["contraseña"]) : "";

        // Validar la contraseña utilizando el modelo
        if ($matriculaDetalle->validarContraseña($contraseña)) {
            // Si la contraseña es válida, eliminar el registro
            $rspta = $matriculaDetalle->eliminar($id_matricula_detalle);
            echo $rspta ? "Registro eliminado correctamente" : "No se pudo eliminar el registro";
        } else {
            echo "Contraseña inválida. No se realizó ninguna acción.";
        }
        break;

    case 'desactivar_con_validacion':
        $id_matricula_detalle = isset($_POST["id_matricula_detalle"]) ? limpiarcadena($_POST["id_matricula_detalle"]) : "";
        $contraseña = isset($_POST["contraseña"]) ? limpiarcadena($_POST["contraseña"]) : "";

        // Validar la contraseña utilizando el modelo
        if ($matriculaDetalle->validarContraseña($contraseña)) {
            // Si la contraseña es válida, desactivar el registro
            $rspta = $matriculaDetalle->desactivar($id_matricula_detalle);
            echo $rspta ? "Registro desactivado correctamente" : "No se pudo desactivar el registro";
        } else {
            echo "Contraseña inválida. No se realizó ninguna acción.";
        }
        break;

    case 'listar_apoderado_tipos_activos':
        $rspta = $matriculaDetalle->listarApoderadoTiposActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id . '">' . $reg->nombre . '</option>';
        }
        break;

    // Listar los documentos activos
    case 'listar_documentos_activos':
        $rspta = $matriculaDetalle->listarDocumentosActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id . '">' . $reg->nombre . '</option>';
        }
        break;

    // Listar los sexos activos
    case 'listar_sexos_activos':
        $rspta = $matriculaDetalle->listarSexosActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id . '">' . $reg->nombre . '</option>';
        }
        break;

    // Listar los estados civiles activos
    case 'listar_estados_civiles_activos':
        $rspta = $matriculaDetalle->listarEstadosCivilesActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id . '">' . $reg->nombre . '</option>';
        }
        break;

    // Listar las matrículas activas
    case 'listar_matriculas_activas':
        $rspta = $matriculaDetalle->listarMatriculasActivas();

        while ($reg = $rspta->fetch_object()) {

            // 1) Atributos fijos
            $attrs = '';
            $attrs .= ' data-id="' . $reg->matricula_id . '"';
            $attrs .= ' data-lectivo="' . htmlspecialchars($reg->lectivo, ENT_QUOTES, 'UTF-8') . '"';
            $attrs .= ' data-nivel="' . htmlspecialchars($reg->nivel, ENT_QUOTES, 'UTF-8') . '"';
            $attrs .= ' data-grado="' . htmlspecialchars($reg->grado, ENT_QUOTES, 'UTF-8') . '"';
            $attrs .= ' data-seccion="' . htmlspecialchars($reg->seccion, ENT_QUOTES, 'UTF-8') . '"';
            $attrs .= ' data-aforo="' . (int)$reg->aforo . '"';
            $attrs .= ' data-matriculados="' . (int)$reg->matriculados . '"';
            $attrs .= ' data-observaciones="' . htmlspecialchars((string)$reg->observaciones, ENT_QUOTES, 'UTF-8') . '"';

            // 2) Atributos dinámicos: detectar qué columnas son "cobros"
            //    Excluimos las columnas fijas conocidas
            $fijas = [
                'matricula_id',
                'lectivo',
                'nivel',
                'grado',
                'seccion',
                'docente',
                'aforo',
                'matriculados',
                'observaciones'
            ];

            foreach ($reg as $col => $val) {
                if (in_array($col, $fijas, true)) continue;

                // Si el valor viene NULL, no lo mandes como data (opcional)
                if ($val === null || $val === '') continue;

                // slug seguro para data-* (minúsculas, sin espacios, sin símbolos raros)
                $slug = strtolower($col);
                $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
                $slug = trim($slug, '-');

                $attrs .= ' data-cobro-' . $slug . '="' . htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8') . '"';
            }

            echo '<option value="' . $reg->matricula_id . '"' . $attrs . '>' . htmlspecialchars($reg->lectivo . ' - ' . $reg->nivel . ' - ' . $reg->grado . ' - ' . $reg->seccion, ENT_QUOTES, 'UTF-8') . ' -->(Aforo: ' . (int)$reg->aforo . ', Matriculados: ' . (int)$reg->matriculados . ')</option>';
        }
        break;


    case 'listar_apoderados_referido_activo':
        $rspta = $matriculaDetalle->listarApoderadosReferidoActivo();
        echo '<option value="">NO TIENE REFERENCIA</Soption>';
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id . '">' . $reg->nombreyapellido . '</option>';
        }
        break;


    // Listar las categorías de matrícula activas
    case 'listar_categorias_activas':
        $rspta = $matriculaDetalle->listarCategoriasActivas();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id . '">' . $reg->nombre . '</option>';
        }
        break;

    // Listar los métodos de pago activos
    case 'listar_metodos_pago_activos':
        $rspta = $matriculaDetalle->listarMetodosPagoActivos();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id . '">' . $reg->nombre . '</option>';
        }
        break;

    case 'listar':
        $rspta = $matriculaDetalle->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => count($data) + 1,
                "1" => $reg->lectivo . ' - ' . $reg->nivel . ' - ' . $reg->grado,
                "2" => $reg->nombre_alumno,
                "3" => $reg->nombre_apoderado,
                "4" => $reg->categoria,
                "5" => '
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#' . $reg->numeracion_pago . '">DATOS</button>
                            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="visually-hidden">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="../../Reportes/Vista/ReciboMatricula.php?id=' . $reg->matricula_detalle_id . '" Target="_blank">RECIBO ' . $reg->numeracion_pago . ' - ' . $reg->fecha_pago . '</a></li>
                                <li><a class="dropdown-item" href="../../Reportes/Vista/ReciboMatricula_copy.php?id=' . $reg->matricula_detalle_id . '" Target="_blank">FIRMA CONTRATO</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="../../Reportes/Vista/Constancia_vacante.php?id=' . $reg->matricula_detalle_id . '" Target="_blank">CONST. DE VACANTE</a></li>
                                <li><a class="dropdown-item" href="../../Reportes/Vista/Constancia_Matricula.php?id=' . $reg->matricula_detalle_id . '" Target="_blank">CONST. DE MATRICULA</a></li>
                                <li><a class="dropdown-item" href="../../Reportes/Vista/Constancia_Estudios.php?id=' . $reg->matricula_detalle_id . '" Target="_blank">CONST. DE ESTUDIOS</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="../../Reportes/Vista/Documentacion_Completa.php?id=' . $reg->matricula_detalle_id . '" Target="_blank">DOCUMENTOS</a></li>
                            </ul>
                        </div>
                        
                        <!-- Modal -->
                        <div class="modal fade" id="' . $reg->numeracion_pago . '" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">' . $reg->nombre_apoderado . ' - ' . $reg->nombre_alumno . '</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                            <!-- body -->
                                <div class="p-3">
                                    <table class="table table-bordered">
                                        
                                        <tr class="table-secondary"><td colspan="2">INSTITUCION</td></tr>
                                        
                                        <tr>    <td>INSTITUCION</td>            <td>' . $reg->institucion . '</td>                                                      </tr>
                                        <tr>    <td>LECTIVO</td>                <td>' . $reg->lectivo . '</td>                                                          </tr>
                                        <tr>    <td>MATRICULA</td>              <td>' . $reg->nivel . ' / ' . $reg->grado . ' / ' . $reg->seccion . '</td>              </tr>
                                        <tr>    <td>TIPO DE MATRICULA</td>      <td>' . $reg->categoria . '</td>                                                        </tr>

                                        <tr class="table-secondary"><td colspan="2">APODERADO(A)</td></tr>

                                        <tr>    <td>PARENTESCO</td>             <td>' . $reg->tipo_apoderado . '</td>                                                   </tr>
                                        <tr>    <td>DOCUMENTO</td>              <td>' . $reg->documento_apoderado . ' ' . $reg->numero_documento_apoderado . '</td>     </tr>
                                        <tr>    <td>NOMBRE</td>                 <td>' . $reg->nombre_apoderado . '</td>                                                 </tr>
                                        <tr>    <td>TELEFONO</td>               <td>' . $reg->telefono_apoderado . '</td>                                               </tr>

                                        <tr class="table-secondary"><td colspan="2">ALUMNO(A)</td></tr>

                                        <tr>    <td>DOCUMENTO</td>              <td>' . $reg->documento_alumno . ' ' . $reg->numero_documento_alumno . '</td>           </tr>
                                        <tr>    <td>NOMBRE</td>                 <td>' . $reg->nombre_alumno . '</td>                                                    </tr>
                                        <tr>    <td>NACIMIENTO</td>             <td>' . $reg->fecha_nacimiento . '</td>                                                 </tr>
                                        <tr>    <td>EDAD</td>                   <td>' . $reg->edad_alumno . ' AÑOS</td>                                                 </tr>

                                        <tr class="table-secondary"><td colspan="2">MATRICULA</td></tr>

                                        <tr>    <td>FECHA</td>                  <td>' . $reg->fecha_pago . '</td>                                                       </tr>
                                        <tr>    <td>NUMERACION</td>             <td>' . $reg->numeracion_pago . '</td>                                                  </tr>
                                        <tr>    <td>MONTO</td>                  <td>' . $reg->monto_pago . '</td>                                                       </tr>
                                        <tr>    <td>METODO DE PAGO</td>         <td>' . $reg->metodo_pago . '</td>                                                      </tr>

                                    </table>
                                    <br>
                                    <center>
                                        <button type="button" onclick="eliminarConValidacion(' . $reg->matricula_detalle_id . ')" class="btn btn-danger  btn-sm">ELIMINAR</button>
                                        <button type="button" onclick="desactivarConValidacion(' . $reg->matricula_detalle_id . ')" class="btn btn-warning  btn-sm">DESACTIVAR</button>
                                    </center>
                                </div>
                            <!-- fin-body -->
                            </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">SALIR</button>
                                </div>
                            </div>
                        </div>
                        </div>'
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

    case 'obtener_siguiente_numeracion_pago':
        $numeracion = $matriculaDetalle->getNextPagoNumeracion();
        echo $numeracion;
        break;

    case 'buscar_apoderado':
        $dni = isset($_POST['dni']) ? limpiarcadena($_POST['dni']) : '';
        $rspta = $matriculaDetalle->buscarApoderadoPorDNI($dni);
        echo json_encode($rspta);
        break;

    case 'buscar_alumno':
        $dni = isset($_POST['dni']) ? limpiarcadena($_POST['dni']) : '';
        $rspta = $matriculaDetalle->buscarAlumnoPorDNI($dni);
        echo json_encode($rspta);
        break;

    case 'listar_mensualidades_activas':
        $matricula_id = isset($_GET["matricula_id"]) ? limpiarcadena($_GET["matricula_id"]) : "";
        $rspta = $matriculaDetalle->listarMensualidadesActivas($matricula_id);
        $rows = "";
        $cont = 1;
        while ($reg = $rspta->fetch_object()) {
            $rows .= "
                    <tr>
                        <th style='width: 10%;'><input  type='hidden'  required  class='form-control-plaintext'  name='mensualidad_id[]'        value='{$reg->id}'                                         >{$cont}</td>
                        <td style='width: 20%;'><input  type='text'    required  class='form-control-plaintext'  name='mensualidad_nombre[]'    value='{$reg->nombre} {$reg->institucion_lectivo_nombre}'  ></td>
                        <td style='width: 15%;'><input  type='text'    required  class='form-control-plaintext'  name='mensualidad_precio[]'    value='{$reg->MENSUALIDAD}'                                ></td>
                        <td style='width: 15%;'><input  type='text'    required  class='form-control-plaintext'  name='mantenimiento_precio[]'  value='{$reg->MANTENIMIENTO}'                              ></td>
                        <td style='width: 15%;'><input  type='text'    required  class='form-control-plaintext'  name='impresion_precio[]'      value='{$reg->IMPRESION}'                                  ></td>
                        <td style='width: 15%;'><input  type='text'    required  class='form-control-plaintext'  name='total_precio[]'          value=''                                                   ></td>
                    </tr>";
            $cont++;
        }
        echo $rows;
        break;

    case 'listar_apoderados_referidos_activos':
        $rspta = $matriculaDetalle->listarApoderadosReferidosActivos();
        echo '<option value="">NO TIENE REFERENCIA</option>'; // Primera opción vacía
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id . '">' . $reg->nombreyapellido . ' (' . $reg->repeticiones . ')</option>';
        }
        break;
}
