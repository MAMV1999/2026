<div class="d-flex align-items-center p-3 my-3 text-light bg-dark rounded shadow-sm">
    <img class="me-3" src="../../General/insignia.png" alt="" width="40" height="50">
    <div class="lh-1">
        <h1 class="h6 mb-1 text-light lh-1"><b><?php echo $_SESSION['nombre']; ?></b></h1>
        <small id="fechaHora"></small>
    </div>
</div>

<script>
    function obtenerFechaHora() {
        const fechaHora = new Date();
        const opcionesFecha = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        const opcionesHora = {
            hour: 'numeric',
            minute: 'numeric',
            second: 'numeric'
        };
        const fechaFormateada = fechaHora.toLocaleDateString('es-ES', opcionesFecha);
        const horaFormateada = fechaHora.toLocaleTimeString('es-ES', opcionesHora);
        return `${fechaFormateada} - ${horaFormateada}`;
    }

    function actualizarFechaHora() {
        const fechaHoraElemento = document.getElementById('fechaHora');
        fechaHoraElemento.textContent = obtenerFechaHora();
    }
</script>