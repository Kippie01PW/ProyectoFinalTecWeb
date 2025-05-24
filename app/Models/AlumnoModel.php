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

    public function findMisClases(int $alumnoId): array
    {
        $sql = "SELECT DISTINCT cl.id, cl.codigo, cl.descripcion, m.nombre as nombre_maestro
                FROM clases cl
                JOIN cursos c ON cl.id = c.clase_id
                JOIN alumnocurso ac ON c.id = ac.curso_id
                JOIN maestro m ON cl.maestro_id = m.id
                WHERE ac.alumno_id = :alumno_id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':alumno_id', $alumnoId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error en AlumnoModel::findMisClases: " . $e->getMessage());
            return [];
        }
    }

    public function joinClaseByCodigo(int $alumnoId, string $codigo): bool
    {
        $this->db->beginTransaction();
        try {
            $sqlClase = "SELECT id FROM clases WHERE codigo = :codigo LIMIT 1";
            $stmtClase = $this->db->prepare($sqlClase);
            $stmtClase->bindParam(':codigo', $codigo, PDO::PARAM_STR);
            $stmtClase->execute();
            $clase = $stmtClase->fetch(PDO::FETCH_ASSOC);

            if (!$clase) { $this->db->rollBack(); return false; }
            $claseId = $clase['id'];

            $sqlCursos = "SELECT id FROM cursos WHERE clase_id = :clase_id";
            $stmtCursos = $this->db->prepare($sqlCursos);
            $stmtCursos->bindParam(':clase_id', $claseId, PDO::PARAM_INT);
            $stmtCursos->execute();
            $cursosIds = $stmtCursos->fetchAll(PDO::FETCH_COLUMN, 0);

            if (empty($cursosIds)) { $this->db->commit(); return true; }

            $sqlInsert = "INSERT INTO alumnocurso (alumno_id, curso_id, estado)
                          SELECT :alumno_id, :curso_id, 'asignado'
                          WHERE NOT EXISTS (
                              SELECT 1 FROM alumnocurso
                              WHERE alumno_id = :alumno_id_check AND curso_id = :curso_id_check
                          )";
            $stmtInsert = $this->db->prepare($sqlInsert);

            foreach ($cursosIds as $cursoId) {
                $stmtInsert->bindValue(':alumno_id', $alumnoId, PDO::PARAM_INT);
                $stmtInsert->bindValue(':curso_id', $cursoId, PDO::PARAM_INT);
                $stmtInsert->bindValue(':alumno_id_check', $alumnoId, PDO::PARAM_INT);
                $stmtInsert->bindValue(':curso_id_check', $cursoId, PDO::PARAM_INT);
                if (!$stmtInsert->execute()) { $this->db->rollBack(); return false; }
            }
            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log("Error en AlumnoModel::joinClaseByCodigo: " . $e->getMessage());
            return false;
        }
    }
}