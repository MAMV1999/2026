<?php
require_once("../../database.php");

class Institucion_estructura
{
    public function __construct() {}

    public function guardarEditarMasivo($institucion, $estructura)
    {
        global $conectar;

        try {
            $conectar->begin_transaction();

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
                ejecutarConsulta($sql);
                $id_institucion = $conectar->insert_id;
            } else {
                $sql = "UPDATE institucion SET 
                            nombre='$nombre',
                            id_usuario_docente='$id_usuario_docente',
                            telefono='$telefono',
                            correo='$correo',
                            ruc='$ruc',
                            razon_social='$razon_social',
                            direccion='$direccion',
                            observaciones='$observaciones',
                            estado='1'
                        WHERE id='$id'";
                ejecutarConsulta($sql);
                $id_institucion = $id;

                $sql = "UPDATE institucion_lectivo 
                        SET estado='0' 
                        WHERE id_institucion='$id_institucion'";
                ejecutarConsulta($sql);

                $sql = "UPDATE institucion_nivel iniv
                        INNER JOIN institucion_lectivo il ON iniv.id_institucion_lectivo = il.id
                        SET iniv.estado='0'
                        WHERE il.id_institucion='$id_institucion'";
                ejecutarConsulta($sql);

                $sql = "UPDATE institucion_grado ig
                        INNER JOIN institucion_nivel iniv ON ig.id_institucion_nivel = iniv.id
                        INNER JOIN institucion_lectivo il ON iniv.id_institucion_lectivo = il.id
                        SET ig.estado='0'
                        WHERE il.id_institucion='$id_institucion'";
                ejecutarConsulta($sql);

                $sql = "UPDATE institucion_seccion isec
                        INNER JOIN institucion_grado ig ON isec.id_institucion_grado = ig.id
                        INNER JOIN institucion_nivel iniv ON ig.id_institucion_nivel = iniv.id
                        INNER JOIN institucion_lectivo il ON iniv.id_institucion_lectivo = il.id
                        SET isec.estado='0'
                        WHERE il.id_institucion='$id_institucion'";
                ejecutarConsulta($sql);
            }

            foreach ($estructura as $lectivo) {
                $lectivo_id = isset($lectivo['id']) ? $lectivo['id'] : "";
                $lectivo_nombre = isset($lectivo['nombre']) ? $lectivo['nombre'] : "";

                if (trim($lectivo_nombre) == "") {
                    continue;
                }

                if (empty($lectivo_id)) {
                    $sql = "INSERT INTO institucion_lectivo 
                            (nombre, nombre_lectivo, id_institucion, observaciones, estado) 
                            VALUES 
                            ('$lectivo_nombre', '$lectivo_nombre', '$id_institucion', '', '1')";
                    ejecutarConsulta($sql);
                    $lectivo_id = $conectar->insert_id;
                } else {
                    $sql = "UPDATE institucion_lectivo SET 
                                nombre='$lectivo_nombre',
                                nombre_lectivo='$lectivo_nombre',
                                id_institucion='$id_institucion',
                                estado='1'
                            WHERE id='$lectivo_id'";
                    ejecutarConsulta($sql);
                }

                foreach ($lectivo['niveles'] as $nivel) {
                    $nivel_id = isset($nivel['id']) ? $nivel['id'] : "";
                    $nivel_nombre = isset($nivel['nombre']) ? $nivel['nombre'] : "";

                    if (trim($nivel_nombre) == "") {
                        continue;
                    }

                    if (empty($nivel_id)) {
                        $sql = "INSERT INTO institucion_nivel 
                                (nombre, id_institucion_lectivo, observaciones, estado) 
                                VALUES 
                                ('$nivel_nombre', '$lectivo_id', '', '1')";
                        ejecutarConsulta($sql);
                        $nivel_id = $conectar->insert_id;
                    } else {
                        $sql = "UPDATE institucion_nivel SET 
                                    nombre='$nivel_nombre',
                                    id_institucion_lectivo='$lectivo_id',
                                    estado='1'
                                WHERE id='$nivel_id'";
                        ejecutarConsulta($sql);
                    }

                    foreach ($nivel['grados'] as $grado) {
                        $grado_id = isset($grado['id']) ? $grado['id'] : "";
                        $grado_nombre = isset($grado['nombre']) ? $grado['nombre'] : "";

                        if (trim($grado_nombre) == "") {
                            continue;
                        }

                        if (empty($grado_id)) {
                            $sql = "INSERT INTO institucion_grado 
                                    (nombre, id_institucion_nivel, observaciones, estado) 
                                    VALUES 
                                    ('$grado_nombre', '$nivel_id', '', '1')";
                            ejecutarConsulta($sql);
                            $grado_id = $conectar->insert_id;
                        } else {
                            $sql = "UPDATE institucion_grado SET 
                                        nombre='$grado_nombre',
                                        id_institucion_nivel='$nivel_id',
                                        estado='1'
                                    WHERE id='$grado_id'";
                            ejecutarConsulta($sql);
                        }

                        foreach ($grado['secciones'] as $seccion) {
                            $seccion_id = isset($seccion['id']) ? $seccion['id'] : "";
                            $seccion_nombre = isset($seccion['nombre']) ? $seccion['nombre'] : "";

                            if (trim($seccion_nombre) == "") {
                                continue;
                            }

                            if (empty($seccion_id)) {
                                $sql = "INSERT INTO institucion_seccion 
                                        (nombre, id_institucion_grado, observaciones, estado) 
                                        VALUES 
                                        ('$seccion_nombre', '$grado_id', '', '1')";
                                ejecutarConsulta($sql);
                            } else {
                                $sql = "UPDATE institucion_seccion SET 
                                            nombre='$seccion_nombre',
                                            id_institucion_grado='$grado_id',
                                            estado='1'
                                        WHERE id='$seccion_id'";
                                ejecutarConsulta($sql);
                            }
                        }
                    }
                }
            }

            $conectar->commit();
            return true;

        } catch (Exception $e) {
            $conectar->rollback();
            return false;
        }
    }

    public function listar()
    {
        $sql = "SELECT 
                    i.id,
                    i.nombre,
                    ud.nombreyapellido AS director,
                    i.telefono,
                    i.correo,
                    i.ruc,
                    i.razon_social,
                    i.estado
                FROM institucion i
                LEFT JOIN usuario_docente ud ON i.id_usuario_docente = ud.id";
        return ejecutarConsulta($sql);
    }

    public function mostrar($id)
    {
        $sql = "SELECT * FROM institucion WHERE id='$id'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar_estructura($id)
    {
        $sql = "SELECT 
                    il.id AS lectivo_id,
                    il.nombre AS lectivo_nombre,
                    iniv.id AS nivel_id,
                    iniv.nombre AS nivel_nombre,
                    ig.id AS grado_id,
                    ig.nombre AS grado_nombre,
                    isec.id AS seccion_id,
                    isec.nombre AS seccion_nombre
                FROM institucion_lectivo il
                LEFT JOIN institucion_nivel iniv ON iniv.id_institucion_lectivo = il.id AND iniv.estado = '1'
                LEFT JOIN institucion_grado ig ON ig.id_institucion_nivel = iniv.id AND ig.estado = '1'
                LEFT JOIN institucion_seccion isec ON isec.id_institucion_grado = ig.id AND isec.estado = '1'
                WHERE il.id_institucion = '$id'
                AND il.estado = '1'
                ORDER BY il.id ASC, iniv.id ASC, ig.id ASC, isec.id ASC";
        return ejecutarConsulta($sql);
    }

    public function desactivar($id)
    {
        $sql = "UPDATE institucion SET estado='0' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    public function activar($id)
    {
        $sql = "UPDATE institucion SET estado='1' WHERE id='$id'";
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