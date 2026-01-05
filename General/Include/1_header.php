<!doctype html>
<html lang="es" data-bs-theme="auto">

<head>
    <script src="../../General/Style/js/color-modes.js"></script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CBE</title>

    <link rel="stylesheet" href="../../General/Style/css@3.css">
    <link rel="stylesheet" href="../../General/Style/dist/css/bootstrap.min.css">


    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

        .b-example-divider {
            width: 100%;
            height: 3rem;
            background-color: rgba(0, 0, 0, .1);
            border: solid rgba(0, 0, 0, .15);
            border-width: 1px 0;
            box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
        }

        .b-example-vr {
            flex-shrink: 0;
            width: 1.5rem;
            height: 100vh;
        }

        .bi {
            vertical-align: -.125em;
            fill: currentColor;
        }

        .nav-scroller {
            position: relative;
            z-index: 2;
            height: 2.75rem;
            overflow-y: hidden;
        }

        .nav-scroller .nav {
            display: flex;
            flex-wrap: nowrap;
            padding-bottom: 1rem;
            margin-top: -1px;
            overflow-x: auto;
            text-align: center;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
        }

        .btn-bd-primary {
            --bd-violet-bg: #712cf9;
            --bd-violet-rgb: 112.520718, 44.062154, 249.437846;

            --bs-btn-font-weight: 600;
            --bs-btn-color: var(--bs-white);
            --bs-btn-bg: var(--bd-violet-bg);
            --bs-btn-border-color: var(--bd-violet-bg);
            --bs-btn-hover-color: var(--bs-white);
            --bs-btn-hover-bg: #6528e0;
            --bs-btn-hover-border-color: #6528e0;
            --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
            --bs-btn-active-color: var(--bs-btn-hover-color);
            --bs-btn-active-bg: #5a23c8;
            --bs-btn-active-border-color: #5a23c8;
        }

        .bd-mode-toggle {
            z-index: 1500;
        }

        .bd-mode-toggle .dropdown-menu .active .bi {
            display: block !important;
        }
    </style>


    <!-- Custom styles for this template -->
    <!-- <link href="offcanvas-navbar.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="../../General/Style/offcanvas-navbar.css">
    <link rel="stylesheet" href="../../General/datatables/datatables.min.css">
    <!-- <link rel="stylesheet" href="../../General/select/dist/css/bootstrap-select.css"> -->
</head>

<body class="bg-body-tertiary">
    <!-- MENU -->
    <nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark" aria-label="Main navigation">
        <div class="container-fluid">
            <a class="navbar-brand" href="../../Inicio/Vista/Escritorio.php"><b>CBE</b></a>
            <button class="navbar-toggler p-0 border-0" type="button" id="navbarSideCollapse" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <?php
            $array = array(
                "1" => array(   "nombre" => "INICIO",           "link" => "Inicio",         "cargo" => array("TODOS")),
                "2" => array(   "nombre" => "USUARIO",          "link" => "Usuario",        "cargo" => array("ADMINISTRATIVO")),
                "3" => array(   "nombre" => "INSTITUCION",      "link" => "Institucion",    "cargo" => array("ADMINISTRATIVO")),
                "4" => array(   "nombre" => "MATRICULA 2026",   "link" => "Matricula",      "cargo" => array("DIRECTOR", "ADMINISTRATIVO")),
                "5" => array(   "nombre" => "MENSUALIDAD",      "link" => "Mensualidad",    "cargo" => array("DIRECTOR", "ADMINISTRATIVO")),
                "6" => array(   "nombre" => "FACTURACION",      "link" => "Facturacion",    "cargo" => array("DIRECTOR", "ADMINISTRATIVO")),
                "8" => array(   "nombre" => "DOCUMENTO",        "link" => "Documento",      "cargo" => array("DIRECTOR", "ADMINISTRATIVO")),
                "9" => array(   "nombre" => "ALMACEN",          "link" => "Almacen",        "cargo" => array("DIRECTOR", "ADMINISTRATIVO")),
                "10" => array(  "nombre" => "BIBLIOTECA",       "link" => "Biblioteca",     "cargo" => array("TODOS")),
            );
            ?>
            <div class="navbar-collapse offcanvas-collapse" id="navbarsExampleDefault">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php
                    $cargoSesion = $_SESSION['docente_cargo']; // por ejemplo: "DIRECTOR", "ADMINISTRATIVO", etc.
                    foreach ($array as $item) {
                        if (in_array("TODOS", $item["cargo"]) || in_array($cargoSesion, $item["cargo"])) {
                            echo '<li class="nav-item">
                        <a class="nav-link" aria-current="page" href="../../' . $item["link"] . '/Vista/Escritorio.php">' . $item["nombre"] . '</a>
                      </li>';
                        }
                    }
                    ?>
                </ul>
                <div class="d-flex" role="search">
                    <div class="btn-group dropstart">
                        <button type="button" class="btn btn-outline-danger dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropstart</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../../Inicio/Vista/Perfil.php"><?php echo $_SESSION['nombre']; ?></a></li>
                            <li><a class="dropdown-item" target="_blank" href="../../Reportes/Vista/usuario_docente.php?id=<?php echo $_SESSION['docente_id']; ?>">INFORMACION PERSONAL - PDF</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                        </ul>
                        <a class="btn btn-outline-danger" href="../../Inicio/Controlador/Acceso.php?op=salir" role="button">CERRAR SESIÓN</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>