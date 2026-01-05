<?php
ob_start();
session_start();

if (!isset($_SESSION['nombre'])) {
    header("Location: ../../Inicio/Controlador/Acceso.php?op=salir");
} else {
?>
    <?php include "../../General/Include/1_header.php"; ?>
    <main class="container">
        <!-- TÍTULO -->
        <?php include "../../General/Include/3_body.php"; ?>

        <!-- LISTADO DE LIBROS -->
        <div class="my-3 p-3 bg-body rounded shadow-sm" id="listado">
            <h5 class="border-bottom pb-2 mb-0"><b>LIBROS - LISTADO</b></h5>
            <div class="p-3">
                <table class="table table-hover" id="myTable">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>CÓDIGO</th>
                            <th>NOMBRE</th>
                            <th>STOCK</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <small class="d-block text-end mt-3">
                <button type="button" onclick="MostrarFormulario();" class="btn btn-success">AGREGAR</button>
            </small>
        </div>

        <!-- FORMULARIO DE LIBROS EN TABLA -->
        <div class="my-3 p-3 bg-body rounded shadow-sm" id="formulario">
            <h5 class="border-bottom pb-2 mb-0"><b>LIBROS - FORMULARIO</b></h5>
            <form id="frm_form" name="frm_form" method="post">
                <div class="p-3">
                    <button type="button" class="btn btn-info" onclick="agregarFila();">Agregar Fila</button>
                </div>

                <table class="table table-bordered" id="tabla_dinamica">
                    <thead>
                        <tr>
                            <th style="width: auto;">N°</th>
                            <th style="width: 20%;">Código</th>
                            <th style="width: 50%;">Nombre</th>
                            <th style="width: 20%;">Observaciones</th>
                            <th style="width: auto;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <button type="submit" class="btn btn-primary">GUARDAR</button>
                <button type="button" onclick="MostrarListado();" class="btn btn-secondary">CANCELAR</button>
            </form>
        </div>
    </main>
    <?php include "../../General/Include/2_footer.php"; ?>
    <script src="biblioteca_libro.js"></script>
<?php
}
ob_end_flush();
?>
