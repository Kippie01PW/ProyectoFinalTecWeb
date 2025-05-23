<?php

class AlumnoModel {
    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->getConexion();
    }

    public function getAlumnos() {
        $stmt = $this->db->query("SELECT * FROM alumno");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>