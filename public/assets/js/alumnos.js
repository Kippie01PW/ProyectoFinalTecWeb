$(document).ready(function() {

    const baseUrl = "/ProyectoFinalTecWeb/public"; 

    function cargarCursosAsignados() {
        $('#loading-asignados').show(); 
        $('#tabla-asignados').hide();   
        $('#tabla-asignados tbody').empty();

        $.ajax({
            url: baseUrl + "/api/alumnos/cursos/asignados",
            type: "GET",
            dataType: "json",
            success: function(cursos) {
                $('#loading-asignados').hide(); 
                $('#tabla-asignados').show();   
                let tablaBody = $('#tabla-asignados tbody');

                if (cursos.length === 0) {
                    tablaBody.append('<tr><td colspan="4">No tienes cursos asignados actualmente.</td></tr>');
                } else {
                    // Recorre cada curso y crea una fila en la tabla
                    cursos.forEach(function(curso) {
                        tablaBody.append(`
                            <tr>
                                <td>${escapeHtml(curso.titulo)}</td>
                                <td>${escapeHtml(curso.descripcion || 'N/A')}</td>
                                <td>${escapeHtml(curso.categoria_nombre || 'Sin categoría')}</td>
                                <td><a href="${escapeHtml(curso.enlace_externo || '#')}" target="_blank" class="btn btn-primary btn-sm">Ir</a></td>
                            </tr>
                        `);
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Error AJAX (Asignados):", textStatus, errorThrown);
                $('#loading-asignados').html('<div class="alert alert-danger">Error al cargar los cursos asignados. Revisa la consola (F12).</div>');
            }
        });
    }


    function cargarCursosCompletados() {
        $('#loading-completados').show();
        $('#tabla-completados').hide();
        $('#tabla-completados tbody').empty();

        $.ajax({
            url: baseUrl + "/api/alumnos/cursos/completados",
            type: "GET",
            dataType: "json",
            success: function(cursos) {
                $('#loading-completados').hide();
                $('#tabla-completados').show();
                let tablaBody = $('#tabla-completados tbody');

                if (cursos.length === 0) {
                    tablaBody.append('<tr><td colspan="3">No has completado ningún curso.</td></tr>');
                } else {
                    cursos.forEach(function(curso) {
                        tablaBody.append(`
                            <tr>
                                <td>${escapeHtml(curso.titulo)}</td>
                                <td>${escapeHtml(curso.fecha_completado || 'N/A')}</td>
                                <td><a href="${escapeHtml(curso.evidencia || '#')}" target="_blank" class="btn btn-info btn-sm">Ver</a></td>
                            </tr>
                        `);
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Error AJAX (Completados):", textStatus, errorThrown);
                $('#loading-completados').html('<div class="alert alert-danger">Error al cargar los cursos completados. Revisa la consola (F12).</div>');
            }
        });
    }


    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
    
        return text == null ? '' : String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }


    
    cargarCursosAsignados();
    cargarCursosCompletados();

}); 