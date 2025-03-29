<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$lat = isset($_GET["lat"]) ? floatval($_GET["lat"]) : 0;
$lon = isset($_GET["lon"]) ? floatval($_GET["lon"]) : 0;
$radio = isset($_GET["radio"]) ? intval($_GET["radio"]) : 100;  
$inicio = isset($_GET["inicio"]) ? $_GET["inicio"] : date("Y-m-d", strtotime("-7 days"));
$fin = isset($_GET["fin"]) ? $_GET["fin"] : date("Y-m-d");

// Conexión a la base de datos
$conn = new mysqli("localhost", "usuario", "contraseña", "base_de_datos");
if ($conn->connect_error) {
    die(json_encode(["error" => "Error en la conexión: " . $conn->connect_error]));
}

// Consulta segura con prepared statements
$sql = "SELECT lat, lon, fecha, hora, 
        (6371000 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lon) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distancia 
        FROM locations2 
        WHERE fecha BETWEEN ? AND ?
        HAVING distancia <= ?
        ORDER BY fecha ASC, hora ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ddssi", $lat, $lon, $lat, $inicio, $fin, $radio);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            "lat" => $row["lat"],
            "lon" => $row["lon"],
            "fecha" => $row["fecha"],
            "hora" => $row["hora"],
            "distancia" => round($row["distancia"], 2)
        ];
    }
} else {
    die(json_encode(["error" => "Error en la consulta: " . $conn->error]));
}

echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
$conn->close();
?>
