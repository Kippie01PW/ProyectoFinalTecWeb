<?php

namespace App\Models;

use App\Core\Conexion;
class AlumnoModelRegister {
    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->getConexion();
    }

    public function createAlumno($usuarioId, $nombre) {
        $sql = "INSERT INTO alumno (usuario_id, nombre) VALUES (:usuario_id, :nombre)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':usuario_id' => $usuarioId,
            ':nombre' => $nombre
        ]);
    }
}
?>