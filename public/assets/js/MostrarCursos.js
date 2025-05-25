$(document).ready(function() {
    // Cargar cursos al iniciar la página
    cargarCursosAsignados();
    cargarCursosCompletados();
});

// Función para cargar cursos asignados
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

// Función para cargar cursos completados
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

// Función para mostrar cursos asignados en la tabla
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
            </tr>
        `;
        tbody.append(fila);
    });
}

// Función para mostrar cursos completados en la tabla
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