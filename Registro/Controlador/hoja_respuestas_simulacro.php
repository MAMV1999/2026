<?php
include_once("../Modelo/hoja_respuestas_simulacro.php");

$simulacro = new Simulacro();

switch ($_GET["op"]) {

    case 'listar':
        $rspta = $simulacro->listar();
        $data = array();
        $cont = 1;

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => $cont,
                "1" => $reg->nivel,
                "2" => $reg->grado,
                "3" => $reg->seccion,
                "4" => '
                    <button type="button" 
                            class="btn btn-danger btn-sm" 
                            onclick="verPdf(' . $reg->id_seccion . ')">
                        VER PDF
                    </button>

                    <div class="modal fade" id="pdf_' . $reg->id_seccion . '" tabindex="-1" aria-labelledby="pdf_' . $reg->id_seccion . 'Label" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="' . $reg->id_seccion . 'Label">
                                        SIMULACRO - ' . $reg->nivel . ' - ' . $reg->grado . ' - ' . $reg->seccion . '
                                    </h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body" id="visor_pdf_' . $reg->id_seccion . '">
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CERRAR</button>
                                </div>
                            </div>
                        </div>
                    </div>
                '
            );

            $cont++;
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );

        echo json_encode($results);
        break;
}
?>