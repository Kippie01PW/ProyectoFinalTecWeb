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
     * Busca las clases a las que pertenece un alumno (indirectamente, a través de cursos).
     *
     * @param int $alumnoId El ID del alumno.
     * @return array Un array con los datos de las clases.
     */
    public function findMisClases(int $alumnoId): array
    {
        // Usamos DISTINCT para no repetir clases si el alumno tiene varios cursos de la misma.
        $sql = "SELECT DISTINCT
                    cl.id,
                    cl.codigo,
                    cl.descripcion,
                    m.nombre as nombre_maestro -- Añadimos el nombre del maestro
                FROM clases cl
                JOIN cursos c ON cl.id = c.clase_id
                JOIN alumnocurso ac ON c.id = ac.curso_id
                JOIN maestro m ON cl.maestro_id = m.id -- Unimos con maestro para el nombre
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

/**
     * Asocia un alumno a una clase usando un código, asignándole todos los cursos
     * de esa clase si no los tiene ya. Usa transacciones.
     *
     * @param int $alumnoId El ID del alumno.
     * @param string $codigo El código de la clase.
     * @return bool True si se unió (o ya estaba/no había cursos), false si hubo error o no existe la clase.
     */
    public function joinClaseByCodigo(int $alumnoId, string $codigo): bool
    {
        $this->db->beginTransaction(); // ¡Iniciamos la transacción!

        try {
            // 1. Buscar el ID de la clase a partir del código.
            $sqlClase = "SELECT id FROM clases WHERE codigo = :codigo LIMIT 1";
            $stmtClase = $this->db->prepare($sqlClase);
            $stmtClase->bindParam(':codigo', $codigo, PDO::PARAM_STR);
            $stmtClase->execute();
            $clase = $stmtClase->fetch(PDO::FETCH_ASSOC);

            // Si la clase no existe, no podemos continuar.
            if (!$clase) {
                $this->db->rollBack(); // Revertimos (aunque no hicimos nada)
                error_log("Intento de unirse a clase inexistente con código: $codigo");
                return false;
            }
            $claseId = $clase['id'];

            // 2. Buscar todos los IDs de los cursos de esa clase.
            $sqlCursos = "SELECT id FROM cursos WHERE clase_id = :clase_id";
            $stmtCursos = $this->db->prepare($sqlCursos);
            $stmtCursos->bindParam(':clase_id', $claseId, PDO::PARAM_INT);
            $stmtCursos->execute();
            $cursosIds = $stmtCursos->fetchAll(PDO::FETCH_COLUMN, 0); // Obtenemos solo los IDs

            // Si no hay cursos en la clase, consideramos la operación exitosa
            // (el alumno se "unió" pero no hay nada que asignar).
            if (empty($cursosIds)) {
                $this->db->commit(); // Confirmamos (no hicimos nada)
                return true;
            }

            // 3. Preparar la consulta para insertar (solo si no existe ya la relación).
            // Usamos una subconsulta con NOT EXISTS para evitar duplicados.
            $sqlInsert = "INSERT INTO alumnocurso (alumno_id, curso_id, estado)
                          SELECT :alumno_id, :curso_id, 'asignado'
                          WHERE NOT EXISTS (
                              SELECT 1 FROM alumnocurso
                              WHERE alumno_id = :alumno_id_check AND curso_id = :curso_id_check
                          )";
            $stmtInsert = $this->db->prepare($sqlInsert);

            // 4. Recorrer cada curso y intentar insertarlo.
            foreach ($cursosIds as $cursoId) {
                $stmtInsert->bindValue(':alumno_id', $alumnoId, PDO::PARAM_INT);
                $stmtInsert->bindValue(':curso_id', $cursoId, PDO::PARAM_INT);
                $stmtInsert->bindValue(':alumno_id_check', $alumnoId, PDO::PARAM_INT);
                $stmtInsert->bindValue(':curso_id_check', $cursoId, PDO::PARAM_INT);

                // Si una sola inserción falla, detenemos todo y revertimos.
                if (!$stmtInsert->execute()) {
                    $this->db->rollBack();
                    error_log("Fallo al insertar curso $cursoId para alumno $alumnoId");
                    return false;
                }
            }

            // 5. Si todo fue bien, ¡confirmamos todos los cambios!
            $this->db->commit();
            return true;

        } catch (\PDOException $e) {
            // Si ocurre cualquier error de BD, revertimos todo.
            $this->db->rollBack();
            error_log("Error en AlumnoModel::joinClaseByCodigo: " . $e->getMessage());
            return false;
        }
    }

}