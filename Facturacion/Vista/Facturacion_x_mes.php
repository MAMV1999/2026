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
            <h5 class="border-bottom pb-2 mb-0"><b>DETALLE DE FACTURACION - LISTADO</b></h5>
            <div class="p-3">
                <table class="table table-hover" id="myTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>MES</th>
                            <th>CANT. PAGADO</th>
                            <th>CANT. EMITIDOS</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="my-3 p-3 bg-body rounded shadow-sm" id="formulario">
            <h5 class="border-bottom pb-2 mb-0"><b>DETALLE DE FACTURACION - FORMULARIO</b></h5>
            <form id="frm_form" name="frm_form" method="post">
                <br>
                <div class="table-responsive">
                    <table class="table table-hover" id="formulario-detalles">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>MATRICULA</th>
                                <th>NOMBRE Y APELLIDO</th>
                                <th>N° DOCUMENTO</th>
                                <th>DESC.</th>
                                <th>MONTO</th>
                                <th>ESTADO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Aquí se insertarán dinámicamente las filas -->
                        </tbody>
                    </table>
                </div>

                <div class="p-3">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" onclick="MostrarListado();" class="btn btn-secondary">Cancelar</button>
                </div>
            </form>
        </div>

        <!-- CUERPO_FIN -->

    </main>
    <?php include "../../General/Include/2_footer.php"; ?>
    <script src="Facturacion_x_mes.js"></script>
    <script>
        function copiarAlPortapapeles(texto, event) {
            event.preventDefault();
            // Si el navegador soporta la API moderna
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(texto)
                    .then(() => alert("COPIADO"))
                    .catch(err => {
                        console.error("Error al copiar con clipboard API", err);
                        fallbackCopiar(texto);
                    });
            } else {
                // Si no, usa método alternativo
                fallbackCopiar(texto);
            }
        }

        function fallbackCopiar(texto) {
            const textarea = document.createElement("textarea");
            textarea.value = texto;
            textarea.style.position = "fixed"; // Evita que se mueva al scroll
            textarea.style.left = "-9999px";
            document.body.appendChild(textarea);
            textarea.focus();
            textarea.select();
            try {
                document.execCommand("copy");
            } catch (err) {
                console.error("Fallback: No se pudo copiar", err);
                alert("No se pudo copiar");
            }
            document.body.removeChild(textarea);
        }
    </script>
<?php
}
ob_end_flush();
?>