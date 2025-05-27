<?php
// app/Models/CursoModel.php
namespace App\Models;

use PDO;
use Exception;

class CursoModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function insertarCategoria($nombre, $descripcion = null) {
        try {
            $stmt = $this->db->prepare("SELECT id FROM categoriascurso WHERE nombre = ?");
            $stmt->execute([$nombre]);
            if ($stmt->fetch()) {
                throw new Exception("La categoría '$nombre' ya existe.");
            }

            $stmt = $this->db->prepare("INSERT INTO categoriascurso (nombre, descripcion) VALUES (?, ?)");
            $stmt->execute([$nombre, $descripcion]);
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception("Error al insertar categoría: " . $e->getMessage());
        }
    }

    public function insertarCurso($categoria_id, $titulo, $descripcion = null, $enlace_externo = null) {
        try {
            if (!$this->existeCategoria($categoria_id)) {
                throw new Exception("La categoría especificada no existe.");
            }

            $stmt = $this->db->prepare("SELECT id FROM cursos WHERE titulo = ? AND categoria_id = ?");
            $stmt->execute([$titulo, $categoria_id]);
            if ($stmt->fetch()) {
                throw new Exception("Ya existe un curso con ese título en esta categoría.");
            }

            $stmt = $this->db->prepare("INSERT INTO cursos (categoria_id, titulo, descripcion, enlace_externo) VALUES (?, ?, ?, ?)");
            $stmt->execute([$categoria_id, $titulo, $descripcion, $enlace_externo]);
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception("Error al insertar curso: " . $e->getMessage());
        }
    }

    public function obtenerCategorias() {
        try {
            if (!$this->db) {
                error_log("Error: Conexión a base de datos no disponible");
                return [];
            }

            $stmt = $this->db->prepare("SELECT id, nombre, descripcion FROM categoriascurso ORDER BY nombre ASC");
            
            if (!$stmt) {
                error_log("Error al preparar consulta: " . print_r($this->db->errorInfo(), true));
                return [];
            }
            
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("Consulta ejecutada. Resultados: " . count($result));
            error_log("Datos obtenidos: " . print_r($result, true));
            
            return $result;
        } catch (Exception $e) {
            error_log("Error al obtener categorías: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return [];
        }
    }

    public function obtenerCategoriaPorId($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM categoriascurso WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al obtener categoría: " . $e->getMessage());
            return null;
        }
    }

    public function existeCategoria($id) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM categoriascurso WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    public function obtenerCursos($categoria_id = null) {
        try {
            if ($categoria_id) {
                $stmt = $this->db->prepare("
                    SELECT c.*, cat.nombre as categoria_nombre 
                    FROM cursos c 
                    JOIN categoriascurso cat ON c.categoria_id = cat.id 
                    WHERE c.categoria_id = ? 
                    ORDER BY c.titulo ASC
                ");
                $stmt->execute([$categoria_id]);
            } else {
                $stmt = $this->db->prepare("
                    SELECT c.*, cat.nombre as categoria_nombre 
                    FROM cursos c 
                    JOIN categoriascurso cat ON c.categoria_id = cat.id 
                    ORDER BY cat.nombre ASC, c.titulo ASC
                ");
                $stmt->execute();
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al obtener cursos: " . $e->getMessage());
            return [];
        }
    }
}