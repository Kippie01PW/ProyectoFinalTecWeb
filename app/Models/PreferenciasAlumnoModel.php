<?php
namespace App\Models;
use App\Core\Conexion;

class PreferenciasAlumnoModel {
    private $conn;
    
    public function __construct($conexion) {
        $this->conn = $conexion;
    }
    
    public function guardarPreferencias($alumno_id, $respuestas) {
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
        
        // Convertimos a JSON las respuestas que sean arrays (respuestas múltiples)
        $respuestas_json = array_map(function ($respuesta) {
            return is_array($respuesta) ? json_encode($respuesta) : $respuesta;
        }, $respuestas);
        
        $stmt->execute(array_merge([$alumno_id], $respuestas_json));
        return $this->conn->lastInsertId();
    }
}