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
      // Crea un párrafo para el mensaje de error
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
