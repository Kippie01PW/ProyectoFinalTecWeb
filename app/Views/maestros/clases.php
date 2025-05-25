<div class="container mt-4">
    <h2>Mis Clases</h2>
    
    <?php if (!empty($clases)): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Fecha de Creación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clases as $clase): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($clase['codigo']) ?></strong></td>
                            <td><?= htmlspecialchars($clase['nombre'] ?? 'Sin nombre') ?></td>
                            <td><?= htmlspecialchars($clase['descripcion'] ?? 'Sin descripción') ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($clase['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            <h4 class="alert-heading">No tienes clases creadas</h4>
            <p>Aún no has creado ninguna clase. Puedes crear tu primera clase para comenzar a gestionar tus cursos y alumnos.</p>
        </div>
    <?php endif; ?>
</div>