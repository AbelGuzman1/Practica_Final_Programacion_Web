<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Manejar búsqueda
$buscar = $_GET['buscar'] ?? '';
$tipo_filtro = $_GET['tipo'] ?? '';

$query = "SELECT l.*, a.nombre, a.apellido 
          FROM libros l 
          INNER JOIN autores a ON l.id_autor = a.id_autor 
          WHERE 1=1";
$params = [];

if (!empty($buscar)) {
    $query .= " AND (l.titulo LIKE ? OR l.descripcion LIKE ? OR a.nombre LIKE ? OR a.apellido LIKE ?)";
    $params[] = '%' . $buscar . '%';
    $params[] = '%' . $buscar . '%';
    $params[] = '%' . $buscar . '%';
    $params[] = '%' . $buscar . '%';
}

if (!empty($tipo_filtro)) {
    $query .= " AND l.tipo = ?";
    $params[] = $tipo_filtro;
}

$query .= " ORDER BY l.titulo";

$stmt = $db->prepare($query);
$stmt->execute($params);
$libros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener tipos únicos para el filtro
$tipos_query = "SELECT DISTINCT tipo FROM libros ORDER BY tipo";
$tipos_stmt = $db->prepare($tipos_query);
$tipos_stmt->execute();
$tipos = $tipos_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libros - Librería Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .book-card:hover {
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }
        .price-tag {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: bold;
        }
        .stock-badge {
            position: absolute;
            top: 10px;
            right: 10px;
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
                <a class="nav-link active" href="libros.php">Libros</a>
                <a class="nav-link" href="autores.php">Autores</a>
                <a class="nav-link" href="contacto.php">Contacto</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1><i class="fas fa-book me-2"></i>Catálogo de Libros</h1>
                <p class="text-muted">Explora nuestra colección de libros disponibles</p>
            </div>
        </div>

        <!-- Filtros y Búsqueda -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Buscar libros</label>
                                <input type="text" class="form-control" name="buscar" 
                                       value="<?= htmlspecialchars($buscar) ?>" 
                                       placeholder="Buscar por título, descripción o autor...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Categoría</label>
                                <select class="form-select" name="tipo">
                                    <option value="">Todas las categorías</option>
                                    <?php foreach($tipos as $tipo): ?>
                                    <option value="<?= htmlspecialchars($tipo['tipo']) ?>" 
                                            <?= $tipo_filtro === $tipo['tipo'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($tipo['tipo']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
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
                    <h5>Encontrados: <?= count($libros) ?> libros</h5>
                    <a href="libros.php" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-refresh"></i> Mostrar todos
                    </a>
                </div>
            </div>
        </div>

        <!-- Grid de Libros -->
        <div class="row">
            <?php if (empty($libros)): ?>
                <div class="col-12">
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-search fa-3x mb-3"></i>
                        <h4>No se encontraron libros</h4>
                        <p>Intenta ajustar los criterios de búsqueda</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach($libros as $libro): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm book-card position-relative">
                        <!-- Badge de stock -->
                        <div class="stock-badge">
                            <?php if ($libro['stock'] > 10): ?>
                                <span class="badge bg-success">Disponible</span>
                            <?php elseif ($libro['stock'] > 0): ?>
                                <span class="badge bg-warning">Pocas unidades</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Agotado</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <div class="mb-3 text-center">
                                <i class="fas fa-book fa-4x text-primary"></i>
                            </div>
                            
                            <h5 class="card-title"><?= htmlspecialchars($libro['titulo']) ?></h5>
                            
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-user"></i>
                                    <?= htmlspecialchars($libro['nombre'] . ' ' . $libro['apellido']) ?>
                                </small>
                            </div>
                            
                            <div class="mb-2">
                                <span class="badge bg-secondary"><?= htmlspecialchars($libro['tipo']) ?></span>
                            </div>
                            
                            <p class="card-text flex-grow-1">
                                <?= htmlspecialchars(substr($libro['descripcion'], 0, 100)) ?>
                                <?= strlen($libro['descripcion']) > 100 ? '...' : '' ?>
                            </p>
                            
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="price-tag">
                                        $<?= number_format($libro['precio'], 2) ?>
                                    </span>
                                    <small class="text-muted">
                                        Stock: <?= $libro['stock'] ?>
                                    </small>
                                </div>
                                
                                <?php if ($libro['fecha_publicacion']): ?>
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i>
                                    <?= date('d/m/Y', strtotime($libro['fecha_publicacion'])) ?>
                                </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-book-open me-2"></i>Librería Online</h5>
                    <p class="text-muted">Portal de libros y autores - Proyecto Final</p>
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
