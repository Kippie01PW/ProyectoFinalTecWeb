<?php

namespace App\Models; 
use PDO; 

class AlumnoModel
{
    /**
     * @var PDO Almacena la conexión a la base de datos.
     */
    private $db;

    /**
     * Constructor que recibe la conexión PDO.
     * Esto se llama Inyección de Dependencias (una forma básica).
     *
     * @param PDO $db Objeto de conexión PDO.
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Busca y devuelve los cursos asignados a un alumno específico.
     *
     * @param int $alumnoId El ID del alumno (proveniente de la tabla `alumno`).
     * @return array Un array con los datos de los cursos asignados.
     */
    public function findCursosAsignados(int $alumnoId): array
    {
        // SQL basado en tu esquema de BD
        $sql = "SELECT
                    c.id,
                    c.titulo,
                    c.descripcion,
                    c.enlace_externo,
                    cat.nombre as categoria_nombre,
                    ac.fecha_asignacion
                FROM alumnocurso ac
                JOIN cursos c ON ac.curso_id = c.id
                LEFT JOIN categoriascurso cat ON c.categoria_id = cat.id
                WHERE ac.alumno_id = :alumno_id
                AND ac.estado = 'asignado'
                ORDER BY ac.fecha_asignacion DESC";

        try {
            // Preparamos la consulta para evitar inyección SQL
            $stmt = $this->db->prepare($sql);
            // Asociamos el valor de :alumno_id con la variable $alumnoId
            $stmt->bindParam(':alumno_id', $alumnoId, PDO::PARAM_INT);
            // Ejecutamos la consulta
            $stmt->execute();
            // Devolvemos todos los resultados como un array asociativo
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Manejo básico de errores (idealmente, se registraría el error)
            error_log("Error en AlumnoModel::findCursosAsignados: " . $e->getMessage());
            return []; // Devolvemos un array vacío en caso de error
        }
    }

/**
     * Busca y devuelve los cursos completados por un alumno específico.
     *
     * @param int $alumnoId El ID del alumno.
     * @return array Un array con los datos de los cursos completados.
     */
    public function findCursosCompletados(int $alumnoId): array
    {
        // La consulta es casi idéntica, solo cambia el estado.
        $sql = "SELECT
                    c.id,
                    c.titulo,
                    c.descripcion,
                    c.enlace_externo,
                    cat.nombre as categoria_nombre,
                    ac.fecha_completado,
                    ac.evidencia
                FROM alumnocurso ac
                JOIN cursos c ON ac.curso_id = c.id
                LEFT JOIN categoriascurso cat ON c.categoria_id = cat.id
                WHERE ac.alumno_id = :alumno_id
                AND ac.estado = 'completado' -- <-- ¡El cambio clave!
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
     * Busca las clases a las que pertenece un alumno.
     * (Lo implementaremos más adelante)
     *
     * @param int $alumnoId El ID del alumno.
     * @return array
     */
    public function findMisClases(int $alumnoId): array
    {
        // TODO: Implementar la consulta SQL para las clases del alumno
        return [['id' => 1, 'codigo' => 'CLASE01', 'descripcion' => 'Clase de Prueba']];
    }

    /**
     * Asocia un alumno a una clase usando un código.
     * (Lo implementaremos más adelante)
     *
     * @param int $alumnoId El ID del alumno.
     * @param string $codigo El código de la clase.
     * @return bool True si se unió, false si no.
     */
    public function addAlumnoToClase(int $alumnoId, string $codigo): bool
    {
        // TODO: Implementar la lógica para unirse a una clase
        return true;
    }

}