<?php

// Obtener variables de entorno
$servername = getenv("DB_HOST");
$username = getenv("DB_USER");
$password = getenv("DB_PASS");
$dbname = getenv("DB_NAME");

header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Error de conexión: " . $conn->connect_error]));
}

// Obtener parámetros de la URL con validación
$lat = isset($_GET["lat"]) ? floatval($_GET["lat"]) : 0;
$lon = isset($_GET["lon"]) ? floatval($_GET["lon"]) : 0;
$radio = isset($_GET["radio"]) ? floatval($_GET["radio"]) : 100; // Radio en metros
$inicio = isset($_GET["inicio"]) ? $_GET["inicio"] : date("Y-m-d", strtotime("-7 days"));
$fin = isset($_GET["fin"]) ? $_GET["fin"] : date("Y-m-d");

// Query SQL con la fórmula Haversine
$sql = "SELECT lat, lon, fecha, hora, 
       (6371000 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lon) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distancia 
       FROM locations2 
       WHERE fecha BETWEEN ? AND ?
       HAVING distancia <= ?
       ORDER BY fecha ASC, hora ASC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(["error" => "Error en la preparación de la consulta: " . $conn->error]));
}

// **Corrección en bind_param()** - 6 placeholders, 6 variables con tipos correctos
$stmt->bind_param("dddssd", $lat, $lon, $lat, $inicio, $fin, $radio);

$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        "lat" => $row["lat"],
        "lon" => $row["lon"],
        "fecha" => $row["fecha"],
        "hora" => $row["hora"],
        "distancia" => round($row["distancia"], 2)
    ];
}

echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$stmt->close();
$conn->close();
?>
