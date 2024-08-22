document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const idColumna = urlParams.get('id_columna');
    const nombreTabla = urlParams.get('nombre_tabla');
    const nombreColumnaId = urlParams.get('nombre_columna_id');

    if (!idColumna || !nombreTabla || !nombreColumnaId) {
        alert('Parámetros inválidos.');
        return;
    }

    fetch(`../php/edit_record.php?id_columna=${idColumna}&nombre_tabla=${nombreTabla}&nombre_columna_id=${nombreColumnaId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            const record = data.record;
            const columnaCambiable = data.columnaCambiable;
            const form = document.getElementById('editForm');

            Object.keys(record).forEach(column => {
                const value = record[column];
                let input;

                if (column === columnaCambiable) {
                    input = `<select id="${column}" name="nuevo_valor" class="form-control" required>
                                <option value="Pendiente" ${value === 'Pendiente' ? 'selected' : ''}>Pendiente</option>
                                <option value="Resuelto" ${value === 'Resuelto' ? 'selected' : ''}>Resuelto</option>
                             </select>`;
                } else {
                    input = `<input type="text" id="${column}" class="form-control" value="${value}" disabled>`;
                }

                form.insertAdjacentHTML('beforeend', `
                    <div class="form-floating mb-3">
                        ${input}
                        <label for="${column}">${column.charAt(0).toUpperCase() + column.slice(1)}</label>
                    </div>
                `);
            });

            form.insertAdjacentHTML('beforeend', `
                <input type="hidden" name="id_columna" value="${idColumna}">
                <input type="hidden" name="nombre_tabla" value="${nombreTabla}">
                <input type="hidden" name="nombre_columna_id" value="${nombreColumnaId}">
            `);
            form.insertAdjacentHTML('beforeend', '<button type="submit" class="btn btn-primary">Actualizar</button> <a href="../php/admin.php" class="btn btn-secondary">Cancelar</a> ');

        });

    document.getElementById('editForm').addEventListener('submit', event => {
        event.preventDefault();

        const formData = new FormData(event.target);

        fetch('../php/update_record.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                alert(data.success);
                window.location.href = '../php/admin.php';
            }
        });
    });
});