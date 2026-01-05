<?php
require_once("../../database.php");

class Institucion
{
    public function __construct()
    {
    }

    // Método para guardar una nueva institución
    public function guardar($nombre, $id_usuario_docente, $telefono, $correo, $ruc, $razon_social, $direccion, $observaciones)
    {
        $sql = "INSERT INTO institucion (nombre, id_usuario_docente, telefono, correo, ruc, razon_social, direccion, observaciones) VALUES ('$nombre', '$id_usuario_docente', '$telefono', '$correo', '$ruc', '$razon_social', '$direccion', '$observaciones')";
        return ejecutarConsulta($sql);
    }

    // Método para editar una institución existente
    public function editar($id, $nombre, $id_usuario_docente, $telefono, $correo, $ruc, $razon_social, $direccion, $observaciones)
    {
        $sql = "UPDATE institucion SET nombre='$nombre', id_usuario_docente='$id_usuario_docente', telefono='$telefono', correo='$correo', ruc='$ruc', razon_social='$razon_social', direccion='$direccion', observaciones='$observaciones' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para mostrar los detalles de una institución específica
    public function mostrar($id)
    {
        $sql = "SELECT * FROM institucion WHERE id='$id'";
        return ejecutarConsultaSimpleFila($sql);
    }

    // Método para listar todas las instituciones
    public function listar()
    {
        $sql = "SELECT i.id, i.nombre, u.nombreyapellido AS usuario_docente, i.telefono, i.correo, i.ruc, i.razon_social, i.direccion, i.observaciones, i.estado, i.fechacreado FROM institucion i LEFT JOIN usuario_docente u ON i.id_usuario_docente = u.id";
        return ejecutarConsulta($sql);
    }

    // Método para desactivar una institución
    public function desactivar($id)
    {
        $sql = "UPDATE institucion SET estado='0' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para activar una institución
    public function activar($id)
    {
        $sql = "UPDATE institucion SET estado='1' WHERE id='$id'";
        return ejecutarConsulta($sql);
    }

    // Método para listar los docentes activos que no están asignados a una institución activa
    public function listarDocentesActivos()
    {
        $sql = "SELECT u.id, u.nombreyapellido, c.nombre AS cargo FROM usuario_docente u LEFT JOIN usuario_cargo c ON u.id_cargo = c.id WHERE u.estado = '1'";
        return ejecutarConsulta($sql);
    }
}
?>
