<?php

namespace App\Models;

use App\Core\Conexion;

class MaestroModel {
    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->getConexion();
    }

    public function createMaestro($usuarioId, $nombre) {
        $sql = "INSERT INTO maestro (usuario_id, nombre) VALUES (:usuario_id, :nombre)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':usuario_id' => $usuarioId,
            ':nombre' => $nombre
        ]);
    }
}
?>