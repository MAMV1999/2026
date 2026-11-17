<?php
require_once("../../database.php");

class BibliotecaLibro
{
    public function __construct()
    {
    }

    public function guardarEditarMasivo($detalles)
    {
        try {
            global $conectar;

            foreach ($detalles as $detalle) {
                $id = isset($detalle['id']) ? limpiarcadena($detalle['id']) : null;
                $codigo = isset($detalle['codigo']) ? limpiarcadena($detalle['codigo']) : '';
                $nombre = isset($detalle['nombre']) ? limpiarcadena($detalle['nombre']) : '';
                $stock = isset($detalle['stock']) ? (int)$detalle['stock'] : 0;
                $observaciones = isset($detalle['observaciones']) ? limpiarcadena($detalle['observaciones']) : '';

                if ($stock < 0) {
                    $stock = 0;
                }

                if ($id) {
                    $sql = "UPDATE biblioteca_libro SET codigo='$codigo', nombre='$nombre', stock='$stock', observaciones='$observaciones' WHERE id='$id'";
                } else {
                    $sql = "INSERT INTO biblioteca_libro (codigo, nombre, observaciones, stock, estado) VALUES ('$codigo', '$nombre', '$observaciones', '$stock', '1')";
                }

                if (!ejecutarConsulta($sql)) {
                    error_log("Error en SQL: " . mysqli_error($conectar));
                    return false;
                }
            }
            return true;
        } catch (Exception $e) {
            error_log("Error en guardarEditarMasivo: " . $e->getMessage());
            return false;
        }
    }

    public function mostrar($id)
    {
        $sql = "SELECT * FROM biblioteca_libro WHERE id='" . limpiarcadena($id) . "'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar()
    {
        $sql = "SELECT id, codigo, nombre, observaciones, stock, estado FROM biblioteca_libro ORDER BY nombre ASC, codigo ASC";
        return ejecutarConsulta($sql);
    }

    public function desactivar($id)
    {
        $sql = "UPDATE biblioteca_libro SET estado='0' WHERE id='" . limpiarcadena($id) . "'";
        return ejecutarConsulta($sql);
    }

    public function activar($id)
    {
        $sql = "UPDATE biblioteca_libro SET estado='1' WHERE id='" . limpiarcadena($id) . "'";
        return ejecutarConsulta($sql);
    }
}
?>