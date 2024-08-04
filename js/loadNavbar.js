document.addEventListener('DOMContentLoaded', function() {
    fetchPrimaryNavbar();
    let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
});

function fetchPrimaryNavbar() {
    fetch('../components/body_navbar.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok.');
            }
            return response.text();
        })
        .then(data => {
            if (!data || data.trim() === '') {
                throw new Error('Received empty or null data.');
            }
            if (!isValidNavbarContent(data)) {
                throw new Error('Invalid navbar content.');
            }
            displayNavbar(data);
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
            fetchFallbackNavbar();
        });
}

function isValidNavbarContent(data) {
    return data.includes('<nav') && data.includes('navbar');
}

function displayNavbar(data) {
    document.getElementById('navbar-placeholder').innerHTML = data;
}

function fetchFallbackNavbar() {
    fetch('../components/navbar_failure/body_navbar.html')
        .then(response => {
            if (!response.ok) {
                throw new Error('Fallback menu not available.');
            }
            return response.text();
        })
        .then(data => {
            displayFallbackNavbar(data);
        })
        .catch(fallbackError => {
            console.error('There was a problem with the fallback fetch operation:', fallbackError);
            displayError();
        });
}

function displayFallbackNavbar(data) {
    document.getElementById('navbar-placeholder').innerHTML = `
        <div id="error-alert-navbar" class="container mt-5">
            <div class="alert alert-danger text-center">
                <h4 class="alert-heading">Error</h4>
                <p>Se ha producido un error al cargar el contenido del menú de navegación principal.</p>
                <hr>
                <p class="mb-0">Mientras tanto, se ha cargado un menú de navegación alternativo.</p>
                <a href="#" class="btn btn-primary mt-3" onclick="handleOkClick()">OK</a>
                <a href="../php/db_connection.php" class="btn btn-secondary mt-3">Saber Más</a>
            </div>
        </div>
    ` + data;
}

function displayError() {
    document.getElementById('navbar-placeholder').innerHTML = `
        <div id="error-alert-navbar" class="container mt-5">
            <div class="alert alert-danger text-center">
                <h4 class="alert-heading">Error</h4>
                <p>Se ha producido un error al cargar el contenido del menú de navegación y el menú alternativo también está indisponible.</p>
                <hr>
                <p class="mb-0">Por favor, contacta con el administrador si el problema persiste.</p>
                <a href="#" class="btn btn-primary mt-3" onclick="handleOkClick()">OK</a>
                <a href="../php/db_connection.php" class="btn btn-secondary mt-3">Saber Más</a>
            </div>
        </div>
    `;
}

function handleOkClick() {
    const errorAlert = document.getElementById('error-alert-navbar');
    if (errorAlert) {
        errorAlert.style.display = 'none'; 
    }
}
