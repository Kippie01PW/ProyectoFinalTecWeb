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
                                            📋
                                        </button>
                                    </td>
                                    <td><?= htmlspecialchars($clase['descripcion'] ?? 'Sin descripción') ?></td>
                                    <td>
                                        <span class="badge bg-primary"><?= $clase['total_alumnos'] ?></span>
                                        <?php if (!empty($clase['alumnos'])): ?>
                                            <br><small class="text-muted">
                                                <?php foreach ($clase['alumnos'] as $alumno): ?>
                                                    • <?= htmlspecialchars($alumno['nombre']) ?><br>
                                                <?php endforeach; ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-success"><?= $clase['total_cursos'] ?></span>
                                        <?php if (!empty($clase['cursos'])): ?>
                                            <br><small class="text-muted">
                                                <?php foreach ($clase['cursos'] as $curso): ?>
                                                    • <?= htmlspecialchars($curso['titulo']) ?><br>
                                                <?php endforeach; ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($clase['created_at'])) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info" 
                                                onclick="verDetalles(<?= $clase['id'] ?>)">
                                            Ver
                                        </button>
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
            '<div class="alert alert-warning">⚠️ El nombre de la clase es obligatorio</div>';
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
        '<div class="alert alert-info">⏳ Creando clase...</div>';
    
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
                    ✅ <strong>Clase creada exitosamente!</strong>
                    <br>📋 <strong>Código de la clase: ${data.codigo}</strong>
                    <br>📚 <strong>${data.cursos_asignados || 0} cursos asignados</strong>
                    <br>👥 <strong>${data.alumnos_asignados || 0} alumnos inscritos</strong>
                    <br>🔗 <strong>${data.relaciones_creadas || 0} relaciones alumno-curso creadas</strong>
                    <br><small class="text-muted">¡La tabla alumnocurso se llenó automáticamente!</small>
                </div>
            `;
            limpiarFormulario();
            
            // Recargar la página para mostrar la nueva clase en la tabla
            setTimeout(() => {
                location.reload();
            }, 3000);
        } else {
            mensaje.innerHTML = `<div class="alert alert-danger">❌ ${data.error}</div>`;
        }
    })
    .catch(error => {
        document.getElementById('mensaje').innerHTML = 
            `<div class="alert alert-danger">❌ Error: ${error.message}</div>`;
    });
});

// Función para copiar código
function copiarCodigo(codigo) {
    navigator.clipboard.writeText(codigo).then(function() {
        // Crear notificación temporal
        const toast = document.createElement('div');
        toast.className = 'alert alert-success position-fixed';
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 250px;';
        toast.innerHTML = `✅ Código copiado: <strong>${codigo}</strong>`;
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

// Función para ver detalles (placeholder)
function verDetalles(claseId) {
    alert('Función de ver detalles de clase ID: ' + claseId + ' - Por implementar');
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>