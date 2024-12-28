$(document).ready(function () {
    // Asegurarse de que el elemento #viewChangesBtn existe antes de asignar el evento
    const viewChangesBtn = document.getElementById("viewChangesBtn");
    if (viewChangesBtn) {
        viewChangesBtn.onclick = function () {
            window.location.href = "historial_movimientos.php";
        };
    }

    $('#proveedores').select2({
        placeholder: "Seleccione uno o más proveedores",
        allowClear: true, // Permite limpiar la selección
        width: '100%',
        height: '5px' // Ajusta el ancho al 100% de su contenedor
    });
});


// Función para alternar la visibilidad del formulario de búsqueda
function toggleFilters() {
    // Obtener el formulario de búsqueda
    var form = document.getElementById('advancedSearchForm');

    // Alternar la clase 'hidden' para mostrar u ocultar el formulario
    form.classList.toggle('hidden');
}


function buscarProducto() {
    const nombre = document.getElementById('nombre').value.trim();  // Usar trim() para eliminar espacios innecesarios
    const precioMin = document.getElementById('precio_min').value || 0;
    const precioMax = document.getElementById('precio_max').value || 9999999;
    const categoria = document.getElementById('categoria').value;
    const ordenarPor = document.getElementById('ordenar_por').value;

    // Validación de precios
    if (parseFloat(precioMin) > parseFloat(precioMax)) {
        alert("El precio mínimo no puede ser mayor que el precio máximo.");
        return;
    }

    if (!nombre && !precioMin && !precioMax && !categoria && !ordenarPor) {
        alert("Por favor, ingrese al menos un filtro para realizar la búsqueda.");
        return;  // Si no hay filtros, no hace la búsqueda.
    }
// Esto obtiene el valor de la categoría seleccionada
    const url = `filtros.php?nombre=${encodeURIComponent(nombre)}&precio_min=${encodeURIComponent(precioMin)}&precio_max=${encodeURIComponent(precioMax)}&categoria=${encodeURIComponent(categoria)}&ordenar_por=${encodeURIComponent(ordenarPor)}&pagina=1`;
    
    const xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4) {
            if (xhr.status == 200) {
                const resultados = JSON.parse(xhr.responseText);
                mostrarResultados(resultados);
            } else {
                alert('Error al realizar la búsqueda. Por favor, intente de nuevo.');
            }
        }
    };
    xhr.send();
}

function mostrarResultados(resultados) {
    const tablaProductos = document.getElementById('tabla-producto').getElementsByTagName('tbody')[0];
    tablaProductos.innerHTML = ''; // Limpiar la tabla de resultados anteriores

    // Limpiar los campos de categoría después de la búsqueda
    document.getElementById('categoria').value = '';
    document.getElementById('ordenar_por').value = '';

    if (resultados.length > 0) {
        resultados.forEach(producto => {
            // Crear una nueva fila en la tabla
            const nuevaFila = tablaProductos.insertRow();

            // Insertar celdas con los datos del producto
            const celdaId = nuevaFila.insertCell(0);
            const celdaNombre = nuevaFila.insertCell(1);
            const celdaPrecio = nuevaFila.insertCell(2);
            const celdaStock = nuevaFila.insertCell(3);
            const celdaCategoria = nuevaFila.insertCell(4);
            const celdaAcciones = nuevaFila.insertCell(5);

            // Rellenar las celdas con la información del producto
            celdaId.textContent = producto.id_productos;
            celdaNombre.textContent = producto.nombre;
            celdaPrecio.textContent = `$ ${producto.precio}`;
            celdaStock.textContent = producto.stock;
            celdaCategoria.textContent = producto.categoria_nombre;

            // Agregar botones de acción
            celdaAcciones.innerHTML = ` 
                <button onclick="editProduct(${JSON.stringify(producto)})">Editar</button>
                <form method="POST" style="display:inline">
                    <input type="hidden" name="id_productos" value="${producto.id_productos}">
                    <button type="submit" name="delete">Eliminar</button>
                </form>
            `;
        });

        // Agregar paginación
        agregarPaginacion(resultados.paginaActual, resultados.totalPaginas);
    } else {
        // Si no hay resultados, mostrar mensaje en la tabla
        const nuevaFila = tablaProductos.insertRow();
        const celdaMensaje = nuevaFila.insertCell(0);
        celdaMensaje.colSpan = 5;
        celdaMensaje.textContent = 'No se encontraron productos.';
    }
}

function agregarPaginacion(paginaActual, totalPaginas) {
    const paginacion = document.getElementById('paginacion');
    paginacion.innerHTML = ''; // Limpiar la paginación actual

    // Agregar botones de paginación
    if (paginaActual > 1) {
        const btnAnterior = document.createElement('button');
        btnAnterior.textContent = 'Anterior';
        btnAnterior.onclick = function () { cambiarPagina(paginaActual - 1); };
        paginacion.appendChild(btnAnterior);
    }

    if (paginaActual < totalPaginas) {
        const btnSiguiente = document.createElement('button');
        btnSiguiente.textContent = 'Siguiente';
        btnSiguiente.onclick = function () { cambiarPagina(paginaActual + 1); };
        paginacion.appendChild(btnSiguiente);
    }
}

function cambiarPagina(pagina) {
    const nombre = document.getElementById('nombre').value.trim();
    const precioMin = document.getElementById('precio_min').value || 0;
    const precioMax = document.getElementById('precio_max').value || 9999999;
    const categoria = document.getElementById('categoria').value;
    const ordenarPor = document.getElementById('ordenar_por').value;

    const url = `filtros.php?nombre=${encodeURIComponent(nombre)}&precio_min=${encodeURIComponent(precioMin)}&precio_max=${encodeURIComponent(precioMax)}&categoria=${encodeURIComponent(categoria)}&ordenar_por=${encodeURIComponent(ordenarPor)}&pagina=${pagina}`;

    const xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4) {
            if (xhr.status == 200) {
                const resultados = JSON.parse(xhr.responseText);
                mostrarResultados(resultados);
            } else {
                alert('Error al cargar los productos de la página.');
            }
        }
    };
    xhr.send();
}


function openAddModal() {
    document.getElementById('modal').style.display = 'block';
    document.getElementById('add-button').style.display = 'inline-block';
    document.getElementById('edit-button').style.display = 'none';
    document.getElementById('modal-title').innerText = 'Agregar Nuevo Producto';
    document.getElementById('modal-form').reset();
}

function closeModal() {
    document.getElementById('modal').style.display = 'none';
}

function editProduct(product) {
    document.getElementById('id_productos').value = product.id_productos;
    document.getElementById('modal-title').innerText = 'Editar Producto';
    document.getElementById('add-button').style.display = 'none';
    document.getElementById('edit-button').style.display = 'inline-block';
    document.querySelector("input[name='nombre']").value = product.nombre;
    document.querySelector("input[name='precio']").value = product.precio;
    document.querySelector("input[name='stock']").value = product.stock;
    document.querySelector("input[name='fecha_caducidad']").value = product.fecha_caducidad;
    document.querySelector("input[name='codigo_barra']").value = product.codigo_barra;
    document.querySelector("select[name='categoria_padre_id']").value = product.categoria_padre_id;
    document.querySelector("select[name='categoria_id']").value = product.categoria_id;
    document.getElementById('modal').style.display = 'block';
}

function cleanPriceBeforeSubmit() {
    let priceInput = document.getElementById('precio');
    let value = priceInput.value.replace(/[^0-9,]/g, "").replace(",", ".");
    priceInput.value = value;
}

function formatPrice(input) {
    let value = input.value.replace(/[^0-9,]/g, "");
    let parts = value.split(",");
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    input.value = "$" + parts.join(",");
}

function exportToExcel() {
    window.location.href = 'exportar.php?formato=excel';
}

function exportToCSV() {
    window.location.href = 'exportar.php?formato=csv';
}


