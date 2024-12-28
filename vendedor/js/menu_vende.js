document.getElementById("codigo_barra").addEventListener("input", function(event) {
    const codigoBarra = event.target.value.trim();

    // Ejecutar cuando el código tenga exactamente 13 caracteres
    if (codigoBarra.length === 13) {
        buscarProductoPorCodigo(codigoBarra);
        event.target.value = ""; // Limpiar el campo de código de barras después de buscar
    }
});


function buscarProductoPorCodigo(codigoBarra) {
    // Hacer una solicitud AJAX al servidor
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "buscar_producto.php?codigo_barra=" + encodeURIComponent(codigoBarra), true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);

            if (response.error) {
                alert(response.error); // Mostrar error si no se encuentra el producto
            } else {
                // Crear un nuevo contenedor para el producto
                const productosDiv = document.getElementById('productos');
                const index = productosDiv.childElementCount; // Obtiene el número de productos en el formulario

                const productoHTML = `
                    <div class="producto-item" id="producto-${index}">
                        <div class="field">
                            <label>Producto:</label>
                            <select name="producto_id[]" onchange="actualizarPrecio(this, ${index})" required>
                                <option value="${response.id_productos}" data-precio="${response.precio}">
                                    ${response.nombre}
                                </option>   
                            </select>
                        </div>
                        <div class="field">
                            <label>Cantidad:</label>
                            <input type="number" name="cantidad[]" min="1" required>
                        </div>
                        <div class="field">
                            <label>Precio Unitario:</label>
                            <input type="number" name="precio_unitario[]" readonly value="${response.precio}">
                        </div>
                        <button type="button" onclick="eliminarProducto(${index})" class="btn-e">Eliminar</button>
                    </div>
                `;
                // Insertar el nuevo bloque en el contenedor
                productosDiv.insertAdjacentHTML('beforeend', productoHTML);
            }
        }
    };
    xhr.send();
}

function eliminarProducto(index) {
    const productoDiv = document.getElementById(`producto-${index}`);
    if (productoDiv) {
        productoDiv.remove();
    }
}
