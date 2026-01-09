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
            <h5 class="border-bottom pb-2 mb-0"><b>CB EBENEZER</b></h5>
            <div class="d-flex text-body-secondary pt-3">
                <div class="card text-center w-100">
                    <div class="card-header">
                        <br>
                    </div>
                    <div class="card-body">
                        <?php
                        date_default_timezone_set("America/Lima");
                        $hora = date("H");
                            if ($hora >= 6 && $hora < 12) { $saludo = "BUENOS DÍAS"; }
                            elseif ($hora >= 12 && $hora < 18) { $saludo = "BUENAS TARDES"; }
                            else { $saludo = "BUENAS NOCHES"; }
                        ?>
                        <h1 class="display-6"><strong><?php echo $saludo . ", " . $_SESSION['nombre']; ?></strong></h1>
                        <h1 class="display-6"><?php echo $_SESSION['docente_cargo']; ?></h1>
                    </div>
                    <div class="card-footer text-body-secondary">
                        <br>
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