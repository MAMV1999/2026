<?php
require_once("../../database.php");

class Menu
{
    public function __construct() {}

    public function listar($id)
    {
        $sql = "SELECT
                    um.id,
                    um.nombre,
                    um.icono,
                    um.ruta,
                    ucm.ingreso,
                    ucm.observaciones AS observaciones_permiso
                FROM usuario_docente ud
                INNER JOIN usuario_cargo uc ON uc.id = ud.id_cargo
                INNER JOIN usuario_cargo_menu ucm ON ucm.id_usuario_cargo = uc.id
                INNER JOIN usuario_menu um ON um.id = ucm.id_usuario_menu
                WHERE ud.id = '$id'
                AND ud.estado = 1
                AND uc.estado = 1
                AND ucm.estado = 1
                AND um.estado = 1
                AND ucm.ingreso = 1
                ORDER BY um.id ASC";
        return ejecutarConsulta($sql);
    }
}
