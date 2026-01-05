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
            <h5 class="border-bottom pb-2 mb-0"><b>COMPRAS - LISTADO</b></h5>
            <div class="p-3">
                <table class="table table-hover" id="myTable">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>APODERADO</th>
                            <th>COMPROBANTE</th>
                            <th>FECHA</th>
                            <th>TOTAL</th>
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

        <div class="my-3 p-3 bg-body rounded shadow-sm" id="formulario" style="display: none;">
            <h5 class="border-bottom pb-2 mb-0"><b>COMPRAS - FORMULARIO</b></h5>

            <form id="frm_form" name="frm_form" method="post">
                <input type="hidden" id="almacen_ingreso_id" name="almacen_ingreso_id" placeholder="almacen_ingreso_id" class="form-control">

                <div class="p-3">
                    <label for="apoderado_dni" class="form-label"><b>COMPROBANTE / NUMERACIÓN / FECHA:</b></label>
                    <div class="input-group">
                        <select id="almacen_comprobante_id" name="almacen_comprobante_id" class="form-control" data-live-search="true"></select>
                        <input type="text" id="numeracion" name="numeracion" placeholder="NUMERACIÓN" class="form-control">
                        <input type="date" id="fecha" name="fecha" placeholder="FECHA" class="form-control">
                    </div>
                </div>

                <div class="p-3">
                    <label for="usuario_apoderado_id" class="form-label"><b>APODERADO:</b></label>
                    <div class="input-group">
                        <select id="usuario_apoderado_id" name="usuario_apoderado_id" class="form-control selectpicker" data-live-search="true"></select>
                    </div>
                </div>

                <div class="p-3">
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">AGREGAR PRODUCTO</button>

                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">PRODUCTOS</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-bordered" style="width: 100%;" id="myTable2">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center; width: 45%;">PRODUCTO</th>
                                                <th style="text-align: center; width: 25%;">CATEGORIA</th>
                                                <th style="text-align: center; width: 10%;">COMPRA</th>
                                                <th style="text-align: center; width: 10%;">STOCK</th>
                                                <th style="text-align: center; width: 10%;">ACCIONES</th>
                                            </tr>
                                        </thead>
                                        <tbody style="text-align: center;"></tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">SALIR</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br><br>
                    <table id="tablaProductos" class="table table-bordered">
                        <thead>
                            <tr>
                                <td style="width: 30%;">PRODUCTO</td>
                                <td style="width: 20%;">CANTIDAD</td>
                                <td style="width: 20%;">COSTO UNITARIO</td>
                                <td style="width: 20%;">OBSERVACIONES</td>
                                <td style="width: 10%;">ELIMINAR</td>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                </div>

                <div class="p-3">
                    <label for="apoderado_dni" class="form-label"><b>COMPROBANTE / NUMERACION / FECHA:</b></label>
                    <div class="input-group">
                        <select id="almacen_metodo_pago_id" name="almacen_metodo_pago_id" class="form-control selectpicker" data-live-search="true"></select>
                        <input type="text" id="total" name="total" placeholder="TOTAL" class="form-control" readonly>
                    </div>
                </div>

                <div class="p-3">
                    <label for="observaciones" class="form-label"><b>OBSERVACIONES:</b></label>
                    <div class="input-group">
                        <textarea id="observaciones" name="observaciones" placeholder="OBSERVACIONES" class="form-control"></textarea>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="p-3">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" onclick="MostrarListado();" class="btn btn-secondary">Cancelar</button>
                </div>
            </form>
        </div>


        <!-- CUERPO_FIN -->

    </main>
    <?php include "../../General/Include/2_footer.php"; ?>
    <script src="almacen_ingreso.js"></script>
<?php
}
ob_end_flush();
?>