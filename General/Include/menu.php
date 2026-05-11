<?php
require_once("../../database.php");

class Menu
{
    public function __construct() {}

    public function listar($id)
    {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        $cargo = isset($_SESSION['docente_cargo']) ? $_SESSION['docente_cargo'] : '';

        $sql = "SELECT
                    um.id,
                    um.nombre,
                    um.icono,
                    um.ruta,
                    ucm.ingreso,
                    ucm.observaciones AS observaciones_permiso
                FROM usuario_cargo uc
                INNER JOIN usuario_cargo_menu ucm ON ucm.id_usuario_cargo = uc.id
                INNER JOIN usuario_menu um ON um.id = ucm.id_usuario_menu
                WHERE uc.nombre = '$cargo'
                AND uc.estado = 1
                AND ucm.estado = 1
                AND um.estado = 1
                AND ucm.ingreso = 1
                ORDER BY um.id ASC";

        return ejecutarConsulta($sql);
    }
}
?>