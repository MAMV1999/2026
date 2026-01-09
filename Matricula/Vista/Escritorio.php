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
            <h5 class="border-bottom pb-2 mb-0"><b>MATRICULA</b></h5>
            <div class="d-flex text-body-secondary pt-3">
                <br>
                <?php
                $array = array(
                    "1" => array("nombre" => "NUEVA MATRICULA", "link" => "matricula_detalle.php"),
                    "2" => array("nombre" => "EDITAR MATRICULA", "link" => "matricula_editar.php"),
                    //"3" => array("nombre" => "EDITAR MENSUALIDAD", "link" => "Matricula_mensualidad.php"),
                    "3" => array("nombre" => "PAGOS MATRICULA", "link" => "Matricula_pago.php"),
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
            <h5 class="border-bottom pb-2 mb-0"><b>REPORTE DE MATRICULA</b></h5>
            <div class="d-flex text-body-secondary pt-3">
                <br>
                <?php
                $array = array(
                    "1" => array("nombre" => "CANTIDAD DE ALUMNOS",             "link" => "../../Reportes/Vista/Matriculados_cantidad.php"),
                    "2" => array("nombre" => "LISTADO DE ALUMNOS",              "link" => "../../Reportes/Vista/ReporteMatricula.php"),
                    "3" => array("nombre" => "LISTADO DE ALUMNOS - TUTORES",    "link" => "../../Reportes/Vista/ReporteAlumnosTutores.php"),
                    "4" => array("nombre" => "LISTADO DE ALUMNOS - REUNIÓN",    "link" => "../../Reportes/Vista/ReporteAlumnosTutoresReuniones.php"),
                    "5" => array("nombre" => "LISTADO DETALLE MATRICULAS",      "link" => "../../Reportes/Vista/ReciboMatriculaTotal.php"),
                    "6" => array("nombre" => "PAGOS AGRUPADOS POR FECHA",       "link" => "../../Reportes/Vista/ReporteMatriculaXFecha.php"),
                    "7" => array("nombre" => "PAGOS AGRUPADOS POR APODERADO",   "link" => "../../Reportes/Vista/ReporteMatriculaXApoderado.php"),
                    "8" => array("nombre" => "CUMPLEAÑOS",                      "link" => "../../Reportes/Vista/Nacimiento.php"),
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
            <a class="btn btn-primary" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">AJUSTES DE MATRICULA</a>
        </p>
        <div class="collapse" id="collapseExample">
            <div class="card card-body">

                <!-- CUERPO_INICIO -->
                <div class="my-3 p-3 bg-body rounded shadow-sm" id="listado">
                    <h5 class="border-bottom pb-2 mb-0"><b>AJUSTES DE MATRICULA</b></h5>
                    <div class="d-flex text-body-secondary pt-3">
                        <br>
                        <?php
                        $array = array(
                            "1" => array("nombre" => "MATRICULA", "link" => "Matricula.php"),
                            "2" => array("nombre" => "COBROS", "link" => "matricula_cobro.php"),
                            "3" => array("nombre" => "MES", "link" => "matricula_mes.php"),
                            "4" => array("nombre" => "CATEGORIA", "link" => "matricula_categoria.php"),
                            "5" => array("nombre" => "METODO PAGO", "link" => "matricula_metodo_pago.php"),
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