$.getJSON('../php/dashboard_data.php', function(data) {
    const errorMessage = document.getElementById('error-message');
    if (data.status === 'error') {
        if (data.message !== '' || data.message !== undefined || data.message !==null){
            const lines = data.message.split('\n');
            let htmlMessage = '';
            lines.forEach(line => {
                if (line.startsWith('Error')) {
                    htmlMessage += `<div class=" alert alert-danger">${line}</div>`;
                } else {
                    htmlMessage += `<div class=" alert alert-warning">${line}</div>`;
                }
            });
            errorMessage.innerHTML = htmlMessage;
            errorMessage.classList.remove('d-none');
        }
        
    }
    if (data.consumo.length > 0) {
        new Morris.Line({
            element: 'consumo-chart',
            data: data.consumo,
            xkey: 'fecha',
            ykeys: ['total'],
            labels: ['Total Consumo']
        });
    } else {
        document.getElementById('consumo-chart').innerText = 'No hay datos de consumo disponibles.';
        document.getElementById('consumo-chart').className = 'text-center';
    }

    if (data.soporte.length > 0) {
        new Morris.Bar({
            element: 'soporte-chart',
            data: data.soporte,
            xkey: 'accion',
            ykeys: ['total'],
            labels: ['Total']
        });
    } else {
        document.getElementById('soporte-chart').innerText = 'No hay datos de soporte disponibles.';
        document.getElementById('soporte-chart').className = 'text-center';
    }

    if (data.alertas.length > 0) {
        new Morris.Donut({
            element: 'alertas-chart',
            data: [
                { label: "Total Alertas", value: data.alertas[0].total_alertas }
            ]
        });
    } else {
        document.getElementById('alertas-chart').innerText = 'No hay datos de alertas disponibles.';
        document.getElementById('alertas-chart').className = 'text-center';
    }
    

   
}).fail(function() {
    alert('Error al obtener los datos del servidor.');
});
