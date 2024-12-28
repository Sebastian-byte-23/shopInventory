const editModal = document.getElementById("editModal");

document.querySelectorAll(".edit-btn").forEach(function(btn) {
    btn.onclick = function() {
        const empleadoId = this.getAttribute('data-id');
        const nombre = this.getAttribute('data-nombre');
        const apellido = this.getAttribute('data-apellido');
        const tipoEmpleado = this.getAttribute('data-tipo_empleado');
        const direccion = this.getAttribute('data-direccion');
        const telefono = this.getAttribute('data-telefono');
        const email = this.getAttribute('data-email');

        // Rellenar los campos del formulario con los datos obtenidos
        document.getElementById("editEmpleadoId").value = empleadoId;
        document.getElementById("editNombre").value = nombre;
        document.getElementById("editApellido").value = apellido;
        document.getElementById("editTipoEmpleado").value = tipoEmpleado;
        document.getElementById("editDireccion").value = direccion;
        document.getElementById("editTelefono").value = telefono;
        document.getElementById("editEmail").value = email;

        // Mostrar el modal
        editModal.style.display = "block";
    };
});

// Enviar formulario de edición mediante AJAX
document.getElementById("editFormContainer").addEventListener("submit", function(event) {
    event.preventDefault();
    const formData = new FormData(this);

    fetch('editar_empleado.php?id=' + formData.get('empleadoId'), {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            if (data.includes("Empleado actualizado correctamente")) {
                editModal.style.display = "none";
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
});

// Función para eliminar empleado con AJAX
document.querySelectorAll(".delete-btn").forEach(function(btn) {
    btn.addEventListener("click", function() {
        const empleadoId = this.getAttribute('data-id');

        fetch('eliminar_empleado.php?id=' + empleadoId, {
                method: 'GET'
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                location.reload();
            })
            .catch(error => console.error('Error:', error));
    });
});