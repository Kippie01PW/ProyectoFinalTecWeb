$(document).ready(function() {
    const baseUrl = "/ProyectoFinalTecWeb/public";

   //QUITA EL INCISO A)
    function getTextoLabel(input) {
        const label = document.querySelector(`label[for="${input.id}"]`);
        if (!label) return '';

        let texto = label.textContent.trim();
        texto = texto.replace(/^[a-z]\)\s*/, '');
        return texto;
    }

//VALIDACIONES
    function validarFormulario() {
        const preguntas = document.querySelectorAll('form .mb-3');
        let isValid = true;

        preguntas.forEach((preguntaDiv, i) => {

            let msgError = preguntaDiv.querySelector('.error-msg');
            if (msgError) msgError.remove();

            const inputs = preguntaDiv.querySelectorAll('input[type="radio"], input[type="checkbox"]');
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

    function guardarDatosTemporales() {
        const respuestas = {};
        const campos = document.querySelectorAll('[name^="q"]');
        const preguntasProcesadas = new Set();

        campos.forEach(input => {
            const nombreRaw = input.name;
            const nombre = nombreRaw.replace(/\[\]$/, '');

            if (preguntasProcesadas.has(nombre)) return;

            const inputsMismoNombre = document.querySelectorAll(`[name="${nombreRaw}"]`);
            let valores = [];

            if (inputsMismoNombre[0].type === "checkbox") {
                inputsMismoNombre.forEach(cb => {
                    if (cb.checked) valores.push(getTextoLabel(cb));
                });
                respuestas[nombre] = valores.length > 1 ? JSON.stringify(valores) : (valores[0] ?? null);
            } else if (inputsMismoNombre[0].type === "radio") {
                let seleccionado = null;
                inputsMismoNombre.forEach(rb => {
                    if (rb.checked) seleccionado = getTextoLabel(rb);
                });
                respuestas[nombre] = seleccionado;
            } else {
                respuestas[nombre] = input.value || null;
            }

            preguntasProcesadas.add(nombre);
        });

        const datosExistentes = JSON.parse(sessionStorage.getItem('preferenciasFormulario') || '{}');
        const datosCombinados = { ...datosExistentes, ...respuestas };
        sessionStorage.setItem('preferenciasFormulario', JSON.stringify(datosCombinados));

        return respuestas;
    }

  
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
            success: function(data) {
            if (data.success) {

                $('#mensajeEnvio').removeClass('d-none').text('Hemos registrado tus respuestas correctamente :). Te recomendamos contestar el formulario solo una vez pero puedes volver a responderlo.');

                $(window).scrollTop($('#mensajeEnvio').offset().top - 20);

                sessionStorage.removeItem('preferenciasFormulario');

            }else {
                            alert("Error al guardar las preferencias: " + (data.error || 'Error desconocido'));
                }
            },

            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Error AJAX:", textStatus, errorThrown);
                console.error("Respuesta del servidor:", jqXHR.responseText);
                alert("Ocurrió un error al enviar las respuestas. Revisa la consola para más detalles.");
            }
        });
    }

    $('#btnEnviar').on('click', function(e) {
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
            const form = document.getElementById('formPreferencias');
            form.submit();
        }
    });

    function cargarDatosGuardados() {
        const datosGuardados = JSON.parse(sessionStorage.getItem('preferenciasFormulario') || '{}');

        Object.keys(datosGuardados).forEach(nombre => {
            const valor = datosGuardados[nombre];
            const inputs = document.querySelectorAll(`[name="${nombre}"], [name="${nombre}[]"]`);

            if (inputs.length > 0) {
                const tipoInput = inputs[0].type;

                if (tipoInput === 'checkbox') {
                    let valores = [];
                    try {
                        valores = typeof valor === 'string' && valor.startsWith('[') ? JSON.parse(valor) : [valor];
                    } catch (e) {
                        valores = [valor];
                    }

                    inputs.forEach(input => {
                        input.checked = valores.includes(input.value);
                    });
                } else if (tipoInput === 'radio') {
                    inputs.forEach(input => {
                        input.checked = input.value === valor;
                    });
                } else {
                    inputs[0].value = valor;
                }
            }
        });
    }

    cargarDatosGuardados();
        // Mostrar mensaje en el dashboard si existe en sessionStorage
    const mensaje = sessionStorage.getItem('mensajeDashboard');
    if (mensaje) {
        const contenedor = $('#mensajeDashboard');
        if (contenedor.length > 0) {
            contenedor.text(mensaje).css({
                'display': 'block',
                'background-color': '#d1e7dd',
                'color': '#0f5132',
                'border': '1px solid #badbcc'
            });
        }
        sessionStorage.removeItem('mensajeDashboard');
    }
});

