<?php
$servername = "dyland.czqa2gymiigq.us-east-2.rds.amazonaws.com";
$username = "ddeorocarmona";
$password = "ingdylan05";
$dbname = "dyland";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$sql = "SELECT lat, lon, fecha, hora FROM locations2 ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(["lat" => "N/A", "lon" => "N/A", "fecha" => "N/A", "hora" => "N/A"]);
}

$conn->close();
?>
