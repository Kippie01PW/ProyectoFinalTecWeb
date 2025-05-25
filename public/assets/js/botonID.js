$(document).ready(function () {
$('#compartirMiId').on('click', function(e) {
    e.preventDefault();
    let alumnoId = $('body').data('alumno-id');
    if (alumnoId) {
        alert("Comparte este ID con tu profesor por si lo necesita: " + alumnoId);
    } else {
        alert("No se pudo obtener tu ID. Asegúrate de haber iniciado sesión como alumno.");
    }
});
}
);