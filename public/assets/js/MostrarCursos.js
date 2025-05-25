$(document).ready(function() {
    cargarCursosAsignados();
    cargarCursosCompletados();
});

function cargarCursosAsignados() {
    $.ajax({
        url: '/ProyectoFinalTecWeb/public/api/alumnos/cursos/mostrar/asignados',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            $('#loading-asignados').hide();
            
            if (response.success && response.data.length > 0) {
                mostrarCursosAsignados(response.data);
                $('#tabla-asignados').show();
            } else {
                $('#tabla-asignados').hide();
                $('#loading-asignados').html('<p class="alert-info">No tienes cursos asignados actualmente.</p>').show();
            }
        },
        error: function(xhr, status, error) {
            $('#loading-asignados').hide();
            console.error('Error al cargar cursos asignados:', error);
            $('#loading-asignados').html('<p class="alert-danger">Error al cargar los cursos asignados.</p>').show();
        }
    });
}

function cargarCursosCompletados() {
    $.ajax({
        url: '/ProyectoFinalTecWeb/public/api/alumnos/cursos/mostrar/completados',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            $('#loading-completados').hide();
            
            if (response.success && response.data.length > 0) {
                mostrarCursosCompletados(response.data);
                $('#tabla-completados').show();
            } else {
                $('#tabla-completados').hide();
                $('#loading-completados').html('<p class="alert-info">No has completado cursos aún.</p>').show();
            }
        },
        error: function(xhr, status, error) {
            $('#loading-completados').hide();
            console.error('Error al cargar cursos completados:', error);
            $('#loading-completados').html('<p class="alert-danger">Error al cargar los cursos completados.</p>').show();
        }
    });
}

function mostrarCursosAsignados(cursos) {
    const tbody = $('#tabla-asignados tbody');
    tbody.empty();
    
    cursos.forEach(function(curso) {
        const fila = `
            <tr>
                <td>${curso.titulo}</td>
                <td>${curso.descripcion || 'Sin descripción'}</td>
                <td>${curso.categoria || 'Sin categoría'}</td>
                <td>
                    ${curso.enlace_externo ? 
                        `<a href="${curso.enlace_externo}" target="_blank" class="btn-primary">Ir al Curso</a>` : 
                        '<span class="text-muted">Sin enlace</span>'
                    }
                </td>
                <td>
                    <button onclick="mostrarModalEvidencia(${curso.asignacion_id})" class="btn-success">
                        Enviar Evidencia
                    </button>
                </td>
            </tr>
        `;
        tbody.append(fila);
    });
}

function mostrarCursosCompletados(cursos) {
    const tbody = $('#tabla-completados tbody');
    tbody.empty();
    
    cursos.forEach(function(curso) {
        const fechaCompletado = new Date(curso.fecha_completado).toLocaleDateString('es-ES');
        
        const fila = `
            <tr>
                <td>${curso.titulo}</td>
                <td>${fechaCompletado}</td>
                <td>
                    ${curso.evidencia ? 
                        `<a href="${curso.evidencia}" target="_blank" class="btn-info">Ver Evidencia</a>` : 
                        '<span class="text-muted">Sin evidencia</span>'
                    }
                </td>
            </tr>
        `;
        tbody.append(fila);
    });
}

function mostrarModalEvidencia(asignacionId) {
    const modal = `
        <div id="modal-evidencia" class="modal-overlay">
            <div class="modal-content">
                <h3>Subir Evidencia</h3>
                <form id="form-evidencia" enctype="multipart/form-data">
                    <input type="hidden" id="asignacion_id" value="${asignacionId}">
                    <div class="form-group">
                        <label for="evidencia">Seleccionar imagen:</label>
                        <input type="file" id="evidencia" name="evidencia" accept="image/*" required>
                        <small>Solo se permiten archivos de imagen (JPEG, PNG, GIF, WebP)</small>
                    </div>
                    <div class="form-buttons">
                        <button type="submit" class="btn-primary">Subir Evidencia</button>
                        <button type="button" onclick="cerrarModal()" class="btn-secondary">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    $('body').append(modal);
    
    $('#form-evidencia').on('submit', function(e) {
        e.preventDefault();
        subirEvidencia();
    });
}

function subirEvidencia() {
    const formData = new FormData();
    const fileInput = document.getElementById('evidencia');
    const asignacionId = document.getElementById('asignacion_id').value;
    
    if (!fileInput.files[0]) {
        alert('Por favor selecciona una imagen');
        return;
    }
    
    formData.append('evidencia', fileInput.files[0]);
    formData.append('asignacion_id', asignacionId);
    
    $.ajax({
        url: '/ProyectoFinalTecWeb/public/api/alumnos/cursos/evidencia',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                alert('Evidencia subida correctamente. El curso ha sido marcado como completado.');
                cerrarModal();
                // Recargar ambas tablas
                cargarCursosAsignados();
                cargarCursosCompletados();
            } else {
                alert('Error: ' + response.error);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al subir evidencia:', error);
            alert('Error al subir la evidencia');
        }
    });
}

function cerrarModal() {
    $('#modal-evidencia').remove();
}