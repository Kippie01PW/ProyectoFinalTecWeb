// public/assets/js/evidencias.js
// Script para manejar la funcionalidad de evidencias

class EvidenciasManager {
    constructor() {
        this.baseUrl = '/ProyectoFinalTecWeb/public/api/clases';
        this.evidenciaModal = null;
        this.init();
    }

    init() {
        this.evidenciaModal = new bootstrap.Modal(document.getElementById('evidenciaModal'));
        this.cargarEvidencias();
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Botón actualizar evidencias
        const btnActualizar = document.querySelector('[onclick="cargarEvidencias()"]');
        if (btnActualizar) {
            btnActualizar.onclick = () => this.cargarEvidencias();
        }

        // Manejo de errores de imagen
        document.addEventListener('error', (e) => {
            if (e.target.classList.contains('evidencia-img')) {
                this.handleImageError(e.target);
            }
        }, true);
    }

    async cargarEvidencias() {
        this.showLoading();
        
        try {
            const response = await fetch(`${this.baseUrl}/evidencias`);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                if (data.evidencias && data.evidencias.length > 0) {
                    this.mostrarEvidencias(data.evidencias);
                    this.mostrarEstadisticas(data.estadisticas);
                    this.showTable();
                } else {
                    this.showNoEvidencias();
                }
            } else {
                throw new Error(data.message || 'Error desconocido al cargar evidencias');
            }
            
        } catch (error) {
            console.error('Error al cargar evidencias:', error);
            this.showError('Error al cargar las evidencias: ' + error.message);
        }
    }

    mostrarEvidencias(evidencias) {
        const tbody = document.getElementById('evidencias-tbody');
        tbody.innerHTML = '';

        evidencias.forEach((evidencia, index) => {
            const row = this.createEvidenciaRow(evidencia, index);
            tbody.appendChild(row);
        });

        // Agregar animación
        tbody.classList.add('fade-in');
    }

    createEvidenciaRow(evidencia, index) {
        const row = document.createElement('tr');
        const fecha = new Date(evidencia.fecha_completado).toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
        
        row.innerHTML = `
            <td>
                <div class="d-flex align-items-center">
                    <div class="me-2">
                        <i class="fas fa-user-graduate text-primary"></i>
                    </div>
                    <div>
                        <strong class="text-dark">${this.escapeHtml(evidencia.alumno_nombre)}</strong>
                        <br>
                        <small class="text-muted">${this.escapeHtml(evidencia.alumno_email)}</small>
                    </div>
                </div>
            </td>
            <td>
                <div>
                    <span class="fw-semibold">${this.escapeHtml(evidencia.curso_titulo)}</span>
                    ${evidencia.categoria_nombre ? 
                        `<br><span class="badge bg-secondary mt-1">${this.escapeHtml(evidencia.categoria_nombre)}</span>` 
                        : ''
                    }
                </div>
            </td>
            <td>
                <span class="badge bg-info">${this.escapeHtml(evidencia.clase_nombre)}</span>
            </td>
            <td>
                <div class="text-center">
                    <i class="fas fa-calendar-check text-success me-1"></i>
                    <span class="fw-medium">${fecha}</span>
                </div>
            </td>
            <td class="text-center">
                ${evidencia.evidencia ? 
                    this.createEvidenciaPreview(evidencia, index) : 
                    '<span class="text-muted"><i class="fas fa-image-slash"></i> Sin evidencia</span>'
                }
            </td>
            <td>
                <div class="d-flex flex-column align-items-center gap-1">
                    <a href="mailto:${evidencia.alumno_email}" 
                       class="btn btn-outline-primary btn-sm email-link"
                       title="Enviar email a ${evidencia.alumno_nombre}">
                        <i class="fas fa-envelope me-1"></i>
                        Contactar
                    </a>
                </div>
            </td>
        `;
        
        return row;
    }

    createEvidenciaPreview(evidencia, index) {
        return `
            <div class="evidencia-preview">
                <img src="${evidencia.evidencia}" 
                     class="evidencia-img" 
                     alt="Evidencia de ${this.escapeHtml(evidencia.alumno_nombre)}"
                     loading="lazy"
                     data-evidencia-index="${index}"
                     onclick="evidenciasManager.mostrarEvidenciaModal('${evidencia.evidencia}', '${this.escapeHtml(evidencia.alumno_nombre)}', '${this.escapeHtml(evidencia.curso_titulo)}', '${new Date(evidencia.fecha_completado).toLocaleDateString('es-ES')}')">
                <div class="evidencia-overlay">
                    <i class="fas fa-search-plus"></i>
                </div>
            </div>
        `;
    }

    mostrarEstadisticas(stats) {
        const statsElement = document.getElementById('stats-evidencias');
        
        document.getElementById('total-evidencias').textContent = stats.total_evidencias || 0;
        document.getElementById('alumnos-con-evidencias').textContent = stats.alumnos_con_evidencias || 0;
        document.getElementById('cursos-con-evidencias').textContent = stats.cursos_con_evidencias || 0;
        
        statsElement.style.display = 'block';
        statsElement.classList.add('fade-in');
    }

    mostrarEvidenciaModal(src, alumno, curso, fecha) {
        const imgElement = document.getElementById('evidencia-img-modal');
        const loadingSpinner = this.createLoadingSpinner();
        
        // Mostrar loading en el modal
        imgElement.parentNode.appendChild(loadingSpinner);
        imgElement.style.display = 'none';
        
        // Precargar imagen
        const tempImg = new Image();
        tempImg.onload = () => {
            imgElement.src = src;
            imgElement.style.display = 'block';
            loadingSpinner.remove();
        };
        tempImg.onerror = () => {
            loadingSpinner.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error al cargar la imagen
                </div>
            `;
        };
        tempImg.src = src;
        
        // Actualizar información del modal
        document.getElementById('evidencia-alumno').textContent = alumno;
        document.getElementById('evidencia-curso').textContent = curso;
        document.getElementById('evidencia-fecha').textContent = `Completado el: ${fecha}`;
        document.getElementById('evidencia-download').href = src;
        
        this.evidenciaModal.show();
    }

    createLoadingSpinner() {
        const spinner = document.createElement('div');
        spinner.className = 'text-center p-3';
        spinner.innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando imagen...</span>
            </div>
        `;
        return spinner;
    }

    handleImageError(imgElement) {
        imgElement.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjgwIiB2aWV3Qm94PSIwIDAgMTAwIDgwIiBmaWxsPSIjY2NjIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxyZWN0IHdpZHRoPSIxMDAiIGhlaWdodD0iODAiIGZpbGw9IiNmOGY5ZmEiLz48dGV4dCB4PSI1MCIgeT0iNDAiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxMiIgZmlsbD0iIzZjNzU3ZCI+SW1hZ2VuIG5vIGRpc3BvbmlibGU8L3RleHQ+PC9zdmc+';
        imgElement.alt = 'Imagen no disponible';
        imgElement.classList.add('evidencia-error');
    }

    showLoading() {
        const loadingElement = document.getElementById('loading-evidencias');
        const tablaContainer = document.getElementById('tabla-evidencias-container');
        const noEvidenciasElement = document.getElementById('no-evidencias');
        const statsElement = document.getElementById('stats-evidencias');
        
        loadingElement.style.display = 'block';
        tablaContainer.style.display = 'none';
        noEvidenciasElement.style.display = 'none';
        statsElement.style.display = 'none';
        
        // Limpiar errores anteriores
        this.clearErrors();
    }

    showTable() {
        const loadingElement = document.getElementById('loading-evidencias');
        const tablaContainer = document.getElementById('tabla-evidencias-container');
        
        loadingElement.style.display = 'none';
        tablaContainer.style.display = 'block';
    }

    showNoEvidencias() {
        const loadingElement = document.getElementById('loading-evidencias');
        const noEvidenciasElement = document.getElementById('no-evidencias');
        
        loadingElement.style.display = 'none';
        noEvidenciasElement.style.display = 'block';
        noEvidenciasElement.classList.add('fade-in');
    }

    showError(mensaje) {
        const loadingElement = document.getElementById('loading-evidencias');
        const container = document.querySelector('.evidencias-section');
        
        loadingElement.style.display = 'none';
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger alert-dismissible fade show';
        errorDiv.innerHTML = `
            <i class="fas fa-exclamation-triangle me-2"></i>
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        container.insertBefore(errorDiv, container.firstChild);
    }

    clearErrors() {
        const errorAlerts = document.querySelectorAll('.evidencias-section .alert-danger');
        errorAlerts.forEach(alert => alert.remove());
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.evidenciasManager = new EvidenciasManager();
});

// Funciones globales para compatibilidad
function cargarEvidencias() {
    if (window.evidenciasManager) {
        window.evidenciasManager.cargarEvidencias();
    }
}

function mostrarEvidenciaModal(src, alumno, curso, fecha) {
    if (window.evidenciasManager) {
        window.evidenciasManager.mostrarEvidenciaModal(src, alumno, curso, fecha);
    }
}