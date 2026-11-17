<?php
session_start();
require_once("../Modelo/Acceso.php");

$acceso = new Acceso();

$usuario = isset($_POST["usuario"]) ? limpiarcadena($_POST["usuario"]) : "";
$clave = isset($_POST["clave"]) ? limpiarcadena($_POST["clave"]) : "";

switch ($_GET["op"]) {

    case 'sesion':

        if (isset($_SESSION['nombre'])) {
            echo json_encode(array(
                "status" => "success"
            ));
        } else {
            echo json_encode(array(
                "status" => "error"
            ));
        }

        break;


    case 'verificar':

        /*
        SI YA EXISTE UNA SESIÓN,
        NO PERMITIR INICIAR OTRA.
        */

        if (isset($_SESSION['nombre'])) {

            echo json_encode(array(
                "status" => "success",
                "sesion" => "activa"
            ));

            break;
        }


        $rspta = $acceso->verificar($usuario, $clave);

        if ($rspta->num_rows > 0) {

            $fetch = $rspta->fetch_object();
            
            $_SESSION['docente_id'] = $fetch->docente_id;
            $_SESSION['tipo_documento'] = $fetch->tipo_documento;
            $_SESSION['docente_documento'] = $fetch->docente_documento;
            $_SESSION['nombre'] = $fetch->docente_nombre;
            $_SESSION['docente_fecha_nacimiento'] = $fetch->docente_fecha_nacimiento;
            $_SESSION['docente_estado_civil'] = $fetch->docente_estado_civil;
            $_SESSION['docente_sexo'] = $fetch->docente_sexo;
            $_SESSION['docente_direccion'] = $fetch->docente_direccion;
            $_SESSION['docente_telefono'] = $fetch->docente_telefono;
            $_SESSION['docente_correo'] = $fetch->docente_correo;
            $_SESSION['docente_cargo'] = $fetch->docente_cargo;
            $_SESSION['docente_tipo_contrato'] = $fetch->docente_tipo_contrato;
            $_SESSION['docente_fecha_inicio'] = $fetch->docente_fecha_inicio;
            $_SESSION['docente_fecha_fin'] = $fetch->docente_fecha_fin;
            $_SESSION['docente_cuenta_bancaria'] = $fetch->docente_cuenta_bancaria;
            $_SESSION['docente_cuenta_interbancaria'] = $fetch->docente_cuenta_interbancaria;
            $_SESSION['docente_sunat_ruc'] = $fetch->docente_sunat_ruc;
            $_SESSION['docente_sunat_usuario'] = $fetch->docente_sunat_usuario;
            $_SESSION['docente_sunat_contraseña'] = $fetch->docente_sunat_contraseña;
            $_SESSION['docente_estado'] = $fetch->docente_estado;

            echo json_encode(array(
                "status" => "success",
                "datos" => $fetch
            ));

        } else {

            echo json_encode(array(
                "status" => "error",
                "message" => "Usuario o contraseña incorrectos o el usuario está desactivado."
            ));
        }

        break;


    case 'salir':

        /*
        ELIMINAR TODAS LAS VARIABLES
        DE LA SESIÓN ACTUAL.
        */

        $_SESSION = array();

        session_unset();

        /*
        ELIMINAR TAMBIÉN LA COOKIE
        DE LA SESIÓN.
        */

        if (ini_get("session.use_cookies")) {

            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();

        header("Location: ../../index.php");
        exit();
}
?>