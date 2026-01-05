<?php
require_once("../../database.php");

class Documentos
{

    public function __construct()
    {
    }

    public function obtenerReporteDinamico($id)
    {
        $sql = "CALL ObtenerDetallesMatricula($id)";
        return ejecutarConsulta($sql);
    }
}
?>
