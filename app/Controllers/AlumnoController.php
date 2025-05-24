<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Core\Conexion;   
use App\Models\AlumnoModel; 
use PDO;                 


class AlumnoController
{

    /**
     * Ruta: GET /api/alumnos/cursos/asignados
     */
    public function getCursosAsignados(Request $request, Response $response, $args)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }


        $alumno_id = $_SESSION['alumno_id'] ?? 1; // Usamos 1 como default para pruebas

        try {
            // 3. Obtener Conexión PDO
            //    Creamos una nueva instancia de nuestra clase Conexion y obtenemos el objeto PDO.
            $pdo = (new Conexion())->getConexion();

            // 4. Instanciar el Modelo, pasando la conexión PDO
            $alumnoModel = new AlumnoModel($pdo);

            // 5. Llamar al método del Modelo para obtener los cursos
            $cursos = $alumnoModel->findCursosAsignados($alumno_id);

            // 6. Preparar la respuesta JSON con los datos reales
            $payload = json_encode($cursos);
            $response->getBody()->write($payload);

            // 7. Devolver la respuesta
            return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200); // 200 OK

        } catch (\Exception $e) {
            // Si algo sale mal (ej: error de BD), devolvemos un error 500.
            $errorData = ['error' => 'No se pudieron obtener los cursos asignados.', 'message' => $e->getMessage()];
            $payload = json_encode($errorData);
            $response->getBody()->write($payload);
            return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(500); // 500 Internal Server Error
        }
    }

    /**
     * Obtiene los cursos completados por el alumno.
     * Ruta: GET /api/alumnos/cursos/completados
     */
    public function getCursosCompletados(Request $request, Response $response, $args)
    {
        // TODO: Implementar usando el Modelo como hicimos arriba.
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
        // TODO: Implementar usando el Modelo.
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
        // TODO: Implementar usando el Modelo.
        $parsedBody = $request->getParsedBody();
        $codigo = $parsedBody['codigo'] ?? null;

        if ($codigo) {
            $data = ['message' => "API: Te has unido (simulado) a la clase con código: " . htmlspecialchars($codigo)];
            $status = 200;
        } else {
            $data = ['error' => 'No se proporcionó un código de clase.'];
            $status = 400;
        }

        $payload = json_encode($data);
        $response->getBody()->write($payload);
        return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus($status);
    }
}