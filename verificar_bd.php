<?php
require_once 'config/database.php';

echo "=== VERIFICACIÓN BASE DE DATOS LIBRERÍA ===\n";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Verificar tablas
    echo "\n📋 TABLAS EXISTENTES:\n";
    $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table'");
    foreach($tables as $table) {
        echo "- " . $table['name'] . "\n";
    }
    
    // Verificar autores
    echo "\n👥 AUTORES (muestra):\n";
    $autores = $db->query("SELECT COUNT(*) as total FROM autores")->fetch();
    echo "Total autores: " . $autores['total'] . "\n";
    
    // Verificar si existe tabla libros
    $libros_existe = $db->query("SELECT COUNT(*) as count FROM sqlite_master WHERE type='table' AND name='libros'")->fetch();
    if ($libros_existe['count'] > 0) {
        $libros = $db->query("SELECT COUNT(*) as total FROM libros")->fetch();
        echo "Total libros: " . $libros['total'] . "\n";
    } else {
        echo "⚠️  Tabla 'libros' NO EXISTE\n";
    }
    
    // Verificar tabla contacto
    $contacto_existe = $db->query("SELECT COUNT(*) as count FROM sqlite_master WHERE type='table' AND name='contacto'")->fetch();
    if ($contacto_existe['count'] > 0) {
        $contactos = $db->query("SELECT COUNT(*) as total FROM contacto")->fetch();
        echo "Total contactos: " . $contactos['total'] . "\n";
    } else {
        echo "⚠️  Tabla 'contacto' NO EXISTE\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
