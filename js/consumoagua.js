document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formularioConsumoAgua');
    const mensajeAlerta = document.getElementById('mensaje-alerta');
    const fechaInput = document.getElementById('fechaConsumo');
    const listaConsumos = document.getElementById('lista-consumos');

    if (!form || !mensajeAlerta || !fechaInput || !listaConsumos) {
        console.error('Elemento(s) no encontrado(s)');
        return;
    }

    const ahora = new Date();
    const hoy = ahora.toISOString().split('T')[0];
    fechaInput.setAttribute('max', hoy);

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(form);
        const fechaSeleccionada = fechaInput.value;

        if (fechaSeleccionada > hoy) {
            mostrarMensaje('La fecha del consumo no puede ser mayor a la fecha actual.', 'danger');
        } else {
            registrarConsumo(formData);
        }
    });

    function mostrarMensaje(mensaje, tipo) {
        mensajeAlerta.innerHTML = `<div class="alert alert-${tipo}">${mensaje}</div>`;
        setTimeout(() => {
            mensajeAlerta.innerHTML = '';
        }, 3000);
    }

    function registrarConsumo(formData) {
        fetch('../php/procesar_registro.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const tipo = data.status === 'success' ? 'success' : 'danger';
            mostrarMensaje(data.message, tipo);
            form.reset();
            cargarConsumos();
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje('Error al registrar el consumo.', 'danger');
        });
    }

    function cargarConsumos() {
        fetch('../php/obtener_registros.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    listaConsumos.innerHTML = '';
                    data.data.forEach(consumo => {
                        const item = document.createElement('li');
                        item.className = 'list-group-item';
                        item.innerHTML = `
                            <strong>ID: </strong>${consumo.idConsumo}<br>
                            <strong>Fecha: </strong>${consumo.fechaConsumo}<br>
                            <strong>Cantidad: </strong> ${consumo.cantidad} litros<br>
                            <strong>Ubicación: </strong> ${consumo.ubicacion}
                        `;
                        listaConsumos.appendChild(item);
                    });
                } else {
                    listaConsumos.innerHTML = `<li class="list-group-item text-danger">${data.message}</li>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                listaConsumos.innerHTML = `<li class="list-group-item text-danger">Error al cargar los consumos.</li>`;
            });
    }

    cargarConsumos();
});
