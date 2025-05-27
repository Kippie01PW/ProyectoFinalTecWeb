<?php
if (!isset($categorias)) {
    $categorias = [];
}
if (!isset($error)) {
    $error = '';
}
if (!isset($success)) {
    $success = '';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php include __DIR__ . '/layouts/header_maestro.php';?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Curso</title>
    <link rel="stylesheet" href="/ProyectoFinalTecWeb/public/assets/css/style.css">
    <style>
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"], input[type="url"], textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .alert {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .categoria-section {
            border: 1px solid #e9ecef;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .categoria-section h3 {
            margin-top: 0;
            color: #495057;
        }
        .hidden {
            display: none;
        }
        .debug-info {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Formulario para Agregar Curso</h1>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="/ProyectoFinalTecWeb/public/cursos/guardar" method="POST" id="cursoForm">
            
            <div class="categoria-section">
                <h3>Selección de Categoría</h3>
                
                <div class="form-group">
                    <label for="categoria_id">Categoría existente:</label>
                    <select name="categoria_id" id="categoria_id">
                        <option value="">-- Selecciona una categoría existente --</option>
                        <?php if (isset($categorias) && !empty($categorias)): ?>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['id']) ?>">
                                    <?= htmlspecialchars($cat['nombre']) ?>
                                    <?php if (!empty($cat['descripcion'])): ?>
                                        - <?= htmlspecialchars($cat['descripcion']) ?>
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No hay categorías disponibles</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div style="text-align: center; margin: 15px 0; font-weight: bold; color: #6c757d;">
                    - O -
                </div>

                <div id="nueva-categoria-section">
                    <div class="form-group">
                        <label for="nueva_categoria">Crear nueva categoría:</label>
                        <input type="text" name="nueva_categoria" id="nueva_categoria" 
                               placeholder="Nombre de nueva categoría" maxlength="100">
                    </div>

                    <div class="form-group" id="descripcion-categoria-group" style="display: none;">
                        <label for="descripcion_categoria">Descripción de nueva categoría:</label>
                        <input type="text" name="descripcion_categoria" id="descripcion_categoria" 
                               placeholder="Descripción (opcional)" maxlength="255">
                    </div>
                </div>
            </div>

            <div class="categoria-section">
                <h3>Información del Curso</h3>
                
                <div class="form-group">
                    <label for="titulo">Título del curso: *</label>
                    <input type="text" name="titulo" id="titulo" required maxlength="200"
                           placeholder="Ingresa el título del curso">
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción del curso:</label>
                    <textarea name="descripcion" id="descripcion" rows="4" 
                              placeholder="Descripción detallada del curso (opcional)"></textarea>
                </div>

                <div class="form-group">
                    <label for="enlace_externo">Enlace externo:</label>
                    <input type="url" name="enlace_externo" id="enlace_externo"
                           placeholder="https://ejemplo.com/curso">
                </div>
            </div>

            <button type="submit">Guardar Curso</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoriaSelect = document.getElementById('categoria_id');
            const nuevaCategoriaInput = document.getElementById('nueva_categoria');
            const descripcionCategoriaGroup = document.getElementById('descripcion-categoria-group');
            const form = document.getElementById('cursoForm');

            nuevaCategoriaInput.addEventListener('input', function() {
                if (this.value.trim()) {
                    descripcionCategoriaGroup.style.display = 'block';
                    categoriaSelect.value = ''; 
                } else {
                    descripcionCategoriaGroup.style.display = 'none';
                }
            });

            categoriaSelect.addEventListener('change', function() {
                if (this.value) {
                    nuevaCategoriaInput.value = '';
                    document.getElementById('descripcion_categoria').value = '';
                    descripcionCategoriaGroup.style.display = 'none';
                }
            });

            form.addEventListener('submit', function(e) {
                const categoriaId = categoriaSelect.value;
                const nuevaCategoria = nuevaCategoriaInput.value.trim();
                const titulo = document.getElementById('titulo').value.trim();

                if (!titulo) {
                    alert('El título del curso es requerido.');
                    e.preventDefault();
                    return;
                }

                if (!categoriaId && !nuevaCategoria) {
                    alert('Debe seleccionar una categoría existente o crear una nueva.');
                    e.preventDefault();
                    return;
                }

                if (nuevaCategoria && nuevaCategoria.length < 2) {
                    alert('El nombre de la nueva categoría debe tener al menos 2 caracteres.');
                    e.preventDefault();
                    return;
                }
            });
        });
    </script>
</body>
</html>
<?php include __DIR__ . '/layouts/footer.php';  ?>