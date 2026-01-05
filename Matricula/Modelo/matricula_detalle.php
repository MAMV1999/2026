<?php
require_once("../../database.php");

class MatriculaDetalle
{
    public function __construct() {}

    public function guardar($apoderado_dni, $apoderado_nombreyapellido, $apoderado_telefono, $apoderado_tipo, $apoderado_documento, $apoderado_observaciones, $alumno_dni, $alumno_nombreyapellido, $alumno_nacimiento,
    $alumno_sexo, $alumno_documento, $alumno_telefono, $alumno_observaciones, $detalle, $matricula_id, $matricula_categoria, $referido_id, $matricula_observaciones, $pago_numeracion, $pago_fecha, $pago_descripcion, $pago_monto, $pago_metodo_id, $pago_observaciones,

        $mensualidad_id, // Array con los mensualidad_id de las mensualidades
        $total_precio, // Array con los total_precio de las mensualidades
        $apoderado_id = null, // ID de apoderado (si ya existe)
        $alumno_id = null // ID de alumno (si ya existe)
    ) {
        // Validar si ya existe el ID del apoderado
        if (!$apoderado_id) {
            $sql_apoderado = "INSERT INTO usuario_apoderado (numerodocumento, nombreyapellido, telefono, id_apoderado_tipo, id_documento, usuario, clave, observaciones, estado) VALUES ('$apoderado_dni', '$apoderado_nombreyapellido', '$apoderado_telefono', '$apoderado_tipo', '$apoderado_documento', '$apoderado_dni', '$apoderado_dni', '$apoderado_observaciones', '1')";
            $apoderado_id = ejecutarConsulta_retornarID($sql_apoderado);

            if (!$apoderado_id) {
                return false; // Falló al guardar apoderado
            }
        }

        // Validar si ya existe el ID del alumno
        if (!$alumno_id) {
            $sql_alumno = "INSERT INTO usuario_alumno (id_apoderado, numerodocumento, nombreyapellido, nacimiento, id_documento, id_sexo, telefono, usuario, clave, observaciones, estado) VALUES ('$apoderado_id', '$alumno_dni', '$alumno_nombreyapellido', '$alumno_nacimiento', '$alumno_documento', '$alumno_sexo', '$alumno_telefono', '$alumno_dni', '$alumno_dni', '$alumno_observaciones', '1')";
            $alumno_id = ejecutarConsulta_retornarID($sql_alumno);

            if (!$alumno_id) {
                // Eliminar apoderado si falla la creación del alumno
                $sql_eliminar_apoderado = "DELETE FROM usuario_apoderado WHERE id = '$apoderado_id'";
                ejecutarConsulta($sql_eliminar_apoderado);
                return false;
            }
        }

        $sql_matricula_detalle = "INSERT INTO matricula_detalle (id_usuario_apoderado, id_usuario_alumno, descripcion, id_matricula, id_matricula_categoria, id_usuario_apoderado_referido, observaciones, estado) VALUES ('$apoderado_id', '$alumno_id', '$detalle', '$matricula_id', '$matricula_categoria', " . ($referido_id ? "'$referido_id'" : "NULL") . ", '$matricula_observaciones', '1')";
        $matricula_detalle_id = ejecutarConsulta_retornarID($sql_matricula_detalle);

        if ($matricula_detalle_id) {
            // Guardar matricula_pago
            $sql_matricula_pago = "INSERT INTO matricula_pago (id_matricula_detalle, numeracion, fecha, descripcion, monto, id_matricula_metodo_pago, observaciones, estado) VALUES ('$matricula_detalle_id', '$pago_numeracion', '$pago_fecha', '$pago_descripcion', '$pago_monto', '$pago_metodo_id', '$pago_observaciones', '1')";
            $pago_id = ejecutarConsulta_retornarID($sql_matricula_pago);

            if ($pago_id) {
                // Guardar varias filas en mensualidad_detalle
                foreach ($mensualidad_id as $index => $matricula_mes_id) {
                    $precio = $total_precio[$index];
                    $sql_mensualidad_detalle = "INSERT INTO mensualidad_detalle (matricula_mes_id, id_matricula_detalle, monto, pagado, observaciones, estado) VALUES ('$matricula_mes_id', '$matricula_detalle_id', '$precio', '0', '', '1')";

                    if (!ejecutarConsulta($sql_mensualidad_detalle)) {
                        // Si falla, eliminar todos los registros creados
                        $sql_eliminar_pago = "DELETE FROM matricula_pago WHERE id = '$pago_id'";
                        ejecutarConsulta($sql_eliminar_pago);

                        $sql_eliminar_matricula_detalle = "DELETE FROM matricula_detalle WHERE id = '$matricula_detalle_id'";
                        ejecutarConsulta($sql_eliminar_matricula_detalle);

                        $sql_eliminar_alumno = "DELETE FROM usuario_alumno WHERE id = '$alumno_id'";
                        ejecutarConsulta($sql_eliminar_alumno);

                        $sql_eliminar_apoderado = "DELETE FROM usuario_apoderado WHERE id = '$apoderado_id'";
                        ejecutarConsulta($sql_eliminar_apoderado);

                        return false;
                    }
                }

                return true; // Todo se guardó correctamente
            } else {
                // Eliminar matricula_detalle si falla matricula_pago
                $sql_eliminar_matricula_detalle = "DELETE FROM matricula_detalle WHERE id = '$matricula_detalle_id'";
                ejecutarConsulta($sql_eliminar_matricula_detalle);

                $sql_eliminar_alumno = "DELETE FROM usuario_alumno WHERE id = '$alumno_id'";
                ejecutarConsulta($sql_eliminar_alumno);

                $sql_eliminar_apoderado = "DELETE FROM usuario_apoderado WHERE id = '$apoderado_id'";
                ejecutarConsulta($sql_eliminar_apoderado);
            }
        } else {
            // Eliminar usuario_alumno si falla matricula_detalle
            $sql_eliminar_alumno = "DELETE FROM usuario_alumno WHERE id = '$alumno_id'";
            ejecutarConsulta($sql_eliminar_alumno);

            $sql_eliminar_apoderado = "DELETE FROM usuario_apoderado WHERE id = '$apoderado_id'";
            ejecutarConsulta($sql_eliminar_apoderado);
        }

        return false; // Falló en algún punto
    }


    public function buscarApoderadoPorDNI($dni)
    {
        $sql = "SELECT * FROM usuario_apoderado WHERE numerodocumento = '$dni' AND estado = '1'";
        $result = ejecutarConsultaSimpleFila($sql);
        return $result ? $result : [];
    }

    public function buscarAlumnoPorDNI($dni)
    {
        $sql = "SELECT * FROM usuario_alumno WHERE numerodocumento = '$dni' AND estado = '1'";
        $result = ejecutarConsultaSimpleFila($sql);
        return $result ? $result : [];
    }

    public function listar()
    {
        $sql = "SELECT
                    md.id AS matricula_detalle_id,
                    i.nombre AS institucion,
                    il.nombre AS lectivo,
                    iniv.nombre AS nivel,
                    ig.nombre AS grado,
                    isec.nombre AS seccion,
                    mc.nombre AS categoria,
                    uat.nombre AS tipo_apoderado,
                    ud_ap.nombre AS documento_apoderado,
                    ua.numerodocumento AS numero_documento_apoderado,
                    ua.nombreyapellido AS nombre_apoderado,
                    ua.telefono AS telefono_apoderado,
                    ud_al.nombre AS documento_alumno,
                    ual.numerodocumento AS numero_documento_alumno,
                    ual.nombreyapellido AS nombre_alumno,
                    DATE_FORMAT(ual.nacimiento, '%d/%m/%Y') AS fecha_nacimiento,
                    TIMESTAMPDIFF(YEAR, ual.nacimiento, CURDATE()) AS edad_alumno,
                    mp.numeracion AS numeracion_pago,
                    DATE_FORMAT(mp.fecha, '%d/%m/%Y') AS fecha_pago,
                    mp.descripcion AS descripcion_pago,
                    mp.monto AS monto_pago,
                    mmp.nombre AS metodo_pago
                FROM matricula_detalle md
                INNER JOIN matricula m ON md.id_matricula = m.id AND m.estado = 1
                INNER JOIN institucion_seccion isec ON m.id_institucion_seccion = isec.id AND isec.estado = 1
                INNER JOIN institucion_grado ig ON isec.id_institucion_grado = ig.id AND ig.estado = 1
                INNER JOIN institucion_nivel iniv ON ig.id_institucion_nivel = iniv.id AND iniv.estado = 1
                INNER JOIN institucion_lectivo il ON iniv.id_institucion_lectivo = il.id AND il.estado = 1
                INNER JOIN institucion i ON il.id_institucion = i.id AND i.estado = 1
                INNER JOIN matricula_categoria mc ON md.id_matricula_categoria = mc.id AND mc.estado = 1
                INNER JOIN usuario_apoderado ua ON md.id_usuario_apoderado = ua.id AND ua.estado = 1
                INNER JOIN usuario_apoderado_tipo uat ON ua.id_apoderado_tipo = uat.id AND uat.estado = 1
                INNER JOIN usuario_documento ud_ap ON ua.id_documento = ud_ap.id AND ud_ap.estado = 1
                INNER JOIN usuario_alumno ual ON md.id_usuario_alumno = ual.id AND ual.estado = 1
                INNER JOIN usuario_documento ud_al ON ual.id_documento = ud_al.id AND ud_al.estado = 1
                LEFT JOIN matricula_pago mp ON md.id = mp.id_matricula_detalle AND mp.estado = 1
                LEFT JOIN matricula_metodo_pago mmp ON mp.id_matricula_metodo_pago = mmp.id AND mmp.estado = 1
                WHERE md.estado = 1
                ORDER BY il.nombre ASC, iniv.nombre ASC, ig.nombre ASC, isec.nombre ASC, mc.nombre ASC, ual.nombreyapellido ASC";
        return ejecutarConsulta($sql);
    }

    // Función para listar los tipos de apoderado activos
    public function listarApoderadoTiposActivos()
    {
        $sql = "SELECT id, nombre FROM usuario_apoderado_tipo WHERE estado = '1'";
        return ejecutarConsulta($sql);
    }

    // Función para listar los documentos activos
    public function listarDocumentosActivos()
    {
        $sql = "SELECT id, nombre FROM usuario_documento WHERE estado = '1'";
        return ejecutarConsulta($sql);
    }

    // Función para listar los sexos activos
    public function listarSexosActivos()
    {
        $sql = "SELECT id, nombre FROM usuario_sexo WHERE estado = '1'";
        return ejecutarConsulta($sql);
    }

    // Función para listar los estados civiles activos
    public function listarEstadosCivilesActivos()
    {
        $sql = "SELECT id, nombre FROM usuario_estado_civil WHERE estado = '1'";
        return ejecutarConsulta($sql);
    }

    // Función para listar las matrículas activas
    public function listarMatriculasActivas()
    {
        $sql = "CALL sp_matricula_listar_pivot()";
        return ejecutarConsulta($sql);
    }

    // Método para listar los apoderados activos referidos
    public function listarApoderadosReferidoActivo()
    {
        $sql = "SELECT id, nombreyapellido FROM usuario_apoderado WHERE estado = '1'";
        return ejecutarConsulta($sql);
    }

    // Función para listar las categorías de matrícula activas
    public function listarCategoriasActivas()
    {
        $sql = "SELECT id, nombre FROM matricula_categoria WHERE estado = '1'";
        return ejecutarConsulta($sql);
    }

    // Función para listar los métodos de pago activos
    public function listarMetodosPagoActivos()
    {
        $sql = "SELECT id, nombre FROM matricula_metodo_pago WHERE estado = '1'";
        return ejecutarConsulta($sql);
    }

    public function getNextPagoNumeracion()
    {
        $sql = "SELECT LPAD(IFNULL(MAX(CAST(numeracion AS UNSIGNED)) + 1, 1), 6, '0') AS numeracion FROM matricula_pago";
        $result = ejecutarConsultaSimpleFila($sql);
        return $result ? $result['numeracion'] : '000001';
    }

    public function listarMensualidadesActivas($matricula_id)
    {
        $sql = "CALL sp_pivot_matricula_mes_cobros($matricula_id)";
        return ejecutarConsulta($sql);
    }

    public function validarContraseña($contraseña)
    {
        $sql = "SELECT COUNT(*) AS total FROM institucion_validacion WHERE nombre = '$contraseña' AND estado = '1'";
        $resultado = ejecutarConsultaSimpleFila($sql);
        return $resultado['total'] > 0;
    }

    public function eliminar($id_matricula_detalle)
    {
        // Obtener los IDs de usuario_apoderado y usuario_alumno desde matricula_detalle
        $sql_obtener_ids = "SELECT id_usuario_apoderado, id_usuario_alumno FROM matricula_detalle WHERE id = '$id_matricula_detalle'";
        $resultado = ejecutarConsultaSimpleFila($sql_obtener_ids);
        $id_apoderado = $resultado['id_usuario_apoderado'];
        $id_alumno = $resultado['id_usuario_alumno'];

        // Eliminar registros relacionados en mensualidad_detalle
        $sql_mensualidad_detalle = "DELETE FROM mensualidad_detalle WHERE id_matricula_detalle = '$id_matricula_detalle'";
        ejecutarConsulta($sql_mensualidad_detalle);

        // Eliminar registros relacionados en matricula_pago
        $sql_matricula_pago = "DELETE FROM matricula_pago WHERE id_matricula_detalle = '$id_matricula_detalle'";
        ejecutarConsulta($sql_matricula_pago);

        // Eliminar registro en matricula_detalle
        $sql_matricula_detalle = "DELETE FROM matricula_detalle WHERE id = '$id_matricula_detalle'";
        ejecutarConsulta($sql_matricula_detalle);

        // Eliminar registros relacionados en usuario_alumno
        $sql_alumno = "DELETE FROM usuario_alumno WHERE id = '$id_alumno'";
        ejecutarConsulta($sql_alumno);

        // Eliminar registros relacionados en usuario_apoderado
        $sql_apoderado = "DELETE FROM usuario_apoderado WHERE id = '$id_apoderado'";
        ejecutarConsulta($sql_apoderado);

        return true;
    }

    public function desactivar($id_matricula_detalle)
    {
        // Obtener los IDs de usuario_apoderado y usuario_alumno desde matricula_detalle
        $sql_obtener_ids = "SELECT id_usuario_apoderado, id_usuario_alumno FROM matricula_detalle WHERE id = '$id_matricula_detalle'";
        $resultado = ejecutarConsultaSimpleFila($sql_obtener_ids);

        $id_apoderado = $resultado['id_usuario_apoderado'];
        $id_alumno = $resultado['id_usuario_alumno'];

        // Desactivar registros relacionados en mensualidad_detalle
        // $sql_mensualidad_detalle = "UPDATE mensualidad_detalle SET estado = 0 WHERE id_matricula_detalle = '$id_matricula_detalle'";
        // ejecutarConsulta($sql_mensualidad_detalle);

        // Desactivar registros relacionados en matricula_pago
        $sql_matricula_pago = "UPDATE matricula_pago SET estado = 0 WHERE id_matricula_detalle = '$id_matricula_detalle'";
        ejecutarConsulta($sql_matricula_pago);

        // Desactivar registro en matricula_detalle
        $sql_matricula_detalle = "UPDATE matricula_detalle SET estado = 0 WHERE id = '$id_matricula_detalle'";
        ejecutarConsulta($sql_matricula_detalle);

        // Desactivar usuario_alumno
        if (!empty($id_alumno)) {
            $sql_alumno = "UPDATE usuario_alumno SET estado = 0 WHERE id = '$id_alumno'";
            ejecutarConsulta($sql_alumno);
        }

        // Desactivar usuario_apoderado
        if (!empty($id_apoderado)) {
            $sql_apoderado = "UPDATE usuario_apoderado SET estado = 0 WHERE id = '$id_apoderado'";
            ejecutarConsulta($sql_apoderado);
        }

        return true;
    }


    public function listarApoderadosReferidosActivos()
    {
        $sql = "SELECT 
                    ua.*,
                    uat.nombre AS tipo_apoderado,
                    ud.nombre AS tipo_documento,
                    us.nombre AS sexo,
                    uec.nombre AS estado_civil,
                    COUNT(md.id_usuario_apoderado_referido) AS repeticiones
                FROM usuario_apoderado ua
                INNER JOIN usuario_apoderado_tipo uat ON ua.id_apoderado_tipo = uat.id
                INNER JOIN usuario_documento ud ON ua.id_documento = ud.id
                INNER JOIN usuario_sexo us ON ua.id_sexo = us.id
                INNER JOIN usuario_estado_civil uec ON ua.id_estado_civil = uec.id
                LEFT JOIN matricula_detalle md ON ua.id = md.id_usuario_apoderado_referido
                WHERE ua.estado = '1'
                GROUP BY ua.id
                ORDER BY COUNT(md.id_usuario_apoderado_referido) DESC, ua.id ASC";
        return ejecutarConsulta($sql);
    }
}
