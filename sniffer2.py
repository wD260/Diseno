import socket
import json
import mysql.connector

# Configurar conexión a MySQL
import os
from dotenv import load_dotenv

# Cargar las variables desde el archivo .env
load_dotenv()
DB_HOST = os.getenv("DB_HOST")
DB_USER = os.getenv("DB_USER")
DB_PASS = os.getenv("DB_PASS")
DB_NAME = os.getenv("DB_NAME")

db = mysql.connector.connect(
    host=DB_HOST,
    user=DB_USER,
    password=DB_PASS,
    database=DB_NAME
)

cursor = db.cursor()

# Configurar servidor UDP para vehículo con OBDII
UDP_IP = "0.0.0.0"
UDP_PORT = 5001  # <- puerto diferente

sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
sock.bind((UDP_IP, UDP_PORT))

print(f"Sniffer OBDII escuchando en el puerto {UDP_PORT}...")

while True:
    data, addr = sock.recvfrom(1024)
    mensaje = data.decode().strip()
    print(f"Recibido: {mensaje} de {addr}")

    try:
        data_json = json.loads(mensaje)

        lat = float(data_json["Latitud"])
        lon = float(data_json["Longitud"])
        fecha = data_json["Fecha"]
        hora = data_json["Hora"]
        rpm = int(data_json["RPM"])  # <- nueva clave

        sql = "INSERT INTO datos_obd (lat, lon, fecha, hora, rpm) VALUES (%s, %s, %s, %s, %s)"
        cursor.execute(sql, (lat, lon, fecha, hora, rpm))
        db.commit()
        print(f"✅ OBDII guardado: {lat}, {lon}, {fecha}, {hora}, RPM: {rpm}")

    except json.JSONDecodeError:
        print("❌ Error: No se pudo decodificar el JSON")
    except KeyError as e:
        print(f"❌ Error: Clave faltante en el JSON -> {e}")
    except Exception as e:
        print(f"❌ Error general: {e}")
