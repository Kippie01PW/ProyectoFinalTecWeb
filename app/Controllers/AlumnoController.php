<?php

namespace App\Controllers; 

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
// Podríamos necesitar la conexión y PDO más adelante:
// use App\Core\Conexion;
// use PDO;

/**
 * Controlador para gestionar las funcionalidades del Alumno.
 */
class AlumnoController
{

    /**
     * Obtiene los cursos asignados al alumno.
     * Ruta: GET /api/alumnos/cursos/asignados
     */
    public function getCursosAsignados(Request $request, Response $response, $args)
    {
        // --- Lógica Temporal ---
        // Aquí obtendríamos el ID del alumno de la sesión.
        // Luego llamaríamos al Modelo para buscar en la BD.
        $data = ['message' => 'API: Aqui se mostraran tus cursos asignados.'];
        
        // Convertimos el array a JSON
        $payload = json_encode($data);

        // Escribimos el JSON en la respuesta
        $response->getBody()->write($payload);

        // Devolvemos la respuesta con el tipo de contenido y el código de estado
        return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200); // 200 OK
    }

    /**
     * Obtiene los cursos completados por el alumno.
     * Ruta: GET /api/alumnos/cursos/completados
     */
    public function getCursosCompletados(Request $request, Response $response, $args)
    {
        $data = ['message' => 'API: Aquí se mostrará tu historial de cursos completados.'];
        $payload = json_encode($data);
        $response->getBody()->write($payload);
        return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
    }

    /**
     * Obtiene las clases a las que pertenece el alumno.
     * Ruta: GET /api/alumnos/clases
     */
    public function getMisClases(Request $request, Response $response, $args)
    {
        $data = ['message' => 'API: Aquí se mostrarán las clases a las que perteneces.'];
        $payload = json_encode($data);
        $response->getBody()->write($payload);
        return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
    }

    /**
     * Permite al alumno unirse a una clase.
     * Ruta: POST /api/alumnos/clases/unirse
     */
    public function unirseAClase(Request $request, Response $response, $args)
    {
        // Obtenemos los datos enviados en el cuerpo de la petición (ej: desde un form AJAX)
        $parsedBody = $request->getParsedBody();
        $codigo = $parsedBody['codigo'] ?? null; // Buscamos el 'codigo'

        if ($codigo) {
            // Aquí iría la lógica para validar el código y registrar al alumno en la clase.
            $data = ['message' => "API: Te has unido (simulado) a la clase con código: " . htmlspecialchars($codigo)];
            $status = 200; // OK
        } else {
            $data = ['error' => 'No se proporcionó un código de clase.'];
            $status = 400; // Bad Request (Falta información)
        }

        $payload = json_encode($data);
        $response->getBody()->write($payload);
        return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus($status);
    }
}