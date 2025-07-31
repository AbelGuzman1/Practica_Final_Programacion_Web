<?php
require_once 'config/database.php';

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $asunto = trim($_POST['asunto'] ?? '');
    $comentario = trim($_POST['comentario'] ?? '');
    
    // Validación
    $errores = [];
    
    if (empty($nombre)) $errores[] = 'El nombre es requerido';
    if (empty($correo)) $errores[] = 'El correo es requerido';
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = 'El correo no es válido';
    if (empty($asunto)) $errores[] = 'El asunto es requerido';
    if (empty($comentario)) $errores[] = 'El comentario es requerido';
    
    if (empty($errores)) {
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            $query = "INSERT INTO contacto (correo, nombre, asunto, comentario) VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            
            if ($stmt->execute([$correo, $nombre, $asunto, $comentario])) {
                $mensaje = '¡Mensaje enviado exitosamente! Nos pondremos en contacto contigo pronto.';
                $tipo_mensaje = 'success';
                
                // Limpiar formulario
                $nombre = $correo = $asunto = $comentario = '';
            } else {
                $mensaje = 'Error al enviar el mensaje. Inténtalo nuevamente.';
                $tipo_mensaje = 'danger';
            }
        } catch (Exception $e) {
            $mensaje = 'Error de sistema: ' . $e->getMessage();
            $tipo_mensaje = 'danger';
        }
    } else {
        $mensaje = 'Por favor, corrige los siguientes errores: ' . implode(', ', $errores);
        $tipo_mensaje = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - Librería Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .contact-form {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 30px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-book-open me-2"></i>Librería Online
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Inicio</a>
                <a class="nav-link" href="libros.php">Libros</a>
                <a class="nav-link" href="autores.php">Autores</a>
                <a class="nav-link active" href="contacto.php">Contacto</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="contact-form">
                    <div class="text-center mb-4">
                        <h1><i class="fas fa-envelope fa-2x text-primary mb-3"></i></h1>
                        <h2>Contáctanos</h2>
                        <p class="text-muted">¿Tienes alguna pregunta o sugerencia? Nos encantaría escuchar de ti.</p>
                    </div>

                    <?php if (!empty($mensaje)): ?>
                        <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert">
                            <i class="fas fa-<?= $tipo_mensaje == 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
                            <?= htmlspecialchars($mensaje) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" id="contactForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">
                                    <i class="fas fa-user me-1"></i>Nombre Completo *
                                </label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       value="<?= htmlspecialchars($nombre ?? '') ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="correo" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>Correo Electrónico *
                                </label>
                                <input type="email" class="form-control" id="correo" name="correo" 
                                       value="<?= htmlspecialchars($correo ?? '') ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="asunto" class="form-label">
                                <i class="fas fa-tag me-1"></i>Asunto *
                            </label>
                            <input type="text" class="form-control" id="asunto" name="asunto" 
                                   value="<?= htmlspecialchars($asunto ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="comentario" class="form-label">
                                <i class="fas fa-comment me-1"></i>Mensaje *
                            </label>
                            <textarea class="form-control" id="comentario" name="comentario" rows="6" 
                                      placeholder="Escribe tu mensaje aquí..." required><?= htmlspecialchars($comentario ?? '') ?></textarea>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Enviar Mensaje
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; <?= date('Y') ?> Librería Online | Programación Web</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
