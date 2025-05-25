<?php include __DIR__ . '/../layouts/header_maestro.php'; ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Editar Clase: <?= htmlspecialchars($clase['nombre']) ?></h2>
        <a href="/ProyectoFinalTecWeb/public/clases/" class="btn btn-secondary">← Volver a Clases</a>
    </div>
    
    <div class="row">
        <!-- Formulario de datos básicos -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>Información Básica</h5>
                </div>
                <div class="card-body">
                    <form id="formBasico">
                        <input type="hidden" name="clase_id" value="<?= $clase['id'] ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre de la Clase</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                           value="<?= htmlspecialchars($clase['nombre']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="2"><?= htmlspecialchars($clase['descripcion'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Actualizar Información</button>
                    </form>
                    <div id="mensajeBasico" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Gestión de Cursos -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>Gestión de Cursos</h5>
                </div>
                <div class="card-body">
                    <!-- Cursos actuales -->
                    <h6 class="text-success">Cursos Actuales (<?= count($cursos_actuales) ?>)</h6>
                    <div class="border rounded p-2 mb-3" style="max-height: 150px; overflow-y: auto;">
                        <?php if (!empty($cursos_actuales)): ?>
                            <?php foreach ($cursos_actuales as $curso): ?>
                                <div class="form-check">
                                    <input class="form-check-input curso-eliminar" type="checkbox" 
                                           value="<?= $curso['id'] ?>" id="eliminar_curso_<?= $curso['id'] ?>">
                                    <label class="form-check-label" for="eliminar_curso_<?= $curso['id'] ?>">
                                        <small>
                                            <strong><?= htmlspecialchars($curso['titulo']) ?></strong>
                                            <?php if (!empty($curso['categoria_nombre'])): ?>
                                                <br><span class="text-muted"><?= htmlspecialchars($curso['categoria_nombre']) ?></span>
                                            <?php endif; ?>
                                        </small>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No hay cursos asignados</p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Cursos disponibles para agregar -->
                    <h6 class="text-primary">Agregar Cursos</h6>
                    <div class="border rounded p-2 mb-3" style="max-height: 150px; overflow-y: auto;">
                        <?php if (!empty($cursos_disponibles)): ?>
                            <?php foreach ($cursos_disponibles as $curso): ?>
                                <div class="form-check">
                                    <input class="form-check-input curso-agregar" type="checkbox" 
                                           value="<?= $curso['id'] ?>" id="agregar_curso_<?= $curso['id'] ?>">
                                    <label class="form-check-label" for="agregar_curso_<?= $curso['id'] ?>">
                                        <small>
                                            <strong><?= htmlspecialchars($curso['titulo']) ?></strong>
                                            <?php if (!empty($curso['categoria_nombre'])): ?>
                                                <br><span class="text-muted"><?= htmlspecialchars($curso['categoria_nombre']) ?></span>
                                            <?php endif; ?>
                                        </small>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No hay cursos disponibles para agregar</p>
                        <?php endif; ?>
                    </div>
                    
                    <button type="button" class="btn btn-success btn-sm" onclick="actualizarCursos()">
                        Actualizar Cursos
                    </button>
                    <div id="mensajeCursos" class="mt-2"></div>
                </div>
            </div>
        </div>
        
        <!-- Gestión de Alumnos -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>👥 Gestión de Alumnos</h5>
                </div>
                <div class="card-body">
                    <!-- Alumnos actuales -->
                    <h6 class="text-success">Alumnos Actuales (<?= count($alumnos_actuales) ?>)</h6>
                    <div class="border rounded p-2 mb-3" style="max-height: 150px; overflow-y: auto;">
                        <?php if (!empty($alumnos_actuales)): ?>
                            <?php foreach ($alumnos_actuales as $alumno): ?>
                                <div class="form-check">
                                    <input class="form-check-input alumno-eliminar" type="checkbox" 
                                           value="<?= $alumno['id'] ?>" id="eliminar_alumno_<?= $alumno['id'] ?>">
                                    <label class="form-check-label" for="eliminar_alumno_<?= $alumno['id'] ?>">
                                        <small>
                                            <strong><?= htmlspecialchars($alumno['nombre']) ?></strong>
                                            <br><span class="text-muted"><?= htmlspecialchars($alumno['email']) ?></span>
                                        </small>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No hay alumnos inscritos</p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Alumnos disponibles para agregar -->
                    <h6 class="text-primary">Agregar Alumnos</h6>
                    <div class="border rounded p-2 mb-3" style="max-height: 150px; overflow-y: auto;">
                        <?php if (!empty($alumnos_disponibles)): ?>
                            <?php foreach ($alumnos_disponibles as $alumno): ?>
                                <div class="form-check">
                                    <input class="form-check-input alumno-agregar" type="checkbox" 
                                           value="<?= $alumno['id'] ?>" id="agregar_alumno_<?= $alumno['id'] ?>">
                                    <label class="form-check-label" for="agregar_alumno_<?= $alumno['id'] ?>">
                                        <small>
                                            <strong><?= htmlspecialchars($alumno['nombre']) ?></strong>
                                            <br><span class="text-muted"><?= htmlspecialchars($alumno['email']) ?></span>
                                        </small>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No hay alumnos disponibles para agregar</p>
                        <?php endif; ?>
                    </div>
                    
                    <button type="button" class="btn btn-success btn-sm" onclick="actualizarAlumnos()">
                        Actualizar Alumnos
                    </button>
                    <div id="mensajeAlumnos" class="mt-2"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Información de la clase -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Información de la Clase</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <strong>Código:</strong><br>
                            <code><?= htmlspecialchars($clase['codigo']) ?></code>
                        </div>
                        <div class="col-md-3">
                            <strong>Maestro:</strong><br>
                            <?= htmlspecialchars($clase['maestro_nombre']) ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Creada:</strong><br>
                            <?= date('d/m/Y', strtotime($clase['created_at'])) ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Email:</strong><br>
                            <?= htmlspecialchars($clase['maestro_email']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const claseId = <?= $clase['id'] ?>;

// Actualizar información básica
document.getElementById('formBasico').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    console.log('Enviando datos básicos:', Object.fromEntries(formData));
    
    fetch(`/ProyectoFinalTecWeb/public/api/clases/actualizar/${claseId}`, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Respuesta recibida:', data);
        const mensaje = document.getElementById('mensajeBasico');
        if (data.success) {
            mensaje.innerHTML = '<div class="alert alert-success">Información actualizada</div>';
        } else {
            mensaje.innerHTML = `<div class="alert alert-danger">Error: ${data.message || data.error}</div>`;
        }
    })
    .catch(error => {
        console.error('Error completo:', error);
        document.getElementById('mensajeBasico').innerHTML = 
            '<div class="alert alert-danger">Error de conexión: ' + error.message + '</div>';
    });
});

// Actualizar cursos
function actualizarCursos() {
    const cursosAgregar = Array.from(document.querySelectorAll('.curso-agregar:checked')).map(el => el.value);
    const cursosEliminar = Array.from(document.querySelectorAll('.curso-eliminar:checked')).map(el => el.value);
    
    console.log('Cursos a agregar:', cursosAgregar);
    console.log('Cursos a eliminar:', cursosEliminar);
    
    if (cursosAgregar.length === 0 && cursosEliminar.length === 0) {
        document.getElementById('mensajeCursos').innerHTML = 
            '<div class="alert alert-warning">Selecciona cursos para agregar o eliminar</div>';
        return;
    }
    
    const formData = new FormData();
    cursosAgregar.forEach(id => formData.append('agregar_cursos[]', id));
    cursosEliminar.forEach(id => formData.append('eliminar_cursos[]', id));
    
    console.log('FormData enviado:', Object.fromEntries(formData));
    
    fetch(`/ProyectoFinalTecWeb/public/api/clases/actualizar/${claseId}`, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Respuesta recibida:', data);
        const mensaje = document.getElementById('mensajeCursos');
        if (data.success) {
            mensaje.innerHTML = `<div class="alert alert-success">
                Cursos actualizados<br>
                ${data.cursos_agregados || 0} agregados, ${data.cursos_eliminados || 0} eliminados
            </div>`;
            setTimeout(() => location.reload(), 1500);
        } else {
            mensaje.innerHTML = `<div class="alert alert-danger">Error: ${data.message || data.error}</div>`;
        }
    })
    .catch(error => {
        console.error('Error completo:', error);
        document.getElementById('mensajeCursos').innerHTML = 
            '<div class="alert alert-danger">Error: ' + error.message + '</div>';
    });
}

// Actualizar alumnos
function actualizarAlumnos() {
    const alumnosAgregar = Array.from(document.querySelectorAll('.alumno-agregar:checked')).map(el => el.value);
    const alumnosEliminar = Array.from(document.querySelectorAll('.alumno-eliminar:checked')).map(el => el.value);
    
    if (alumnosAgregar.length === 0 && alumnosEliminar.length === 0) {
        document.getElementById('mensajeAlumnos').innerHTML = 
            '<div class="alert alert-warning">Selecciona alumnos para agregar o eliminar</div>';
        return;
    }
    
    const formData = new FormData();
    alumnosAgregar.forEach(id => formData.append('agregar_alumnos[]', id));
    alumnosEliminar.forEach(id => formData.append('eliminar_alumnos[]', id));
    
    fetch(`/ProyectoFinalTecWeb/public/api/clases/actualizar/${claseId}`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const mensaje = document.getElementById('mensajeAlumnos');
        if (data.success) {
            mensaje.innerHTML = `<div class="alert alert-success">
                Alumnos actualizados<br>
                ${data.alumnos_agregados || 0} agregados, ${data.alumnos_eliminados || 0} eliminados
            </div>`;
            setTimeout(() => location.reload(), 1500);
        } else {
            mensaje.innerHTML = `<div class="alert alert-danger">Error: ${data.error}</div>`;
        }
    })
    .catch(error => {
        document.getElementById('mensajeAlumnos').innerHTML = 
            '<div class="alert alert-danger">Error de conexión</div>';
    });
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>