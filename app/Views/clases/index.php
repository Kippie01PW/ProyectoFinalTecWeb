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
                            <label for="nombre" class="form-label">Nombre de la Clase</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Crear Clase</button>
                <button type="reset" class="btn btn-secondary">Limpiar</button>
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
                                <th>Creada</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaClases">
                            <?php foreach ($clases as $clase): ?>
                                <tr>
                                    <td><?= htmlspecialchars($clase['nombre']) ?></td>
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
// Manejar formulario de crear clase
document.getElementById('formClase').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
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
                    ✅ Clase creada exitosamente. 
                    <br><strong>Código de la clase: ${data.codigo}</strong>
                    <br><small>Comparte este código con tus alumnos para que se unan.</small>
                </div>
            `;
            document.getElementById('formClase').reset();
            
            // Recargar la página para mostrar la nueva clase en la tabla
            setTimeout(() => {
                location.reload();
            }, 2000);
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