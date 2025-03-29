$lat = isset($_GET["lat"]) ? floatval($_GET["lat"]) : 0;
$lon = isset($_GET["lon"]) ? floatval($_GET["lon"]) : 0;
$radio = isset($_GET["radio"]) ? intval($_GET["radio"]) : 100;  // Radio en metros
$inicio = isset($_GET["inicio"]) ? $_GET["inicio"] : date("Y-m-d", strtotime("-7 days"));
$fin = isset($_GET["fin"]) ? $_GET["fin"] : date("Y-m-d");

// Convertir el radio de metros a grados (aproximadamente)
// 1 grado de latitud = aproximadamente 111.32 km
// 1 grado de longitud varía según la latitud, aproximadamente cos(lat) * 111.32 km
$radioLatitud = $radio / 111320;  // convertir metros a grados para latitud
$radioLongitud = $radio / (111320 * cos(deg2rad($lat)));  // ajustar para longitud

// Consulta SQL utilizando la fórmula Haversine para encontrar puntos dentro del radio
// Esta es una aproximación rápida para distancias pequeñas
$sql = "SELECT lat, lon, fecha, hora, 
        (6371000 * acos(cos(radians($lat)) * cos(radians(lat)) * cos(radians(lon) - radians($lon)) + sin(radians($lat)) * sin(radians(lat)))) AS distancia 
        FROM locations2 
        WHERE fecha BETWEEN '$inicio' AND '$fin'
        HAVING distancia <= $radio 
        ORDER BY fecha ASC, hora ASC";

$result = $conn->query($sql);
$data = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            "lat" => $row["lat"],
            "lon" => $row["lon"],
            "fecha" => $row["fecha"],
            "hora" => $row["hora"],
            "distancia" => round($row["distancia"], 2)  // Redondear a 2 decimales
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$conn->close();
?>