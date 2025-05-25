<?php
namespace App\Models;

use App\Core\Conexion;

class PreferenciasAlumnoModel {
    private $conn;
    
    public function __construct($conexion) {
        $this->conn = $conexion;
    }
    
    public function guardarPreferencias($alumno_id, $respuestas) {
        // Mapear las respuestas recibidas a un array ordenado
        $respuestasOrdenadas = [];
        
        // Ordenar las respuestas por número de pregunta
        for ($i = 1; $i <= 20; $i++) {
            $preguntaKey = $this->buscarPregunta($respuestas, $i);
            if ($preguntaKey) {
                $respuestasOrdenadas[] = $respuestas[$preguntaKey];
            } else {
                throw new \Exception("Falta la respuesta para la pregunta $i");
            }
        }
        
        // Verificar que tenemos exactamente 20 respuestas
        if (count($respuestasOrdenadas) !== 20) {
            throw new \Exception("Se requieren exactamente 20 respuestas, se recibieron " . count($respuestasOrdenadas));
        }
        
        // Verificar si el alumno ya tiene preferencias guardadas
        $sqlCheck = "SELECT id FROM preferenciasalumno WHERE alumno_id = ?";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->execute([$alumno_id]);
        $exists = $stmtCheck->fetch();
        
        if ($exists) {
            // Actualizar preferencias existentes
            $sql = "UPDATE preferenciasalumno SET 
                        pregunta1 = ?, pregunta2 = ?, pregunta3 = ?, pregunta4 = ?, pregunta5 = ?,
                        pregunta6 = ?, pregunta7 = ?, pregunta8 = ?, pregunta9 = ?, pregunta10 = ?,
                        pregunta11 = ?, pregunta12 = ?, pregunta13 = ?, pregunta14 = ?, pregunta15 = ?,
                        pregunta16 = ?, pregunta17 = ?, pregunta18 = ?, pregunta19 = ?, pregunta20 = ?
                    WHERE alumno_id = ?";
            
            $stmt = $this->conn->prepare($sql);
            $params = array_merge($respuestasOrdenadas, [$alumno_id]);
            $stmt->execute($params);
            return $exists['id'];
        } else {
            // Insertar nuevas preferencias
            $sql = "INSERT INTO preferenciasalumno (
                        alumno_id,
                        pregunta1, pregunta2, pregunta3, pregunta4, pregunta5,
                        pregunta6, pregunta7, pregunta8, pregunta9, pregunta10,
                        pregunta11, pregunta12, pregunta13, pregunta14, pregunta15,
                        pregunta16, pregunta17, pregunta18, pregunta19, pregunta20
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                    )";
            
            $stmt = $this->conn->prepare($sql);
            $params = array_merge([$alumno_id], $respuestasOrdenadas);
            $stmt->execute($params);
            return $this->conn->lastInsertId();
        }
    }
    
    private function buscarPregunta($respuestas, $numeroPregunta) {
        // Buscar claves que correspondan al número de pregunta
        foreach (array_keys($respuestas) as $key) {
            if (preg_match('/q(\d+)_(\d+)/', $key, $matches)) {
                $pagina = (int)$matches[1];
                $preguntaEnPagina = (int)$matches[2];
                
                // Calcular el número de pregunta global
                $preguntaGlobal = ($pagina - 1) * 5 + $preguntaEnPagina;
                if ($preguntaGlobal === $numeroPregunta) {
                    return $key;
                }
            }
        }
        return null;
    }
    
    public function obtenerPreferencias($alumno_id) {
        $sql = "SELECT * FROM preferenciasalumno WHERE alumno_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$alumno_id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}