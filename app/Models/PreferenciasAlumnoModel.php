<?php
namespace App\Models;

use App\Core\Conexion; // Aún necesitas esta línea para el namespace si no usas Composer para cargarla directamente

class PreferenciasAlumnoModel {
    private $conn;
    
    public function __construct($conexion) {
        $this->conn = $conexion;
    }
    
    public function guardarPreferencias($alumno_id, $respuestas) {
        $respuestasMapeadas = $this->mapearRespuestasAPreguntasBD($respuestas); // Nuevo método para mapear
        
        // Verificar que tenemos exactamente 20 respuestas mapeadas y no nulas
        if (count($respuestasMapeadas) !== 20 || in_array(null, $respuestasMapeadas, true)) {
            throw new \Exception("Faltan respuestas o el mapeo es incorrecto. Se esperaban 20 respuestas.");
        }
        
        // Extrae los valores en el orden correcto para el SQL
        $valoresParaBD = array_values($respuestasMapeadas);
        
        // Verificar si el alumno ya tiene preferencias guardadas
        $sqlCheck = "SELECT id FROM preferenciasalumno WHERE alumno_id = ?";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->execute([$alumno_id]);
        $exists = $stmtCheck->fetch();
        
        if ($exists) {
            // Actualizar preferencias existentes
            $setClauses = [];
            $columnNames = [];
            for ($i = 1; $i <= 20; $i++) {
                $setClauses[] = "pregunta{$i} = ?";
                $columnNames[] = "pregunta{$i}"; // Solo para referencia, no usado directamente en UPDATE SET
            }

            $sql = "UPDATE preferenciasalumno SET " . implode(', ', $setClauses) . " WHERE alumno_id = ?";
            
            $stmt = $this->conn->prepare($sql);
            $params = array_merge($valoresParaBD, [$alumno_id]); // Los valores seguidos del alumno_id
            $stmt->execute($params);
            return $exists['id'];
        } else {
            // Insertar nuevas preferencias
            $columnNames = ['alumno_id'];
            $placeholders = ['?'];
            
            for ($i = 1; $i <= 20; $i++) {
                $columnNames[] = "pregunta{$i}";
                $placeholders[] = "?";
            }
            
            $sql = "INSERT INTO preferenciasalumno (" . implode(', ', $columnNames) . ") VALUES (" . implode(', ', $placeholders) . ")";
            
            $stmt = $this->conn->prepare($sql);
            $params = array_merge([$alumno_id], $valoresParaBD); // alumno_id primero, luego los valores de las preguntas
            $stmt->execute($params);
            return $this->conn->lastInsertId();
        }
    }
    
    // **NUEVO MÉTODO:** Mapea las respuestas de JS (q_pagina_index) a las columnas de la BD (preguntaN)
    private function mapearRespuestasAPreguntasBD($respuestasJS) {
        $mapped = array_fill(1, 20, null); // Inicializa un array con 20 nulos (pregunta1 a pregunta20)
        $globalQuestionIndex = 0;

        // Itera sobre las páginas y preguntas del formulario para mapear a pregunta1...pregunta20
        // Esta lógica debe ser consistente con la estructura de $questions en Formulario.php
        $formStructure = [ // Define la estructura de tu formulario para un mapeo correcto
            1 => 5, // Página 1 tiene 5 preguntas
            2 => 5, // Página 2 tiene 5 preguntas
            3 => 10 // Página 3 tiene 10 preguntas
        ];

        foreach ($formStructure as $pageNumber => $numQuestionsInPage) {
            for ($qIndexInPage = 1; $qIndexInPage <= $numQuestionsInPage; $qIndexInPage++) {
                $globalQuestionIndex++; // Incrementa para mapear a pregunta1, pregunta2, etc.
                $jsKey = "q{$pageNumber}_{$qIndexInPage}"; // Clave esperada del JS: q1_1, q1_2, q2_1, etc.
                
                // Si la respuesta existe en el array de JS, la asigna
                if (isset($respuestasJS[$jsKey])) {
                    $mapped[$globalQuestionIndex] = $respuestasJS[$jsKey];
                } else {
                    // Si una respuesta no se encuentra, la deja como null.
                    // Esto causará una excepción en la verificación de count($respuestasMapeadas) !== 20
                    // si alguna pregunta obligatoria no fue respondida.
                }
            }
        }
        return $mapped;
    }
    
    public function obtenerPreferencias($alumno_id) {
        $sql = "SELECT * FROM preferenciasalumno WHERE alumno_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$alumno_id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}