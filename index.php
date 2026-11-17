<?php
session_start();

if (isset($_SESSION['nombre'])) {
    header("Location: Inicio/Vista/Escritorio.php");
} else {
    header("Location: Inicio/Vista/login.php");
}

exit();
?>