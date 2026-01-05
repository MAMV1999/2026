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
            <h5 class="border-bottom pb-2 mb-0"><b>VENTAS</b></h5>
            <div class="d-flex text-body-secondary pt-3">
                <br>
                <?php
                $array = array(
                    "1" => array("nombre" => "REGISTRO VENTA", "link" => "almacen_salida.php"),
                    "2" => array("nombre" => "REGISTRO COMPRA", "link" => "almacen_ingreso.php"),
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

        <!-- CUERPO_INICIO -->
        <div class="my-3 p-3 bg-body rounded shadow-sm" id="listado">
            <h5 class="border-bottom pb-2 mb-0"><b>ALMACEN</b></h5>
            <div class="d-flex text-body-secondary pt-3">
                <br>
                <?php
                $array = array(
                    "1" => array("nombre" => "REGISTRO ALMACEN x PRODUCTO", "link" => "almacen_producto.php"),
                    "2" => array("nombre" => "REGISTRO ALMACEN x CATEGORIA", "link" => "almacen_producto_categoria.php"),
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

        <!-- CUERPO_INICIO -->
        <div class="my-3 p-3 bg-body rounded shadow-sm" id="listado">
            <h5 class="border-bottom pb-2 mb-0"><b>REPORTES</b></h5>
            <div class="d-flex text-body-secondary pt-3">
                <br>
                <?php
                $array = array(
                    "1" => array("nombre" => "REPORTE DE PRODUCTOS", "link" => "../../Reportes/Vista/almacen_producto.php"),
                    "2" => array("nombre" => "REPORTE DE VENTAS X DIA", "link" => "../../Reportes/Vista/Reporte_salida.php"),
                    "3" => array("nombre" => "REPORTE DE VENTAS X APODERADO", "link" => "../../Reportes/Vista/Reporte_salida_x_apoderado.php"),
                    "4" => array("nombre" => "REPORTE DE VENTAS X PRODUCTO", "link" => "../../Reportes/Vista/Reporte_salida_x_producto.php"),
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

        <hr>

        <p class="d-inline-flex gap-1">
            <a class="btn btn-primary" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">AJUSTES</a>
        </p>
        <div class="collapse" id="collapseExample">
            <div class="card card-body">

                <!-- CUERPO_INICIO -->
                <div class="my-3 p-3 bg-body rounded shadow-sm" id="listado">
                    <h5 class="border-bottom pb-2 mb-0"><b>AJUSTES</b></h5>
                    <div class="d-flex text-body-secondary pt-3">
                        <br>
                        <?php
                        $array = array(
                            "1" => array("nombre" => "CATEGORIA - ALMACEN", "link" => "almacen_categoria.php"),
                            "2" => array("nombre" => "COMPROBANTE", "link" => "almacen_comprobante.php"),
                            "3" => array("nombre" => "METODO PAGO", "link" => "almacen_metodo_pago.php"),
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