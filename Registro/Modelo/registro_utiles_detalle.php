<?php
require_once("../../database.php");

class Registroutil
{
    public function __construct() {}

    // Guarda/Actualiza detalle de útiles por alumno (matricula_detalle)
    // $utiles: array( registro_utiles_id => ['stock' => x, 'observaciones' => '...'] )
    public function guardarUtilesDetalle($matricula_detalle_id, $utiles)
    {
        try {
            $matricula_detalle_id = limpiarcadena($matricula_detalle_id);

            foreach ($utiles as $registro_utiles_id => $detalle) {
                $registro_utiles_id = limpiarcadena($registro_utiles_id);

                $stock = isset($detalle['stock']) ? limpiarcadena($detalle['stock']) : 0;
                $observaciones = isset($detalle['observaciones']) ? limpiarcadena($detalle['observaciones']) : '';

                // Normalizar stock
                if ($stock === '' || $stock === null) $stock = 0;

                // Verificar si ya existe el registro
                $sql_verificar = "SELECT COUNT(*) as count FROM registro_utiles_detalle WHERE id_matricula_detalle = '$matricula_detalle_id' AND id_registro_utiles = '$registro_utiles_id'";
                $resultado = ejecutarConsultaSimpleFila($sql_verificar);

                if (isset($resultado['count']) && (int)$resultado['count'] > 0) {
                    // Actualizar
                    $sql_actualizar = "UPDATE registro_utiles_detalle SET stock = '$stock', observaciones = '$observaciones' WHERE id_matricula_detalle = '$matricula_detalle_id' AND id_registro_utiles = '$registro_utiles_id'";
                    ejecutarConsulta($sql_actualizar);
                } else {
                    // Insertar
                    $sql_insertar = "INSERT INTO registro_utiles_detalle (id_matricula_detalle, id_registro_utiles, stock, observaciones) VALUES ('$matricula_detalle_id', '$registro_utiles_id', '$stock', '$observaciones')";
                    ejecutarConsulta($sql_insertar);
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // LISTADO PRINCIPAL (alumnos matriculados)
    public function listar()
    {
        $sql = "SELECT
                md.id AS id_matricula_detalle,
                il.nombre AS lectivo,
                iniv.nombre AS nivel,
                igr.nombre AS grado,
                isec.nombre AS seccion,
                ua.nombreyapellido AS apoderado_nombre,
                ual.nombreyapellido AS alumno_nombre,
                mc.nombre AS categoria_matricula
                FROM matricula_detalle md
                INNER JOIN matricula m ON md.id_matricula = m.id AND m.estado = 1
                INNER JOIN institucion_seccion isec ON m.id_institucion_seccion = isec.id AND isec.estado = 1
                INNER JOIN institucion_grado igr ON isec.id_institucion_grado = igr.id AND igr.estado = 1
                INNER JOIN institucion_nivel iniv ON igr.id_institucion_nivel = iniv.id AND iniv.estado = 1
                INNER JOIN institucion_lectivo il ON iniv.id_institucion_lectivo = il.id AND il.estado = 1
                INNER JOIN matricula_categoria mc ON md.id_matricula_categoria = mc.id AND mc.estado = 1
                INNER JOIN usuario_apoderado ua ON md.id_usuario_apoderado = ua.id AND ua.estado = 1
                INNER JOIN usuario_alumno ual ON md.id_usuario_alumno = ual.id AND ual.estado = 1
                WHERE md.estado = 1
                ORDER BY il.nombre ASC, iniv.nombre ASC, igr.nombre ASC, isec.nombre ASC, ual.nombreyapellido ASC";
        return ejecutarConsulta($sql);
    }

    // Lista los útiles disponibles para una matrícula (cabecera: registro_utiles)
    public function listar_registro_utiles_por_matricula($matricula_id)
    {
        $matricula_id = limpiarcadena($matricula_id);

        $sql = "SELECT
                ru.id,
                ru.id_matricula,
                ru.nombre AS nombre_util,
                ru.observaciones,
                ru.fechacreado,
                ru.estado
                FROM registro_utiles ru
                WHERE ru.estado = 1
                AND ru.id_matricula = '$matricula_id'
                ORDER BY ru.id ASC";
        return ejecutarConsulta($sql);
    }

    // Lista detalle de útiles ya registrados para un alumno (registro_utiles_detalle)
    public function listar_registro_utiles_detalle($matricula_detalle_id)
    {
        $matricula_detalle_id = limpiarcadena($matricula_detalle_id);

        $sql = "SELECT
                id,
                id_matricula_detalle,
                id_registro_utiles,
                stock,
                observaciones,
                fechacreado,
                estado
                FROM registro_utiles_detalle
                WHERE id_matricula_detalle = '$matricula_detalle_id'
                AND estado = 1";
        return ejecutarConsulta($sql);
    }

    // Info del alumno + matricula_id (IMPORTANTE para traer los útiles por matrícula)
    public function listar_info_matricula_detalle($id)
    {
        $id = limpiarcadena($id);

        $sql = "SELECT
                md.id AS id_matricula_detalle,
                md.id_matricula AS matricula_id,
                il.nombre AS lectivo,
                iniv.nombre AS nivel,
                igr.nombre AS grado,
                isec.nombre AS seccion,
                ua.nombreyapellido AS apoderado_nombre,
                ua.telefono AS apoderado_telefono,
                ual.nombreyapellido AS alumno_nombre,
                mc.nombre AS categoria_matricula
                FROM matricula_detalle md
                INNER JOIN matricula m ON md.id_matricula = m.id AND m.estado = 1
                INNER JOIN institucion_seccion isec ON m.id_institucion_seccion = isec.id AND isec.estado = 1
                INNER JOIN institucion_grado igr ON isec.id_institucion_grado = igr.id AND igr.estado = 1
                INNER JOIN institucion_nivel iniv ON igr.id_institucion_nivel = iniv.id AND iniv.estado = 1
                INNER JOIN institucion_lectivo il ON iniv.id_institucion_lectivo = il.id AND il.estado = 1
                INNER JOIN matricula_categoria mc ON md.id_matricula_categoria = mc.id AND mc.estado = 1
                INNER JOIN usuario_apoderado ua ON md.id_usuario_apoderado = ua.id AND ua.estado = 1
                INNER JOIN usuario_alumno ual ON md.id_usuario_alumno = ual.id AND ual.estado = 1
                WHERE md.id = '$id'
                AND md.estado = 1
                ORDER BY il.nombre ASC, iniv.nombre ASC, igr.nombre ASC, isec.nombre ASC, ual.nombreyapellido ASC";
        return ejecutarConsultaSimpleFila($sql);
    }
}
