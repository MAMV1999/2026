<?php
require_once("../../database.php");

class UsuarioMenu
{
    public function __construct()
    {
    }

    // Método para guardar o editar múltiples registros a la vez
    public function guardarEditarMasivo($detalles)
    {
        try {
            global $conectar;

            foreach ($detalles as $detalle) {

                $id = isset($detalle['id']) ? limpiarcadena($detalle['id']) : null;
                $nombre = isset($detalle['nombre']) ? limpiarcadena($detalle['nombre']) : '';
                $icono = isset($detalle['icono']) ? limpiarcadena($detalle['icono']) : '';
                $ruta = isset($detalle['ruta']) ? limpiarcadena($detalle['ruta']) : '';
                $observaciones = isset($detalle['observaciones']) ? limpiarcadena($detalle['observaciones']) : '';

                // Validación mínima
                if ($nombre == '') {
                    continue;
                }

                if ($id) {
                    // Actualizar existente (NO tocamos fechacreado)
                    $sql = "UPDATE usuario_menu 
                            SET nombre='$nombre',
                                icono='$icono',
                                ruta='$ruta',
                                observaciones='$observaciones'
                            WHERE id='$id'";
                } else {
                    // Insertar nuevo (estado por defecto 1; fechacreado lo pone la BD)
                    $sql = "INSERT INTO usuario_menu (nombre, icono, ruta, observaciones, estado) 
                            VALUES ('$nombre', '$icono', '$ruta', '$observaciones', '1')";
                }

                if (!ejecutarConsulta($sql)) {
                    error_log("Error en SQL: " . mysqli_error($conectar));
                    return false;
                }
            }

            return true;

        } catch (Exception $e) {
            error_log("Error en guardarEditarMasivo (UsuarioMenu): " . $e->getMessage());
            return false;
        }
    }

    // Mostrar un registro
    public function mostrar($id)
    {
        $sql = "SELECT * FROM usuario_menu WHERE id='" . limpiarcadena($id) . "'";
        return ejecutarConsultaSimpleFila($sql);
    }

    // Listar todos
    public function listar()
    {
        $sql = "SELECT id, nombre, icono, ruta, observaciones, fechacreado, estado FROM usuario_menu ORDER BY id ASC";
        return ejecutarConsulta($sql);
    }

    // Desactivar
    public function desactivar($id)
    {
        $sql = "UPDATE usuario_menu SET estado='0' WHERE id='" . limpiarcadena($id) . "'";
        return ejecutarConsulta($sql);
    }

    // Activar
    public function activar($id)
    {
        $sql = "UPDATE usuario_menu SET estado='1' WHERE id='" . limpiarcadena($id) . "'";
        return ejecutarConsulta($sql);
    }
}
?>
