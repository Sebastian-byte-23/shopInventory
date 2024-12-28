function applyFilters() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);

    // Verifica que los valores de ordenar_por y ordenar_direction se estén pasando
    const ordenarPorSelect = document.querySelector('select[name="ordenar_por"]');
    const ordenarDirectionSelect = document.querySelector('select[name="ordenar_direction"]');

    // Si los selectores de ordenación no tienen un valor, configurarlos por defecto
    if (!formData.has('ordenar_por')) {
        formData.append('ordenar_por', ordenarPorSelect.value || 'nombre'); // Default: ordenar por nombre
    }

    if (!formData.has('ordenar_direction')) {
        formData.append('ordenar_direction', ordenarDirectionSelect.value || 'ASC'); // Default: ASC
    }

    // Convertir los datos del formulario a parámetros de URL
    const params = new URLSearchParams(formData).toString();

    // Depuración: Verifica qué parámetros se están enviando
    console.log('Parametros enviados:', params);

    // Este código AJAX se encargará de actualizar solo el tbody de la tabla
    fetch('filtros.php?' + params)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.text();
        })
        .then(html => {
            // Selecciona el tbody y actualízalo
            const tbody = document.querySelector('#productTableBody');
            tbody.innerHTML = html;  // Esto reemplaza solo las filas dentro de tbody
        })
        .catch(error => console.error('Error al aplicar filtros:', error));

}
