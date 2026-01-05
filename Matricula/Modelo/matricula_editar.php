<?php
require_once("../../database.php");

class Matricula_detalle
{
    public function __construct()
    {
    }

    // Método para guardar un nuevo detalle de matrícula
    public function guardar($id_matricula, $id_matricula_categoria, $id_usuario_apoderado, $id_usuario_alumno, $id_usuario_apoderado_referido, $descripcion, $observaciones)
    {
        // IMPORTANTE:
        // Si llega "NULL" (string) desde el controlador, insertar NULL real (sin comillas)
        $sql = "INSERT INTO matricula_detalle (
                    id_matricula,
                    id_matricula_categoria,
                    id_usuario_apoderado,
                    id_usuario_alumno,
                    id_usuario_apoderado_referido,
                    descripcion,
                    observaciones
                ) VALUES (
                    '$id_matricula',
                    '$id_matricula_categoria',
                    '$id_usuario_apoderado',
                    '$id_usuario_alumno',
                    " . ($id_usuario_apoderado_referido === "NULL" ? "NULL" : "'$id_usuario_apoderado_referido'") . ",
                    '$descripcion',
                    '$observaciones'
                )";

        return ejecutarConsulta($sql);
    }

    // Método para editar un detalle de matrícula existente
    public function editar($id, $id_matricula, $id_matricula_categoria, $id_usuario_apoderado, $id_usuario_alumno, $id_usuario_apoderado_referido, $descripcion, $observaciones)
    {
        $sql = "UPDATE matricula_detalle 
                SET
                    id_matricula='$id_matricula',
                    id_matricula_categoria='$id_matricula_categoria',
                    id_usuario_apoderado='$id_usuario_apoderado',
                    id_usuario_alumno='$id_usuario_alumno',
                    id_usuario_apoderado_referido=" . ($id_usuario_apoderado_referido === "NULL" ? "NULL" : "'$id_usuario_apoderado_referido'") . ",
                    descripcion='$descripcion',
                    observaciones='$observaciones' 
                WHERE id='$id'";
        return ejecutarConsulta($sql);
    }    
    
    // Método para mostrar un detalle de matrícula específico
    public function mostrar($id)
    {
        $sql = "SELECT * FROM matricula_detalle WHERE id='$id'";
        return ejecutarConsultaSimpleFila($sql);
    }

    // Método para listar todos los detalles de matrícula
    public function listar()
    {
        // Nota:
        // Mantengo tu estructura, pero agrego filtros de estado = 1 en las relaciones (recomendado).
        // md.estado se deja como viene para que puedas activar/desactivar y seguir listando.
        $sql = "SELECT
                    md.id,
                    il.nombre AS lectivo,
                    iniv.nombre AS nivel,
                    ig.nombre AS grado,
                    isec.nombre AS seccion,
                    mc.nombre AS categoria,
                    ua.nombreyapellido AS apoderado,
                    ual.nombreyapellido AS alumno,
                    md.estado
                FROM matricula_detalle md
                LEFT JOIN matricula m 
                    ON md.id_matricula = m.id
                LEFT JOIN institucion_seccion isec 
                    ON m.id_institucion_seccion = isec.id AND isec.estado = 1
                LEFT JOIN institucion_grado ig 
                    ON isec.id_institucion_grado = ig.id AND ig.estado = 1
                LEFT JOIN institucion_nivel iniv 
                    ON ig.id_institucion_nivel = iniv.id AND iniv.estado = 1
                LEFT JOIN institucion_lectivo il 
                    ON iniv.id_institucion_lectivo = il.id AND il.estado = 1
                LEFT JOIN institucion i 
                    ON il.id_institucion = i.id AND i.estado = 1
                LEFT JOIN matricula_categoria mc 
                    ON md.id_matricula_categoria = mc.id AND mc.estado = 1
                LEFT JOIN usuario_apoderado ua 
                    ON md.id_usuario_apoderado = ua.id AND ua.estado = 1
                LEFT JOIN usuario_alumno ual 
                    ON md.id_usuario_alumno = ual.id AND ual.estado = 1
                LEFT JOIN usuario_apoderado uar 
                    ON md.id_usuario_apoderado_referido = uar.id AND uar.estado = 1
                ORDER BY il.nombre ASC, iniv.nombre ASC, ig.nombre ASC, isec.nombre ASC, mc.nombre ASC, ual.nombreyapellido ASC";
        return ejecutarConsulta($sql);
    }

    // Método para listar las matrículas activas
    public function listarMatriculasActivas()
    {
        // CAMBIO OBLIGATORIO:
        // Tu tabla 'matricula' ya NO tiene: preciomatricula, preciomensualidad, preciomantenimiento
        // Por eso se elimina del SELECT para evitar "Unknown column".
        $sql = "SELECT 
                    m.id,
                    il.nombre AS lectivo,
                    iniv.nombre AS nivel,
                    ig.nombre AS grado,
                    isec.nombre AS seccion,
                    m.aforo,
                    (SELECT COUNT(*) 
                        FROM matricula_detalle 
                        WHERE id_matricula = m.id AND estado = 1
                    ) AS matriculados,
                    m.observaciones
                FROM matricula m
                INNER JOIN institucion_seccion isec ON m.id_institucion_seccion = isec.id
                INNER JOIN institucion_grado ig ON isec.id_institucion_grado = ig.id
                INNER JOIN institucion_nivel iniv ON ig.id_institucion_nivel = iniv.id
                INNER JOIN institucion_lectivo il ON iniv.id_institucion_lectivo = il.id
                WHERE 
                    m.estado = '1'
                    AND isec.estado = '1'
                    AND ig.estado = '1'
                    AND iniv.estado = '1'
                    AND il.estado = '1'";
        return ejecutarConsulta($sql);
    }

    // Método para listar las categorías de matrícula activas
    public function listarCategoriasMatriculaActivas()
    {
        $sql = "SELECT id, nombre FROM matricula_categoria WHERE estado = '1'";
        return ejecutarConsulta($sql);
    }

    // Método para listar los apoderados activos
    public function listarApoderadosActivos()
    {
        $sql = "SELECT id, nombreyapellido FROM usuario_apoderado WHERE estado = '1'";
        return ejecutarConsulta($sql);
    }

    // Método para listar los alumnos activos
    public function listarAlumnosActivos()
    {
        $sql = "SELECT id, nombreyapellido FROM usuario_alumno WHERE estado = '1'";
        return ejecutarConsulta($sql);
    }

    // Método para listar los apoderados referidos activos
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
                WHERE 
                    ua.estado = '1'
                    AND uat.estado = '1'
                    AND ud.estado = '1'
                    AND us.estado = '1'
                    AND uec.estado = '1'
                GROUP BY ua.id
                ORDER BY COUNT(md.id_usuario_apoderado_referido) DESC, ua.id ASC";
        return ejecutarConsulta($sql);
    }

    // Método para desactivar un detalle de matrícula
    public function desactivar($id)
    {
        $sql = "UPDATE matricula_detalle SET estado='0' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para activar un detalle de matrícula
    public function activar($id)
    {
        $sql = "UPDATE matricula_detalle SET estado='1' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }
}
