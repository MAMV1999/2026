<?php
require_once("../../database.php");

class UsuarioCargo
{
    public function __construct() {}

    public function guardar($nombre, $observaciones, $menus)
    {
        try {
            $nombre = limpiarcadena($nombre);
            $observaciones = limpiarcadena($observaciones);

            // 1) Insertar cabecera en usuario_cargo
            $sqlCargo = "INSERT INTO usuario_cargo (nombre, observaciones) VALUES ('$nombre', '$observaciones')";
            $usuario_cargo_id = ejecutarConsulta_retornarID($sqlCargo);

            // 2) Insertar detalle en usuario_cargo_menu (referenciando usuario_menu)
            foreach ($menus as $item) {
                $id_usuario_menu = limpiarcadena($item['id_usuario_menu']);
                $ingreso = limpiarcadena($item['ingreso']);
                $obs_detalle = limpiarcadena($item['observaciones']);

                $sqlDet = "INSERT INTO usuario_cargo_menu (id_usuario_cargo, id_usuario_menu, ingreso, observaciones) VALUES ('$usuario_cargo_id', '$id_usuario_menu', '$ingreso', '$obs_detalle')";
                ejecutarConsulta($sqlDet);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function editar($id, $nombre, $observaciones, $menus)
    {
        try {
            $id = limpiarcadena($id);
            $nombre = limpiarcadena($nombre);
            $observaciones = limpiarcadena($observaciones);

            // 1) Actualizar cabecera
            $sqlUpd = "UPDATE usuario_cargo SET nombre='$nombre', observaciones='$observaciones' WHERE id='$id'";
            ejecutarConsulta($sqlUpd);

            // 2) Reemplazar detalle (eliminar y volver a insertar)
            $sqlDel = "DELETE FROM usuario_cargo_menu WHERE id_usuario_cargo='$id'";
            ejecutarConsulta($sqlDel);

            foreach ($menus as $item) {
                $id_usuario_menu = limpiarcadena($item['id_usuario_menu']);
                $ingreso = limpiarcadena($item['ingreso']);
                $obs_detalle = limpiarcadena($item['observaciones']);

                $sqlDet = "INSERT INTO usuario_cargo_menu (id_usuario_cargo, id_usuario_menu, ingreso, observaciones) VALUES ('$id', '$id_usuario_menu', '$ingreso', '$obs_detalle')";
                ejecutarConsulta($sqlDet);
            }

            return true;
        } catch (Exception $e) { return false; }
    }

    public function mostrar($id)
    {
        $id = limpiarcadena($id);

        // Cabecera
        $sqlCab = "SELECT id, nombre, observaciones, estado FROM usuario_cargo WHERE id='$id'";
        $cabecera = ejecutarConsultaSimpleFila($sqlCab);

        // Detalle (por menu)
        $sqlDet = "SELECT id_usuario_menu, ingreso, observaciones FROM usuario_cargo_menu WHERE id_usuario_cargo='$id'";
        $rsptaDet = ejecutarConsulta($sqlDet);

        $detalle = array();
        while ($reg = $rsptaDet->fetch_object()) {
            $detalle[$reg->id_usuario_menu] = array(
                "ingreso" => $reg->ingreso,
                "observaciones" => $reg->observaciones
            );
        }

        return array(
            "cabecera" => $cabecera,
            "detalle" => $detalle
        );
    }

    public function listar()
    {
        $sql = "SELECT * FROM usuario_cargo";
        return ejecutarConsulta($sql);
    }

    public function listar_usuario_menu_activos()
    {
        $sql = "SELECT
                    um.id,
                    um.nombre,
                    um.icono,
                    um.ruta,
                    um.observaciones,
                    um.estado
                FROM usuario_menu um
                WHERE um.estado = 1
                ORDER BY um.id ASC";
        return ejecutarConsulta($sql);
    }

    public function desactivar($id)
    {
        try {
            $id = limpiarcadena($id);

            // 1) Desactivar cabecera
            $sqlCab = "UPDATE usuario_cargo SET estado='0' WHERE id='$id'";
            ejecutarConsulta($sqlCab);

            // 2) Desactivar detalle
            $sqlDet = "UPDATE usuario_cargo_menu SET estado='0' WHERE id_usuario_cargo='$id'";
            ejecutarConsulta($sqlDet);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function activar($id)
    {
        try {
            $id = limpiarcadena($id);

            // 1) Activar cabecera
            $sqlCab = "UPDATE usuario_cargo SET estado='1' WHERE id='$id'";
            ejecutarConsulta($sqlCab);

            // 2) Activar detalle
            $sqlDet = "UPDATE usuario_cargo_menu SET estado='1' WHERE id_usuario_cargo='$id'";
            ejecutarConsulta($sqlDet);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
