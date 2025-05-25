<?php

namespace App\Models;

use PDO;

class ClasesModel
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findClasesByMaestro(int $maestroId): array
    {
        $sql = "SELECT id, codigo, nombre, descripcion, created_at 
                FROM clases 
                WHERE maestro_id = :maestro_id 
                ORDER BY created_at DESC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':maestro_id', $maestroId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error en ClasesModel::findClasesByMaestro: " . $e->getMessage());
            return [];
        }
    }
}