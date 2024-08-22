document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("filter-form");
    const filterSelect = document.getElementById("filter-select");
    const searchInput = document.getElementById("search-input");
    const submitButton = form.querySelector("button[type='submit']");
    const tableBody = document.getElementById("data-table-body");

    function updateFormState() {
        const filter = filterSelect.value;

        if (filter === "TODOS") {
            searchInput.disabled = true;
            submitButton.disabled = false;
            searchInput.value = ""; 
        } else {
            searchInput.disabled = false;
            submitButton.disabled = false;
        }
    }

    filterSelect.addEventListener("change", updateFormState);
    updateFormState();

    form.addEventListener("submit", function (event) {
        event.preventDefault();
        const filter = filterSelect.value;
        const search = searchInput.value;
        fetchUserData(filter, search);
    });

    async function fetchUserData(filter, search) {
        const urlParams = new URLSearchParams(window.location.search);
        const id = urlParams.get('id');
        
        let filtersToFetch = [];
        const filterNames = {
            'Alertas': 'Alertas',
            'Soporte': 'Soporte',
            'RegistroConsumoAgua': 'Registro de Consumo de Agua',
            'Reportes': 'Reportes',
            'Recomendaciones': 'Recomendaciones'
        };


        if (filter === "TODOS") {
            filtersToFetch = Object.keys(filterNames);
        } else {
            filtersToFetch = [filter];
        }

        let allResults = [];
        let foundResults = false;

        for (const currentFilter of filtersToFetch) {
            const response = await fetch(`../php/user_detailsAdmin.php?id=${id}&filter=${currentFilter}&search=${search}`);
            const data = await response.json();

            if (data.success) {
                let value = 0;
                const filteredResults = data.success.map(record => ({
                    ...record,
                    unFilter: currentFilter,
                    filter: filterNames[currentFilter], 
                    id: ((currentFilter != filtersToFetch[record.id]) ? value+=1 : 0 )
                }));
                allResults = allResults.concat(filteredResults);
                foundResults = true; 
            } else {
                console.log("Unexpected response structure:", data);
            }
        }
        if (!foundResults) {
            displayError("No se encontraron resultados para la búsqueda.");
        } else {
            updateTable(allResults);
        }
    }

    function updateTable(records) {
        const fieldNames = {
            'idAlerta': 'ID de Alerta',
            'idSoporte': 'ID de Soporte',
            'idConsumo': 'ID de Consumo',
            'idReporte': 'ID de Reporte',
            'idRec': 'ID de Recomendación',
            'idUsuario': 'ID de Usuario',
            'mensaje': 'Mensaje',
            'asunto': 'Asunto',
            'fechaAlerta': 'Fecha de Alerta',
            'fechaTicketSoporte': 'Fecha del Ticket a Soporte',
            'fechaConsumo': 'Fecha de Consumo',
            'fechaReporte': 'Fecha de Reporte',
            'cantidad': 'Cantidad',
            'ubicacion': 'Ubicación',
            'mensajeReporte': 'Mensaje de Reporte',
            'mensajeRec': 'Mensaje de Recomendación',
            'estado': 'Estado del Detalle',
            'accion': 'Accion',
            'estadoUsuario': 'Estado del Usuario',
            'fechaRec': 'Fecha de Recomendación',
        };

        const columnMapping = {
            'Alertas': 'idAlerta',
            'Soporte': 'idSoporte',
            'Registro de Consumo de Agua': 'idConsumo',
            'Reportes': 'idReporte',
            'Recomendaciones': 'idRec'
        };


        tableBody.innerHTML = "";

        if (records.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center">No se encontraron registros para el usuario actual.</td>
                </tr>
            `;
            return;
        }

        records.forEach(record => {
            const details = Object.entries(record)
                .filter(([key]) => key !== 'filter' && key !== 'id' && key !== 'unFilter')
                .map(([key, value]) => {
                    let colorClass = '';
                    if (value === 'Pendiente') {
                        colorClass = 'text-danger'; 
                    } else if (value === 'Resuelto') {
                        colorClass = 'text-success'; 
                    } else {
                        colorClass = ''; 
                    }
                    return `<li><strong>${fieldNames[key] || key}:</strong> <span class="${colorClass}">${value}</span></li>`;
                })
                .join('');

            
           

            const columnNameId = columnMapping[record.filter];
            const idColumna = record[columnNameId]; 

            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${record.id}</td>
                <td>${record.filter}</td>
                <td>
                    <ul>${details}</ul>
                </td>
                <td>                                     
                     <div class="button-group">
                        ${record.filter !== 'Alertas' && record.filter !== 'Registro de Consumo de Agua' ? `
                            <a href="../pages/edit_user_details_view.php?id_columna=${idColumna}&nombre_tabla=${record.unFilter}&nombre_columna_id=${columnNameId}" class="btn btn-warning btn-sm">Editar</a>
                        ` : ''}
                        ${record.filter === 'Alertas' || record.filter === 'Registro de Consumo de Agua' ? `
                            <a href="../php/delete_record.php?id_columna=${idColumna}&nombre_tabla=${record.unFilter}&nombre_columna_id=${columnNameId}" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar este registro?')">Eliminar</a>
                        ` : ''}
                    </div>
                </td>
            `;
            tableBody.appendChild(row);
        });
    }

    function displayError(message) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center">${message}</td>
            </tr>
        `;
    }

    fetchUserData('TODOS', '');
});
