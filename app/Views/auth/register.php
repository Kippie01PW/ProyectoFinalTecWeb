<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro - NexoLearn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/PROYECTOFINALTECWEB/public/assets/css/style.css">
</head>
<body class="register bg-light">

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-registro">
                <div class="card-header card-header-registro">
                    <h2 class="titulo-registro">Únete a NexoLearn</h2>
                </div>
                <div class="card-body p-4">
                    <form id="registerForm">
                        <div class="mb-3">
                            <label class="form-label text-dark">Nombre de usuario</label>
                            <input type="text" name="username" class="form-control border-success" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-dark">Correo electrónico</label>
                            <input type="email" name="email" class="form-control border-success" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-dark">Contraseña</label>
                            <input type="password" name="password" class="form-control border-success" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label text-dark">Rol</label>
                            <select name="role" class="form-select border-success" required>
                                <option value="alumno">Alumno</option>
                                <option value="maestro">Maestro</option>
                            </select>
                        </div>
                        
                         <div class="d-flex justify-content-between gap-3 mt-4">
                            <a href="/PROYECTOFINALTECWEB/?action=home" 
                                class="btn btn-regresar text-white flex-grow-1">Regresar</a>
                            <button type="submit" class="btn btn-registro text-white flex-grow-1"> Crear cuenta </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="text-dark">¿Ya tienes cuenta? 
                            <a href="/PROYECTOFINALTECWEB/?action=auth/login" class="enlace-login text-decoration-none">
                                Inicia sesión aquí
                            </a>
                        </p>
                    </div>
                    
                    <div id="message" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/PROYECTOFINALTECWEB/public/assets/js/register.js"></script>
</body>
</html>