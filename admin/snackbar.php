<!-- HTML: Estructura del Snackbar -->
<div id="snackbar">¡Atención! Hay productos con bajo stock.</div>



<!-- JavaScript para mostrar el Snackbar -->
<script>
    function showSnackbar() {
        var snackbar = document.getElementById("snackbar");
        snackbar.classList.add("show");  // Añadir la clase "show"
        setTimeout(function() {
            snackbar.classList.remove("show");  // Eliminar la clase "show"
        }, 3000);  // 3 segundos
    }

    var productosBajoStock = <?php echo json_encode(!empty($productos_bajo_stock)); ?>;
    if (productosBajoStock) {
        showSnackbar();
    }
</script>
