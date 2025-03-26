<?php
// Habilitar la visualización de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = getenv("DB_HOST");
$username = getenv("DB_USER");
$password = getenv("DB_PASS");
$dbname = getenv("DB_NAME");

// Verificar si las variables de entorno se están cargando correctamente
if (!$servername || !$username || !$password || !$dbname) {
    die("❌ Error: No se pudieron obtener las variables de entorno.");
}

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión a la base de datos
if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

$sql = "SELECT lat, lon, fecha, hora FROM locations2 ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

// Verificar si la consulta se ejecutó correctamente
if (!$result) {
    die("❌ Error en la consulta SQL: " . $conn->error);
}

// Verificar si hay resultados en la consulta
if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(["lat" => "N/A", "lon" => "N/A", "fecha" => "N/A", "hora" => "N/A"]);
}

$conn->close();
?>
