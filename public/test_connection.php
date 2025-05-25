<?php
// test_connection.php
// Coloca este archivo en tu carpeta public/ para probar la conexión

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Test de Conexión y Categorías</h1>";

try {
    // 1. Cargar autoloader
    require __DIR__ . '/../vendor/autoload.php';
    require __DIR__ . '/../app/config/config.php';
    
    echo "<p>✅ Autoloader y config cargados correctamente</p>";
    
    // 2. Probar conexión
    $conexion = new App\Core\Conexion();
    $db = $conexion->getConexion();
    
    if ($db) {
        echo "<p>✅ Conexión a base de datos establecida</p>";
    } else {
        echo "<p>❌ No se pudo establecer conexión</p>";
        exit;
    }
    
    // 3. Verificar que las tablas existen
    echo "<h2>Verificación de Tablas</h2>";
    
    // Verificar tabla categoriascurso
    $stmt = $db->query("SHOW TABLES LIKE 'categoriascurso'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✅ Tabla 'categoriascurso' existe</p>";
        
        // Mostrar estructura
        $stmt = $db->query("DESCRIBE categoriascurso");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<details><summary>Ver estructura de categoriascurso</summary>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>{$col['Field']}</td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "<td>{$col['Default']}</td>";
            echo "</tr>";
        }
        echo "</table></details>";
    } else {
        echo "<p>❌ Tabla 'categoriascurso' NO existe</p>";
        echo "<p>Creando tabla...</p>";
        $db->exec("CREATE TABLE categoriascurso (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL UNIQUE,
            descripcion TEXT
        )");
        echo "<p>✅ Tabla 'categoriascurso' creada</p>";
    }
    
    // Verificar tabla cursos
    $stmt = $db->query("SHOW TABLES LIKE 'cursos'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✅ Tabla 'cursos' existe</p>";
        
        // Mostrar estructura
        $stmt = $db->query("DESCRIBE cursos");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<details><summary>Ver estructura de cursos</summary>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>{$col['Field']}</td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "<td>{$col['Default']}</td>";
            echo "</tr>";
        }
        echo "</table></details>";
    } else {
        echo "<p>❌ Tabla 'cursos' NO existe</p>";
        echo "<p>Creando tabla...</p>";
        $db->exec("CREATE TABLE cursos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            categoria_id INT NOT NULL,
            titulo VARCHAR(200) NOT NULL,
            descripcion TEXT,
            enlace_externo VARCHAR(500),
            FOREIGN KEY (categoria_id) REFERENCES categoriascurso(id)
        )");
        echo "<p>✅ Tabla 'cursos' creada</p>";
    }
    
    // 4. Contar registros en categoriascurso
    $stmt = $db->query("SELECT COUNT(*) as total FROM categoriascurso");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>📊 Total de categorías en la tabla: {$count['total']}</p>";
    
    if ($count['total'] == 0) {
        echo "<p>⚠️ No hay categorías. Insertando datos de prueba...</p>";
        
        $db->exec("INSERT INTO categoriascurso (nombre, descripcion) VALUES 
            ('Programación', 'Cursos de desarrollo de software'),
            ('Diseño', 'Cursos de diseño gráfico y web'),
            ('Marketing', 'Cursos de marketing digital')");
        
        echo "<p>✅ Datos de prueba insertados</p>";
    }
    
    // 5. Contar registros en cursos
    $stmt = $db->query("SELECT COUNT(*) as total FROM cursos");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>📊 Total de cursos en la tabla: {$count['total']}</p>";
    
    // 6. Probar el modelo
    $model = new App\Models\CursoModel($db);
    $categorias = $model->obtenerCategorias();
    
    echo "<h2>Resultado del Modelo:</h2>";
    echo "<p>Categorías obtenidas: " . count($categorias) . "</p>";
    
    if (!empty($categorias)) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Descripción</th></tr>";
        foreach ($categorias as $cat) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($cat['id']) . "</td>";
            echo "<td>" . htmlspecialchars($cat['nombre']) . "</td>";
            echo "<td>" . htmlspecialchars($cat['descripcion'] ?? 'Sin descripción') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<p>✅ <strong>Todo funciona correctamente. El problema podría estar en las rutas.</strong></p>";
        echo "<p>Prueba ahora: <a href='/ProyectoFinalTecWeb/public/cursos/nuevo'>Formulario de Cursos</a></p>";
    } else {
        echo "<p>❌ El modelo no retorna categorías</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ <strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>