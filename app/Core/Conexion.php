<?php
namespace App\Core; 

class Conexion {
    private $conect;
   

    public function __construct() {
        $connectionString = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        try {
            $this->conect = new \PDO($connectionString, DB_USER, DB_PASSWORD);
            $this->conect->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch(\PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public function getConexion() {
        return $this->conect;
    }



}

?>