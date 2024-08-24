document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formularioReporte');
    const mensajeAlerta = document.getElementById('mensaje-alerta');
    const fechaInput = document.getElementById('fechaReporte');
    const horaInput = document.getElementById('horaReporte');
    const listaReportes = document.getElementById('lista-reportes');

    if (!form || !mensajeAlerta || !fechaInput || !horaInput || !listaReportes) {
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
            // console.log('Datos enviados:', {
            //     mensajeReporte: formData.get('mensajeReporte'),
            //     fechaReporte: formData.get('fechaReporte'),
            //     horaReporte: formData.get('horaReporte')
            // });
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
        .then(response => response.text())
        .then(text => {
            // console.log('Respuesta cruda:', text);
            try {
                const data = JSON.parse(text);
                // console.log('Respuesta parseada:', data);
                const tipo = data.status === 'success' ? 'success' : 'danger';
                mostrarMensaje(data.message, tipo);
                form.reset();
                cargarReportes();
            } catch (error) {
                console.error('Error al analizar JSON:', error);
                mostrarMensaje('Respuesta del servidor no es válida.', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje('Error al enviar el reporte.', 'danger');
        });
    }

    function cargarReportes() {
        fetch('../php/obtener_reportes.php')
            .then(response => response.json())
            .then(data => {
                // console.log('Reportes obtenidos:', data);
                if (data.status === 'success') {
                    listaReportes.innerHTML = '';
                    data.data.forEach(reporte => {
                        // console.log('Reporte:', reporte);
                        const item = document.createElement('li');
                        item.className = 'list-group-item';
                        item.innerHTML = `
                            <strong>ID: </strong>${reporte.idReporte}<br>
                            <strong>Fecha: </strong>${reporte.fechaReporte}<br>
                            <strong>Mensaje: </strong> ${reporte.mensajeReporte}
                        `;
                        listaReportes.appendChild(item);
                    });
                } else {
                    listaReportes.innerHTML = `<li class="list-group-item text-danger">${data.message}</li>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                listaReportes.innerHTML = `<li class="list-group-item text-danger">Error al cargar los reportes.</li>`;
            });
    }

    cargarReportes();
});
