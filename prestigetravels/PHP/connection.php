<?php
$server = "db";
$username = "user";
$password = "pass";
$database = "prestigetravels";

$conn = new mysqli($server, $username, $password, $database);
if (!$conn) {
    die("Error en la conexión: " . $conn->connect_error);
}

session_start();

if (!isset($_SESSION['previous_pages'])) {
    $_SESSION['previous_pages'] = array();
}

array_push($_SESSION['previous_pages'], $_SERVER['REQUEST_URI']);

if (count($_SESSION['previous_pages']) > 2) {
    array_shift($_SESSION['previous_pages']);
}
reset($_SESSION['previous_pages']);

// var_dump($_SESSION['previous_pages']);
// $queryProcedure = "CREATE PROCEDURE mayorReservados (IN num INT) BEGIN SELECT id_paquete as id, nombre as nombre, precio as precio, num_disponibles as disponible, 'paquete' as tipo FROM paquetes UNION ALL SELECT id_hotel as id, nombre_hotel, precio_por_noche, hab_disponibles, 'hotel' as tipo FROM hoteles ORDER BY disponible DESC LIMIT num; END";
// $mayorReservados = $conn->query($queryProcedure);

// $queryProcedureCalificiados = "CREATE PROCEDURE mejorCalificados (IN num INT)
//     BEGIN 
//     SELECT 'hotel' AS tipo, hotel_usuario.id_hotel AS id, hoteles.nombre_hotel AS nombre, hoteles.precio_por_noche AS precio, AVG(hotel_usuario.calificacion_total) AS promedio_calificacion
//     FROM hotel_usuario INNER JOIN hoteles
//     ON hotel_usuario.id_hotel = hoteles.id_hotel
//     GROUP BY hotel_usuario.id_hotel
//     UNION ALL SELECT 'paquete' AS tipo, paquetes_usuario.id_paquete AS id, paquetes.nombre AS nombre, paquetes.precio AS precio, AVG(paquetes_usuario.calificacion_total) AS promedio_calificacion
//     FROM paquetes_usuario INNER JOIN paquetes
//     ON paquetes_usuario.id_paquete = paquetes.id_paquete
//     GROUP BY paquetes_usuario.id_paquete
//     ORDER BY promedio_calificacion DESC
//     LIMIT num;
//     END;";

// $mejorCalificados = $conn->query($queryProcedureCalificiados);

