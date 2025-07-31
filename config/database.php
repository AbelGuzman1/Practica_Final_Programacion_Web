<?php
class Database {
    private $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $db_path = __DIR__ . '/../database/libreria.db';
            $this->conn = new PDO("sqlite:" . $db_path);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            $this->createTables();
            $this->insertSampleData();
            
        } catch(PDOException $exception) {
            die("Error de conexión: " . $exception->getMessage());
        }
        
        return $this->conn;
    }
    
    private function createTables() {
        $sql = "
        CREATE TABLE IF NOT EXISTS autores (
            id_autor VARCHAR(11) PRIMARY KEY,
            apellido VARCHAR(15) NOT NULL,
            nombre VARCHAR(15) NOT NULL,
            telefono VARCHAR(12) NOT NULL,
            direccion VARCHAR(20) NOT NULL,
            ciudad VARCHAR(15) NOT NULL,
            estado VARCHAR(2) NOT NULL,
            pais VARCHAR(3) NOT NULL,
            cod_postal INTEGER
        );
        
        CREATE TABLE IF NOT EXISTS libros (
            id_libro INTEGER PRIMARY KEY AUTOINCREMENT,
            titulo VARCHAR(100) NOT NULL,
            tipo VARCHAR(20) NOT NULL,
            id_autor VARCHAR(11) NOT NULL,
            precio DECIMAL(10,2) NOT NULL,
            fecha_publicacion DATE,
            descripcion TEXT,
            stock INTEGER DEFAULT 0,
            FOREIGN KEY (id_autor) REFERENCES autores(id_autor)
        );
        
        CREATE TABLE IF NOT EXISTS contacto (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
            correo VARCHAR(100) NOT NULL,
            nombre VARCHAR(50) NOT NULL,
            asunto VARCHAR(100) NOT NULL,
            comentario TEXT NOT NULL
        );";
        
        $this->conn->exec($sql);
    }
    
    private function insertSampleData() {
        $stmt = $this->conn->query("SELECT COUNT(*) as count FROM autores");
        $count = $stmt->fetch()['count'];
        if ($count > 0) return;
        
        $autores = [
            ['172-32-1176', 'White', 'Johnson', '408 496-7223', '10932 Bigge Rd.', 'Menlo Park', 'CA', 'USA', 94025],
            ['213-46-8915', 'Green', 'Marjorie', '415 986-7020', '309 63rd St. #411', 'Oakland', 'CA', 'USA', 94618],
            ['238-95-7766', 'Carson', 'Cheryl', '415 548-7723', '589 Darwin Ln.', 'Berkeley', 'CA', 'USA', 94705]
        ];
        
        $sql = "INSERT INTO autores (id_autor, apellido, nombre, telefono, direccion, ciudad, estado, pais, cod_postal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        foreach ($autores as $autor) {
            $stmt->execute($autor);
        }
        
        // Insertar libros de muestra
        $libros = [
            ['Programación Web con PHP', 'Tecnología', '172-32-1176', 45.99, '2024-01-15', 'Guía completa para desarrollo web con PHP y MySQL', 25],
            ['JavaScript Avanzado', 'Tecnología', '213-46-8915', 39.99, '2024-02-20', 'Técnicas avanzadas de JavaScript para desarrolladores', 18],
            ['Bases de Datos Modernas', 'Tecnología', '238-95-7766', 52.50, '2024-03-10', 'Diseño y optimización de bases de datos relacionales', 12],
            ['HTML5 y CSS3', 'Tecnología', '172-32-1176', 35.00, '2024-01-25', 'Desarrollo front-end moderno con HTML5 y CSS3', 30],
            ['Algoritmos y Estructuras', 'Académico', '213-46-8915', 67.99, '2024-02-05', 'Fundamentos de algoritmos y estructuras de datos', 15],
            ['Desarrollo Ágil', 'Metodología', '238-95-7766', 42.75, '2024-03-15', 'Metodologías ágiles para equipos de desarrollo', 22]
        ];
        
        $sql = "INSERT INTO libros (titulo, tipo, id_autor, precio, fecha_publicacion, descripcion, stock) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        foreach ($libros as $libro) {
            $stmt->execute($libro);
        }
    }
}
?>
