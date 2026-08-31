<?php
require_once("../../database.php");

class Institucion_estructura
{
    public function __construct() {}

    public function guardarEditarMasivo($institucion, $detalles)
    {
        $id = isset($institucion['id']) ? $institucion['id'] : "";
        $nombre = isset($institucion['nombre']) ? $institucion['nombre'] : "";
        $id_usuario_docente = isset($institucion['id_usuario_docente']) ? $institucion['id_usuario_docente'] : "";
        $telefono = isset($institucion['telefono']) ? $institucion['telefono'] : "";
        $correo = isset($institucion['correo']) ? $institucion['correo'] : "";
        $ruc = isset($institucion['ruc']) ? $institucion['ruc'] : "";
        $razon_social = isset($institucion['razon_social']) ? $institucion['razon_social'] : "";
        $direccion = isset($institucion['direccion']) ? $institucion['direccion'] : "";
        $observaciones = isset($institucion['observaciones']) ? $institucion['observaciones'] : "";

        if (empty($id)) {
            $sql = "INSERT INTO institucion 
                    (nombre, id_usuario_docente, telefono, correo, ruc, razon_social, direccion, observaciones) 
                    VALUES 
                    ('$nombre', '$id_usuario_docente', '$telefono', '$correo', '$ruc', '$razon_social', '$direccion', '$observaciones')";

            $id = ejecutarConsulta_retornarID($sql);
        } else {
            $sql = "UPDATE institucion SET 
                        nombre='$nombre',
                        id_usuario_docente='$id_usuario_docente',
                        telefono='$telefono',
                        correo='$correo',
                        ruc='$ruc',
                        razon_social='$razon_social',
                        direccion='$direccion',
                        observaciones='$observaciones'
                    WHERE id='$id'";

            if (!ejecutarConsulta($sql)) {
                return false;
            }
        }

        foreach ($detalles as $detalle) {
            $tipo = isset($detalle['tipo']) ? $detalle['tipo'] : "";
            $id_detalle = isset($detalle['id']) ? $detalle['id'] : "";
            $nombre_detalle = isset($detalle['nombre']) ? $detalle['nombre'] : "";
            $nombre_lectivo = isset($detalle['nombre_lectivo']) ? $detalle['nombre_lectivo'] : "";

            if ($tipo == "lectivo") {
                $sql = "UPDATE institucion_lectivo SET 
                            nombre='$nombre_detalle',
                            nombre_lectivo='$nombre_lectivo'
                        WHERE id='$id_detalle'";
                ejecutarConsulta($sql);
            }

            if ($tipo == "nivel") {
                $sql = "UPDATE institucion_nivel SET 
                            nombre='$nombre_detalle'
                        WHERE id='$id_detalle'";
                ejecutarConsulta($sql);
            }

            if ($tipo == "grado") {
                $sql = "UPDATE institucion_grado SET 
                            nombre='$nombre_detalle'
                        WHERE id='$id_detalle'";
                ejecutarConsulta($sql);
            }

            if ($tipo == "seccion") {
                $sql = "UPDATE institucion_seccion SET 
                            nombre='$nombre_detalle'
                        WHERE id='$id_detalle'";
                ejecutarConsulta($sql);
            }
        }

        return $id;
    }

    public function listar()
    {
        $sql = "SELECT 
                    i.*,
                    ud.nombreyapellido AS director
                FROM institucion i
                LEFT JOIN usuario_docente ud ON i.id_usuario_docente = ud.id
                ORDER BY i.id DESC";
        return ejecutarConsulta($sql);
    }

    public function mostrar($id)
    {
        $sql = "SELECT * FROM institucion WHERE id='$id'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listarEstructura($id_institucion)
    {
        $sql = "SELECT 
                    il.id AS lectivo_id,
                    il.nombre AS lectivo_nombre,
                    il.nombre_lectivo AS lectivo_nombre_lectivo,
                    il.estado AS lectivo_estado,

                    iniv.id AS nivel_id,
                    iniv.nombre AS nivel_nombre,
                    iniv.estado AS nivel_estado,

                    ig.id AS grado_id,
                    ig.nombre AS grado_nombre,
                    ig.estado AS grado_estado,

                    isec.id AS seccion_id,
                    isec.nombre AS seccion_nombre,
                    isec.estado AS seccion_estado

                FROM institucion_lectivo il
                LEFT JOIN institucion_nivel iniv ON iniv.id_institucion_lectivo = il.id
                LEFT JOIN institucion_grado ig ON ig.id_institucion_nivel = iniv.id
                LEFT JOIN institucion_seccion isec ON isec.id_institucion_grado = ig.id
                WHERE il.id_institucion = '$id_institucion'
                ORDER BY il.id ASC, iniv.id ASC, ig.id ASC, isec.id ASC";
        return ejecutarConsulta($sql);
    }

    public function agregarLectivo($id_institucion, $nombre, $nombre_lectivo)
    {
        $sql = "INSERT INTO institucion_lectivo 
                (nombre, nombre_lectivo, id_institucion) 
                VALUES 
                ('$nombre', '$nombre_lectivo', '$id_institucion')";
        return ejecutarConsulta($sql);
    }

    public function agregarNivel($id_institucion_lectivo, $nombre)
    {
        $sql = "INSERT INTO institucion_nivel 
                (nombre, id_institucion_lectivo) 
                VALUES 
                ('$nombre', '$id_institucion_lectivo')";
        return ejecutarConsulta($sql);
    }

    public function agregarGrado($id_institucion_nivel, $nombre)
    {
        $sql = "INSERT INTO institucion_grado 
                (nombre, id_institucion_nivel) 
                VALUES 
                ('$nombre', '$id_institucion_nivel')";
        return ejecutarConsulta($sql);
    }

    public function agregarSeccion($id_institucion_grado, $nombre)
    {
        $sql = "INSERT INTO institucion_seccion 
                (nombre, id_institucion_grado) 
                VALUES 
                ('$nombre', '$id_institucion_grado')";
        return ejecutarConsulta($sql);
    }

    public function cambiarEstado($tipo, $id, $estado)
    {
        if ($tipo == "institucion") {
            $sql = "UPDATE institucion SET estado='$estado' WHERE id='$id'";
        }

        if ($tipo == "lectivo") {
            $sql = "UPDATE institucion_lectivo SET estado='$estado' WHERE id='$id'";
        }

        if ($tipo == "nivel") {
            $sql = "UPDATE institucion_nivel SET estado='$estado' WHERE id='$id'";
        }

        if ($tipo == "grado") {
            $sql = "UPDATE institucion_grado SET estado='$estado' WHERE id='$id'";
        }

        if ($tipo == "seccion") {
            $sql = "UPDATE institucion_seccion SET estado='$estado' WHERE id='$id'";
        }

        return ejecutarConsulta($sql);
    }

    public function listarDocentesActivos()
    {
        $sql = "SELECT 
                    u.id, 
                    u.nombreyapellido, 
                    c.nombre AS cargo 
                FROM usuario_docente u 
                LEFT JOIN usuario_cargo c ON u.id_cargo = c.id 
                WHERE u.estado = '1'";
        return ejecutarConsulta($sql);
    }
}
?>