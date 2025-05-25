<?php 
    include __DIR__ . '/layouts/header_maestro.php';
?>
<?php
$host = 'localhost';
$usuario = 'root';
$contrasena = 'Angueles.3';
$base_de_datos = 'educacionps';

$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$maestro_id = 4;


function contarOpciones($conexion, $pregunta, $maestro_id) {
    $consulta = "SELECT JSON_UNQUOTE(JSON_EXTRACT(pa.$pregunta, '$')) AS opcion
                 FROM preferenciasalumno pa
                 JOIN alumno a ON pa.alumno_id = a.id
                 JOIN alumnocurso ac ON a.id = ac.alumno_id
                 JOIN cursos c ON ac.curso_id = c.id
                 JOIN clases cl ON c.clase_id = cl.id
                 WHERE cl.maestro_id = $maestro_id";

    $resultado = mysqli_query($conexion, $consulta);

    $conteo = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $opcion = $fila['opcion'];
        if (!isset($conteo[$opcion])) {
            $conteo[$opcion] = 0;
        }
        $conteo[$opcion]++;
    }
    return $conteo;
}


$datosPreguntas = [
    'pregunta1' => contarOpciones($conexion, 'pregunta1',$maestro_id),
    'pregunta2' => contarOpciones($conexion, 'pregunta2',$maestro_id),
    'pregunta4' => contarOpciones($conexion, 'pregunta4',$maestro_id),
    'pregunta5' => contarOpciones($conexion, 'pregunta5',$maestro_id),
    'pregunta11' => contarOpciones($conexion, 'pregunta11',$maestro_id),
    'pregunta15' => contarOpciones($conexion, 'pregunta15',$maestro_id),
    'pregunta20' => contarOpciones($conexion, 'pregunta20',$maestro_id),
];

$preguntasTexto = [
    'pregunta1' => '¿Qué área del conocimiento te interesa más?',
    'pregunta2' => '¿En qué área sueles obtener mejores calificaciones?',
    'pregunta4' => '¿Qué prefieres hacer?',
    'pregunta5' => '¿Cómo te gusta aprender temas nuevos?',
    'pregunta11' => '¿Cómo aprendes mejor?',
    'pregunta15' => '¿Con qué frecuencia utilizas tecnología para estudiar?',
    'pregunta20' => '¿Qué te motiva más a aprender en un curso?'
];
// Total de alumnos
$sqlAlumnos = "SELECT COUNT(DISTINCT a.id) AS total_alumnos
               FROM alumno a
               JOIN alumnocurso ac ON a.id = ac.alumno_id
               JOIN cursos c ON ac.curso_id = c.id
               JOIN clases cl ON c.clase_id = cl.id
               WHERE cl.maestro_id = $maestro_id";

$resultAlumnos = $conexion->query($sqlAlumnos);
if (!$resultAlumnos) {
    die("Error en consulta de alumnos: " . $conexion->error);
}
$totalAlumnos = $resultAlumnos->fetch_assoc()['total_alumnos'];


$sqlCursos = "SELECT COUNT(*) AS total_cursos FROM cursos";
$resultCursos = $conexion->query($sqlCursos);
if (!$resultCursos) {
    die("Error en consulta de cursos: " . $conexion->error);
}
$totalCursos = $resultCursos->fetch_assoc()['total_cursos'];


$sqlAlumnosTerminados = "SELECT COUNT(DISTINCT ac.alumno_id) AS alumnos_terminados
                         FROM alumnocurso ac
                         JOIN cursos c ON ac.curso_id = c.id
                         JOIN clases cl ON c.clase_id = cl.id
                         WHERE cl.maestro_id = $maestro_id AND ac.estado = 'completado'";
$resultTerminados = $conexion->query($sqlAlumnosTerminados);
if (!$resultTerminados) {
    die("Error en consulta de alumnos terminados: " . $conexion->error);
}
$alumnosTerminados = $resultTerminados->fetch_assoc()['alumnos_terminados'];

// Progreso por fecha de completado
$sqlProgreso = "SELECT ac.estado, COUNT(*) AS total
                FROM alumnocurso ac
                JOIN cursos c ON ac.curso_id = c.id
                JOIN clases cl ON c.clase_id = cl.id
                WHERE cl.maestro_id = $maestro_id
                GROUP BY ac.estado";

$resultProgreso = $conexion->query($sqlProgreso);
if (!$resultProgreso) {
    die("Error en consulta de progreso: " . $conexion->error);
}

$etiquetasProgreso = [];
$valoresProgreso = [];
while ($row = $resultProgreso->fetch_assoc()) {
    $etiquetasProgreso[] = ucfirst($row['estado']);
    $valoresProgreso[] = (int)$row['total'];
}

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Maestros</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>


</head>
<body>

<!-- ================== CONTENIDO ================== -->
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-lg-10 mx-auto p-4 mt-5">
            <!-- ===== GRÁFICAS ===== -->
             <div class="titulo px-5 ms-5 mb-4">
                <h1 class="text-dark fw-bold fs-2" >DASHBOARD MAESTRO</h1>
             </div>
             
            <!-- ===== SECCIÓN DINÁMICA DE GRÁFICA ===== -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                    <!-- Gráfica principal a la izquierda -->
                        <div class="col-md-7 d-flex flex-column align-items-center">
                        <!-- Botones de la gráfica -->
                            <h5 id="tituloGrafica" class="fw-bold text-center mb-4"></h5>
                            <div class="grafico-container mx-auto" style="width: 100%; height: 400px;">
                                <canvas id="grafico"></canvas>
                            </div>


                            <div class="d-flex justify-content-center mt-3">
                                <select id="dataSelect" class="form-select me-2 text-center w-auto">
                                    <option value="formulario">Formulario</option>
                                    <option value="progreso">Progreso</option>
                                </select>
                                <button class="btn btn-outline-primary" id="Cambiar_Gráfico">
                                    Cambiar gráfico
                                </button>
                                <button class="btn btn-outline-secondary ms-2" id="Cambiar_Pregunta">
                                    Cambiar pregunta
                                </button>
                            </div>
                        </div>

                        <!-- Recuadro adicional a la derecha -->
                        <div class="col-md-5 d-flex flex-column align-items-start">
                            <div class="card border-secondary w-100">
                                <div class="card-header bg-secondary text-white text-center">
                                    Anexos
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span class="fw-semibold">Total de alumnos:</span>
                                            <span><?= $totalAlumnos ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span class="fw-semibold">Total de cursos:</span>
                                            <span><?= $totalCursos ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span class="fw-semibold">Alumnos que terminaron un curso:</span>
                                            <span><?= $alumnosTerminados ?></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>


<script>
// Variables desde PHP (convertidas a JSON)
const textosPreguntas = <?= json_encode($preguntasTexto) ?>;
const datos = {
    formulario: {
        preguntas: <?= json_encode($datosPreguntas) ?>
    },
    progreso: {
        etiquetas: <?= json_encode($etiquetasProgreso) ?>,
        valores: <?= json_encode($valoresProgreso) ?>
    }
};

let chart;
let tipoGrafico = 'bar';
let indicePregunta = 0;

function dividirTexto(texto, longitud = 20) {
    if (texto.length <= longitud) return texto;
    const palabras = texto.split(" ");
    const lineas = [];
    let linea = "";

    palabras.forEach(palabra => {
        if ((linea + palabra).length > longitud) {
            lineas.push(linea.trim());
            linea = "";
        }
        linea += palabra + " ";
    });
    if (linea.trim()) lineas.push(linea.trim());
    return lineas;
}

function generarColores(cantidad) {
    const baseColor = [54, 162, 235]; // Azul base
    return Array.from({ length: cantidad }, (_, i) => {
        const factor = 0.5 + (i / cantidad) * 0.5; // Tonos del 50% al 100%
        return `rgba(${baseColor[0]}, ${baseColor[1]}, ${baseColor[2]}, ${factor})`;
    });
}


function crearGrafico(tipoContenido) {
    actualizarTituloGrafica(tipoContenido);
    const ctx = document.getElementById('grafico').getContext('2d');
    if (chart) chart.destroy();

    if (tipoContenido === 'formulario') {
        const clavesPreguntas = Object.keys(datos.formulario.preguntas);
        const clave = clavesPreguntas[indicePregunta];
        const valores = datos.formulario.preguntas[clave];
        const etiquetas = Object.keys(valores).map(e => dividirTexto(e));
        const datosValores = Object.values(valores);

        chart = new Chart(ctx, {
            type: tipoGrafico,
            data: {
                labels: etiquetas,
                datasets: [{
                    label: tipoContenido,
                    data: datosValores,
                    backgroundColor: [
                        '#a8e6a3', '#81d981', '#5cc25c',
                        '#42b342', '#2e9e2e', '#1c7f1c'
                    ],

                    borderWidth: 1,
                    fill: false,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: tipoGrafico === 'pie' || tipoGrafico === 'doughnut',
                        position: 'top',
                        labels: {
                            padding: 20 
                        }
                    },
                datalabels: tipoGrafico !== 'line' ? {
                    color: '#000',
                    anchor: 'center',
                    align: 'center',
                    formatter: (value, context) => {
                        const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                        const porcentaje = total ? (value / total * 100).toFixed(1) + '%' : '0%';
                        return porcentaje;
                    }
                } : false

                },
                scales: tipoGrafico !== 'pie' && tipoGrafico !== 'doughnut' ? {
                    y: {
                        beginAtZero: true
                    },
                    x: {
                        ticks: {
                            callback: function(value) {
                                const label = this.getLabelForValue(value);
                                return Array.isArray(label) ? label.join(' ') : label;
                            }
                        }
                    }
                } : {}
            },
            plugins: [ChartDataLabels]
        });

    } else {
        const etiquetas = datos[tipoContenido].etiquetas;
        const valores = datos[tipoContenido].valores;
        if (chart) {
            chart.destroy();
        }

        chart = new Chart(ctx, {
            type: tipoGrafico,
            data: {
                labels: etiquetas,
                datasets: [{
                    label: tipoContenido,
                    data: valores,
                    backgroundColor: tipoGrafico === 'pie' || tipoGrafico === 'doughnut'
                        ? ['#a8e6a3', '#81d981', '#5cc25c', '#42b342', '#2e9e2e'] 
                        : Array(valores.length).fill('rgba(85, 192, 75, 0.5)'), 
                    borderColor: tipoGrafico === 'pie' || tipoGrafico === 'doughnut'
                        ? ['#66bb6a', '#4caf50', '#43a047', '#388e3c', '#2e7d32']
                        : Array(valores.length).fill('rgb(75, 192, 87)'),

                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: tipoGrafico === 'pie' || tipoGrafico === 'doughnut' ? 'bottom' : 'top',
                        labels: {
                            padding: tipoGrafico === 'pie' || tipoGrafico === 'doughnut' ? 20 : 10,
                            boxWidth: 20,
                            boxHeight: 20
                        }
                    },
                    datalabels: tipoGrafico !== 'line' ? {
                        color: '#000',
                        anchor: 'center',
                        align: 'center',
                        formatter: (value, context) => {
                            const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            const porcentaje = total ? (value / total * 100).toFixed(1) + '%' : '0%';
                            return porcentaje;
                        }
                    } : false
                },
                scales: tipoGrafico !== 'pie' && tipoGrafico !== 'doughnut' ? {
                    y: {
                        beginAtZero: true
                    },
                    x: {
                        ticks: {
                            callback: function(value, index) {
                                const label = this.getLabelForValue(value);
                                return Array.isArray(label) ? label : [label];
                            }
                        }
                    }
                } : {},
            },
            plugins: [ChartDataLabels]
        });

    }
}

function cambiarTipoGrafico() {
    const tipos = ['bar', 'line', 'doughnut', 'pie'];
    const index = tipos.indexOf(tipoGrafico);
    tipoGrafico = tipos[(index + 1) % tipos.length];
    const tipoContenido = document.getElementById('dataSelect').value;

    crearGrafico(tipoContenido);
}
function cambiarPregunta() {
    const tipoContenido = document.getElementById('dataSelect').value;
    if (tipoContenido === 'formulario') {
        const total = Object.keys(datos.formulario.preguntas).length;
        indicePregunta = (indicePregunta + 1) % total;
        crearGrafico(tipoContenido);
    }
}

function actualizarTituloGrafica(tipoContenido) {
    const titulo = document.getElementById('tituloGrafica');
    const botonCambiarPregunta = document.getElementById('Cambiar_Pregunta');
    if (tipoContenido === 'formulario') {
        const clavesPreguntas = Object.keys(datos.formulario.preguntas);
        const clave = clavesPreguntas[indicePregunta];
        titulo.textContent = "Formulario - " + (textosPreguntas[clave] || clave);
        botonCambiarPregunta.style.display = 'inline-block'; // Mostrar botón
    } else if (tipoContenido === 'progreso') {
        titulo.textContent = "Progreso de Alumnos";
        botonCambiarPregunta.style.display = 'none';
    } else {
        titulo.textContent = "";
        botonCambiarPregunta.style.display = 'none';
    }
}


document.addEventListener("DOMContentLoaded", () => {
   document.getElementById('dataSelect').addEventListener('change', () => {
       const seleccion = document.getElementById('dataSelect').value;
       indicePregunta = 0;
       crearGrafico(seleccion);
   });

   document.getElementById('Cambiar_Gráfico').addEventListener('click', cambiarTipoGrafico);

   document.getElementById('Cambiar_Pregunta').addEventListener('click', cambiarPregunta);

   crearGrafico('formulario');
});


</script>
<?php
$conexion->close();
?>

<?php 
    include __DIR__ . '/layouts/footer.php'; 
?>