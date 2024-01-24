# PrestigeTravels

Pagina Web llamada PrestigeTravels en donde se realiza el manejo de un sistema de ventas, donde se pueden añadir comentarios, calificaciones, eliminar comentarios, agregar al carrito, eliminar del carrito, etc. Realizando un crud de muchas maneras diferentes manejando la eliminación en cascada.

Laboratorio 2 de base de datos.

Integrantes: Sebastian Enrique Arrieta Moron, 202173511-9, paralelo: 201 Jonathan Olivares Salinas, 202073096-2, paralelo: 201
    Se asume que al poner los filtros solo afecta a quienes poseen esas características. el otro los ignora.
    Se asume que los paquetes son aparte de las habitaciones disponibles, es decir el cupo que tienen los paquetes en esos hoteles están reservados, y aunque en los hoteles diga 0 habitaciones, aun quedan esos cupos reservados por los paquetes, por eso es que aun se pueden comprar paquetes que contengan esos hoteles con 0 habitaciones.

## Modo de uso

Se debe tener docker y docker compose. 

Con docker, dentro del directorio en donde se encuentra docker-compose.yaml y Docker file, se debe ejecutar el siguiente comando:

```bash
docker compose up -d --build
```

De este modo se conectará mysql, php y phpmyadmin.

Primeramente la base de datos leera la carpeta db-scripts (es la base de datos exportada) e inicializará la base de datos. Ya cuando se encuentre inicializada, todos los archivos se encontrarán en la carpeta llamada DB dentro de la carpeta prestigetravels, todos los datos importantes estarán ahí. 

Si necesitas exportar la base de datos para guardarla y replazarla en db-scripts. Entonces debes ir a phpmyadmin.

**Para acceder a phpmyadmin la url es -> localhost:8080**

**Para acceder a la pagina web la url es -> localhost o localhost:80**



