<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../Core/Conexion.php';

use App\Core\Conexion;

class Data_Alumno {
    private $conexion;
    private $alumno_id;

    public function __construct($alumno_id) {
        $dbConnection = new Conexion();
        $this->conexion = $dbConnection->getConexion();
        $this->alumno_id = $alumno_id;
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
    
    public function getDatosProgreso() {
        $sql = "SELECT ac.estado, COUNT(*) AS total
                FROM alumnocurso ac
                WHERE ac.alumno_id = :alumno_id
                GROUP BY ac.estado";

        try {
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':alumno_id', $this->alumno_id, \PDO::PARAM_INT);
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
    public function getCursosAlumno() {
    $sql = "SELECT c.titulo as nombre, ac.estado, c.id as curso_id
            FROM cursos c 
            INNER JOIN alumnocurso ac ON c.id = ac.curso_id 
            WHERE ac.alumno_id = :alumno_id";
    
    try {
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':alumno_id', $this->alumno_id, \PDO::PARAM_INT);
        $stmt->execute();
        
        $etiquetas = [];
        $valores = [];
        // Los estados válidos según tu tabla son: 'asignado' y 'completado'
        $estados = ['asignado' => 0, 'completado' => 0];
        
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $estados[$row['estado']]++;
        }
        
        foreach ($estados as $estado => $cantidad) {
            if ($cantidad > 0) {
                $etiquetas[] = ucfirst($estado);
                $valores[] = $cantidad;
            }
        }
        
        return ['etiquetas' => $etiquetas, 'valores' => $valores];
    } catch (\PDOException $e) {
        die("Error en consulta de cursos: " . $e->getMessage());
    }
}

public function getProgresoDetallado() {
    // Como no existe campo 'progreso' en tu tabla, calculamos el progreso
    // basado en el estado (completado = 100%, asignado = 0%)
    $sql = "SELECT c.titulo as nombre,
                   CASE 
                       WHEN ac.estado = 'completado' THEN 100 
                       ELSE 0 
                   END as progreso
            FROM cursos c 
            INNER JOIN alumnocurso ac ON c.id = ac.curso_id 
            WHERE ac.alumno_id = :alumno_id";
    
    try {
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':alumno_id', $this->alumno_id, \PDO::PARAM_INT);
        $stmt->execute();
        
        $etiquetas = [];
        $valores = [];
        
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $etiquetas[] = $row['nombre'];
            $valores[] = (int)$row['progreso'];
        }
        
        return ['etiquetas' => $etiquetas, 'valores' => $valores];
    } catch (\PDOException $e) {
        die("Error en consulta de progreso detallado: " . $e->getMessage());
    }
}

public function getCursosConProgreso() {
    $sql = "SELECT c.titulo as nombre,
                   CASE 
                       WHEN ac.estado = 'completado' THEN 100 
                       ELSE 0 
                   END as progreso,
                   ac.estado,
                   ac.fecha_asignacion,
                   ac.fecha_completado,
                   ac.evidencia,
                   cat.nombre as categoria
            FROM cursos c 
            INNER JOIN alumnocurso ac ON c.id = ac.curso_id 
            LEFT JOIN categoriascurso cat ON c.categoria_id = cat.id
            WHERE ac.alumno_id = :alumno_id";
    
    try {
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':alumno_id', $this->alumno_id, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        die("Error en consulta de cursos con progreso: " . $e->getMessage());
    }
}

// Método adicional para obtener información completa del alumno
public function getInfoComplementariaAlumno() {
    $sql = "SELECT 
                a.nombre as alumno_nombre,
                COUNT(ac.id) as total_cursos,
                SUM(CASE WHEN ac.estado = 'completado' THEN 1 ELSE 0 END) as cursos_completados,
                SUM(CASE WHEN ac.estado = 'asignado' THEN 1 ELSE 0 END) as cursos_asignados
            FROM alumno a
            LEFT JOIN alumnocurso ac ON a.id = ac.alumno_id
            WHERE a.id = :alumno_id
            GROUP BY a.id, a.nombre";
    
    try {
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':alumno_id', $this->alumno_id, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        die("Error en consulta de información del alumno: " . $e->getMessage());
    }
}

// Método para obtener cursos por categoría
public function getCursosPorCategoria() {
    $sql = "SELECT 
                cat.nombre as categoria,
                COUNT(ac.id) as cantidad_cursos,
                SUM(CASE WHEN ac.estado = 'completado' THEN 1 ELSE 0 END) as completados
            FROM categoriascurso cat
            INNER JOIN cursos c ON cat.id = c.categoria_id
            INNER JOIN alumnocurso ac ON c.id = ac.curso_id
            WHERE ac.alumno_id = :alumno_id
            GROUP BY cat.id, cat.nombre";
    
    try {
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':alumno_id', $this->alumno_id, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        die("Error en consulta de cursos por categoría: " . $e->getMessage());
    }
}

// Método para obtener las clases del alumno
public function getClasesAlumno() {
    $sql = "SELECT 
                cl.nombre as clase_nombre,
                cl.codigo,
                cl.descripcion,
                m.nombre as maestro_nombre,
                ac.fecha_inscripcion,
                ac.estado
            FROM clases cl
            INNER JOIN alumnoclase ac ON cl.id = ac.clase_id
            INNER JOIN maestro m ON cl.maestro_id = m.id
            WHERE ac.alumno_id = :alumno_id
            AND ac.estado = 'activo'";
    
    try {
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':alumno_id', $this->alumno_id, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        die("Error en consulta de clases del alumno: " . $e->getMessage());
    }
}

public function cerrarConexion() {
    $this->conexion = null;
}
}
?>