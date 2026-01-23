<?php
ob_start();
session_start();

if (!isset($_SESSION['nombre'])) {
    header("Location: ../../Inicio/Controlador/Acceso.php?op=salir");
} else {
?>
    <?php include "../../General/Include/1_header.php"; ?>
    <main class="container">
        <!-- TITULO -->
        <?php include "../../General/Include/3_body.php"; ?>

        <!-- CUERPO_INICIO -->
        <div class="my-3 p-3 bg-body rounded shadow-sm" id="listado">
            <h5 class="border-bottom pb-2 mb-0"><b>MENSUALIDAD</b></h5>
            <div class="d-flex text-body-secondary pt-3">
                <br>
                <?php
                $array = array(
                    "1" => array("nombre" => "REGISTRO MENSUALIDAD X ALUMNO", "link" => "Mensualidad_detalle.php"),
                    "2" => array("nombre" => "REGISTRO MENSUALIDAD X APODERADO", "link" => "mensualidad_x_apoderado.php"),
                    "3" => array("nombre" => "REGISTRO MENSUALIDAD X MES", "link" => "mensualidad_x_mes.php"),
                    "4" => array("nombre" => "REGISTRO MENSUALIDAD X AÑO", "link" => "mensualidad_x_grupo.php"),
                );
                ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">NOMBRE</th>
                            <th scope="col">PAGUINA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $a = 1;
                        while ($a <= count($array)) {
                            echo '<tr>
                                <th scope="row">' . $a . '</th>
                                <td>' . $array[$a]["nombre"] . '</td>
                                <td><a class="btn btn-primary" href="' . $array[$a]["link"] . '" role="button">Ir a ' . $array[$a]["nombre"] . '</a></td>
                            </tr>';
                            $a++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <hr>

        <!-- CUERPO_INICIO -->
        <div class="my-3 p-3 bg-body rounded shadow-sm" id="listado">
            <h5 class="border-bottom pb-2 mb-0"><b>REPORTES</b></h5>
            <div class="d-flex text-body-secondary pt-3">
                <br>
                <?php
                $array = array(
                    "1" => array("nombre" => "DETALLE DE MENSUALIDADES", "link" => "../../Reportes/Vista/Mensualidad_reporte_general.php"),
                    "2" => array("nombre" => "DETALLE DE MENSUALIDADES X MES", "link" => "../../Reportes/Vista/mensualidad_detalle_general.php"),
                    "3" => array("nombre" => "DETALLE DE MENSUALIDADES X APODERADO", "link" => "../../Reportes/Vista/Mensualidad_reporte_x_apoderado.php"),
                    "4" => array("nombre" => "DETALLE DE MENSUALIDADES X GRADO", "link" => "../../Reportes/Vista/Mensualidad_reporte_x_grado.php"),
                    "5" => array("nombre" => "DETALLE DE MENSUALIDADES PAGADAS", "link" => "../../Reportes/Vista/mensualidad_detalle_pagado.php"),
                    "6" => array("nombre" => "DETALLE DE MENSUALIDADES PENDIENTES", "link" => "../../Reportes/Vista/mensualidad_detalle_deudores.php"),
                    "7" => array("nombre" => "DETALLE DE MENSUALIDADES PENDIENTES MONTOS", "link" => "../../Reportes/Vista/mensualidad_detalle_deudores_montos.php"),
                    "8" => array("nombre" => "LISTADO DE MENSUALIDADES BCP", "link" => "../../Reportes/Vista/Mensualidad_reporte_bcp.php"),
                );
                ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">NOMBRE</th>
                            <th scope="col">PAGUINA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $a = 1;
                        while ($a <= count($array)) {
                            echo '<tr>
                                    <th scope="row">' . $a . '</th>
                                    <td>' . $array[$a]["nombre"] . '</td>
                                    <td><a class="btn btn-primary" Target="_blank" href="' . $array[$a]["link"] . '" role="button">Ir a ' . $array[$a]["nombre"] . '</a></td>
                                </tr>';
                            $a++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
    <?php include "../../General/Include/2_footer.php"; ?>
    <script>
        function init() {
            actualizarFechaHora();
            setInterval(actualizarFechaHora, 1000);
        }

        init();
    </script>
<?php
}
ob_end_flush();
?>