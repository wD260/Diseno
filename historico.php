<?php
// Habilitar la visualización de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Obtener variables de entorno
$servername = getenv("DB_HOST");
$username = getenv("DB_USER");
$password = getenv("DB_PASS");
$dbname = getenv("DB_NAME");

if (!$servername || !$username || !$password || !$dbname) {
    die("❌ Error: No se pudieron obtener las variables de entorno.");
}

// Conexión
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

// Determinar vehículo
$vehiculo = isset($_GET['vehiculo']) ? intval($_GET['vehiculo']) : 1;

// Validar fechas
if (!isset($_GET["inicio"]) || !isset($_GET["fin"])) {
    die("❌ Error: Parámetros 'inicio' y 'fin' requeridos.");
}

$inicio = str_replace("T", " ", $_GET["inicio"]) . ":00";
$fin = str_replace("T", " ", $_GET["fin"]) . ":00";

// Consulta según vehículo
if ($vehiculo === 2) {
    $sql = "SELECT lat, lon, fecha, hora, rpm FROM datos_obd 
            WHERE CONCAT(fecha, ' ', hora) BETWEEN '$inicio' AND '$fin' 
            ORDER BY fecha ASC, hora ASC";
} else {
    $sql = "SELECT lat, lon, fecha, hora FROM locations2 
            WHERE CONCAT(fecha, ' ', hora) BETWEEN '$inicio' AND '$fin' 
            ORDER BY fecha ASC, hora ASC";
}

$result = $conn->query($sql);
if (!$result) {
    die("❌ Error en la consulta SQL: " . $conn->error);
}

// Preparar respuesta
$data = [];
while ($row = $result->fetch_assoc()) {
    // Consistencia: agregar rpm nulo si es vehículo 1
    if ($vehiculo === 1) {
        $row["rpm"] = null;
    }
    $data[] = $row;
}

// Respuesta en JSON
header('Content-Type: application/json');
echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$conn->close();
?>
