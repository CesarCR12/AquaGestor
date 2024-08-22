document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('soporteForm').addEventListener('submit', function (e) {
        e.preventDefault();

        var formData = {
            asunto: document.getElementById('asunto').value,
            mensaje: document.getElementById('mensaje').value
        };

        fetch('../php/procesarSoporte.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            var responseMessage = document.getElementById('responseMessage');
            responseMessage.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
            document.getElementById('soporteForm').reset();

            setTimeout(function() {
                responseMessage.innerHTML = '';
            }, 3000);
        })
        .catch(() => {
            var responseMessage = document.getElementById('responseMessage');
            responseMessage.innerHTML = '<div class="alert alert-danger">Ocurrió un error al enviar el formulario. Inténtalo nuevamente.</div>';
            setTimeout(function() {
                responseMessage.innerHTML = '';
            }, 3000);
        });
    });
});
