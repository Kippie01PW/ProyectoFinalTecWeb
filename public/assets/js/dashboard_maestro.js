function actualizarDato(tipo) {
    let valor, inputId;
    
    if (tipo === 'correo') {
        inputId = 'inputCorreo';
        valor = document.getElementById(inputId).value.trim();
        
        if (!valor || !validarEmail(valor)) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Por favor ingresa un correo electrónico válido'
            });
            return;
        }
    } else if (tipo === 'contrasena') {
        inputId = 'inputContrasena';
        valor = document.getElementById(inputId).value;
        
        if (!valor || valor.length < 6) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'La contraseña debe tener al menos 6 caracteres'
            });
            return;
        }
        
        if (!validarContrasena(valor)) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'La contraseña debe contener al menos una mayúscula, una minúscula y un número'
            });
            return;
        }
    }
    mostrarModalBootstrap(tipo, valor);
}

function realizarActualizacion(tipo, valor) {
    const formData = new FormData();
    formData.append('maestro_id', window.MAESTRO_ID);
    formData.append('accion', tipo);

    if (tipo === 'correo') {
        formData.append('correo', valor);
    } else if (tipo === 'contrasena') {
        formData.append('contrasena', valor);
    }

    Swal.fire({
        title: 'Actualizando...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch('../app/Controllers/Maestro_actu.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        try {
            const data = JSON.parse(text);

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Actualizado!',
                    text: data.message || 'Los datos se han actualizado correctamente',
                    timer: 2000,
                    showConfirmButton: false
                });

                if (tipo === 'contrasena') {
                    document.getElementById('inputContrasena').value = '';
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Ocurrió un error al actualizar los datos'
                });
            }
        } catch (e) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error en la respuesta del servidor'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error de conexión'
        });
    });
}

function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

function validarContrasena(password) {
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/;
    return regex.test(password);
}

function mostrarModalBootstrap(tipo, valor) {
    const modalId = 'modalActualizar';
    
    const modalHtml = `
        <div class="modal fade" id="${modalId}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar Actualización</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro de que deseas actualizar tu ${tipo}?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="confirmarBtn">Actualizar</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    let existingModal = document.getElementById(modalId);
    if (existingModal) {
        existingModal.remove();
    }

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById(modalId));
    modal.show();

    setTimeout(() => {
        const btnConfirmar = document.getElementById('confirmarBtn');
        if (btnConfirmar) {
            btnConfirmar.onclick = () => {
                modal.hide();
                realizarActualizacion(tipo, valor);
            };
        }
    }, 100);
}
