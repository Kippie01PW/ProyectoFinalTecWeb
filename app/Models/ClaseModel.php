<?php
namespace App\Models;

use PDO;

class ClaseModel {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Crea una nueva clase con cursos y alumnos asignados
     */
    public function crearClase($maestro_id, $nombre, $descripcion = null, $cursos = [], $alumnos = []) {
        $this->db->beginTransaction();
        
        try {
            $codigo = $this->generarCodigo();
            
            $sql = "INSERT INTO clases (maestro_id, codigo, nombre, descripcion) 
                    VALUES (:maestro_id, :codigo, :nombre, :descripcion)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':maestro_id' => $maestro_id,
                ':codigo' => $codigo,
                ':nombre' => $nombre,
                ':descripcion' => $descripcion
            ]);
            
            $clase_id = $this->db->lastInsertId();
            
            // Asignar cursos a la clase
            if (!empty($cursos)) {
                $this->asignarCursosAClase($clase_id, $cursos);
            }
            
            // Asignar alumnos a la clase
            if (!empty($alumnos)) {
                $this->asignarAlumnosAClase($clase_id, $alumnos);
            }
            
            $this->db->commit();
            return $clase_id;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Obtiene las clases de un maestro con alumnos y cursos
     */
    public function obtenerClasesPorMaestro($maestro_id) {
        $sql = "SELECT c.*, COUNT(DISTINCT ac.alumno_id) as total_alumnos,
                       COUNT(DISTINCT cc.curso_id) as total_cursos
                FROM clases c 
                LEFT JOIN alumnoclase ac ON c.id = ac.clase_id AND ac.estado = 'activo'
                LEFT JOIN clasecurso cc ON c.id = cc.clase_id AND cc.estado = 'activo'
                WHERE c.maestro_id = :maestro_id 
                GROUP BY c.id
                ORDER BY c.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':maestro_id' => $maestro_id]);
        
        $clases = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Para cada clase, obtener alumnos y cursos
        foreach ($clases as &$clase) {
            $clase['alumnos'] = $this->obtenerAlumnosPorClase($clase['id']);
            $clase['cursos'] = $this->obtenerCursosPorClase($clase['id']);
        }
        
        return $clases;
    }

    /**
     * Obtiene una clase por su código
     */
    public function obtenerClasePorCodigo($codigo) {
        $sql = "SELECT id FROM clases WHERE codigo = :codigo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':codigo' => $codigo]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id'] : null;
    }

    /**
     * Obtiene el código de una clase
     */
    public function obtenerCodigoClase($clase_id) {
        $sql = "SELECT codigo FROM clases WHERE id = :clase_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':clase_id' => $clase_id]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['codigo'] : null;
    }

    /**
     * Inscribe un alumno a una clase
     */
    public function inscribirAlumno($alumno_id, $clase_id) {
        return $this->inscribirAlumnoConCursos($alumno_id, $clase_id);
    }

    /**
     * Obtiene los alumnos de una clase
     */
    public function obtenerAlumnosPorClase($clase_id) {
        $sql = "SELECT a.id, a.nombre, u.email, ac.fecha_inscripcion
                FROM alumnoclase ac
                JOIN alumno a ON ac.alumno_id = a.id
                JOIN usuarios u ON a.usuario_id = u.id
                WHERE ac.clase_id = :clase_id AND ac.estado = 'activo'
                ORDER BY ac.fecha_inscripcion DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':clase_id' => $clase_id]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Genera un código único para la clase
     */
    private function generarCodigo() {
        do {
            $codigo = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
            $sql = "SELECT id FROM clases WHERE codigo = :codigo";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':codigo' => $codigo]);
        } while ($stmt->fetch());
        
        return $codigo;
    }

    /**
     * Obtiene información completa de una clase
     */
    public function obtenerClase($clase_id) {
        $sql = "SELECT c.*, m.nombre as maestro_nombre, u.email as maestro_email
                FROM clases c
                JOIN maestro m ON c.maestro_id = m.id
                JOIN usuarios u ON m.usuario_id = u.id
                WHERE c.id = :clase_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':clase_id' => $clase_id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene los cursos asignados a un alumno específico
     */
    public function obtenerCursosDeAlumno($alumno_id) {
        $sql = "SELECT c.id, c.titulo, c.descripcion, cat.nombre as categoria_nombre, 
                       ac.estado, ac.fecha_asignacion, ac.fecha_completado,
                       cl.nombre as clase_nombre, cl.codigo as clase_codigo
                FROM alumnocurso ac
                JOIN cursos c ON ac.curso_id = c.id
                LEFT JOIN categoriascurso cat ON c.categoria_id = cat.id
                JOIN clases cl ON ac.clase_id = cl.id
                WHERE ac.alumno_id = :alumno_id
                ORDER BY ac.fecha_asignacion DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':alumno_id' => $alumno_id]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Marca un curso como completado para un alumno
     */
    public function marcarCursoCompletado($alumno_id, $curso_id, $evidencia = null) {
        $sql = "UPDATE alumnocurso 
                SET estado = 'completado', 
                    fecha_completado = NOW(),
                    evidencia = :evidencia
                WHERE alumno_id = :alumno_id AND curso_id = :curso_id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':alumno_id' => $alumno_id,
            ':curso_id' => $curso_id,
            ':evidencia' => $evidencia
        ]);
    }

    /**
     * Obtiene estadísticas de una clase
     */
    public function obtenerEstadisticasClase($clase_id) {
        $stats = [];
        
        // Total de alumnos
        $sql = "SELECT COUNT(*) as total FROM alumnoclase WHERE clase_id = :clase_id AND estado = 'activo'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':clase_id' => $clase_id]);
        $stats['total_alumnos'] = $stmt->fetchColumn();
        
        // Total de cursos
        $sql = "SELECT COUNT(*) as total FROM clasecurso WHERE clase_id = :clase_id AND estado = 'activo'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':clase_id' => $clase_id]);
        $stats['total_cursos'] = $stmt->fetchColumn();
        
        // Total de asignaciones alumno-curso
        $sql = "SELECT COUNT(*) as total FROM alumnocurso WHERE clase_id = :clase_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':clase_id' => $clase_id]);
        $stats['total_asignaciones'] = $stmt->fetchColumn();
        
        // Cursos completados
        $sql = "SELECT COUNT(*) as total FROM alumnocurso WHERE clase_id = :clase_id AND estado = 'completado'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':clase_id' => $clase_id]);
        $stats['cursos_completados'] = $stmt->fetchColumn();
        
        return $stats;
    }

    /**
     * Actualiza los datos básicos de una clase
     */
    public function actualizarClase($clase_id, $nombre, $descripcion = null) {
        $sql = "UPDATE clases SET nombre = :nombre, descripcion = :descripcion WHERE id = :clase_id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':nombre' => $nombre,
            ':descripcion' => $descripcion,
            ':clase_id' => $clase_id
        ]);
    }

    /**
     * Agrega nuevos cursos a una clase existente
     */
    public function agregarCursosAClase($clase_id, $nuevos_cursos) {
        if (empty($nuevos_cursos)) return true;
        
        $this->db->beginTransaction();
        
        try {
            // Insertar nuevos cursos en clasecurso
            $sql = "INSERT IGNORE INTO clasecurso (clase_id, curso_id) VALUES (:clase_id, :curso_id)";
            $stmt = $this->db->prepare($sql);
            
            foreach ($nuevos_cursos as $curso_id) {
                $stmt->execute([
                    ':clase_id' => $clase_id,
                    ':curso_id' => $curso_id
                ]);
            }
            
            // Crear relaciones alumno-curso para los nuevos cursos
            $this->crearRelacionesAlumnoCurso($clase_id, $nuevos_cursos);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Agrega nuevos alumnos a una clase existente
     */
    public function agregarAlumnosAClase($clase_id, $nuevos_alumnos) {
        if (empty($nuevos_alumnos)) return true;
        
        $this->db->beginTransaction();
        
        try {
            // Insertar nuevos alumnos en alumnoclase
            $sql = "INSERT IGNORE INTO alumnoclase (alumno_id, clase_id) VALUES (:alumno_id, :clase_id)";
            $stmt = $this->db->prepare($sql);
            
            foreach ($nuevos_alumnos as $alumno_id) {
                $stmt->execute([
                    ':alumno_id' => $alumno_id,
                    ':clase_id' => $clase_id
                ]);
            }
            
            // Obtener cursos existentes de la clase y asignarlos a los nuevos alumnos
            $cursos = $this->obtenerCursosDeLaClase($clase_id);
            if (!empty($cursos)) {
                $this->crearRelacionesAlumnoCurso($clase_id, array_column($cursos, 'id'), $nuevos_alumnos);
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Elimina cursos de una clase
     */
    public function eliminarCursosDeClase($clase_id, $cursos_a_eliminar) {
        if (empty($cursos_a_eliminar)) return true;
        
        $this->db->beginTransaction();
        
        try {
            // Eliminar de clasecurso
            $sql = "DELETE FROM clasecurso WHERE clase_id = :clase_id AND curso_id = :curso_id";
            $stmt = $this->db->prepare($sql);
            
            foreach ($cursos_a_eliminar as $curso_id) {
                $stmt->execute([
                    ':clase_id' => $clase_id,
                    ':curso_id' => $curso_id
                ]);
            }
            
            // Eliminar de alumnocurso
            $sql = "DELETE FROM alumnocurso WHERE clase_id = :clase_id AND curso_id = :curso_id";
            $stmt = $this->db->prepare($sql);
            
            foreach ($cursos_a_eliminar as $curso_id) {
                $stmt->execute([
                    ':clase_id' => $clase_id,
                    ':curso_id' => $curso_id
                ]);
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Elimina alumnos de una clase
     */
    public function eliminarAlumnosDeClase($clase_id, $alumnos_a_eliminar) {
        if (empty($alumnos_a_eliminar)) return true;
        
        $this->db->beginTransaction();
        
        try {
            // Eliminar de alumnoclase
            $sql = "DELETE FROM alumnoclase WHERE clase_id = :clase_id AND alumno_id = :alumno_id";
            $stmt = $this->db->prepare($sql);
            
            foreach ($alumnos_a_eliminar as $alumno_id) {
                $stmt->execute([
                    ':clase_id' => $clase_id,
                    ':alumno_id' => $alumno_id
                ]);
            }
            
            // Eliminar de alumnocurso
            $sql = "DELETE FROM alumnocurso WHERE clase_id = :clase_id AND alumno_id = :alumno_id";
            $stmt = $this->db->prepare($sql);
            
            foreach ($alumnos_a_eliminar as $alumno_id) {
                $stmt->execute([
                    ':clase_id' => $clase_id,
                    ':alumno_id' => $alumno_id
                ]);
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Obtiene cursos disponibles que NO están en la clase
     */
    public function obtenerCursosDisponibles($clase_id) {
        $sql = "SELECT c.id, c.titulo, c.descripcion, cat.nombre as categoria_nombre
                FROM cursos c
                LEFT JOIN categoriascurso cat ON c.categoria_id = cat.id
                WHERE c.id NOT IN (
                    SELECT curso_id FROM clasecurso WHERE clase_id = :clase_id AND estado = 'activo'
                )
                ORDER BY cat.nombre ASC, c.titulo ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':clase_id' => $clase_id]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene alumnos disponibles que NO están en la clase
     */
    public function obtenerAlumnosDisponibles($clase_id) {
        $sql = "SELECT a.id, a.nombre, u.email
                FROM alumno a
                JOIN usuarios u ON a.usuario_id = u.id
                WHERE u.estado = 1 AND a.id NOT IN (
                    SELECT alumno_id FROM alumnoclase WHERE clase_id = :clase_id AND estado = 'activo'
                )
                ORDER BY a.nombre ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':clase_id' => $clase_id]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crea las relaciones automáticas en la tabla alumnocurso
     */
    private function crearRelacionesAlumnoCurso($clase_id, $cursos, $alumnos_especificos = null) {
        // Si no se especifican alumnos, obtener todos los de la clase
        if ($alumnos_especificos === null) {
            $sql_alumnos = "SELECT alumno_id FROM alumnoclase WHERE clase_id = :clase_id AND estado = 'activo'";
            $stmt_alumnos = $this->db->prepare($sql_alumnos);
            $stmt_alumnos->execute([':clase_id' => $clase_id]);
            $alumnos = $stmt_alumnos->fetchAll(PDO::FETCH_COLUMN);
        } else {
            $alumnos = $alumnos_especificos;
        }

        // Crear relación alumno-curso para cada combinación
        $sql = "INSERT IGNORE INTO alumnocurso (alumno_id, curso_id, clase_id, estado) 
                VALUES (:alumno_id, :curso_id, :clase_id, 'asignado')";
        $stmt = $this->db->prepare($sql);

        foreach ($alumnos as $alumno_id) {
            foreach ($cursos as $curso_id) {
                $stmt->execute([
                    ':alumno_id' => $alumno_id,
                    ':curso_id' => $curso_id,
                    ':clase_id' => $clase_id
                ]);
            }
        }
    }

    /**
     * Obtiene los IDs de los cursos asignados a una clase
     */
    private function obtenerCursosDeLaClase($clase_id) {
        $sql = "SELECT curso_id as id FROM clasecurso WHERE clase_id = :clase_id AND estado = 'activo'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':clase_id' => $clase_id]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Inscribe un alumno a una clase y le asigna automáticamente todos los cursos
     */
    public function inscribirAlumnoConCursos($alumno_id, $clase_id) {
        // Verificar si ya está inscrito
        $sqlCheck = "SELECT id FROM alumnoclase 
                     WHERE alumno_id = :alumno_id AND clase_id = :clase_id";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->execute([
            ':alumno_id' => $alumno_id,
            ':clase_id' => $clase_id
        ]);
        
        if ($stmtCheck->fetch()) {
            throw new \Exception('Ya estás inscrito en esta clase');
        }

        $this->db->beginTransaction();
        
        try {
            // Inscribir en alumnoclase
            $sql = "INSERT INTO alumnoclase (alumno_id, clase_id) 
                    VALUES (:alumno_id, :clase_id)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':alumno_id' => $alumno_id,
                ':clase_id' => $clase_id
            ]);

            // Obtener cursos de la clase y asignarlos al alumno
            $cursos = $this->obtenerCursosDeLaClase($clase_id);
            if (!empty($cursos)) {
                $this->crearRelacionesAlumnoCurso($clase_id, array_column($cursos, 'id'), [$alumno_id]);
            }

            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Obtiene los cursos asignados a una clase
     */
    public function obtenerCursosPorClase($clase_id) {
        $sql = "SELECT c.id, c.titulo, c.descripcion, cat.nombre as categoria_nombre
                FROM clasecurso cc
                JOIN cursos c ON cc.curso_id = c.id
                LEFT JOIN categoriascurso cat ON c.categoria_id = cat.id
                WHERE cc.clase_id = :clase_id AND cc.estado = 'activo'
                ORDER BY c.titulo ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':clase_id' => $clase_id]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todos los cursos disponibles
     */
    public function obtenerTodosLosCursos() {
        $sql = "SELECT c.id, c.titulo, c.descripcion, cat.nombre as categoria_nombre
                FROM cursos c
                LEFT JOIN categoriascurso cat ON c.categoria_id = cat.id
                ORDER BY cat.nombre ASC, c.titulo ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todos los alumnos disponibles
     */
    public function obtenerTodosLosAlumnos() {
        $sql = "SELECT a.id, a.nombre, u.email
                FROM alumno a
                JOIN usuarios u ON a.usuario_id = u.id
                WHERE u.estado = 1
                ORDER BY a.nombre ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Asigna cursos a una clase y crea las relaciones alumno-curso
     */
    private function asignarCursosAClase($clase_id, $cursos) {
        // Insertar en clasecurso
        $sql = "INSERT INTO clasecurso (clase_id, curso_id) VALUES (:clase_id, :curso_id)";
        $stmt = $this->db->prepare($sql);
        
        foreach ($cursos as $curso_id) {
            $stmt->execute([
                ':clase_id' => $clase_id,
                ':curso_id' => $curso_id
            ]);
        }
        
        // Crear relaciones alumno-curso para todos los alumnos ya inscritos en la clase
        $this->crearRelacionesAlumnoCurso($clase_id, $cursos);
    }





/**
 * Obtiene evidencias de cursos completados para un maestro
 */
public function obtenerEvidenciasCompletadas($maestro_id) {
    $sql = "SELECT 
                a.nombre as alumno_nombre,
                u.email as alumno_email,
                c.titulo as curso_titulo,
                cl.nombre as clase_nombre,
                ac.evidencia,
                ac.fecha_completado
            FROM alumnocurso ac
            INNER JOIN alumno a ON ac.alumno_id = a.id
            INNER JOIN usuarios u ON a.usuario_id = u.id
            INNER JOIN cursos c ON ac.curso_id = c.id
            INNER JOIN clases cl ON ac.clase_id = cl.id
            WHERE cl.maestro_id = :maestro_id 
              AND ac.estado = 'completado' 
              AND ac.evidencia IS NOT NULL
            ORDER BY ac.fecha_completado DESC";
    
    try {
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':maestro_id' => $maestro_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\Exception $e) {
        error_log("Error al obtener evidencias: " . $e->getMessage());
        return [];
    }
}    






    /**
     * Asigna alumnos a una clase y crea las relaciones alumno-curso
     */
    private function asignarAlumnosAClase($clase_id, $alumnos) {
        // Insertar en alumnoclase
        $sql = "INSERT INTO alumnoclase (alumno_id, clase_id) VALUES (:alumno_id, :clase_id)";
        $stmt = $this->db->prepare($sql);
        
        foreach ($alumnos as $alumno_id) {
            $stmt->execute([
                ':alumno_id' => $alumno_id,
                ':clase_id' => $clase_id
            ]);
        }
        
        // Crear relaciones alumno-curso para todos los cursos ya asignados a la clase
        $cursos = $this->obtenerCursosDeLaClase($clase_id);
        if (!empty($cursos)) {
            $this->crearRelacionesAlumnoCurso($clase_id, array_column($cursos, 'id'), $alumnos);
        }
    }
}