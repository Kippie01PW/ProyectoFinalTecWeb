<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inicio Sesion - NexoLearn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/PROYECTOFINALTECWEB/public/assets/css/style.css">
</head>
<body class="register bg-light">

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-login shadow">
                <div class="card-header bg-custom-green text-white text-center py-3">
                    <h2>Bienvenido de vuelta a NexoLearn</h2>
                </div>
                <div class="card-body p-4">
                    <form id="loginForm">
                        <div class="mb-3">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" name="email" class="form-control border-success" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control border-success" required>
                        </div>
                        
                        <button type="submit" class="btn btn-login w-100 text-black">Ingresar</button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="text-dark">¿No tienes cuenta? 
                            <a href="/PROYECTOFINALTECWEB/?action=auth/register" class="text-decoration-none text-azul-logo">
                                Regístrate aquí
                            </a>
                        </p>
                    </div>
                    
                    <div id="loginMessage" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/PROYECTOFINALTECWEB/public/assets/js/login.js"></script>
</body>
</html>