<?php
require_once("../../database.php");

class MatriculaCobro
{
    public function __construct() {}

    public function guardar($nombre, $apertura, $observaciones, $meses)
    {
        try {
            // Limpiar datos principales
            $nombre = limpiarcadena($nombre);
            $apertura = limpiarcadena($apertura);
            $observaciones = limpiarcadena($observaciones);

            // 1) Insertar cabecera en matricula_cobro
            $sqlCobro = "INSERT INTO matricula_cobro (nombre, apertura, observaciones) VALUES ('$nombre', '$apertura', '$observaciones')";
            $matricula_cobro_id = ejecutarConsulta_retornarID($sqlCobro);

            foreach ($meses as $item) {
                $matricula_mes_id = limpiarcadena($item['matricula_mes_id']);
                $aplica = limpiarcadena($item['aplica']);
                $observaciones_detalle = limpiarcadena($item['observaciones']);

                $sqlDetalle = "INSERT INTO matricula_cobro_detalle (matricula_cobro_id, matricula_mes_id, aplica, observaciones) VALUES ('$matricula_cobro_id', '$matricula_mes_id', '$aplica', '$observaciones_detalle')";
                ejecutarConsulta($sqlDetalle);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function editar($id, $nombre, $apertura, $observaciones, $meses)
    {
        try {
            $id = limpiarcadena($id);
            $nombre = limpiarcadena($nombre);
            $apertura = limpiarcadena($apertura);
            $observaciones = limpiarcadena($observaciones);

            // 1) Actualizar cabecera
            $sqlUpdate = "UPDATE matricula_cobro SET nombre='$nombre', apertura='$apertura', observaciones='$observaciones' WHERE id='$id'";
            ejecutarConsulta($sqlUpdate);

            // 2) Reemplazar detalle (eliminar y volver a insertar)
            $sqlDeleteDetalle = "DELETE FROM matricula_cobro_detalle WHERE matricula_cobro_id='$id'";
            ejecutarConsulta($sqlDeleteDetalle);

            foreach ($meses as $item) {
                $matricula_mes_id = limpiarcadena($item['matricula_mes_id']);
                $aplica = limpiarcadena($item['aplica']);
                $observaciones_detalle = limpiarcadena($item['observaciones']);

                $sqlDetalle = "INSERT INTO matricula_cobro_detalle (matricula_cobro_id, matricula_mes_id, aplica, observaciones) VALUES ('$id', '$matricula_mes_id', '$aplica', '$observaciones_detalle')";
                ejecutarConsulta($sqlDetalle);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function mostrar($id)
    {
        $id = limpiarcadena($id);

        // Cabecera
        $sqlCabecera = "SELECT id, nombre, apertura, observaciones, estado FROM matricula_cobro WHERE id='$id'";
        $cabecera = ejecutarConsultaSimpleFila($sqlCabecera);

        // Detalle (por mes)
        $sqlDetalle = "SELECT matricula_mes_id, aplica, observaciones FROM matricula_cobro_detalle WHERE matricula_cobro_id='$id'";
        $rsptaDetalle = ejecutarConsulta($sqlDetalle);

        $detalle = array();
        while ($reg = $rsptaDetalle->fetch_object()) {
            $detalle[$reg->matricula_mes_id] = array(
                "aplica" => $reg->aplica,
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
        $sql = "SELECT * FROM matricula_cobro";
        return ejecutarConsulta($sql);
    }

    public function listar_matricula_mes_activas()
    {
        $sql = "SELECT
                    mm.id,
                    il.nombre AS institucion_lectivo_nombre,
                    CONCAT(mm.nombre, ' ', il.nombre) AS nombre_con_institucion,
                    mm.nombre,
                    mm.observaciones,
                    DATE_FORMAT(mm.fecha_vencimiento, '%d/%m/%Y') AS fecha_vencimiento,
                    mm.mora,
                    mm.estado
                FROM matricula_mes mm
                INNER JOIN institucion_lectivo il ON il.id = mm.institucion_lectivo_id
                WHERE mm.estado = 1
                ORDER BY mm.id ASC";
        return ejecutarConsulta($sql);
    }

    public function desactivar($id)
    {
        try {
            $id = limpiarcadena($id);

            // 1) Desactivar cabecera
            $sqlCab = "UPDATE matricula_cobro SET estado='0' WHERE id='$id'";
            ejecutarConsulta($sqlCab);

            // 2) Desactivar detalle
            $sqlDet = "UPDATE matricula_cobro_detalle SET estado='0' WHERE matricula_cobro_id='$id'";
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
            $sqlCab = "UPDATE matricula_cobro SET estado='1' WHERE id='$id'";
            ejecutarConsulta($sqlCab);

            // 2) Activar detalle
            $sqlDet = "UPDATE matricula_cobro_detalle SET estado='1' WHERE matricula_cobro_id='$id'";
            ejecutarConsulta($sqlDet);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
