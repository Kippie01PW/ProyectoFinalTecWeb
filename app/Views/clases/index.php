<?php include __DIR__ . '/../layouts/header_maestro.php'; ?>

<div class="container">
    <h2>Gestión de Clases</h2>
    
    <!-- Formulario para crear nueva clase -->
    <div class="card mb-4">
        <div class="card-header">
            <h4>Crear Nueva Clase</h4>
        </div>
        <div class="card-body">
            <form id="formClase">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre de la Clase *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Cursos a Asignar</label>
                            <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                                <div class="mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="seleccionarTodosCursos()">
                                        Seleccionar Todos
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="limpiarCursos()">
                                        Limpiar
                                    </button>
                                </div>
                                <?php if (!empty($cursos)): ?>
                                    <?php foreach ($cursos as $curso): ?>
                                        <div class="form-check">
                                            <input class="form-check-input curso-checkbox" type="checkbox" 
                                                   name="cursos[]" value="<?= $curso['id'] ?>" 
                                                   id="curso_<?= $curso['id'] ?>">
                                            <label class="form-check-label" for="curso_<?= $curso['id'] ?>">
                                                <small>
                                                    <strong><?= htmlspecialchars($curso['titulo']) ?></strong>
                                                    <?php if ($curso['categoria_nombre']): ?>
                                                        <br><span class="text-muted"><?= htmlspecialchars($curso['categoria_nombre']) ?></span>
                                                    <?php endif; ?>
                                                </small>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted">No hay cursos disponibles</p>
                                <?php endif; ?>
                            </div>
                            <small class="text-info">Selecciona los cursos que quieres asignar a esta clase</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Alumnos a Asignar</label>
                            <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                                <div class="mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="seleccionarTodosAlumnos()">
                                        Seleccionar Todos
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="limpiarAlumnos()">
                                        Limpiar
                                    </button>
                                </div>
                                <?php if (!empty($alumnos)): ?>
                                    <?php foreach ($alumnos as $alumno): ?>
                                        <div class="form-check">
                                            <input class="form-check-input alumno-checkbox" type="checkbox" 
                                                   name="alumnos[]" value="<?= $alumno['id'] ?>" 
                                                   id="alumno_<?= $alumno['id'] ?>">
                                            <label class="form-check-label" for="alumno_<?= $alumno['id'] ?>">
                                                <small>
                                                    <strong><?= htmlspecialchars($alumno['nombre']) ?></strong>
                                                    <br><span class="text-muted"><?= htmlspecialchars($alumno['email']) ?></span>
                                                </small>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted">No hay alumnos disponibles</p>
                                <?php endif; ?>
                            </div>
                            <small class="text-info">Selecciona los alumnos que quieres inscribir en esta clase</small>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Crear Clase
                </button>
                <button type="reset" class="btn btn-secondary" onclick="limpiarFormulario()">
                    <i class="bi bi-arrow-clockwise"></i> Limpiar Todo
                </button>
                
                <!-- Contador de selecciones -->
                <div class="mt-2">
                    <small class="text-muted">
                        <span id="contadorCursos">0 cursos seleccionados</span> | 
                        <span id="contadorAlumnos">0 alumnos seleccionados</span>
                    </small>
                </div>
            </form>
            
            <div id="mensaje" class="mt-3"></div>
        </div>
    </div>
    
    <!-- Lista de clases existentes -->
    <div class="card">
        <div class="card-header">
            <h4>Mis Clases</h4>
        </div>
        <div class="card-body">
            <?php if (empty($clases)): ?>
                <div class="alert alert-info">
                    No tienes clases creadas. Usa el formulario de arriba para crear tu primera clase.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Código</th>
                                <th>Descripción</th>
                                <th>Alumnos</th>
                                <th>Cursos</th>
                                <th>Creada</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaClases">
                            <?php foreach ($clases as $clase): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($clase['nombre']) ?></strong>
                                    </td>
                                    <td>
                                        <code><?= htmlspecialchars($clase['codigo']) ?></code>
                                        <button class="btn btn-sm btn-outline-secondary ms-1" 
                                                onclick="copiarCodigo('<?= $clase['codigo'] ?>')">
                                                copiar
                                        </button>
                                    </td>
                                    <td><?= htmlspecialchars($clase['descripcion'] ?? 'Sin descripción') ?></td>
                                    <td>
                                        <span class="badge bg-primary"><?= $clase['total_alumnos'] ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success"><?= $clase['total_cursos'] ?></span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($clase['created_at'])) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info me-1" 
                                                onclick="verDetalles(<?= $clase['id'] ?>, '<?= htmlspecialchars($clase['nombre']) ?>')">
                                                Ver
                                        </button>
                                        <a href="/ProyectoFinalTecWeb/public/clases/editar/<?= $clase['id'] ?>" 
                                           class="btn btn-sm btn-warning">
                                           Editar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para mostrar detalles de la clase -->
<div class="modal fade" id="modalDetalles" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitulo">Detalles de la Clase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="contenidoModal">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p>Cargando detalles...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Sección de Evidencias -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-camera me-2"></i>
                                Evidencias de Cursos Completados
                            </h5>
                            
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="evidencias-contenido">
                            <div class="text-center p-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p class="mt-2">Cargando evidencias...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- Modal para ver evidencia -->
    <div class="modal fade" id="evidenciaModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Evidencia del Curso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="evidencia-img" class="img-fluid" src="" alt="Evidencia" style="max-height: 500px;">
                    <div class="mt-3">
                        <h6 id="evidencia-alumno"></h6>
                        <p id="evidencia-curso" class="text-muted"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function cargarEvidencias() {
            document.getElementById('evidencias-contenido').innerHTML = `
                <div class="text-center p-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando evidencias...</p>
                </div>
            `;

            fetch('/ProyectoFinalTecWeb/public/api/clases/evidencias')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.evidencias && data.evidencias.length > 0) {
                        mostrarEvidencias(data.evidencias);
                    } else {
                        mostrarSinEvidencias();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarErrorEvidencias();
                });
        }

        function mostrarEvidencias(evidencias) {
            const html = `
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Alumno</th>
                                <th>Curso</th>
                                <th>Clase</th>
                                <th>Fecha Completado</th>
                                <th>Evidencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${evidencias.map(evidencia => `
                                <tr>
                                    <td><strong>${evidencia.alumno_nombre}</strong></td>
                                    <td>${evidencia.curso_titulo}</td>
                                    <td><span class="badge bg-primary">${evidencia.clase_nombre}</span></td>
                                    <td>${new Date(evidencia.fecha_completado).toLocaleDateString('es-ES')}</td>
                                    <td>
                                        <img src="${evidencia.evidencia}" 
                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; cursor: pointer;" 
                                             onclick="verEvidencia('${evidencia.evidencia}', '${evidencia.alumno_nombre}', '${evidencia.curso_titulo}')"
                                             alt="Evidencia">
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <small class="text-muted">Total de evidencias: <strong>${evidencias.length}</strong></small>
                </div>
            `;
            
            document.getElementById('evidencias-contenido').innerHTML = html;
        }

        function mostrarSinEvidencias() {
            document.getElementById('evidencias-contenido').innerHTML = `
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i>
                    No hay evidencias de cursos completados disponibles.
                </div>
            `;
        }

        function mostrarErrorEvidencias() {
            document.getElementById('evidencias-contenido').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error al cargar las evidencias. 
                    <button class="btn btn-sm btn-outline-danger ms-2" onclick="cargarEvidencias()">
                        Intentar de nuevo
                    </button>
                </div>
            `;
        }

        function verEvidencia(src, alumno, curso) {
            document.getElementById('evidencia-img').src = src;
            document.getElementById('evidencia-alumno').textContent = alumno;
            document.getElementById('evidencia-curso').textContent = curso;
            
            const modal = new bootstrap.Modal(document.getElementById('evidenciaModal'));
            modal.show();
        }

        // Cargar evidencias al iniciar la página
        document.addEventListener('DOMContentLoaded', function() {
            cargarEvidencias();
        });
    </script>


<script>
// Funciones para manejar selecciones múltiples
function seleccionarTodosCursos() {
    const checkboxes = document.querySelectorAll('.curso-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    actualizarContadores();
}

function limpiarCursos() {
    const checkboxes = document.querySelectorAll('.curso-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    actualizarContadores();
}

function seleccionarTodosAlumnos() {
    const checkboxes = document.querySelectorAll('.alumno-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    actualizarContadores();
}

function limpiarAlumnos() {
    const checkboxes = document.querySelectorAll('.alumno-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    actualizarContadores();
}

function limpiarFormulario() {
    document.getElementById('formClase').reset();
    limpiarCursos();
    limpiarAlumnos();
    actualizarContadores();
}

function actualizarContadores() {
    const cursosSeleccionados = document.querySelectorAll('.curso-checkbox:checked').length;
    const alumnosSeleccionados = document.querySelectorAll('.alumno-checkbox:checked').length;
    
    document.getElementById('contadorCursos').textContent = `${cursosSeleccionados} cursos seleccionados`;
    document.getElementById('contadorAlumnos').textContent = `${alumnosSeleccionados} alumnos seleccionados`;
}

// Event listeners para actualizar contadores
document.addEventListener('DOMContentLoaded', function() {
    // Agregar listeners a todos los checkboxes
    document.querySelectorAll('.curso-checkbox, .alumno-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', actualizarContadores);
    });
    
    // Actualizar contadores iniciales
    actualizarContadores();
});

// Manejar formulario de crear clase
document.getElementById('formClase').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validar que al menos se haya puesto un nombre
    const nombre = document.getElementById('nombre').value.trim();
    if (!nombre) {
        document.getElementById('mensaje').innerHTML = 
            '<div class="alert alert-warning">El nombre de la clase es obligatorio</div>';
        return;
    }
    
    // Mostrar información de lo que se va a crear
    const cursosSeleccionados = document.querySelectorAll('.curso-checkbox:checked').length;
    const alumnosSeleccionados = document.querySelectorAll('.alumno-checkbox:checked').length;
    
    if (cursosSeleccionados === 0 && alumnosSeleccionados === 0) {
        const confirmar = confirm('¿Crear clase sin cursos ni alumnos asignados?\n\nPodrás agregarlos después o los alumnos pueden unirse con el código.');
        if (!confirmar) return;
    }
    
    const formData = new FormData(this);
    
    // Mostrar mensaje de carga
    document.getElementById('mensaje').innerHTML = 
        '<div class="alert alert-info">Creando clase...</div>';
    
    fetch('/ProyectoFinalTecWeb/public/api/clases/crear', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const mensaje = document.getElementById('mensaje');
        if (data.success) {
            mensaje.innerHTML = `
                <div class="alert alert-success">
                    <strong>Clase creada exitosamente!</strong>
                    <br>Código de la clase: <strong>${data.codigo}</strong>
                    <br>Cursos asignados: <strong>${data.cursos_asignados || 0}</strong>
                    <br>Alumnos inscritos: <strong>${data.alumnos_asignados || 0}</strong>
                    <br>Relaciones alumno-curso creadas: <strong>${data.relaciones_creadas || 0}</strong>
                    <br><small class="text-muted">¡La tabla alumnocurso se llenó automáticamente!</small>
                </div>
            `;
            limpiarFormulario();
            
            // Recargar la página para mostrar la nueva clase en la tabla
            setTimeout(() => {
                location.reload();
            }, 3000);
        } else {
            mensaje.innerHTML = `<div class="alert alert-danger">Error: ${data.error}</div>`;
        }
    })
    .catch(error => {
        document.getElementById('mensaje').innerHTML = 
            `<div class="alert alert-danger">Error: ${error.message}</div>`;
    });
});

// Función para copiar código
function copiarCodigo(codigo) {
    navigator.clipboard.writeText(codigo).then(function() {
        // Crear notificación temporal
        const toast = document.createElement('div');
        toast.className = 'alert alert-success position-fixed';
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 250px;';
        toast.innerHTML = `Código copiado: <strong>${codigo}</strong>`;
        document.body.appendChild(toast);
        
        // Remover después de 2 segundos
        setTimeout(() => {
            toast.remove();
        }, 2000);
    }).catch(function(err) {
        alert('Error al copiar el código');
        console.error('Error al copiar: ', err);
    });
}

// Función para ver detalles de la clase
function verDetalles(claseId, nombreClase) {
    const modal = new bootstrap.Modal(document.getElementById('modalDetalles'));
    document.getElementById('modalTitulo').textContent = `Detalles de: ${nombreClase}`;
    
    // Mostrar modal con loading
    modal.show();
    
    // Debug: mostrar la URL que se está llamando
    const url = `/ProyectoFinalTecWeb/public/api/clases/detalles/${claseId}`;
    console.log('Llamando a URL:', url);
    
    // Cargar detalles via AJAX
    fetch(url)
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response ok:', response.ok);
            
            // Si la respuesta no es OK, mostrar el error
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos:', data);
            
            let contenido = '';
            
            if (data.error) {
                contenido = `<div class="alert alert-danger">Error: ${data.error}</div>`;
            } else {
                contenido = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Alumnos Inscritos (${data.alumnos.length})</h6>
                            ${data.alumnos.length > 0 ? `
                                <div class="list-group mb-3">
                                    ${data.alumnos.map(alumno => `
                                        <div class="list-group-item">
                                            <strong>${alumno.nombre}</strong>
                                            <br><small class="text-muted">${alumno.email}</small>
                                            <br><small class="text-info">Inscrito: ${new Date(alumno.fecha_inscripcion).toLocaleDateString()}</small>
                                        </div>
                                    `).join('')}
                                </div>
                            ` : '<p class="text-muted">No hay alumnos inscritos</p>'}
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-success">Cursos Asignados (${data.cursos.length})</h6>
                            ${data.cursos.length > 0 ? `
                                <div class="list-group mb-3">
                                    ${data.cursos.map(curso => `
                                        <div class="list-group-item">
                                            <strong>${curso.titulo}</strong>
                                            ${curso.categoria_nombre ? `<br><small class="text-muted">Categoría: ${curso.categoria_nombre}</small>` : ''}
                                            ${curso.descripcion ? `<br><small>${curso.descripcion}</small>` : ''}
                                        </div>
                                    `).join('')}
                                </div>
                            ` : '<p class="text-muted">No hay cursos asignados</p>'}
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">${data.estadisticas.total_alumnos}</h5>
                                    <p class="card-text">Total Alumnos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title text-success">${data.estadisticas.total_cursos}</h5>
                                    <p class="card-text">Total Cursos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title text-warning">${data.estadisticas.total_asignaciones}</h5>
                                    <p class="card-text">Asignaciones</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title text-info">${data.estadisticas.cursos_completados}</h5>
                                    <p class="card-text">Completados</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            document.getElementById('contenidoModal').innerHTML = contenido;
        })
        .catch(error => {
            console.error('Error completo:', error);
            document.getElementById('contenidoModal').innerHTML = 
                `<div class="alert alert-danger">
                    <strong>Error al cargar los detalles:</strong><br>
                    ${error.message}<br><br>
                    <small>Revisa la consola del navegador (F12) para más detalles</small>
                </div>`;
        });
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>