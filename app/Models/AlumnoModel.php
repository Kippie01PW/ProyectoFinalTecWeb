<?php

namespace App\Models;

use PDO;

class AlumnoModel
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findCursosAsignados(int $alumnoId): array
    {
        $sql = "SELECT c.id, c.titulo, c.descripcion, c.enlace_externo, cat.nombre as categoria_nombre, ac.fecha_asignacion
                FROM alumnocurso ac
                JOIN cursos c ON ac.curso_id = c.id
                LEFT JOIN categoriascurso cat ON c.categoria_id = cat.id
                WHERE ac.alumno_id = :alumno_id AND ac.estado = 'asignado'
                ORDER BY ac.fecha_asignacion DESC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':alumno_id', $alumnoId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error en AlumnoModel::findCursosAsignados: " . $e->getMessage());
            return [];
        }
    }

    public function findCursosCompletados(int $alumnoId): array
    {
        $sql = "SELECT c.id, c.titulo, c.descripcion, c.enlace_externo, cat.nombre as categoria_nombre, ac.fecha_completado, ac.evidencia
                FROM alumnocurso ac
                JOIN cursos c ON ac.curso_id = c.id
                LEFT JOIN categoriascurso cat ON c.categoria_id = cat.id
                WHERE ac.alumno_id = :alumno_id AND ac.estado = 'completado'
                ORDER BY ac.fecha_completado DESC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':alumno_id', $alumnoId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error en AlumnoModel::findCursosCompletados: " . $e->getMessage());
            return [];
        }
    }
/**
     * Busca las clases a las que un alumno está directamente inscrito.
     *
     * @param int $alumnoId El ID del alumno.
     * @return array Un array con los datos de las clases.
     */
    // Dentro de AlumnoModel.php
public function findMisClases(int $alumnoId): array
{
    $sql = "SELECT
                cl.id,
                cl.codigo,
                cl.nombre AS nombre_clase,
                cl.descripcion,
                m.nombre AS nombre_maestro
            FROM alumnoclase acl
            JOIN clases cl ON acl.clase_id = cl.id
            JOIN maestro m ON cl.maestro_id = m.id
            WHERE acl.alumno_id = :alumno_id AND acl.estado = 'activo'
            ORDER BY cl.nombre ASC";
    try {
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':alumno_id', $alumnoId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        error_log("Error en AlumnoModel::findMisClases: " . $e->getMessage());
        return [];
    }
}
    
}