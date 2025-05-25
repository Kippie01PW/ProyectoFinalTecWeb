<?php
// Views/maestros/clases.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Clases - Panel Maestro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
        }
        .clase-card {
            transition: transform 0.2s, box-shadow 0.2s;
            border-left: 4px solid #007bff;
        }
        .clase-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .alumno-count {
            background: #e3f2fd;
            color: #1565c0;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .btn-view-alumnos {
            font-size: 0.875rem;
        }
        .collapse-content {
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }
        .alumno-item {
            border-bottom: 1px solid #e9ecef;
            padding: 0.75rem 0;
        }
        .alumno-item:last-child {
            border-bottom: none;
        }
        .estado-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <!-- Header con estadísticas -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="mb-3">
                    <i class="fas fa-chalkboard-teacher me-2"></i>
                    Mis Clases
                </h2>
            </div>
        </div>

        <!-- Tarjetas de estadísticas -->
        <?php if (isset($estadisticas)): ?>
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-book fa-2x mb-2"></i>
                        <h4><?= $estadisticas['total_clases'] ?></h4>
                        <p class="mb-0">Total Clases</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <h4><?= $estadisticas['total_alumnos_unicos'] ?></h4>
                        <p class="mb-0">Alumnos Únicos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-user-graduate fa-2x mb-2"></i>
                        <h4><?= $estadisticas['total_inscripciones'] ?></h4>
                        <p class="mb-0">Total Inscripciones</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-line fa-2x mb-2"></i>
                        <h4><?= $estadisticas['promedio_alumnos_por_clase'] ?></h4>
                        <p class="mb-0">Promedio por Clase</p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Lista de clases -->
        <?php if (!empty($clases)): ?>
            <div class="row">
                <?php foreach ($clases as $index => $clase): ?>
                    <div class="col-lg-6 col-md-12 mb-4">
                        <div class="card clase-card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">
                                        <strong><?= htmlspecialchars($clase['codigo']) ?></strong>
                                    </h5>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        <?= date('d/m/Y', strtotime($clase['created_at'])) ?>
                                    </small>
                                </div>
                                <span class="alumno-count">
                                    <i class="fas fa-user-friends me-1"></i>
                                    <?= $clase['total_alumnos'] ?> alumno<?= $clase['total_alumnos'] != 1 ? 's' : '' ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <h6 class="card-title"><?= htmlspecialchars($clase['nombre'] ?? 'Sin nombre') ?></h6>
                                <p class="card-text text-muted">
                                    <?= htmlspecialchars($clase['descripcion'] ?? 'Sin descripción') ?>
                                </p>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <?php if ($clase['total_alumnos'] > 0): ?>
                                            <button class="btn btn-outline-primary btn-sm btn-view-alumnos" 
                                                    type="button" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#alumnos-<?= $clase['id'] ?>" 
                                                    data-clase-id="<?= $clase['id'] ?>"
                                                    aria-expanded="false">
                                                <i class="fas fa-eye me-1"></i>
                                                Ver Alumnos
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted small">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Sin alumnos inscritos
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <a href="/ProyectoFinalTecWeb/public/maestros/clases/<?= $clase['id'] ?>" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-cog me-1"></i>
                                            Gestionar
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sección colapsable para mostrar alumnos -->
                            <?php if ($clase['total_alumnos'] > 0): ?>
                            <div class="collapse" id="alumnos-<?= $clase['id'] ?>">
                                <div class="collapse-content p-3">
                                    <h6 class="mb-3">
                                        <i class="fas fa-users me-2"></i>
                                        Alumnos Inscritos (<?= $clase['total_alumnos'] ?>)
                                    </h6>
                                    <div id="lista-alumnos-<?= $clase['id'] ?>">
                                        <div class="text-center p-3">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                <span class="visually-hidden">Cargando...</span>
                                            </div>
                                            <p class="mb-0 mt-2 small text-muted">Cargando alumnos...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info text-center" role="alert">
                        <i class="fas fa-info-circle fa-3x mb-3 text-primary"></i>
                        <h4 class="alert-heading">No tienes clases creadas</h4>
                        <p>Aún no has creado ninguna clase. Puedes crear tu primera clase para comenzar a gestionar tus cursos y alumnos.</p>
                        <hr>
                        <button class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Crear Primera Clase
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Manejar clic en botones "Ver Alumnos"
            document.querySelectorAll('.btn-view-alumnos').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const claseId = this.getAttribute('data-clase-id');
                    const collapse = document.querySelector('#alumnos-' + claseId);
                    
                    // Solo cargar alumnos si el collapse se está abriendo y no se han cargado antes
                    if (!collapse.classList.contains('show') && !collapse.hasAttribute('data-loaded')) {
                        cargarAlumnos(claseId);
                        collapse.setAttribute('data-loaded', 'true');
                    }
                });
            });
        });

        function cargarAlumnos(claseId) {
            const container = document.querySelector('#lista-alumnos-' + claseId);
            
            fetch(`/ProyectoFinalTecWeb/public/api/maestros/clases/${claseId}/alumnos`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.alumnos) {
                        mostrarAlumnos(container, data.alumnos);
                    } else {
                        mostrarError(container, 'No se pudieron cargar los alumnos');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarError(container, 'Error al cargar los alumnos');
                });
        }

        function mostrarAlumnos(container, alumnos) {
            if (alumnos.length === 0) {
                container.innerHTML = `
                    <div class="text-center p-3">
                        <i class="fas fa-user-slash text-muted"></i>
                        <p class="mb-0 text-muted">No hay alumnos inscritos</p>
                    </div>
                `;
                return;
            }

            let html = '';
            alumnos.forEach(function(alumno) {
                const estadoBadge = getEstadoBadge(alumno.estado);
                const fechaAsignacion = new Date(alumno.fecha_asignacion).toLocaleDateString('es-ES');
                
                html += `
                    <div class="alumno-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-user-circle fa-2x text-secondary"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">${escapeHtml(alumno.nombre)}</h6>
                                <small class="text-muted">
                                    <i class="fas fa-calendar-plus me-1"></i>
                                    Inscrito: ${fechaAsignacion}
                                </small>
                            </div>
                        </div>
                        <div>
                            ${estadoBadge}
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }

        function mostrarError(container, mensaje) {
            container.innerHTML = `
                <div class="text-center p-3">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    <p class="mb-0 text-muted">${mensaje}</p>
                </div>
            `;
        }

        function getEstadoBadge(estado) {
            const estados = {
                'activo': '<span class="badge bg-success estado-badge">Activo</span>',
                'inactivo': '<span class="badge bg-secondary estado-badge">Inactivo</span>',
                'completado': '<span class="badge bg-primary estado-badge">Completado</span>',
                'suspendido': '<span class="badge bg-warning estado-badge">Suspendido</span>'
            };
            
            return estados[estado] || '<span class="badge bg-secondary estado-badge">Sin estado</span>';
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>