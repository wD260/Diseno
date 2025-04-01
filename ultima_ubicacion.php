<?php
$servername = getenv("DB_HOST");
$username = getenv("DB_USER");
$password = getenv("DB_PASS");
$dbname = getenv("DB_NAME");

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consulta para obtener la última ubicación registrada
$sql = "SELECT lat, lon FROM locations2 ORDER BY fecha DESC, hora DESC LIMIT 1";

$result = $conn->query($sql);
$data = [];

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $data = [
        "lat" => $row["lat"],
        "lon" => $row["lon"]
    ];
} else {
    // Si no hay datos, enviar coordenadas predeterminadas (puedes ajustarlas)
    $data = [
        "lat" => "0",
        "lon" => "0"
    ];
}

header('Content-Type: application/json');
echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$conn->close();
?>