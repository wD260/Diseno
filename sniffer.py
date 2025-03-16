import socket
import json
import mysql.connector

# Configurar conexión a MySQL
db = mysql.connector.connect(
    host="dyland.czqa2gymiigq.us-east-2.rds.amazonaws.com",
    user="ddeorocarmona",  # Cambia esto si tienes otro usuario
    password="ingdylan05",  # Si usaste contraseña en MySQL, ponla aquí
    database="dyland"
)
cursor = db.cursor()

# Configurar servidor UDP
UDP_IP = "0.0.0.0"  # Escucha en todas las interfaces
UDP_PORT = 5000      # Puerto en el que la app está enviando los datos

sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
sock.bind((UDP_IP, UDP_PORT))

print(f"Sniffer escuchando en el puerto {UDP_PORT}...")

while True:
    data, addr = sock.recvfrom(1024)  # Recibe datos del GPS
    mensaje = data.decode().strip()
    print(f"Recibido: {mensaje} de {addr}")

    try:
        # Convertir de JSON a diccionario
        data_json = json.loads(mensaje)

        # Extraer valores
        lat = float(data_json["Latitud"])
        lon = float(data_json["Longitud"])
        fecha = data_json["Fecha"]  # Ahora toma "Fecha" en lugar de "Tiempo"
        hora = data_json["Hora"]    # Ahora toma "Hora"

        # Insertar en la base de datos con los campos correctos
        sql = "INSERT INTO locations2 (lat, lon, fecha, hora) VALUES (%s, %s, %s, %s)"
        cursor.execute(sql, (lat, lon, fecha, hora))
        db.commit()
        print(f"✅ Datos guardados en la base de datos: {lat}, {lon}, {fecha}, {hora}")

    except json.JSONDecodeError:
        print("❌ Error: No se pudo decodificar el JSON")
    except KeyError as e:
        print(f"❌ Error: Clave faltante en el JSON recibido -> {e}")
    except Exception as e:
        print(f"❌ Error general: {e}")


