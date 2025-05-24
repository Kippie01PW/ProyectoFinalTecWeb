<?php
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$totalPages = 3;

$questions = [
  1 => [
    'title' => 'I. Intereses Académicos y Personales (Parte I)',
    'questions' => [
      '¿Qué área del conocimiento te interesa más?' => ['Ciencias', 'Matemáticas', 'Lenguaje y comunicación', 'Ciencias sociales', 'Tecnología y programación', 'Arte y creatividad'],
      '¿En qué área sueles obtener mejores calificaciones?' => ['Ciencias', 'Matemáticas', 'Lenguaje y comunicación', 'Ciencias sociales', 'Tecnología y programación', 'Arte'],
      '¿Te gusta más trabajar solo o en equipo?' => ['Solo', 'En equipo', 'Depende del tipo de actividad'],
      '¿Qué prefieres hacer?' => ['Resolver problemas numéricos o lógicos', 'Escribir textos o historias', 'Dibujar o crear cosas manualmente', 'Investigar y leer información'],
      '¿Cómo te gusta aprender temas nuevos?' => ['Haciendo experimentos o actividades prácticas', 'Leyendo libros o artículos', 'Viendo videos explicativos', 'Escuchando a otras personas']
    ]
  ],
  2 => [
    'title' => 'I. Intereses Académicos y Personales (Parte II)',
    'questions' => [
      '¿Qué te gusta hacer en tu tiempo libre?' => ['Leer', 'Jugar videojuegos', 'Hacer deporte', 'Dibujar o hacer manualidades', 'Programar o usar la computadora'],
      '¿Qué prefieres usar en tus actividades escolares o creativas?' => ['Computadora o dispositivos digitales', 'Materiales para manualidades', 'Libros y cuadernos', 'Herramientas científicas o tecnológicas'],
      '¿Qué tipo de actividades disfrutas más?' => ['Crear proyectos', 'Resolver retos de lógica o matemáticas', 'Participar en debates o escribir textos', 'Explorar el mundo natural o histórico'],
      '¿Qué tipo de juegos prefieres?' => ['De lógica o estrategia', 'De construcción y simulación', 'De aventuras y exploración', 'De creatividad y diseño'],
      'Si pudieras aprender algo nuevo, ¿qué escogerías?' => ['Programación o desarrollo de apps', 'Idiomas', 'Música o arte', 'Robótica', 'Escritura creativa']
    ]
  ],
  3 => [
    'title' => 'II. Preferencias de Aprendizaje',
    'questions' => [
      '¿Cómo aprendes mejor?' => ['Escuchando explicaciones (auditivo)', 'Viendo imágenes o videos (visual)', 'Tocando, escribiendo o practicando (kinestésico)'],
      '¿Prefieres aprender solo o en grupo?' => ['Solo', 'En pareja', 'En grupo pequeño', 'No tengo preferencia'],
      '¿Qué formato de contenido te resulta más útil?' => ['Videos explicativos', 'Textos escritos', 'Infografías o esquemas visuales', 'Ejercicios prácticos o interactivos', 'Podcasts o audios'],
      '¿Qué herramientas digitales sueles usar para estudiar?' => ['Videos de YouTube', 'Aplicaciones móviles educativas', 'Plataformas como Moodle, Google Classroom', 'Foros o grupos en redes sociales', 'Juegos o simulaciones educativas'],
      '¿Con qué frecuencia utilizas tecnología para estudiar?' => ['Todos los días', 'Varias veces por semana', 'Rara vez', 'Casi nunca'],
      '¿Prefieres estudiar en un dispositivo específico?' => ['Computadora o laptop', 'Tableta', 'Teléfono móvil', 'No tengo preferencia'],
      '¿Te gusta que el curso tenga ejercicios interactivos?' => ['Sí, mucho', 'Sí, pero no es indispensable', 'No, prefiero otros métodos'],
      '¿Utilizas alguna técnica de estudio en formato digital?' => ['Mapas mentales', 'Resúmenes o esquemas', 'Fichas digitales', 'Subrayado digital', 'Grabación de audio o notas de voz'],
      '¿Te gustaría tener retroalimentación inmediata?' => ['Sí, siempre que sea posible', 'Solo al final de cada módulo', 'No, prefiero revisar todo junto al final del curso'],
      '¿Qué te motiva más a aprender en un curso?' => ['Obtener buenas calificaciones', 'Comprender y aplicar lo aprendido', 'Resolver retos o problemas interesantes', 'Trabajar en equipo o compartir ideas', 'Usar herramientas tecnológicas divertidas']
    ]
  ]
];

$preguntasMultiples = [
  1 => [1, 4], 
  2 => [1,3],   
  3 => [3], 
];


$currentSection = $questions[$page];
?>
<!DOCTYPE html>
<html>
<head>
  <title>Formulario de Intereses</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h2><?= $currentSection['title'] ?></h2>
  <form method="post" action="<?= $page < $totalPages ? '?page=' . ($page + 1) : 'resultado.php' ?>">
    <?php $index = 1; foreach ($currentSection['questions'] as $question => $options): ?>
        <?php $isMultiple = isset($preguntasMultiples[$page]) && in_array($index, $preguntasMultiples[$page]);?>
      <div class="mb-3">
        <label class="form-label"><strong><?= $question ?></strong></label>

        
        <?php if ($isMultiple): ?>
        <p class="text-info"><small>Puedes seleccionar más de una opción.</small></p>
        <?php endif; ?>

        <?php foreach ($options as $key => $option): ?>
          <div class="form-check">
            <input class="form-check-input"
                  type="<?= $isMultiple ? 'checkbox' : 'radio' ?>"
                  name="q<?= $page ?>_<?= $index ?><?= $isMultiple ? '[]' : '' ?>"
                  value="<?= chr(97 + $key) ?>"
                  <?= $isMultiple ? '' : 'required' ?>>
            <label class="form-check-label">a) <?= $option ?></label>
          </div>
        <?php endforeach; ?>
      </div>
      <?php $index++; endforeach; ?>
    <button type="submit" class="btn btn-primary"><?= $page < $totalPages ? 'Siguiente' : 'Enviar' ?></button>
  </form>
</div>
</body>
</html>
