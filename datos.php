<?php
// Habilitar la visualización de errores para depuración
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

// Determinar vehículo (1 por defecto)
$vehiculo = isset($_GET['vehiculo']) ? intval($_GET['vehiculo']) : 1;

// Consulta según el vehículo
if ($vehiculo === 2) {
    $sql = "SELECT lat, lon, fecha, hora, rpm FROM datos_obd ORDER BY id DESC LIMIT 1";
} else {
    $sql = "SELECT lat, lon, fecha, hora FROM locations2 ORDER BY id DESC LIMIT 1";
}

$result = $conn->query($sql);
if (!$result) {
    die("❌ Error en la consulta SQL: " . $conn->error);
}

// Preparar respuesta
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Asegurar claves si es vehículo 1
    if ($vehiculo === 1) {
        $row['rpm'] = null; // Agregar rpm con valor nulo para consistencia
    }

    echo json_encode($row);
} else {
    echo json_encode([
        "lat" => "N/A",
        "lon" => "N/A",
        "fecha" => "N/A",
        "hora" => "N/A",
        "rpm" => $vehiculo === 2 ? "N/A" : null
    ]);
}

$conn->close();
?>
