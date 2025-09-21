Proyecto de Sistema de Localización GPS con AWS y OBDII

Este proyecto fue desarrollado por Daniel Delgado, Wilman Daza, Dylan De Oro y Juan Lozano como parte de un trabajo colaborativo.

El sistema consiste en una aplicación Android que se conecta al vehículo mediante OBDII para medir las RPM, capturando además latitud, longitud y hora. Estos datos se enviaban en formato JSON a una base de datos MySQL en AWS. Paralelamente, se desarrolló una segunda aplicación que enviaba únicamente la ubicación (sin RPM), lo que permitió manejar dos tablas en la base de datos: una con información de RPM y otra solo con posiciones geográficas.

En una instancia EC2 en AWS se desplegaron tres páginas web que consultaban esta información:

  1. Visualización en tiempo real: mostraba la llegada de los paquetes y trazaba polilíneas para cada vehículo.

  2. Históricos: permitía buscar en un rango de tiempo y visualizar las rutas recorridas por cada vehículo.

  3. Localizador por zonas: permitía seleccionar un punto en el mapa con un radio ajustable, mostrando todas las veces que un vehículo pasó por esa zona, dividiendo las rutas por franjas horarias.

La idea principal era crear un sistema GPS accesible desde la web, para que un usuario pudiera visualizar la ubicación de su dispositivo (por defecto, su celular) y las rutas recorridas.


https://www.youtube.com/watch?v=KGWZhKI7f9c
