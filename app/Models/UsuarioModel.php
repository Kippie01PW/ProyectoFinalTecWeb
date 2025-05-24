<?php

namespace App\Models;

use App\Core\Conexion;
class UsuarioModel {
    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->getConexion();
    }

    public function createUser($username, $email, $passwordHash, $role) {
        $sql = "INSERT INTO usuarios (username, email, password_hash, role, estado) 
            VALUES (:username, :email, :password, :role, 1)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $passwordHash,
            ':role' => $role
        ]) ? $this->db->lastInsertId() : false;
    }

    public function getUserByEmail($email) {
        $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
    return $stmt->fetch(\PDO::FETCH_ASSOC);
}
}
?>