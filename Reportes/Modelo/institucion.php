<?php
require_once("../../database.php");

class ReporteInstitucion
{
    public function __construct() {}

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
                    i.direccion,
                    DATE_FORMAT(i.fechacreado, '%d/%m/%Y') AS fecha,
                    i.estado
                FROM institucion i
                LEFT JOIN usuario_docente ud ON i.id_usuario_docente = ud.id
                ORDER BY i.nombre ASC";
        return ejecutarConsulta($sql);
    }
}
?>
