<?php
class MaestroController {
    private $datosPreguntas;
    private $preguntasTexto;
    private $datosProgreso;

    public function __construct($datosPreguntas, $preguntasTexto, $datosProgreso) {
        $this->datosPreguntas = $datosPreguntas;
        $this->preguntasTexto = $preguntasTexto;
        $this->datosProgreso = $datosProgreso;
    }

    
    public function generarJavaScript() {
        $textosPreguntas = json_encode($this->preguntasTexto);
        $datosPreguntas = json_encode($this->datosPreguntas);
        $etiquetasProgreso = json_encode($this->datosProgreso['etiquetas']);
        $valoresProgreso = json_encode($this->datosProgreso['valores']);

        return "
        <script>
        // Variables desde PHP (convertidas a JSON)
        const textosPreguntas = {$textosPreguntas};
        const datos = {
            formulario: {
                preguntas: {$datosPreguntas}
            },
            progreso: {
                etiquetas: {$etiquetasProgreso},
                valores: {$valoresProgreso}
            }
        };

        let chart;
        let tipoGrafico = 'bar';
        let indicePregunta = 0;

        function dividirTexto(texto, longitud = 20) {
            if (texto.length <= longitud) return texto;
            const palabras = texto.split(' ');
            const lineas = [];
            let linea = '';

            palabras.forEach(palabra => {
                if ((linea + palabra).length > longitud) {
                    lineas.push(linea.trim());
                    linea = '';
                }
                linea += palabra + ' ';
            });
            if (linea.trim()) lineas.push(linea.trim());
            return lineas;
        }

        function generarColores(cantidad) {
            const baseColor = [75, 192, 192];
            return Array.from({ length: cantidad }, (_, i) => {
                const factor = 0.5 + (i / cantidad) * 0.5; // Tonos del 50% al 100%
                return `rgba(\${baseColor[0]}, \${baseColor[1]}, \${baseColor[2]}, \${factor})`;
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
                            backgroundColor: generarColores(datosValores.length),
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

                chart = new Chart(ctx, {
                    type: tipoGrafico,
                    data: {
                        labels: etiquetas,
                        datasets: [{
                            label: tipoContenido,
                            data: valores,
                            backgroundColor: generarColores(valores.length),
                            borderColor: generarColores(valores.length).map(color => 
                                color.replace(/[\\d\\.]+\\)$/g, '1)')
                            ),
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
                titulo.textContent = 'Formulario - ' + (textosPreguntas[clave] || clave);
                botonCambiarPregunta.style.display = 'inline-block';
            } else if (tipoContenido === 'progreso') {
                titulo.textContent = 'Progreso de Alumnos';
                botonCambiarPregunta.style.display = 'none';
            } else {
                titulo.textContent = '';
                botonCambiarPregunta.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
           document.getElementById('dataSelect').addEventListener('change', () => {
               const seleccion = document.getElementById('dataSelect').value;
               indicePregunta = 0;
               crearGrafico(seleccion);
           });

           document.getElementById('Cambiar_Gráfico').addEventListener('click', cambiarTipoGrafico);
           document.getElementById('Cambiar_Pregunta').addEventListener('click', cambiarPregunta);

           crearGrafico('formulario');
        });
        </script>";
    }

    public function generarDatosJSON() {
        return [
            'textosPreguntas' => $this->preguntasTexto,
            'datosPreguntas' => $this->datosPreguntas,
            'datosProgreso' => $this->datosProgreso
        ];
    }
}
?>