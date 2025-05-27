<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Core\Conexion;
use App\Models\MostrarCursosModel;

class MostrarCursosController {
    
    public function getCursosAsignados(Request $request, Response $response, $args) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            $payload = json_encode(['error' => 'Usuario no autenticado']);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $conexionObj = new Conexion();
        $conexion = $conexionObj->getConexion();
        $cursoModel = new MostrarCursosModel($conexion);

        $alumno = $cursoModel->getAlumnoByUsuarioId($_SESSION['user_id']);
        
        if (!$alumno) {
            $payload = json_encode(['error' => 'Alumno no encontrado']);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $alumno_id = $alumno['id'];

        try {
            $cursos = $cursoModel->getCursosAsignados($alumno_id);
            $payload = json_encode([
                'success' => true,
                'data' => $cursos
            ]);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $payload = json_encode(['error' => 'Error al obtener cursos asignados: ' . $e->getMessage()]);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function getCursosCompletados(Request $request, Response $response, $args) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            $payload = json_encode(['error' => 'Usuario no autenticado']);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $conexionObj = new Conexion();
        $conexion = $conexionObj->getConexion();
        $cursoModel = new MostrarCursosModel($conexion);

        $alumno = $cursoModel->getAlumnoByUsuarioId($_SESSION['user_id']);
        
        if (!$alumno) {
            $payload = json_encode(['error' => 'Alumno no encontrado']);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $alumno_id = $alumno['id'];

        try {
            $cursos = $cursoModel->getCursosCompletados($alumno_id);
            $payload = json_encode([
                'success' => true,
                'data' => $cursos
            ]);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $payload = json_encode(['error' => 'Error al obtener cursos completados: ' . $e->getMessage()]);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function subirEvidencia(Request $request, Response $response, $args) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            $payload = json_encode(['error' => 'Usuario no autenticado']);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $conexionObj = new Conexion();
        $conexion = $conexionObj->getConexion();
        $cursoModel = new MostrarCursosModel($conexion);

        $alumno = $cursoModel->getAlumnoByUsuarioId($_SESSION['user_id']);
        
        if (!$alumno) {
            $payload = json_encode(['error' => 'Alumno no encontrado']);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        try {
            $data = $request->getParsedBody();
            $asignacion_id = $data['asignacion_id'] ?? null;

            if (!$asignacion_id) {
                $payload = json_encode(['error' => 'ID de asignación requerido']);
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            $uploadedFiles = $request->getUploadedFiles();
            
            if (!isset($uploadedFiles['evidencia']) || $uploadedFiles['evidencia']->getError() !== UPLOAD_ERR_OK) {
                $payload = json_encode(['error' => 'No se pudo subir la imagen']);
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            $uploadedFile = $uploadedFiles['evidencia'];
            
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = $uploadedFile->getClientMediaType();
            
            if (!in_array($fileType, $allowedTypes)) {
                $payload = json_encode(['error' => 'Solo se permiten archivos de imagen (JPEG, PNG, GIF, WebP)']);
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            $uploadDir = __DIR__ . '/../../public/uploads/evidencias/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
            $filename = 'evidencia_' . $asignacion_id . '_' . time() . '.' . $extension;
            $filepath = $uploadDir . $filename;

            $uploadedFile->moveTo($filepath);

            $evidenciaUrl = '/ProyectoFinalTecWeb/public/uploads/evidencias/' . $filename;

            $resultado = $cursoModel->subirEvidencia($asignacion_id, $alumno['id'], $evidenciaUrl);

            if ($resultado) {
                $payload = json_encode([
                    'success' => true,
                    'message' => 'Evidencia subida correctamente. El curso ha sido marcado como completado.',
                    'evidencia_url' => $evidenciaUrl
                ]);
            } else {
                $payload = json_encode(['error' => 'Error al actualizar la base de datos']);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');

        } catch (Exception $e) {
            $payload = json_encode(['error' => 'Error al subir evidencia: ' . $e->getMessage()]);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}