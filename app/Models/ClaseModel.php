<?php
namespace App\Models;

use PDO;

class ClaseModel {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Crea una nueva clase
     */
    public function crearClase($maestro_id, $nombre, $descripcion = null) {
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
        
        return $this->db->lastInsertId();
    }

    /**
     * Obtiene las clases de un maestro
     */
    public function obtenerClasesPorMaestro($maestro_id) {
        $sql = "SELECT c.*, COUNT(ac.alumno_id) as total_alumnos
                FROM clases c 
                LEFT JOIN alumnoclase ac ON c.id = ac.clase_id AND ac.estado = 'activo'
                WHERE c.maestro_id = :maestro_id 
                GROUP BY c.id
                ORDER BY c.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':maestro_id' => $maestro_id]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

        $sql = "INSERT INTO alumnoclase (alumno_id, clase_id) 
                VALUES (:alumno_id, :clase_id)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':alumno_id' => $alumno_id,
            ':clase_id' => $clase_id
        ]);
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
}