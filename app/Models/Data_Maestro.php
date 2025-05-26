<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../Core/Conexion.php';

use App\Core\Conexion;

class Data_Maestro {
    private $conexion;
    private $maestro_id;

    public function __construct($maestro_id) {
        $dbConnection = new Conexion();
        $this->conexion = $dbConnection->getConexion();
        $this->maestro_id = $maestro_id;
    }

private function contarOpciones($pregunta) {
    $consulta = "SELECT pa.$pregunta AS opcion
                 FROM preferenciasalumno pa
                 JOIN alumno a ON pa.alumno_id = a.id
                 WHERE a.id IN (
                     SELECT DISTINCT ac.alumno_id
                     FROM alumnocurso ac
                     JOIN cursos c ON ac.curso_id = c.id
                     JOIN clases cl ON ac.clase_id = cl.id
                     WHERE cl.maestro_id = :maestro_id
                 )";

    try {
        $stmt = $this->conexion->prepare($consulta);
        $stmt->bindParam(':maestro_id', $this->maestro_id, \PDO::PARAM_INT);
        $stmt->execute();

        $conteo = [];
        while ($fila = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $opcion = $fila['opcion'];
            if (!isset($conteo[$opcion])) {
                $conteo[$opcion] = 0;
            }
            $conteo[$opcion]++;
        }
        return $conteo;
    } catch (\PDOException $e) {
        die("Error en consulta contarOpciones: " . $e->getMessage());
    }
}


    public function getDatosPreguntas() {
        return [
            'pregunta1' => $this->contarOpciones('pregunta1'),
            'pregunta2' => $this->contarOpciones('pregunta2'),
            'pregunta3' => $this->contarOpciones('pregunta3'),
            'pregunta4' => $this->contarOpciones('pregunta4'),
            'pregunta5' => $this->contarOpciones('pregunta5'),
            'pregunta6' => $this->contarOpciones('pregunta6'),
            'pregunta7' => $this->contarOpciones('pregunta7'),
            'pregunta8' => $this->contarOpciones('pregunta8'),
            'pregunta9' => $this->contarOpciones('pregunta9'),
            'pregunta10' => $this->contarOpciones('pregunta10'),
            'pregunta11' => $this->contarOpciones('pregunta11'),
            'pregunta12' => $this->contarOpciones('pregunta12'),
            'pregunta13' => $this->contarOpciones('pregunta13'),
            'pregunta14' => $this->contarOpciones('pregunta14'),
            'pregunta15' => $this->contarOpciones('pregunta15'),
            'pregunta16' => $this->contarOpciones('pregunta16'),
            'pregunta17' => $this->contarOpciones('pregunta17'),
            'pregunta18' => $this->contarOpciones('pregunta18'),
            'pregunta19' => $this->contarOpciones('pregunta19'),
            'pregunta20' => $this->contarOpciones('pregunta20'),
        ];
    }

    public function getPreguntasTexto() {
        return [
            'pregunta1' => '¿Qué área del conocimiento te interesa más?',
            'pregunta2' => '¿En qué área sueles obtener mejores calificaciones?',
            'pregunta3' => '¿Te gusta más trabajar solo o en equipo?',
            'pregunta4' => '¿Qué prefieres hacer?',
            'pregunta5' => '¿Cómo te gusta aprender temas nuevos?',
            'pregunta6' => '¿Qué te gusta hacer en tu tiempo libre?',
            'pregunta7' => '¿Qué prefieres usar en tus actividades escolares o creativas?',
            'pregunta8' => '¿Qué tipo de actividades disfrutas más?',
            'pregunta9' => '¿Qué tipo de juegos prefieres?',
            'pregunta10' => 'Si pudieras aprender algo nuevo, ¿qué escogerías?',
            'pregunta11' => '¿Cómo aprendes mejor?',
            'pregunta12' => '¿Prefieres aprender solo o en grupo?',
            'pregunta13' => '¿Qué formato de contenido te resulta más útil?',
            'pregunta14' => '¿Qué herramientas digitales sueles usar para estudiar?',
            'pregunta15' => '¿Con qué frecuencia utilizas tecnología para estudiar?',
            'pregunta16' => '¿Prefieres estudiar en un dispositivo específico?',
            'pregunta17' => '¿Te gusta que el curso tenga ejercicios interactivos (cuestionarios, juegos, simuladores)?',
            'pregunta18' => '¿Utilizas alguna técnica de estudio en formato digital? (marca una o varias)',
            'pregunta19' => '¿Te gustaría tener retroalimentación inmediata en las actividades del curso?',
            'pregunta20' => '¿Qué te motiva más a aprender en un curso?'
        ];
    }
    public function getTotalAlumnos() {
        $sql = "SELECT COUNT(DISTINCT a.id) AS total_alumnos
                FROM alumno a
                JOIN alumnocurso ac ON a.id = ac.alumno_id
                JOIN cursos c ON ac.curso_id = c.id
                JOIN clases cl ON ac.clase_id = cl.id

                WHERE cl.maestro_id = :maestro_id";

        try {
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':maestro_id', $this->maestro_id, \PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result['total_alumnos'] ?? 0;
        } catch (\PDOException $e) {
            die("Error en consulta de alumnos: " . $e->getMessage());
        }
    }
    public function getTotalCursos() {
        $sql = "SELECT COUNT(*) AS total_cursos FROM cursos";

        try {
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result['total_cursos'] ?? 0;
        } catch (\PDOException $e) {
            die("Error en consulta de cursos: " . $e->getMessage());
        }
    }
    public function getAlumnosTerminados() {
        $sql = "SELECT COUNT(DISTINCT ac.alumno_id) AS alumnos_terminados
                FROM alumnocurso ac
                JOIN cursos c ON ac.curso_id = c.id
                JOIN clases cl ON ac.clase_id = cl.id

                WHERE cl.maestro_id = :maestro_id AND ac.estado = 'completado'";

        try {
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':maestro_id', $this->maestro_id, \PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result['alumnos_terminados'] ?? 0;
        } catch (\PDOException $e) {
            die("Error en consulta de alumnos terminados: " . $e->getMessage());
        }
    }

    public function getDatosMaestro() {
        $sql = "SELECT username, email FROM usuarios WHERE id = :maestro_id";

        try {
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':maestro_id', $this->maestro_id, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            die("Error al obtener datos del maestro: " . $e->getMessage());
        }
    }

    public function getDatosProgreso() {
    $sql = "SELECT 
                estado_alumno,
                COUNT(*) as total
            FROM (
                SELECT 
                    ac.alumno_id,
                    CASE 
                        WHEN SUM(ac.estado = 'completado') > 0 THEN 'Completado'
                        WHEN SUM(ac.estado = 'en_progreso') > 0 THEN 'En_progreso'
                        ELSE 'Inscrito'
                    END AS estado_alumno
                FROM alumnocurso ac
                JOIN cursos c ON ac.curso_id = c.id
                JOIN clases cl ON ac.clase_id = cl.id
                WHERE cl.maestro_id = :maestro_id
                GROUP BY ac.alumno_id
            ) as progreso_por_alumno
            GROUP BY estado_alumno";

    try {
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':maestro_id', $this->maestro_id, \PDO::PARAM_INT);
        $stmt->execute();

        $etiquetas = [];
        $valores = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $etiquetas[] = ucfirst($row['estado_alumno']);
            $valores[] = (int)$row['total'];
        }

        return ['etiquetas' => $etiquetas, 'valores' => $valores];
    } catch (\PDOException $e) {
        die("Error en consulta de progreso: " . $e->getMessage());
    }
}


    public function cerrarConexion() {
        $this->conexion = null;
    }
}
?>