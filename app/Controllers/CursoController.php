<?php
// app/Controllers/CursoController.php
namespace App\Controllers;

use App\Models\CursoModel;
use App\Core\Conexion;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Exception;

class CursoController {

    public function showForm(Request $request, Response $response, $args) {
        try {
            $db = (new Conexion())->getConexion();
            $model = new CursoModel($db);
            $categorias = $model->obtenerCategorias();

            error_log("Categorías obtenidas: " . print_r($categorias, true));

            if ($categorias === false || $categorias === null) {
                error_log("Error: No se pudieron obtener las categorías de la base de datos");
                $categorias = [];
            }

            $error = '';
            $success = '';
            
            if (isset($_GET['success']) && $_GET['success'] == '1') {
                $success = 'Curso guardado exitosamente.';
            }
            if (isset($_GET['error'])) {
                $error = htmlspecialchars($_GET['error']);
            }

            $data = [
                'categorias' => $categorias,
                'error' => $error,
                'success' => $success
            ];

            extract($data);

            ob_start();
            include APP_ROOT . '/Views/Form_cursos.php';
            $output = ob_get_clean();

            $response->getBody()->write($output);
            return $response;
        } catch (Exception $e) {
            error_log("Error en showForm: " . $e->getMessage());
            $response->getBody()->write("Error al cargar el formulario: " . $e->getMessage());
            return $response->withStatus(500);
        }
    }

    public function guardarCurso(Request $request, Response $response, $args) {
        try {
            $data = $request->getParsedBody();
            
            if (empty($data['titulo'])) {
                throw new Exception("El título del curso es requerido.");
            }

            $db = (new Conexion())->getConexion();
            $model = new CursoModel($db);

            $db->beginTransaction();

            $categoria_id = null;

            if (!empty($data['nueva_categoria'])) {
                if (empty(trim($data['nueva_categoria']))) {
                    throw new Exception("El nombre de la nueva categoría no puede estar vacío.");
                }
                $categoria_id = $model->insertarCategoria(
                    trim($data['nueva_categoria']), 
                    trim($data['descripcion_categoria']) ?: null
                );
            } elseif (!empty($data['categoria_id'])) {
                $categoria_id = intval($data['categoria_id']);
                if (!$model->existeCategoria($categoria_id)) {
                    throw new Exception("La categoría seleccionada no existe.");
                }
            } else {
                throw new Exception("Debe seleccionar una categoría existente o crear una nueva.");
            }

            if (!empty($data['enlace_externo']) && !filter_var($data['enlace_externo'], FILTER_VALIDATE_URL)) {
                throw new Exception("El enlace externo debe ser una URL válida.");
            }

            $curso_id = $model->insertarCurso(
                $categoria_id,
                trim($data['titulo']),
                trim($data['descripcion']) ?: null,
                trim($data['enlace_externo']) ?: null
            );

            $db->commit();

            return $response
                ->withHeader('Location', '/ProyectoFinalTecWeb/public/cursos/nuevo?success=1')
                ->withStatus(302);

        } catch (Exception $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            
            error_log("Error al guardar curso: " . $e->getMessage());
            
            return $response
                ->withHeader('Location', '/ProyectoFinalTecWeb/public/cursos/nuevo?error=' . urlencode($e->getMessage()))
                ->withStatus(302);
        }
    }

    public function listarCursos(Request $request, Response $response, $args) {
        try {
            $db = (new Conexion())->getConexion();
            $model = new CursoModel($db);
            
            $params = $request->getQueryParams();
            $categoria_id = isset($params['categoria']) ? intval($params['categoria']) : null;
            
            $cursos = $model->obtenerCursos($categoria_id);
            $categorias = $model->obtenerCategorias();

            ob_start();
            include APP_ROOT . '/Views/lista_cursos.php';
            $output = ob_get_clean();

            $response->getBody()->write($output);
            return $response;
        } catch (Exception $e) {
            error_log("Error al listar cursos: " . $e->getMessage());
            $response->getBody()->write("Error al cargar los cursos.");
            return $response->withStatus(500);
        }
    }
}