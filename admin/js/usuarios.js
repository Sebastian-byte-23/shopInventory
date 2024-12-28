// Abrir el modal de agregar usuario
function openModal() {
    document.getElementById('myModal').style.display = "block";
}

// Cerrar el modal
function closeModal() {
    document.getElementById('myModal').style.display = "none";
}

// Abrir el modal de editar usuario
function openEditUserModal(id, usuario, tipoEmpleado) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_user').value = usuario;
    document.getElementById('edit_tipo_empleado').value = tipoEmpleado;
    document.getElementById('editUserModal').style.display = "block";
}

// Cerrar el modal de editar usuario
function hideModal(modalId) {
    document.getElementById(modalId).style.display = "none";
}



// Función para abrir y cerrar el modal
function openModal() {
    document.getElementById("myModal").style.display = "block";
}

function closeModal() {
    document.getElementById("myModal").style.display = "none";
}

// Función para actualizar el tipo de empleado cuando se selecciona un empleado
function updateTipoEmpleado() {
    const empleadoSelect = document.getElementById('empleado_id');
    const tipoEmpleadoInput = document.getElementById('tipo_empleado');
    const selectedOption = empleadoSelect.options[empleadoSelect.selectedIndex];
    const tipoEmpleado = selectedOption.getAttribute('data-tipo');
    tipoEmpleadoInput.value = tipoEmpleado || '';
}

// Función para eliminar usuario con AJAX
document.querySelectorAll(".delete-btn").forEach(function (btn) {
    btn.addEventListener("click", function () {
        const userId = this.getAttribute('data-id');

        fetch('eliminar_usuario.php?id=' + userId, {
            method: 'POST'
        })
            .then(response => response.text())
            .then(data => {
                alert(data);
                location.reload();
            })
            .catch(error => console.error('Error:', error));
    });
});

// Función para redirigir al listado de empleados
document.getElementById("viewEmployeesBtn").onclick = function () {
    window.location.href = "empleados.php"; // Redirige a empleados.php donde puedas mostrar la lista de empleados
};

// Función para formatear el RUT
function formatRUT(input) {
    // Remueve puntos y guion para formatear desde cero
    let rut = input.value.replace(/\D/g, '');

    // Si el RUT tiene más de 1 carácter, aplica los puntos y el guion
    if (rut.length > 1) {
        rut = rut.slice(0, -1)
            .replace(/\B(?=(\d{3})+(?!\d))/g, ".") + '-' + rut.slice(-1);
    }

    // Actualiza el valor del campo de entrada con el RUT formateado
    input.value = rut;
}

// Función para abrir y cerrar el modal de agregar empleado
const addEmployeeModal = document.getElementById("addEmployeeModal");

document.getElementById("addEmployeeBtn").onclick = function () {
    addEmployeeModal.style.display = "block";
};

window.onclick = function (event) {
    if (event.target == addEmployeeModal) {
        addEmployeeModal.style.display = "none";
    }
};

// Función para abrir el modal de edición y cargar los datos del usuario
document.querySelectorAll('.edit-btn').forEach(button => {
    button.onclick = function () {
        const userId = this.getAttribute('data-id');
        const userName = this.getAttribute('data-user');
        const userType = this.getAttribute('data-tipo_empleado');

        document.getElementById('edit_id').value = userId;
        document.getElementById('edit_user').value = userName;
        document.getElementById('edit_tipo_empleado').value = userType;

        showModal('editUserModal');
    };
});

// Función para cerrar el modal
function hideModal(modalId) {
    document.getElementById(modalId).style.display = "none";
}

// Función para mostrar el modal
function showModal(modalId) {
    document.getElementById(modalId).style.display = "block";
}

// Cerrar el modal si se hace clic fuera de él
window.onclick = function (event) {
    const modal = document.getElementById("editUserModal");
    if (event.target === modal) {
        hideModal("editUserModal");
    }
};

document.getElementById('editUserForm').addEventListener('submit', function (event) {
    event.preventDefault(); // Prevenir el envío del formulario de forma tradicional

    // Obtener los datos del formulario
    const formData = new FormData(this); // Recoge todos los datos del formulario

    fetch('cambiar_clave.php', {
        method: 'POST',
        body: formData, // Enviar los datos del formulario a cambiar_clave.php
    })
    .then(response => response.json()) // Parsear la respuesta como JSON
    .then(data => {
        const messageContainer = document.getElementById('message'); // Contenedor del mensaje
        const messageText = document.getElementById('messageText'); // Elemento para mostrar el mensaje

        // Verificar si la operación fue exitosa o no
        if (data.success) {
            messageText.textContent = data.message; // Mostrar el mensaje de éxito
            messageContainer.classList.add('success'); // Aplicar estilo de éxito
            messageContainer.classList.remove('error'); // Eliminar estilo de error
        } else {
            messageText.textContent = data.message; // Mostrar el mensaje de error
            messageContainer.classList.add('error'); // Aplicar estilo de error
            messageContainer.classList.remove('success'); // Eliminar estilo de éxito
        }

        // Mostrar el mensaje
        messageContainer.style.display = 'block';

        // Opcional: Ocultar el mensaje después de 5 segundos
        setTimeout(() => {
            messageContainer.style.display = 'none';
        }, 5000); // 5 segundos
    })
    .catch(error => {
        console.error('Error:', error); // En caso de error en la solicitud
    });
});
