<?php
require_once("../../database.php");

class BibliotecaLibro
{
    public function __construct()
    {
    }

    // Método para guardar o editar múltiples libros a la vez
    public function guardarEditarMasivo($detalles)
    {
        try {
            global $conectar;
            foreach ($detalles as $detalle) {
                $id = isset($detalle['id']) ? limpiarcadena($detalle['id']) : null;
                $codigo = isset($detalle['codigo']) ? limpiarcadena($detalle['codigo']) : null;
                $nombre = isset($detalle['nombre']) ? limpiarcadena($detalle['nombre']) : null;
                $observaciones = isset($detalle['observaciones']) ? limpiarcadena($detalle['observaciones']) : '';

                if ($id) {
                    // Actualizar libro existente
                    $sql = "UPDATE biblioteca_libro SET codigo='$codigo', nombre='$nombre', observaciones='$observaciones' WHERE id='$id'";
                } else {
                    // Insertar nuevo libro
                    $sql = "INSERT INTO biblioteca_libro (codigo, nombre, observaciones, stock, estado) VALUES ('$codigo', '$nombre', '$observaciones', '0', '1')";
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

    // Método para mostrar los detalles de un libro específico
    public function mostrar($id)
    {
        $sql = "SELECT * FROM biblioteca_libro WHERE id='" . limpiarcadena($id) . "'";
        return ejecutarConsultaSimpleFila($sql);
    }

    // Método para listar todos los libros
    public function listar()
    {
        $sql = "SELECT id, codigo, nombre, observaciones, stock, estado FROM biblioteca_libro";
        return ejecutarConsulta($sql);
    }

    // Método para desactivar un libro
    public function desactivar($id)
    {
        $sql = "UPDATE biblioteca_libro SET estado='0' WHERE id='" . limpiarcadena($id) . "'";
        return ejecutarConsulta($sql);
    }

    // Método para activar un libro
    public function activar($id)
    {
        $sql = "UPDATE biblioteca_libro SET estado='1' WHERE id='" . limpiarcadena($id) . "'";
        return ejecutarConsulta($sql);
    }
}
?>
