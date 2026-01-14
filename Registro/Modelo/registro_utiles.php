<?php
require_once("../../database.php");

class Utiles_escolares
{
    public function __construct() {}

    // LISTADO DE MATRÍCULAS (como lo tienes)
    public function listar_matriculas()
    {
        $sql = "SELECT
                    m.id,
                    il.nombre AS lectivo,
                    iniv.nombre AS nivel,
                    ig.nombre AS grado,
                    isec.nombre AS seccion,
                    ud.nombreyapellido AS docente,
                    m.aforo,
                    m.observaciones,
                    m.fechacreado,
                    m.estado
                FROM matricula m
                INNER JOIN institucion_seccion isec ON isec.id = m.id_institucion_seccion
                INNER JOIN institucion_grado ig ON ig.id = isec.id_institucion_grado
                INNER JOIN institucion_nivel iniv ON iniv.id = ig.id_institucion_nivel
                INNER JOIN institucion_lectivo il ON il.id = iniv.id_institucion_lectivo
                INNER JOIN institucion i ON i.id = il.id_institucion
                INNER JOIN usuario_docente ud ON ud.id = m.id_usuario_docente
                WHERE m.estado = 1
                AND isec.estado = 1 AND ig.estado = 1 AND iniv.estado = 1 AND il.estado = 1 AND i.estado = 1 AND ud.estado = 1
                ORDER BY m.id ASC";
        return ejecutarConsulta($sql);
    }

    // OBTENER DATOS DE LA MATRÍCULA (para título del formulario)
    public function mostrar_matricula($id_matricula)
    {
        $id_matricula = limpiarcadena($id_matricula);

        $sql = "SELECT
                    m.id,
                    il.nombre AS lectivo,
                    iniv.nombre AS nivel,
                    ig.nombre AS grado,
                    isec.nombre AS seccion,
                    ud.nombreyapellido AS docente
                FROM matricula m
                INNER JOIN institucion_seccion isec ON isec.id = m.id_institucion_seccion
                INNER JOIN institucion_grado ig ON ig.id = isec.id_institucion_grado
                INNER JOIN institucion_nivel iniv ON iniv.id = ig.id_institucion_nivel
                INNER JOIN institucion_lectivo il ON il.id = iniv.id_institucion_lectivo
                INNER JOIN institucion i ON i.id = il.id_institucion
                INNER JOIN usuario_docente ud ON ud.id = m.id_usuario_docente
                WHERE m.id = '$id_matricula'
                AND m.estado = 1
                AND isec.estado = 1 AND ig.estado = 1 AND iniv.estado = 1 AND il.estado = 1 AND i.estado = 1 AND ud.estado = 1
                LIMIT 1";

        return ejecutarConsultaSimpleFila($sql);
    }

    // LISTAR ÚTILES POR MATRÍCULA
    public function listar_utiles_por_matricula($id_matricula)
    {
        $id_matricula = limpiarcadena($id_matricula);

        // Si deseas incluir también desactivados, quita "AND estado = 1"
        $sql = "SELECT id, id_matricula, nombre, observaciones, fechacreado, estado FROM registro_utiles WHERE id_matricula = '$id_matricula' AND estado = 1 ORDER BY id ASC";
        return ejecutarConsulta($sql);
    }

    // GUARDAR / EDITAR MASIVO (por matrícula)
    public function guardarEditarMasivo($id_matricula, $detalles)
    {
        try {
            global $conectar;

            $id_matricula = limpiarcadena($id_matricula);

            foreach ($detalles as $detalle) {

                $id = isset($detalle['id']) ? limpiarcadena($detalle['id']) : null;
                $nombre = isset($detalle['nombre']) ? limpiarcadena($detalle['nombre']) : null;
                $observaciones = isset($detalle['observaciones']) ? limpiarcadena($detalle['observaciones']) : '';

                if ($nombre == null || $nombre === '') {
                    // Si viene una fila vacía, simplemente la ignoramos
                    continue;
                }

                if ($id) {
                    // Actualizar registro existente (validando que pertenezca a la matrícula)
                    $sql = "UPDATE registro_utiles SET nombre='$nombre', observaciones='$observaciones' WHERE id='$id' AND id_matricula='$id_matricula'";
                } else {
                    // Insertar nuevo registro
                    $sql = "INSERT INTO registro_utiles (id_matricula, nombre, observaciones, estado) VALUES ('$id_matricula', '$nombre', '$observaciones', '1')";
                }

                if (!ejecutarConsulta($sql)) {
                    error_log("Error en SQL: " . mysqli_error($conectar));
                    return false;
                }
            }

            return true;
        } catch (Exception $e) {
            error_log("Error en guardarEditarMasivo (útiles): " . $e->getMessage());
            return false;
        }
    }
}
?>
