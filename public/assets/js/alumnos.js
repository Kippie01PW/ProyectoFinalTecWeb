$(document).ready(function () {
    const baseUrl = "/ProyectoFinalTecWeb/public";

    // Función para validar el formulario
    function validarFormulario() {
        const preguntas = document.querySelectorAll('form .mb-3');
        let isValid = true;

        preguntas.forEach((preguntaDiv) => {
            // Elimina mensajes previos de error
            let msgError = preguntaDiv.querySelector('.error-msg');
            if (msgError) msgError.remove();

            const inputs = preguntaDiv.querySelectorAll('input[type="radio"]');
            const seleccionado = Array.from(inputs).some(input => input.checked);

            if (!seleccionado) {
                const error = document.createElement('p');
                error.classList.add('error-msg', 'text-danger', 'mt-1');
                error.style.fontSize = '0.875em';
                error.textContent = 'Por favor, responde esta pregunta.';
                preguntaDiv.appendChild(error);

                if (isValid) {
                    inputs[0].focus();
                }

                isValid = false;
            }
        });

        return isValid;
    }

    // Función para guardar datos en sessionStorage
    function guardarDatosTemporales() {
        const respuestas = {};
        const campos = document.querySelectorAll('[name^="q"]');
        const preguntasProcesadas = new Set();

        campos.forEach(input => {
            const nombre = input.name;

            if (preguntasProcesadas.has(nombre)) return;

            const inputsMismoNombre = document.querySelectorAll(`[name="${nombre}"]`);

            if (inputsMismoNombre[0].type === "radio") {
                let seleccionado = null;
                inputsMismoNombre.forEach(rb => {
                    if (rb.checked) seleccionado = rb.value;
                });
                respuestas[nombre] = seleccionado;
            }

            preguntasProcesadas.add(nombre);
        });

        const datosExistentes = JSON.parse(sessionStorage.getItem('preferenciasFormulario') || '{}');
        const datosCombinados = { ...datosExistentes, ...respuestas };
        sessionStorage.setItem('preferenciasFormulario', JSON.stringify(datosCombinados));

        return respuestas;
    }

    // Función para enviar todas las preferencias
    function enviarPreferencias() {
        const todasLasRespuestas = JSON.parse(sessionStorage.getItem('preferenciasFormulario') || '{}');

        console.log("Todas las respuestas a enviar:", todasLasRespuestas);

        const numRespuestas = Object.keys(todasLasRespuestas).length;
        if (numRespuestas < 20) {
            alert(`Faltan respuestas. Se encontraron ${numRespuestas} de 20 esperadas.`);
            return;
        }

        $.ajax({
            url: baseUrl + "/alumnos/preferencias/guardar",
            method: "POST",
            dataType: "json",
            data: { respuestas: todasLasRespuestas },
            success: function (data) {
                if (data.success) {
                    alert("Preferencias guardadas correctamente.");
                    sessionStorage.removeItem('preferenciasFormulario');
                    window.location.href = baseUrl + "/alumnos/dashboard";
                } else {
                    alert("Error al guardar las preferencias: " + (data.error || 'Error desconocido'));
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Error AJAX:", textStatus, errorThrown);
                console.error("Respuesta del servidor:", jqXHR.responseText);
                alert("Ocurrió un error al enviar las respuestas. Revisa la consola para más detalles.");
            }
        });
    }

    // Manejar el clic del botón
    $('#btnEnviar').on('click', function (e) {
        e.preventDefault();

        if (!validarFormulario()) {
            return false;
        }

        const btnText = $(this).text().trim();

        if (btnText === "Enviar") {
            guardarDatosTemporales();
            enviarPreferencias();
        } else {
            guardarDatosTemporales();
            document.getElementById('formPreferencias').submit();
        }
    });

    // Cargar datos guardados al cargar la página
    function cargarDatosGuardados() {
        const datosGuardados = JSON.parse(sessionStorage.getItem('preferenciasFormulario') || '{}');

        Object.keys(datosGuardados).forEach(nombre => {
            const valor = datosGuardados[nombre];
            const inputs = document.querySelectorAll(`[name="${nombre}"]`);

            if (inputs.length > 0 && inputs[0].type === 'radio') {
                inputs.forEach(input => {
                    input.checked = input.value === valor;
                });
            }
        });
    }

    cargarDatosGuardados();
    
    $('#compartirMiId').on('click', function(e) {
        e.preventDefault(); // Prevenir la acción por defecto del enlace href="#"

        // Necesitamos obtener el alumno_id. Si no lo tenemos en JS,
        // la forma más fácil es que PHP lo imprima en algún lugar.
        // Por ejemplo, si en tu dashboard.php (alumno) o header_alumnos.php tienes:
        // <script> var miAlumnoIdGlobal = <?php echo json_encode($_SESSION['alumno_id'] ?? null); ?>; </script>
        // Entonces podrías usarlo aquí:

        // FORMA MÁS DIRECTA (si PHP puede ponerlo en la página)
        // Asumamos que tienes una variable JS global `miAlumnoIdGlobal` 
        // o un elemento en el HTML que lo contenga.
        // Por ejemplo, si lo pones en el body como un data-attribute:
        // <body data-alumno-id="<?php echo htmlspecialchars($_SESSION['alumno_id'] ?? ''); ?>">
        let alumnoId = $('body').data('alumno-id'); 

        if (alumnoId) {
            alert("Comparte este ID con tu profesor: " + alumnoId);
        } else {
            alert("No se pudo obtener tu ID. Asegúrate de haber iniciado sesión.");
        }
    });
});
