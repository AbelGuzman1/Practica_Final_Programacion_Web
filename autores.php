<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Manejar búsqueda
$buscar = $_GET['buscar'] ?? '';

$query = "SELECT * FROM autores WHERE 1=1";
$params = [];

if (!empty($buscar)) {
    $query .= " AND (nombre LIKE ? OR apellido LIKE ? OR ciudad LIKE ? OR pais LIKE ?)";
    $params[] = '%' . $buscar . '%';
    $params[] = '%' . $buscar . '%';
    $params[] = '%' . $buscar . '%';
    $params[] = '%' . $buscar . '%';
}

$query .= " ORDER BY apellido, nombre";

$stmt = $db->prepare($query);
$stmt->execute($params);
$autores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autores - Librería Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
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
                <a class="nav-link" href="index.php">Inicio</a>
                <a class="nav-link" href="libros.php">Libros</a>
                <a class="nav-link active" href="autores.php">Autores</a>
                <a class="nav-link" href="contacto.php">Contacto</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1><i class="fas fa-user-edit me-2"></i>Directorio de Autores</h1>
                <p class="text-muted">Conoce a nuestros autores destacados</p>
            </div>
        </div>

        <!-- Búsqueda -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Buscar autores</label>
                                <input type="text" class="form-control" name="buscar" 
                                       value="<?= htmlspecialchars($buscar) ?>" 
                                       placeholder="Buscar por nombre, apellido, ciudad o país...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resultados -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>Encontrados: <?= count($autores) ?> autores</h5>
                    <a href="autores.php" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-refresh"></i> Mostrar todos
                    </a>
                </div>
            </div>
        </div>

        <!-- Grid de Autores -->
        <div class="row">
            <?php if (empty($autores)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-user-times fa-3x mb-3"></i>
                        <h4>No se encontraron autores</h4>
                        <p>Intenta modificar los términos de búsqueda</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach($autores as $autor): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm author-card">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-user-circle fa-5x text-primary"></i>
                            </div>
                            
                            <h5 class="card-title text-primary">
                                <?= htmlspecialchars($autor['nombre']) ?> 
                                <?= htmlspecialchars($autor['apellido']) ?>
                            </h5>
                            
                            <div class="mb-3">
                                <p class="card-text mb-1">
                                    <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                    <strong>Ubicación:</strong><br>
                                    <?= htmlspecialchars($autor['ciudad']) ?>, 
                                    <?= htmlspecialchars($autor['estado']) ?>, 
                                    <?= htmlspecialchars($autor['pais']) ?>
                                </p>
                                
                                <p class="card-text mb-1">
                                    <i class="fas fa-phone text-success me-2"></i>
                                    <strong>Teléfono:</strong><br>
                                    <?= htmlspecialchars($autor['telefono']) ?>
                                </p>
                                
                                <p class="card-text mb-1">
                                    <i class="fas fa-home text-info me-2"></i>
                                    <strong>Dirección:</strong><br>
                                    <?= htmlspecialchars($autor['direccion']) ?>
                                </p>
                                
                                <p class="card-text">
                                    <i class="fas fa-mail-bulk text-warning me-2"></i>
                                    <strong>Código Postal:</strong><br>
                                    <?= htmlspecialchars($autor['cod_postal']) ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-light text-center">
                            <small class="text-muted">
                                <i class="fas fa-id-badge me-1"></i>
                                ID: <?= htmlspecialchars($autor['id_autor']) ?>
                            </small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
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
