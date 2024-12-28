function abrirModalAgregar() {
    document.getElementById("modalAgregar").style.display = "block";
}

function abrirModalEditar(local) {
    // Asegúrate de que los elementos existan antes de asignar valores
    const idLocalInput = document.getElementById('id_local');
    const tipoLocalSelect = document.getElementById('tipo_local');
    const nombreInput = document.getElementById('nombre');
    const direccionInput = document.getElementById('direccion');
    const telefonoInput = document.getElementById('telefono');
    const emailInput = document.getElementById('email');

    if (!idLocalInput || !tipoLocalSelect || !nombreInput || !direccionInput || !telefonoInput || !emailInput) {
        console.error("Uno o más elementos no existen en el DOM.");
        return;
    }

    // Asigna los valores del objeto local a los campos del formulario
    idLocalInput.value = local.id_local || '';
    tipoLocalSelect.value = local.tipo_local || '';
    nombreInput.value = local.nombre || '';
    direccionInput.value = local.direccion || '';
    telefonoInput.value = local.telefono || '';
    emailInput.value = local.email || '';

    // Muestra el modal
    document.getElementById('modalEditar').style.display = 'block';
}


// Función para cerrar el modal
function cerrarModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}

function editarLocal() {
    console.log("Iniciando la función editarLocal");

    const id_local = document.getElementById('id_local').value;
    const tipo_local = document.getElementById('tipo_local').value;
    const nombre = document.getElementById('nombre').value;
    const direccion = document.getElementById('direccion').value;
    const telefono = document.getElementById('telefono').value;
    const email = document.getElementById('email').value;

    // Verificar que los valores estén llenos
    if (!id_local || !tipo_local || !nombre || !direccion || !telefono || !email) {
        alert("Todos los campos son obligatorios");
        return;
    }

    const formData = new FormData();
    formData.append('editar', true);
    formData.append('id_local', id_local);
    formData.append('tipo_local', tipo_local);
    formData.append('nombre', nombre);
    formData.append('direccion', direccion);
    formData.append('telefono', telefono);
    formData.append('email', email);

    fetch("gestion_local.php", {
        method: "POST",
        body: formData
    })
        .then(response => {
            if (!response.ok) {
                throw new Error("Error en la respuesta del servidor");
            }
            return response.json();
        })
        .then(data => {
            console.log("Respuesta del servidor:", data);
            if (data.status === "success") {
                alert(data.message);
                cerrarModal('modalEditar');
                actualizarLocalEnUI(id_local, tipo_local, nombre, direccion, telefono, email);
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error("Error al enviar la solicitud:", error);
            alert("Hubo un problema al guardar los cambios.");
        });
}


// Función para actualizar los datos en la UI
function actualizarLocalEnUI(id_local, tipo_local, nombre, direccion, telefono, email) {
    document.getElementById(`nombre_${id_local}`).textContent = nombre;
    document.getElementById(`direccion_${id_local}`).textContent = direccion;
    document.getElementById(`telefono_${id_local}`).textContent = telefono;
    document.getElementById(`email_${id_local}`).textContent = email;
    document.getElementById(`tipo_local_${id_local}`).textContent = tipo_local;
}
