
import './style.css';

const API_URL = '/api/users';

// Elementos de nuestra interfaz
const form = document.getElementById('user-form');
const nameInput = document.getElementById('name');
const emailInput = document.getElementById('email');
const usersTable = document.getElementById('users-table');
const message = document.getElementById('message');
const formTitle = document.getElementById('form-title');
const saveButton = document.getElementById('save-button');
const cancelButton = document.getElementById('cancel-button');

// Identificador del usuario que estamos editando
let editingId = null;

// Mostrar mensajes al usuario
function showMessage(text, isError = false) {
    message.textContent = text;
    message.style.color = isError ? '#dc2626' : '#16a34a';
}

// Restablecer el formulario
function resetForm() {
    form.reset();

    editingId = null;

    formTitle.textContent = 'Registrar usuario';
    saveButton.textContent = 'Guardar usuario';
    cancelButton.hidden = true;
}

// Consultar y mostrar los usuarios
async function loadUsers() {
    try {
        const response = await fetch(API_URL);

        if (!response.ok) {
            throw new Error('No se pudieron consultar los usuarios');
        }

        const users = await response.json();

        usersTable.innerHTML = '';

        users.forEach(user => {
            const row = document.createElement('tr');

            const fields = [
                user.id,
                user.name,
                user.email,
                user.created_at
            ];

            fields.forEach(value => {
                const cell = document.createElement('td');

                cell.textContent = value;

                row.appendChild(cell);
            });

            // Celda de acciones
            const actionsCell = document.createElement('td');

            // Botón editar
            const editButton = document.createElement('button');

            editButton.textContent = 'Editar';

            editButton.addEventListener('click', () => {
                editUser(user);
            });

            // Botón eliminar
            const deleteButton = document.createElement('button');

            deleteButton.textContent = 'Eliminar';
            deleteButton.classList.add('delete');

            deleteButton.addEventListener('click', () => {
                deleteUser(user.id);
            });

            actionsCell.appendChild(editButton);
            actionsCell.appendChild(deleteButton);

            row.appendChild(actionsCell);

            usersTable.appendChild(row);
        });

    } catch (error) {
        showMessage(error.message, true);
    }
}

// Preparar el formulario para editar un usuario
function editUser(user) {
    editingId = user.id;

    nameInput.value = user.name;
    emailInput.value = user.email;

    formTitle.textContent = 'Editar usuario';
    saveButton.textContent = 'Guardar cambios';

    cancelButton.hidden = false;

    showMessage('');

    nameInput.focus();
}

// Crear o actualizar un usuario
form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const name = nameInput.value.trim();
    const email = emailInput.value.trim();

    if (!name || !email) {
        showMessage('Completa todos los campos', true);
        return;
    }

    const isEditing = editingId !== null;

    const url = isEditing
        ? `${API_URL}/${editingId}`
        : API_URL;

    const method = isEditing ? 'PUT' : 'POST';

    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name: name,
                email: email
            })
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.error || 'Error al guardar usuario');
        }

        resetForm();

        await loadUsers();

        showMessage(
            isEditing
                ? 'Usuario actualizado correctamente'
                : 'Usuario creado correctamente'
        );

    } catch (error) {
        showMessage(error.message, true);
    }
});

// Eliminar un usuario
async function deleteUser(id) {
    const confirmed = confirm(
        '¿Estás seguro de que deseas eliminar este usuario?'
    );

    if (!confirmed) {
        return;
    }

    try {
        const response = await fetch(`${API_URL}/${id}`, {
            method: 'DELETE'
        });

        if (!response.ok) {
            const result = await response.json();
            throw new Error(result.error || 'Error al eliminar usuario');
        }

        if (editingId === id) {
            resetForm();
        }

        await loadUsers();

        showMessage('Usuario eliminado correctamente');

    } catch (error) {
        showMessage(error.message, true);
    }
}

// Cancelar la edición
cancelButton.addEventListener('click', () => {
    resetForm();
    showMessage('');
});

// Cargar los usuarios al abrir la página
loadUsers();
