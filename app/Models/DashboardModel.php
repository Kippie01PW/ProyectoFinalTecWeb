<?php
namespace App\Models;
use App\Core\Conexion; 
use \PDO; 

class DashboardModel {
    private $db;
    
   
    public function __construct(\PDO $db) { 
        $this->db = $db; 
    }
    

    public function getCursosEstadisticas($alumno_id) {
        try {
            $query = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN estado = 'asignado' THEN 1 ELSE 0 END) as asignados,
                        SUM(CASE WHEN estado = 'completado' THEN 1 ELSE 0 END) as completados
                      FROM alumnocurso 
                      WHERE alumno_id = :alumno_id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':alumno_id', $alumno_id, \PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            return [
                'total' => (int)$resultado['total'],
                'asignados' => (int)$resultado['asignados'],
                'completados' => (int)$resultado['completados']
            ];
            
        } catch (\PDOException $e) {
            error_log("Error en getCursosEstadisticas: " . $e->getMessage());
            return [
                'total' => 0,
                'asignados' => 0,
                'completados' => 0
            ];
        }
    }
}