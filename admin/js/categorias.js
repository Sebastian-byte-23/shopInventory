// Función para expandir/contraer subcategorías
function toggleSubcategorias(categoriaId) {
    const subcategorias = document.getElementById('subcategorias-' + categoriaId);
    const toggleBtn = document.querySelector('#categoria-' + categoriaId + ' .toggle-btn');

    if (subcategorias.style.display === "none" || subcategorias.style.display === "") {
        subcategorias.style.display = "block";  // Muestra las subcategorías
        toggleBtn.textContent = "-";  // Cambia el texto del botón a "-"
    } else {
        subcategorias.style.display = "none";  // Oculta las subcategorías
        toggleBtn.textContent = "+";  // Cambia el texto del botón a "+"
    }
}

// Asegurarse de que las subcategorías estén cerradas al cargar la página
window.onload = function() {
    const categorias = document.querySelectorAll('.categoria');  // Busca todas las categorías
    categorias.forEach(categoria => {
        const subcategorias = categoria.querySelector('.subcategorias');
        if (subcategorias) {
            subcategorias.style.display = "none";  // Asegura que las subcategorías estén cerradas
        }
    });
};



// Modal para agregar categoría
const modal = document.getElementById("myModal");
const openModalBtn = document.getElementById("openModalBtn");
const closeModalBtn = document.getElementById("closeModalBtn");

openModalBtn.onclick = function() {
    modal.style.display = "block";
}

closeModalBtn.onclick = function() {
    modal.style.display = "none";
}

window.onclick = function(event) {
    if (event.target === modal) {
        modal.style.display = "none";
    }
}

// Modal para agregar categoría padre
const modalPadre = document.getElementById("myModalPadre");
const openModalPadreBtn = document.getElementById("openModalPadreBtn");
const closeModalPadreBtn = document.getElementById("closeModalPadreBtn");

openModalPadreBtn.onclick = function() {
    modalPadre.style.display = "block";
}

closeModalPadreBtn.onclick = function() {
    modalPadre.style.display = "none";
}

window.onclick = function(event) {
    if (event.target === modalPadre) {
        modalPadre.style.display = "none";
    }
}
