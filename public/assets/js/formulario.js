$(document).ready(function() {
    const baseUrl = "/ProyectoFinalTecWeb/public";
    
    function enviarPreferencias() {
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
                    if (cb.checked) valores.push(cb.value);
                });
                respuestas[nombre] = valores.length > 1 ? JSON.stringify(valores) : (valores[0] ?? null);
            } else if (inputsMismoNombre[0].type === "radio") {
                let seleccionado = null;
                inputsMismoNombre.forEach(rb => {
                    if (rb.checked) seleccionado = rb.value;
                });
                respuestas[nombre] = seleccionado;
            } else {
                respuestas[nombre] = input.value || null;
            }
            
            preguntasProcesadas.add(nombre);
        });
        
        console.log("Respuestas a enviar:", respuestas); // Para depuración
        
        $.ajax({
            url: baseUrl + "/api/alumnos/preferencias/guardar",
            method: "POST",
            dataType: "json",
            data: { respuestas: respuestas },
            success: function (data) {
                if (data.success) {
                    alert("Preferencias guardadas correctamente.");
                    window.location.href = baseUrl + "/alumnos/panel";
                } else {
                    alert("Error al guardar las preferencias.");
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Error AJAX:", textStatus, errorThrown);
                console.error("Respuesta del servidor:", jqXHR.responseText);
                alert("Ocurrió un error al enviar las respuestas. Revisa la consola para más detalles.");
            }
        });
    }
    
    $('#btnEnviar').on('click', function(e) {
        const btnText = $(this).text().trim();
        
        if (btnText === "Enviar") {
            e.preventDefault();
            enviarPreferencias();
        }
        // Si no es "Enviar", deja que el formulario se envíe normalmente (páginas 1 y 2)
    });
});


function validarFormulario(page) {
  const preguntas = document.querySelectorAll('form .mb-3');
  let isValid = true;

  preguntas.forEach((preguntaDiv, i) => {
    // Elimina mensajes previos de error
    let msgError = preguntaDiv.querySelector('.error-msg');
    if (msgError) msgError.remove();

    const inputs = preguntaDiv.querySelectorAll('input[type="radio"], input[type="checkbox"]');
    const seleccionado = Array.from(inputs).some(input => input.checked);

    if (!seleccionado) {
      // Crea un parrafo para el mensaje de error
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