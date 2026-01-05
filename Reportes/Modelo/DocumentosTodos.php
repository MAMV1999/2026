<?php
require_once("../../database.php");

class Documentos
{

    public function __construct()
    {
    }

    public function obtenerReporteDinamico()
    {
        $sql = "CALL ObtenerDetallesMatriculaTodos()";
        return ejecutarConsulta($sql);
    }
}
?>
