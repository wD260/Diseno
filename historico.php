<?php
$servername = getenv("DB_HOST");
$username = getenv("DB_USER");
$password = getenv("DB_PASS");
$dbname = getenv("DB_NAME");

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$inicio = $_GET["inicio"];
$fin = $_GET["fin"];

// Convertir formato de entrada `YYYY-MM-DDTHH:MM` a `YYYY-MM-DD HH:MM:SS`
$inicio = str_replace("T", " ", $inicio) . ":00";
$fin = str_replace("T", " ", $fin) . ":00";

// Consulta SQL asegurando que fecha y hora estén en el rango
$sql = "SELECT lat, lon, fecha, hora FROM locations2 
        WHERE CONCAT(fecha, ' ', hora) BETWEEN '$inicio' AND '$fin' 
        ORDER BY fecha ASC, hora ASC";


$result = $conn->query($sql);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = [
        "lat" => $row["lat"],
        "lon" => $row["lon"],
        "fecha" => $row["fecha"],   // Agregar fecha
        "hora" => $row["hora"]      // Agregar hora
    ];
}

header('Content-Type: application/json'); // Asegura que el navegador lo interprete como JSON
var_dump($data);
exit;

echo json_encode($data);

$conn->close();
?>
