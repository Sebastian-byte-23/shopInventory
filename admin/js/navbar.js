// Obtén todos los enlaces de la barra de navegación
const navLinks = document.querySelectorAll('nav ul li a');

// Recorrer todos los enlaces y agregar un listener para añadir la clase 'active'
navLinks.forEach(link => {
    link.addEventListener('click', function() {
        // Eliminar la clase 'active' de todos los enlaces
        navLinks.forEach(link => link.classList.remove('active'));
        
        // Añadir la clase 'active' al enlace clicado
        this.classList.add('active');
    });
});
