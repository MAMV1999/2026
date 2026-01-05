<?php
require_once("../../database.php");

class Matricula_pago
{
    public function __construct() {}

    // Método para guardar un nuevo registro de pago de matrícula
    public function guardar($id_matricula_detalle, $numeracion, $fecha, $descripcion, $monto, $id_matricula_metodo_pago, $observaciones)
    {
        $sql = "INSERT INTO matricula_pago (id_matricula_detalle, numeracion, fecha, descripcion, monto, id_matricula_metodo_pago, observaciones) VALUES ('$id_matricula_detalle', '$numeracion', '$fecha', '$descripcion', '$monto', '$id_matricula_metodo_pago', '$observaciones')";
        return ejecutarConsulta($sql);
    }

    // Método para editar un registro de pago de matrícula existente
    public function editar($id, $id_matricula_detalle, $numeracion, $fecha, $descripcion, $monto, $id_matricula_metodo_pago, $observaciones)
    {
        $sql = "UPDATE matricula_pago SET id_matricula_detalle='$id_matricula_detalle', numeracion='$numeracion', fecha='$fecha', descripcion='$descripcion', monto='$monto', id_matricula_metodo_pago='$id_matricula_metodo_pago', observaciones='$observaciones' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para mostrar los detalles de un pago específico
    public function mostrar($id)
    {
        $sql = "SELECT * FROM matricula_pago WHERE id='$id'";
        return ejecutarConsultaSimpleFila($sql);
    }

    // Método para listar todos los pagos de matrícula
    public function listar()
    {
        $sql = "SELECT 
                    mp.id, 
                    mp.numeracion, 
                    DATE_FORMAT(mp.fecha, '%d/%m/%Y') AS fecha, 
                    mp.descripcion, 
                    mp.monto, 
                    mm.nombre AS metodo_pago, 
                    ual.nombreyapellido AS alumno, 
                    uap.nombreyapellido AS apoderado,
                    il.nombre AS lectivo, 
                    iniv.nombre AS nivel, 
                    ig.nombre AS grado, 
                    isec.nombre AS seccion,
                    mp.estado, 
                    DATE_FORMAT(mp.fechacreado, '%d/%m/%Y') AS fechacreado
                FROM matricula_pago mp
                LEFT JOIN matricula_metodo_pago mm ON mp.id_matricula_metodo_pago = mm.id
                LEFT JOIN matricula_detalle md ON mp.id_matricula_detalle = md.id
                LEFT JOIN usuario_alumno ual ON md.id_usuario_alumno = ual.id
                LEFT JOIN usuario_apoderado uap ON md.id_usuario_apoderado = uap.id
                LEFT JOIN institucion_seccion isec ON md.id_matricula = isec.id
                LEFT JOIN institucion_grado ig ON isec.id_institucion_grado = ig.id
                LEFT JOIN institucion_nivel iniv ON ig.id_institucion_nivel = iniv.id
                LEFT JOIN institucion_lectivo il ON iniv.id_institucion_lectivo = il.id
                WHERE mp.estado = '1'";
        return ejecutarConsulta($sql);
    }



    // Método para desactivar un registro de pago
    public function desactivar($id)
    {
        $sql = "UPDATE matricula_pago SET estado='0' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para activar un registro de pago
    public function activar($id)
    {
        $sql = "UPDATE matricula_pago SET estado='1' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para listar los métodos de pago activos
    public function listarMetodosPagoActivos()
    {
        $sql = "SELECT id, nombre FROM matricula_metodo_pago WHERE estado = '1'";
        return ejecutarConsulta($sql);
    }

    // Método para listar los detalles de matrícula activos
    public function listarMatriculaDetallesActivos()
    {
        $sql = "SELECT 
                    md.id, 
                    il.nombre AS lectivo, 
                    iniv.nombre AS nivel, 
                    ig.nombre AS grado, 
                    isec.nombre AS seccion, 
                    uap.nombreyapellido AS apoderado, 
                    ual.nombreyapellido AS alumno
                FROM matricula_detalle md
                LEFT JOIN usuario_apoderado uap ON md.id_usuario_apoderado = uap.id
                LEFT JOIN usuario_alumno ual ON md.id_usuario_alumno = ual.id
                LEFT JOIN institucion_seccion isec ON md.id_matricula = isec.id
                LEFT JOIN institucion_grado ig ON isec.id_institucion_grado = ig.id
                LEFT JOIN institucion_nivel iniv ON ig.id_institucion_nivel = iniv.id
                LEFT JOIN institucion_lectivo il ON iniv.id_institucion_lectivo = il.id
                WHERE md.estado = '1'";
        return ejecutarConsulta($sql);
    }
}
