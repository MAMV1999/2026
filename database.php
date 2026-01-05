<?php
    $servidor="127.0.0.1";
    $usuario="root";
    $clave="";
    $bd="2025";
    $encode="utf8";

    setlocale(LC_TIME, 'es_ES.UTF-8');
    $conectar=new mysqli($servidor,$usuario,$clave,$bd);
    $conectar->set_charset($encode);

    if (mysqli_connect_errno()) {
        printf("FALLO ALGO EN LA CONEXION CON LA BD, AVISA AREA DE SISTEMAS: 994 947 452");
        exit();
    }

    if (!function_exists('ejecutarConsulta')) {
        
        function ejecutarConsulta($sql){
            global $conectar;
            $query=$conectar->query($sql);
            return $query;
        }
        
        function ejecutarConsultaSimpleFila($sql){
            global $conectar;
            $query=$conectar->query($sql);
            $row=$query->fetch_assoc();
            return $row;
        }
        
        function ejecutarConsulta_retornarID($sql){
            global $conectar;
            $query=$conectar->query($sql);
            return $conectar->insert_id;
        }
        
        function limpiarcadena($str){
            global $conectar;
            $str=mysqli_real_escape_string($conectar,trim($str));
            return htmlspecialchars($str);
        }
        
    }
?>