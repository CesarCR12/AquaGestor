document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formularioReporte');
    const mensajeAlerta = document.getElementById('mensaje-alerta');
    const fechaInput = document.getElementById('fechaReporte');
    const horaInput = document.getElementById('horaReporte');

    if (!form || !mensajeAlerta || !fechaInput || !horaInput) {
        console.error('Elemento(s) no encontrado(s)');
        return;
    }

    const ahora = new Date();

    const hoy = ahora.toLocaleDateString(undefined, {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    }).split('/').reverse().join('-');

    const horaActual = ahora.toTimeString().split(' ')[0].substring(0, 5); 

    fechaInput.setAttribute('max', hoy);

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(form);

        const fechaSeleccionada = fechaInput.value;
        const horaSeleccionada = horaInput.value;

        if (fechaSeleccionada > hoy) {
            mostrarMensaje('La fecha del reporte no puede ser mayor a la fecha actual.', 'danger');
        } else if (fechaSeleccionada === hoy && horaSeleccionada > horaActual) {
            mostrarMensaje('La hora del reporte no puede ser mayor a la hora actual.', 'danger');
        } else {
            doReport(formData);
        }
    });

    function mostrarMensaje(mensaje, tipo) {
        mensajeAlerta.innerHTML = `<div class="alert alert-${tipo}">${mensaje}</div>`;
        setTimeout(() => {
            mensajeAlerta.innerHTML = '';
        }, 3000);
    }

    function doReport(formData) {
        fetch('../php/procesar_reporte.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const tipo = data.status === 'success' ? 'success' : 'danger';
            mostrarMensaje(data.message, tipo);
            form.reset();
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje('Error al enviar el reporte.', 'danger');
        });
    }
});
