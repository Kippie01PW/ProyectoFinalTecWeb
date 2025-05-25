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

    /**
     * Cuenta las opciones de una pregunta específica del formulario
     */
    private function contarOpciones($pregunta) {
        $consulta = "SELECT JSON_UNQUOTE(JSON_EXTRACT(pa.$pregunta, '$')) AS opcion
                     FROM preferenciasalumno pa
                     JOIN alumno a ON pa.alumno_id = a.id
                     JOIN alumnocurso ac ON a.id = ac.alumno_id
                     JOIN cursos c ON ac.curso_id = c.id
                     JOIN clases cl ON c.clase_id = cl.id
                     WHERE cl.maestro_id = :maestro_id";

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

    /**
     * Obtiene los datos de todas las preguntas del formulario
     */
    public function getDatosPreguntas() {
        return [
            'pregunta1' => $this->contarOpciones('pregunta1'),
            'pregunta2' => $this->contarOpciones('pregunta2'),
            'pregunta4' => $this->contarOpciones('pregunta4'),
            'pregunta5' => $this->contarOpciones('pregunta5'),
            'pregunta11' => $this->contarOpciones('pregunta11'),
            'pregunta15' => $this->contarOpciones('pregunta15'),
            'pregunta20' => $this->contarOpciones('pregunta20'),
        ];
    }

    /**
     * Obtiene los textos de las preguntas
     */
    public function getPreguntasTexto() {
        return [
            'pregunta1' => '¿Qué área del conocimiento te interesa más?',
            'pregunta2' => '¿En qué área sueles obtener mejores calificaciones?',
            'pregunta4' => '¿Qué prefieres hacer?',
            'pregunta5' => '¿Cómo te gusta aprender temas nuevos?',
            'pregunta11' => '¿Cómo aprendes mejor?',
            'pregunta15' => '¿Con qué frecuencia utilizas tecnología para estudiar?',
            'pregunta20' => '¿Qué te motiva más a aprender en un curso?'
        ];
    }

    /**
     * Obtiene el total de alumnos del maestro
     */
    public function getTotalAlumnos() {
        $sql = "SELECT COUNT(DISTINCT a.id) AS total_alumnos
                FROM alumno a
                JOIN alumnocurso ac ON a.id = ac.alumno_id
                JOIN cursos c ON ac.curso_id = c.id
                JOIN clases cl ON c.clase_id = cl.id
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

    /**
     * Obtiene el total de cursos
     */
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

    /**
     * Obtiene el total de alumnos que terminaron cursos
     */
    public function getAlumnosTerminados() {
        $sql = "SELECT COUNT(DISTINCT ac.alumno_id) AS alumnos_terminados
                FROM alumnocurso ac
                JOIN cursos c ON ac.curso_id = c.id
                JOIN clases cl ON c.clase_id = cl.id
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

    /**
     * Obtiene los datos del maestro
     */
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

    /**
     * Obtiene los datos de progreso de alumnos
     */
    public function getDatosProgreso() {
        $sql = "SELECT ac.estado, COUNT(*) AS total
                FROM alumnocurso ac
                JOIN cursos c ON ac.curso_id = c.id
                JOIN clases cl ON c.clase_id = cl.id
                WHERE cl.maestro_id = :maestro_id
                GROUP BY ac.estado";

        try {
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':maestro_id', $this->maestro_id, \PDO::PARAM_INT);
            $stmt->execute();

            $etiquetas = [];
            $valores = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $etiquetas[] = ucfirst($row['estado']);
                $valores[] = (int)$row['total'];
            }

            return ['etiquetas' => $etiquetas, 'valores' => $valores];
        } catch (\PDOException $e) {
            die("Error en consulta de progreso: " . $e->getMessage());
        }
    }

    /**
     * Cierra la conexión PDO (opcional)
     */
    public function cerrarConexion() {
        $this->conexion = null;
    }
}
?>
