<?php
namespace App\Models;

use App\Core\Conexion; 

class PreferenciasAlumnoModel {
    private $conn;
    
    public function __construct($conexion) {
        $this->conn = $conexion;
    }
    
    public function guardarPreferencias($alumno_id, $respuestas) {
        $respuestasMapeadas = $this->mapearRespuestasAPreguntasBD($respuestas); 
        
        if (count($respuestasMapeadas) !== 20 || in_array(null, $respuestasMapeadas, true)) {
            throw new \Exception("Faltan respuestas o el mapeo es incorrecto. Se esperaban 20 respuestas.");
        }
        
        $valoresParaBD = array_values($respuestasMapeadas);
        
        $sqlCheck = "SELECT id FROM preferenciasalumno WHERE alumno_id = ?";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->execute([$alumno_id]);
        $exists = $stmtCheck->fetch();
        
        if ($exists) {
            
            $setClauses = [];
            $columnNames = [];
            for ($i = 1; $i <= 20; $i++) {
                $setClauses[] = "pregunta{$i} = ?";
                $columnNames[] = "pregunta{$i}"; 
            }

            $sql = "UPDATE preferenciasalumno SET " . implode(', ', $setClauses) . " WHERE alumno_id = ?";
            
            $stmt = $this->conn->prepare($sql);
            $params = array_merge($valoresParaBD, [$alumno_id]); 
            $stmt->execute($params);
            return $exists['id'];
        } else {
            
            $columnNames = ['alumno_id'];
            $placeholders = ['?'];
            
            for ($i = 1; $i <= 20; $i++) {
                $columnNames[] = "pregunta{$i}";
                $placeholders[] = "?";
            }
            
            $sql = "INSERT INTO preferenciasalumno (" . implode(', ', $columnNames) . ") VALUES (" . implode(', ', $placeholders) . ")";
            
            $stmt = $this->conn->prepare($sql);
            $params = array_merge([$alumno_id], $valoresParaBD); 
            $stmt->execute($params);
            return $this->conn->lastInsertId();
        }
    }
    

    private function mapearRespuestasAPreguntasBD($respuestasJS) {
        $mapped = array_fill(1, 20, null); 
        $globalQuestionIndex = 0;

        $formStructure = [ 
            1 => 5, 
            2 => 5, 
            3 => 10 
        ];

        foreach ($formStructure as $pageNumber => $numQuestionsInPage) {
            for ($qIndexInPage = 1; $qIndexInPage <= $numQuestionsInPage; $qIndexInPage++) {
                $globalQuestionIndex++; 
                $jsKey = "q{$pageNumber}_{$qIndexInPage}"; 
                
               
                if (isset($respuestasJS[$jsKey])) {
                    $mapped[$globalQuestionIndex] = $respuestasJS[$jsKey];
                } else {
                    
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