<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Obtener autores
$query = "SELECT * FROM autores LIMIT 6";
$stmt = $db->prepare($query);
$stmt->execute();
$autores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener libros destacados
$query = "SELECT l.*, a.nombre, a.apellido FROM libros l 
          INNER JOIN autores a ON l.id_autor = a.id_autor 
          ORDER BY l.fecha_publicacion DESC LIMIT 3";
$stmt = $db->prepare($query);
$stmt->execute();
$libros_destacados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contar totales
$query = "SELECT COUNT(*) as total FROM autores";
$stmt = $db->prepare($query);
$stmt->execute();
$total_autores = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$query = "SELECT COUNT(*) as total FROM libros";
$stmt = $db->prepare($query);
$stmt->execute();
$total_libros = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Librería Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
        }
        .stat-card {
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            margin: 10px;
        }
        .author-card:hover {
            transform: translateY(-5px);
            transition: all 0.3s ease;
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
                <a class="nav-link active" href="index.php">Inicio</a>
                <a class="nav-link" href="libros.php">Libros</a>
                <a class="nav-link" href="autores.php">Autores</a>
                <a class="nav-link" href="contacto.php">Contacto</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold">📚 Librería Online</h1>
                    <p class="lead">Descubre nuestra colección y conoce a nuestros autores destacados</p>
                    <div class="d-flex gap-3 mt-4">
                        <a href="libros.php" class="btn btn-light btn-lg">Ver Libros</a>
                        <a href="autores.php" class="btn btn-outline-light btn-lg">Ver Autores</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="stat-card">
                                <i class="fas fa-book fa-3x mb-3"></i>
                                <h2><?= $total_libros ?></h2>
                                <p>Libros Disponibles</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-card">
                                <i class="fas fa-user-edit fa-3x mb-3"></i>
                                <h2><?= $total_autores ?></h2>
                                <p>Autores Registrados</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Books Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Libros Destacados</h2>
            <div class="row">
                <?php foreach($libros_destacados as $libro): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <i class="fas fa-book fa-3x text-success"></i>
                            </div>
                            <h5 class="card-title"><?= htmlspecialchars($libro['titulo']) ?></h5>
                            <p class="card-text">
                                <small class="text-muted">
                                    <i class="fas fa-user"></i>
                                    <?= htmlspecialchars($libro['nombre'] . ' ' . $libro['apellido']) ?>
                                </small>
                            </p>
                            <p class="card-text"><?= htmlspecialchars(substr($libro['descripcion'], 0, 80)) ?>...</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-secondary"><?= htmlspecialchars($libro['tipo']) ?></span>
                                <strong class="text-success">$<?= number_format($libro['precio'], 2) ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="libros.php" class="btn btn-success">Ver Todos los Libros</a>
            </div>
        </div>
    </section>

    <!-- Authors Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Autores Destacados</h2>
            <div class="row">
                <?php foreach($autores as $autor): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm author-card">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-user-circle fa-4x text-primary"></i>
                            </div>
                            <h5 class="card-title"><?= htmlspecialchars($autor['nombre'] . ' ' . $autor['apellido']) ?></h5>
                            <p class="card-text">
                                <i class="fas fa-map-marker-alt"></i>
                                <?= htmlspecialchars($autor['ciudad']) ?>, <?= htmlspecialchars($autor['estado']) ?>
                            </p>
                            <p class="card-text">
                                <i class="fas fa-phone"></i>
                                <?= htmlspecialchars($autor['telefono']) ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="autores.php" class="btn btn-primary">Ver Todos los Autores</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-book-open me-2"></i>Librería Online</h5>
                    <p class="text-muted">Portal de autores y contacto - Proyecto Final</p>
                </div>
                <div class="col-md-6 text-center">
                    <p class="text-muted mb-0">
                        &copy; <?= date('Y') ?> Librería Online | Programación Web
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
