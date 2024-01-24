<?php
include("connection.php");
?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
	$fecha_inicio = isset($_GET["fecha_inicio"]) ? $_GET["fecha_inicio"] : "";
	$fecha_fin = isset($_GET["fecha_fin"]) ? $_GET["fecha_fin"] : "";
	$ciudad = isset($_GET["ciudad"]) ? $_GET["ciudad"] : "";
	$calificacion = isset($_GET["calificacion"]) ? $_GET["calificacion"] : "";
	$precio_min = isset($_GET["precio_min"]) ? $_GET["precio_min"] : "";
	$precio_max = isset($_GET["precio_max"]) ? $_GET["precio_max"] : "";
	$avanzada = !isset($_GET["Avanzada"]) ? 0 : $_GET["Avanzada"];
	$search = isset($_GET["search"]) ? $_GET["search"] : "";
	if ($avanzada != 1) {
		$query_paquete = 'SELECT paquetes.id_paquete, paquetes.fecha_llegada, paquetes.fecha_salida,paquetes.nombre, 
		hoteles.ciudad, paquetes.precio, paquetes.num_disponibles
		FROM paquetes
		LEFT JOIN paquetes_hotel ON paquetes.id_paquete = paquetes_hotel.id_paquete
		LEFT JOIN hoteles ON paquetes_hotel.id_hotel = hoteles.id_hotel
		-- GROUP BY paquetes.id_paquete, hoteles.ciudad
		WHERE (paquetes.fecha_llegada = ? OR ? = "")
		AND (paquetes.fecha_salida = ? OR ? = "")
		AND (paquetes.nombre LIKE ? OR ? = "")
		AND (hoteles.ciudad LIKE ? OR ? = "")
		AND paquetes.num_disponibles > 0;';

		$search_param = "%" . $search . "%";
		$preRes = $conn->prepare($query_paquete);
		$preRes->bind_param("ssssssss", $fecha_fin, $fecha_fin, $fecha_inicio, $fecha_inicio, $search_param, $search, $ciudad, $ciudad);
		$preRes->execute();
		$preRes = $preRes->get_result();
		$result_search_paquete = $preRes->fetch_all(MYSQLI_ASSOC);

		$conn->next_result();

		$query_hotel = 'SELECT * FROM hoteles
		WHERE (hoteles.nombre_hotel LIKE ? OR ? = "")
		AND (hoteles.ciudad LIKE ? OR ? = "")
		AND hoteles.hab_disponibles > 0;';

		$preRes = $conn->prepare($query_hotel);
		$preRes->bind_param("ssss", $search_param, $search, $ciudad, $ciudad);
		$preRes->execute();
		$preRes = $preRes->get_result();
		$result_search_hoteles = $preRes->fetch_all(MYSQLI_ASSOC);
	} else {
		$query_paquete = 'SELECT paquetes.id_paquete, paquetes.fecha_llegada, paquetes.fecha_salida,paquetes.nombre, 
		hoteles.ciudad, paquetes.precio, AVG(paquetes_usuario.calificacion_total) AS calificacion, paquetes.num_disponibles
		FROM paquetes
		LEFT JOIN paquetes_hotel ON paquetes.id_paquete = paquetes_hotel.id_paquete
		LEFT JOIN hoteles ON paquetes_hotel.id_hotel = hoteles.id_hotel
        LEFT JOIN paquetes_usuario ON paquetes_usuario.id_paquete = paquetes.id_paquete
		GROUP BY paquetes.id_paquete, hoteles.ciudad
		HAVING (paquetes.fecha_llegada = ? OR ? = "")
		AND (paquetes.fecha_salida = ? OR ? = "")
		AND (paquetes.nombre LIKE ? OR ? = "")
		AND (hoteles.ciudad LIKE ? OR ? = "")
        AND (paquetes.precio BETWEEN IF(? = "",0,?) AND IF(? = "",(SELECT MAX(precio) FROM paquetes),?))
    	AND calificacion >= ?
		AND paquetes.num_disponibles > 0;';
		$search_param = "%" . $search . "%";
		$preRes = $conn->prepare($query_paquete);
		$preRes->bind_param("sssssssssssss", $fecha_fin, $fecha_fin, $fecha_inicio, $fecha_inicio, $search_param, $search, $ciudad, $ciudad, $precio_min, $precio_min, $precio_max, $precio_max, $calificacion);
		$preRes->execute();
		$preRes = $preRes->get_result();
		$result_search_paquete = $preRes->fetch_all(MYSQLI_ASSOC);

		$conn->next_result();

		$query_hotel = 'SELECT hoteles.id_hotel,hoteles.num_estrellas, hoteles.hab_disponibles, hoteles.nombre_hotel, hoteles.ciudad, 
		hoteles.precio_por_noche, AVG(hotel_usuario.calificacion_total) AS calificacion
		FROM hoteles
        LEFT JOIN hotel_usuario ON hotel_usuario.id_hotel = hoteles.id_hotel
		GROUP BY hoteles.id_hotel
		HAVING (hoteles.nombre_hotel LIKE ? OR ? = "")
		AND (hoteles.ciudad LIKE ? OR ? = "")
        AND (hoteles.precio_por_noche BETWEEN IF(? = "",0,?) AND IF(?="",(SELECT MAX(hoteles.precio_por_noche) FROM hoteles),?))
    	AND calificacion >= ?
		AND hoteles.hab_disponibles > 0;';

		$preRes = $conn->prepare($query_hotel);
		$preRes->bind_param("sssssssss", $search_param, $search, $ciudad, $ciudad, $precio_min, $precio_min, $precio_max, $precio_max, $calificacion);
		$preRes->execute();
		$preRes = $preRes->get_result();
		$result_search_hoteles = $preRes->fetch_all(MYSQLI_ASSOC);
	}
} else {
	header('Location: error.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php include("head.php"); ?>
	<title>Buscar</title>
</head>

<body>
	<header>
		<?php include("nav.php"); ?>
	</header>


	<div class="container mb-5">
		<?php if (!isset($_GET["solo_hoteles"]) || (isset($_GET["solo_paquetes"])  && isset($_GET["solo_hoteles"]))) : ?>
			<div class="d-flex justify-content-center mt-5 align-items-center">
				<h2>Paquetes</h2>
			</div>

			<div class="container mt-1">
				<div class="row g-5 mt-1">
					<?php foreach ($result_search_paquete as $i => $paquete) : ?>
						<?php
						$fecha_salida = date("d-m-Y", strtotime($paquete["fecha_salida"]));
						$fecha_llegada = date("d-m-Y", strtotime($paquete["fecha_llegada"]));
						$id_paquete = $paquete["id_paquete"];
						$url_paquete = "infoPaquete.php?id=" . $id_paquete;
						?>

						<div class="col col-md-4 col-lg-4">
							<div class="card">
								<img src="/IMG/paquete<?php echo ($id_paquete % 3) ?>.jpg" class="card-img-top" alt="..." style="height: 30vh">
								<div class="card-body card-body-custom">
									<h5 class="card-title"><?php echo $paquete["nombre"] ?></h5>
								</div>
								<ul class="list-group list-group-flush">
									<li class="list-group-item">Fecha salida: <?php echo $fecha_salida ?></li>
									<li class="list-group-item">Fecha llegada: <?php echo $fecha_llegada ?></li>
									<li class="list-group-item">Precio: <?php echo $paquete["precio"] ?></li>
								</ul>
								<div class="card-body">
									<form action="añadirCarro.php" method="post">
										<input type="hidden" name="id_producto" value="<?php echo $id_paquete?>">
										<input type="hidden" name="tipoId" value="id_paquete">
										<input type="hidden" name="tipo" value="p">
										<input type="hidden" name="tabla" value="carrito_paquete">
										<input type="hidden" name="cantidad" value="1">
										<a href="<?php echo $url_paquete ?>" class="btn btn-primary">Ver</a>
										<input class="btn btn-primary" type="submit" name="Añadir" value="Añadir al carrito" />
									</form>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif ?>

		<?php if (!isset($_GET["solo_paquetes"]) || (isset($_GET["solo_paquetes"])  && isset($_GET["solo_hoteles"]))) : ?>
			<div class="d-flex justify-content-center mt-5 align-items-center">
				<h2>Hoteles</h2>
			</div>

			<div class="container mb-5">
				<div class="row g-5 mt-1">
					<?php foreach ($result_search_hoteles as $i => $hotel) : ?>
						<?php
						$num_estrellas = $hotel["num_estrellas"];
						$hab_dis = $hotel["hab_disponibles"];
						$precio_por_noche = $hotel["precio_por_noche"];
						$id_hotel = $hotel["id_hotel"];
						$url_hotel = "infoHotel.php?id=" . $id_hotel;
						?>

						<div class="col col-md-4 col-lg-4">
							<div class="card">
								<img src="/IMG/hotel<?php echo ($id_hotel % 3) ?>.jpg" class="card-img-top" alt="..." style="height: 30vh">
								<div class="card-body card-body-custom">
									<h5 class="card-title"><?php echo $hotel["nombre_hotel"] ?></h5>
								</div>
								<ul class="list-group list-group-flush">
									<li class="list-group-item">Numero de num_estrellas: <?php echo $num_estrellas ?></li>
									<li class="list-group-item">Habitaciones disponibles: <?php echo $hab_dis ?></li>
									<li class="list-group-item">Precio por noche: <?php echo $precio_por_noche ?></li>
								</ul>
								<div class="card-body">
									<form action="añadirCarro.php" method="post">
										<input type="hidden" name="id_producto" value="<?php echo $id_hotel?>">
										<input type="hidden" name="tipoId" value="id_hotel">
										<input type="hidden" name="tipo" value="h">
										<input type="hidden" name="tabla" value="carrito_hoteles">
										<input type="hidden" name="cantidad" value="1">
										<a href="<?php echo $url_hotel ?>" class="btn btn-primary">Ver</a>
										<input class="btn btn-primary" type="submit" name="Añadir" value="Añadir al carrito" />
									</form>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif ?>
	</div>
</body>

</html>