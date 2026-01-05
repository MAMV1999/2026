<?php
require_once("../../database.php");

class Matricula
{
    public function __construct() {}

    // Método para guardar una nueva matrícula
    public function guardar($id_institucion_seccion, $id_usuario_docente, $aforo, $observaciones, $montos)
    {
        try {
            $id_institucion_seccion = limpiarcadena($id_institucion_seccion);
            $id_usuario_docente     = limpiarcadena($id_usuario_docente);
            $aforo                  = limpiarcadena($aforo);
            $observaciones          = limpiarcadena($observaciones);

            $sqlMatricula = "INSERT INTO matricula (id_institucion_seccion, id_usuario_docente, aforo, observaciones) VALUES ('$id_institucion_seccion', '$id_usuario_docente', '$aforo', '$observaciones')";
            $matricula_id = ejecutarConsulta_retornarID($sqlMatricula);

            foreach ($montos as $item) {
                $matricula_cobro_id   = limpiarcadena($item['matricula_cobro_id']);
                $monto                = limpiarcadena($item['monto']);
                $observaciones_monto  = limpiarcadena($item['observaciones']);

                $sqlDetalle = "INSERT INTO matricula_monto (matricula_id, matricula_cobro_id, monto, observaciones) VALUES ('$matricula_id', '$matricula_cobro_id', '$monto', '$observaciones_monto')";
                ejecutarConsulta($sqlDetalle);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // Método para editar matrícula + montos (estrategia: UPDATE cabecera + DELETE/INSERT montos)
    public function editar($id, $id_institucion_seccion, $id_usuario_docente, $aforo, $observaciones, $montos)
    {
        try {
            $id                   = limpiarcadena($id);
            $id_institucion_seccion = limpiarcadena($id_institucion_seccion);
            $id_usuario_docente     = limpiarcadena($id_usuario_docente);
            $aforo                  = limpiarcadena($aforo);
            $observaciones          = limpiarcadena($observaciones);

            // 1) Actualizar cabecera
            $sqlUpdate = "UPDATE matricula
                          SET id_institucion_seccion = '$id_institucion_seccion',
                              id_usuario_docente     = '$id_usuario_docente',
                              aforo                  = '$aforo',
                              observaciones          = '$observaciones'
                          WHERE id = '$id'";
            ejecutarConsulta($sqlUpdate);

            // 2) Eliminar montos existentes de esa matrícula (solo estado=1 si deseas conservar históricos)
            // Si quieres borrar todos sin importar estado, deja sin condición de estado.
            $sqlDelete = "DELETE FROM matricula_monto WHERE matricula_id = '$id'";
            ejecutarConsulta($sqlDelete);

            // 3) Insertar nuevamente
            foreach ($montos as $item) {
                $matricula_cobro_id   = limpiarcadena($item['matricula_cobro_id']);
                $monto                = limpiarcadena($item['monto']);
                $observaciones_monto  = limpiarcadena($item['observaciones']);

                $sqlDetalle = "INSERT INTO matricula_monto (matricula_id, matricula_cobro_id, monto, observaciones) VALUES ('$id', '$matricula_cobro_id', '$monto', '$observaciones_monto')";
                ejecutarConsulta($sqlDetalle);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // Método para listar todas las matrículas
    public function listar()
    {
        $sql = "SELECT 
                m.id, 
                l.nombre AS nombre_lectivo,
                n.nombre AS nombre_nivel,
                g.nombre AS nombre_grado,
                isec.nombre AS nombre_seccion,
                u.nombreyapellido AS docente_nombre,
                m.aforo, 
                m.observaciones, 
                m.estado, 
                m.fechacreado
            FROM matricula m
            LEFT JOIN institucion_seccion isec ON m.id_institucion_seccion = isec.id
            LEFT JOIN institucion_grado g ON isec.id_institucion_grado = g.id
            LEFT JOIN institucion_nivel n ON g.id_institucion_nivel = n.id
            LEFT JOIN institucion_lectivo l ON n.id_institucion_lectivo = l.id
            LEFT JOIN usuario_docente u ON m.id_usuario_docente = u.id";
        return ejecutarConsulta($sql);
    }

    public function listarSeccionesActivas()
    {
        $sql = "SELECT 
                    s.id AS id_seccion,
                    s.nombre AS nombre_seccion,
                    g.nombre AS nombre_grado,
                    n.nombre AS nombre_nivel,
                    l.nombre AS nombre_lectivo
                FROM institucion_seccion s
                INNER JOIN institucion_grado g ON s.id_institucion_grado = g.id
                INNER JOIN institucion_nivel n ON g.id_institucion_nivel = n.id
                INNER JOIN institucion_lectivo l ON n.id_institucion_lectivo = l.id
                WHERE s.estado = '1' 
                  AND g.estado = '1' 
                  AND n.estado = '1' 
                  AND l.estado = '1'
                  AND s.id NOT IN ( SELECT id_institucion_seccion FROM matricula WHERE estado = '1')";
        return ejecutarConsulta($sql);
    }

    // Método para listar docentes activos con su cargo
    public function listarDocentesActivos()
    {
        $sql = "SELECT 
                    u.id AS id_docente, 
                    u.nombreyapellido AS nombre_docente, 
                    c.nombre AS nombre_cargo
                FROM usuario_docente u
                LEFT JOIN usuario_cargo c ON u.id_cargo = c.id
                WHERE u.estado = '1'";
        return ejecutarConsulta($sql);
    }

    public function listar_matricula_cobro_activos()
    {
        $sql = "SELECT id, nombre, observaciones, fechacreado, estado FROM matricula_cobro WHERE estado = 1 ORDER BY id ASC";
        return ejecutarConsulta($sql);
    }

    // NUEVO: listar cobros activos, pero trayendo el monto guardado si existe para la matrícula (para EDITAR)
    public function listar_matricula_cobro_activos_con_montos($matricula_id)
    {
        $matricula_id = limpiarcadena($matricula_id);

        $sql = "SELECT
                    mc.id,
                    mc.nombre,
                    mc.observaciones,
                    mc.fechacreado,
                    mc.estado,
                    IFNULL(mm.monto, '') AS monto,
                    IFNULL(mm.observaciones, '') AS monto_observaciones
                FROM matricula_cobro mc
                LEFT JOIN matricula_monto mm ON mm.matricula_cobro_id = mc.id AND mm.matricula_id = '$matricula_id'
                WHERE mc.estado = 1
                ORDER BY mc.id ASC";
        return ejecutarConsulta($sql);
    }

    // Método para activar una matrícula
    public function activar($id)
    {
        $sql = "UPDATE matricula SET estado='1' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para desactivar una matrícula
    public function desactivar($id)
    {
        $sql = "UPDATE matricula SET estado='0' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para eliminar una matrícula
    public function eliminar($id)
    {
        $sql = "DELETE FROM matricula WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // NUEVO: obtener para llenar el formulario (EDITAR)
    public function mostrar($id)
    {
        $id = limpiarcadena($id);
        $sql = "SELECT id, id_institucion_seccion, id_usuario_docente, aforo, observaciones FROM matricula WHERE id = '$id'";
        return ejecutarConsultaSimpleFila($sql);
    }

}
