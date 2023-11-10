<?php
include("connection.php");
?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET["id"];
    $query_paquete = 'SELECT * FROM paquetes WHERE id_paquete = ?';
    $preRes = $conn->prepare($query_paquete);
    $preRes->bind_param("s", $id);
    $preRes->execute();
    $preRes = $preRes->get_result();
    $result_info = $preRes->fetch_all(MYSQLI_ASSOC);
    $result_info = $result_info[0];

    $conn->next_result();

    $query_hoteles = "SELECT * 
    FROM 
    paquetes_hotel INNER JOIN hoteles
    ON paquetes_hotel.id_hotel = hoteles.id_hotel
    WHERE paquetes_hotel.id_paquete = ?;";

    $preRes = $conn->prepare($query_hoteles);
    $preRes->bind_param("s", $id);
    $preRes->execute();
    $preRes = $preRes->get_result();
    $result_hotel = $preRes->fetch_all(MYSQLI_ASSOC);

    $queryComment = "SELECT paquetes_usuario.id_paquete, usuarios.nombre, paquetes_usuario.calificacion_calidad_hoteles, paquetes_usuario.opinion_calidad_hoteles,
    paquetes_usuario.calificacion_transporte, paquetes_usuario.opinion_transporte,
    paquetes_usuario.calificacion_servicio, paquetes_usuario.opinion_servicio,
    paquetes_usuario.calificacion_relacion_precio_calidad, paquetes_usuario.opinion_relacion_precio_calidad, paquetes_usuario.fecha
    FROM paquetes_usuario
    INNER JOIN usuarios ON paquetes_usuario.rut = usuarios.rut
    WHERE paquetes_usuario.id_paquete = ?
    ORDER BY paquetes_usuario.fecha DESC";

    $preRes = $conn->prepare($queryComment);
    $preRes->bind_param("s", $id);
    $preRes->execute();
    $preRes = $preRes->get_result();
    $result_Comment = $preRes->fetch_all(MYSQLI_ASSOC);
}
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include("head.php"); ?>
    <link rel="stylesheet" href="/Lab2/CSS/info.css" />
    <title>Paquete: <?php echo $result_info["nombre"] ?></title>
</head>

<body>
    <?php include("nav.php"); ?>
    <div class="container mt-3">
        <div class="row">
            <div class="col col-md-5">
                <img src="/Lab2/IMG/paquete<?php echo $id % 3 ?>.jpg" class="rounded-2" style="width: 500px; height: 320px;" alt="...">
            </div>

            <div class="col-md-7">
                <div class="col-12">
                    <div class="nombre-paquete">
                        <h1>
                            <?php
                            echo "<span>" . $result_info["nombre"] . "</span>"
                            ?>
                        </h1>
                        <hr>
                    </div>
                </div>
                <div class="col d-flex">
                    <div class="col-md-6">
                        <div class="col">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td class="atributo-paquete">
                                            <span>Aerolinea ida</span>
                                        </td>
                                        <td>
                                            <?php
                                            echo "<span>" . $result_info["aerolinea_ida"] . "</span>"
                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="atributo-paquete">
                                            <span>Aerolinea vuelta</span>
                                        </td>
                                        <td>
                                            <?php
                                            echo "<span>" . $result_info["aerolinea_vuelta"] . "</span>"
                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="atributo-paquete">
                                            <span>Fecha salida</span>
                                        </td>
                                        <td>
                                            <?php
                                            echo "<span>" . date('d/m/Y', strtotime($result_info["fecha_salida"])) . "</span>"
                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="atributo-paquete">
                                            <span>Fecha llegada</span>
                                        </td>
                                        <td>
                                            <?php
                                            echo "<span>" . date('d/m/Y', strtotime($result_info["fecha_llegada"])) . "</span>"
                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="atributo-paquete">
                                            <span>Noches totales</span>
                                        </td>
                                        <td>
                                            <?php
                                            echo "<span>" . $result_info["noches_totales"] . "</span>"
                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="atributo-paquete">
                                            <span>Disponibles</span>
                                        </td>
                                        <td>
                                            <?php
                                            echo "<span>" . $result_info["num_disponibles"] . "</span>"
                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="atributo-paquete">
                                            <span>Persona por paquete</span>
                                        </td>
                                        <td>
                                            <?php
                                            echo "<span>" . $result_info["persona_por_paquete"] . "</span>"
                                            ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-6 d-flex justify-content-center  align-items-end">
                        <div class="card" style="width: 18rem;">
                            <div class="card-body">
                                <div class="precio-paquete">
                                    <?php
                                    echo "<span>" . $result_info["precio"] . "$</span>"
                                    ?>
                                </div>
                                <div class="container">
                                    <form action="añadirCarro.php" method="post" class="row gy-1 justify-content-center">
                                            <div class="row">
                                                <label class="form-label" for="cantidad">Cantidad:</label>
                                                <input class="form-control" type="number" id="cantidad" name="cantidad" min="1" value="1" max=<?php echo $result_info['num_disponibles'] ?>>
                                            </div>
                                            
                                            <div class="row">
                                                <input class="btn btn-primary col gy-1" type="submit" name="Añadir" value="Añadir al carrito" />
                                            </div>

                                            <input type="hidden" name="id_producto" value="<?php echo $id; ?>">
                                            <input type="hidden" name="tipoId" value="id_paquete">
                                            <input type="hidden" name="tipo" value="p">
                                            <input type="hidden" name="tabla" value="carrito_paquete">
                                    </form>

                                    <form action="addWishList.php" method="post" class = "row">
                                        <input class="btn btn-primary col gy-1" type="submit" name="addWishList" value="+ Wish List" />
                                        <input type="hidden" name="id_producto" value="<?php echo $id; ?>">
                                        <input type="hidden" name="tipoId" value="id_paquete">
                                    </form>
                                    <?php
                                    if (isset($_GET["error"]) and $_GET["error"] === "1") {
                                        echo "<p>la cantidad seleccionada en el carrito excede el limite disponible</p>";
                                    }elseif(isset($_GET["error"]) and $_GET["error"] === "2"){
                                        echo "<p>la cantidad no puede ser 0, verifique la disponibilidad del paquete</p>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <span class="atributo-paquete">Hoteles</span>
            </div>
            <div class="d-flex">
                <?php foreach ($result_hotel as $hotel) : ?>
                    <a href="infoHotel.php?id=<?php echo $hotel['id_hotel']; ?>" class="card-link">
                        <div class="card mt-3 me-5 card-custom" style="width: 18rem;">
                            <div class="card-header">
                                <?php echo $hotel['nombre_hotel']; ?>
                            </div>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Ciudad: <?php echo $hotel['ciudad']; ?></li>
                            </ul>
                        </div>
                    </a>
                <?php endforeach; ?>

            </div>
        </div>

        <div class="container mt-5">
            <div class="col-12">
                <span><?php echo count($result_Comment) ?> Comentarios</span>
                <hr>
                <div class="row">
                    <?php
                    $conn->next_result();
                    $flag = isset($_SESSION["rut"]);
                    if ($flag) {
                        $queryUserComment = "SELECT * FROM paquetes_usuario WHERE rut = ? AND id_paquete = ?";
                        $preRes = $conn->prepare($queryUserComment);
                        $preRes->bind_param("ss", $_SESSION["rut"], $id);
                        $preRes->execute();
                        $preRes = $preRes->get_result();
                        $resultUserComment = $preRes->fetch_all(MYSQLI_ASSOC);
                    }
                    ?>

                    <?php if ($flag && count($resultUserComment) > 0) : ?>
                        <form action="editar.php" method="post">
                            <input type="hidden" name="rating-calidad-hotel" value="<?php echo $resultUserComment[0]['calificacion_calidad_hoteles']; ?>">
                            <input type="hidden" name="rating-transporte" value="<?php echo $resultUserComment[0]['calificacion_transporte']; ?>">
                            <input type="hidden" name="rating-servicio" value="<?php echo $resultUserComment[0]['calificacion_servicio']; ?>">
                            <input type="hidden" name="rating-precio-calidad" value="<?php echo $resultUserComment[0]['calificacion_relacion_precio_calidad']; ?>">

                            <div class="accordion" id="accordionExample">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading1">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="false" aria-controls="collapse1">
                                            <?php
                                            $ratings = [];

                                            if ($resultUserComment[0]["calificacion_calidad_hoteles"] !== null) {
                                                $ratings[] = $resultUserComment[0]["calificacion_calidad_hoteles"];
                                            }

                                            if ($resultUserComment[0]["calificacion_transporte"] !== null) {
                                                $ratings[] = $resultUserComment[0]["calificacion_transporte"];
                                            }

                                            if ($resultUserComment[0]["calificacion_servicio"] !== null) {
                                                $ratings[] = $resultUserComment[0]["calificacion_servicio"];
                                            }

                                            if ($resultUserComment[0]["calificacion_relacion_precio_calidad"] !== null) {
                                                $ratings[] = $resultUserComment[0]["calificacion_relacion_precio_calidad"];
                                            }

                                            $prom_rating = count($ratings) > 0 ? array_sum($ratings) / count($ratings) : null;
                                            $fecha = $resultUserComment[0]["fecha"]
                                            ?>
                                            <label for="floatingTextarea1">Tu reseña</label>
                                            <label class="ms-auto" for="floatingTextarea1">Calificación total: <?php echo round($prom_rating, 1) ?></label>
                                            <div class="rating-c ms-2">
                                                <input type="radio" name="rating-c" id="rating-c-"><label for="rating-c">★</label>
                                            </div>
                                            <label class="ms-auto" for="floatingTextarea1">Fecha: <?php echo date('d/m/Y H:i', strtotime($fecha)) ?></label>
                                        </button>
                                    </h2>
                                    <div id="collapse1" class="accordion-collapse collapse" aria-labelledby="heading1" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <div class="form-floating">
                                                <textarea class="form-control" name="comment-calidad-hotel" placeholder="Dejar comentario" id="floatingTextarea1" style="height: 100px" readonly><?php echo $resultUserComment[0]["opinion_calidad_hoteles"] ?></textarea>
                                                <label for="floatingTextarea1">Opinión calidad de los hoteles</label>
                                            </div>
                                            <div class="col d-flex mt-2 mb-5 align-items-center">
                                                <?php
                                                $cal = $resultUserComment[0]["calificacion_calidad_hoteles"];
                                                $stars = array_map(function ($j) use ($cal) {
                                                    return ($j <= $cal) ? "★" : "☆";
                                                }, range(1, 5));
                                                ?>
                                                <label for="cantidad">Tu calificacion: </label>
                                                <div class="rating-c ms-2">
                                                    <?php foreach ($stars as $index => $star) : ?>
                                                        <input type="radio" name="rating-c" value="<?php echo 5 - $index ?>" id="<?php echo 5 - $index ?>"><label for="<?php echo 5 - $index ?>"><?php echo $star ?></label>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>

                                            <div class="form-floating">
                                                <textarea class="form-control" name="comment-transporte" placeholder="Dejar comentario" id="floatingTextarea2" style="height: 100px" readonly><?php echo $resultUserComment[0]["opinion_transporte"] ?></textarea>
                                                <label for="floatingTextarea2">Opinión del transporte</label>
                                            </div>
                                            <div class="col d-flex mt-2 mb-5 align-items-center">
                                                <?php
                                                $cal = $resultUserComment[0]["calificacion_transporte"];
                                                $stars = array_map(function ($j) use ($cal) {
                                                    return ($j <= $cal) ? "★" : "☆";
                                                }, range(1, 5));
                                                ?>
                                                <label for="cantidad">Tu calificacion: </label>
                                                <div class="rating-c ms-2">
                                                    <?php foreach ($stars as $index => $star) : ?>
                                                        <input type="radio" name="rating-c" value="<?php echo 5 - $index ?>" id="<?php echo 5 - $index ?>"><label for="<?php echo 5 - $index ?>"><?php echo $star ?></label>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>

                                            <div class="form-floating">
                                                <textarea class="form-control" name="comment-servicio" placeholder="Dejar comentario" id="floatingTextarea3" style="height: 100px" readonly><?php echo $resultUserComment[0]["opinion_servicio"] ?></textarea>
                                                <label for="floatingTextarea3">Opinión servicio</label>
                                            </div>
                                            <div class="col d-flex mt-2 mb-5 align-items-center">
                                                <?php
                                                $cal = $resultUserComment[0]["calificacion_servicio"];
                                                $stars = array_map(function ($j) use ($cal) {
                                                    return ($j <= $cal) ? "★" : "☆";
                                                }, range(1, 5));
                                                ?>
                                                <label for="cantidad">Tu calificacion: </label>
                                                <div class="rating-c ms-2">
                                                    <?php foreach ($stars as $index => $star) : ?>
                                                        <input type="radio" name="rating-c" value="<?php echo 5 - $index ?>" id="<?php echo 5 - $index ?>"><label for="<?php echo 5 - $index ?>"><?php echo $star ?></label>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>

                                            <div class="form-floating">
                                                <textarea class="form-control" name="comment-precio-calidad" placeholder="Dejar comentario" id="floatingTextarea4" style="height: 100px" readonly><?php echo $resultUserComment[0]["opinion_relacion_precio_calidad"] ?></textarea>
                                                <label for="floatingTextarea4">Opinión precio-calidad</label>
                                            </div>
                                            <div class="col d-flex mt-2 align-items-center">
                                                <?php
                                                $cal = $resultUserComment[0]["calificacion_relacion_precio_calidad"];
                                                $stars = array_map(function ($j) use ($cal) {
                                                    return ($j <= $cal) ? "★" : "☆";
                                                }, range(1, 5));
                                                ?>
                                                <label for="cantidad">Tu calificacion: </label>
                                                <div class="rating-c ms-2">
                                                    <?php foreach ($stars as $index => $star) : ?>
                                                        <input type="radio" name="rating-c" value="<?php echo 5 - $index ?>" id="<?php echo 5 - $index ?>"><label for="<?php echo 5 - $index ?>"><?php echo $star ?></label>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col mt-2 d-flex justify-content-end">
                                <input type="hidden" name="id" value="<?php echo $id; ?>">
                                <input type="hidden" name="tipo" value="paquete">
                                <input class="btn btn-primary me-2" type="submit" name="editar" value="Editar" />
                                <input class="btn btn-primary" type="submit" name="borrar" value="Borrar" />
                            </div>
                        </form>
                    <?php else : ?>
                        <form action="editar.php" method="post">
                            <div class="accordion" id="accordionExample">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading1">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="false" aria-controls="collapse1">
                                            Dejar reseña.
                                        </button>
                                    </h2>
                                    <div id="collapse1" class="accordion-collapse collapse" aria-labelledby="heading1" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <div class="form-floating">
                                                <textarea class="form-control" name="comment-calidad-hotel" placeholder="Dejar comentario" id="floatingTextarea1" style="height: 100px"></textarea>
                                                <label for="floatingTextarea1">Opinión calidad de los hoteles</label>
                                            </div>
                                            <div class="col d-flex mt-2 mb-5 align-items-center">
                                                <label for="cantidad">Calificación calidad de los hoteles: </label>
                                                <div class="rating-calidad-hotel ms-2">
                                                    <?php for ($i = 5; $i >= 1; $i--) : ?>
                                                        <input type="radio" name="rating-calidad-hotel" value="<?php echo $i ?>" id="rating-calidad-hotel-<?php echo $i ?>"><label for="rating-calidad-hotel-<?php echo $i ?>">☆</label>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>

                                            <div class="mt-2"></div>

                                            <div class="form-floating">
                                                <textarea class="form-control" name="comment-transporte" placeholder="Dejar comentario" id="floatingTextarea2" style="height: 100px"></textarea>
                                                <label for="floatingTextarea2">Opinión del transporte</label>
                                            </div>
                                            <div class="col d-flex mt-2 mb-5 align-items-center">
                                                <label for="cantidad">Calificación del transporte: </label>
                                                <div class="rating-transporte ms-2">
                                                    <?php for ($i = 5; $i >= 1; $i--) : ?>
                                                        <input type="radio" name="rating-transporte" value="<?php echo $i ?>" id="rating-transporte-<?php echo $i ?>"><label for="rating-transporte-<?php echo $i ?>">☆</label>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>

                                            <div class="mt-2"></div>

                                            <div class="form-floating">
                                                <textarea class="form-control" name="comment-servicio" placeholder="Dejar comentario" id="floatingTextarea3" style="height: 100px"></textarea>
                                                <label for="floatingTextarea3">Opinión servicio</label>
                                            </div>
                                            <div class="col d-flex mt-2 mb-5 align-items-center">
                                                <label for="cantidad">Calificación servicio: </label>
                                                <div class="rating-servicio ms-2">
                                                    <?php for ($i = 5; $i >= 1; $i--) : ?>
                                                        <input type="radio" name="rating-servicio" value="<?php echo $i ?>" id="rating-servicio-<?php echo $i ?>"><label for="rating-servicio-<?php echo $i ?>">☆</label>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>

                                            <div class="mt-2"></div>

                                            <div class="form-floating">
                                                <textarea class="form-control" name="comment-precio-calidad" placeholder="Dejar comentario" id="floatingTextarea4" style="height: 100px"></textarea>
                                                <label for="floatingTextarea4">Opinión precio-calidad</label>
                                            </div>
                                            <div class="col d-flex mt-2 align-items-center">
                                                <label for="cantidad">Calificación precio-calidad: </label>
                                                <div class="rating-precio-calidad ms-2">
                                                    <?php for ($i = 5; $i >= 1; $i--) : ?>
                                                        <input type="radio" name="rating-precio-calidad" value="<?php echo $i ?>" id="rating-precio-calidad-<?php echo $i ?>"><label for="rating-precio-calidad-<?php echo $i ?>">☆</label>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col mt-2 d-flex justify-content-end">
                                <input type="hidden" name="id" value="<?php echo $id; ?>">
                                <input type="hidden" name="tipo" value="paquete">
                                <input class="btn btn-primary" type="submit" name="publicar" value="Publicar" />
                            </div>
                        </form>
                    <?php endif; ?>
                </div>

                <div class="col-12 mt-4 scrollable-container mb-5">
                    <h5 class="atributo-paquete">Comentarios:</h5>
                    <ul class="list-unstyled comentarios">
                        <?php foreach ($result_Comment as $index1 => $comment) : ?>
                            <?php
                            $index1 += 2;
                            $rating_calidad_hotel = $comment["calificacion_calidad_hoteles"];
                            $comment_calidad_hotel = $comment["opinion_calidad_hoteles"];

                            $rating_transporte = $comment["calificacion_transporte"];
                            $comment_transporte = $comment["opinion_transporte"];

                            $rating_servicio = $comment["calificacion_servicio"];
                            $comment_servicio = $comment["opinion_servicio"];

                            $rating_precio_calidad = $comment["calificacion_relacion_precio_calidad"];
                            $comment_precio_calidad = $comment["opinion_relacion_precio_calidad"];

                            ?>
                            <li class="comentario">
                                <div class="col mb-2">
                                    <span class="nombre"><?php echo $comment["nombre"]; ?></span>
                                </div>
                                <div class="accordion" id="accordionExample-<?php echo $index1 ?>">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading<?php echo $index1 ?>">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index1 ?>" aria-expanded="false" aria-controls="collapse<?php echo $index1 ?>">
                                                <?php
                                                $ratings = [];

                                                if ($comment["calificacion_calidad_hoteles"] !== null) {
                                                    $ratings[] = $comment["calificacion_calidad_hoteles"];
                                                }

                                                if ($comment["calificacion_transporte"] !== null) {
                                                    $ratings[] = $comment["calificacion_transporte"];
                                                }

                                                if ($comment["calificacion_servicio"] !== null) {
                                                    $ratings[] = $comment["calificacion_servicio"];
                                                }

                                                if ($comment["calificacion_relacion_precio_calidad"] !== null) {
                                                    $ratings[] = $comment["calificacion_relacion_precio_calidad"];
                                                }

                                                $prom_rating = count($ratings) > 0 ? array_sum($ratings) / count($ratings) : null;
                                                $fecha = $comment["fecha"]
                                                ?>
                                                <label for="floatingTextarea1">Reseña</label>
                                                <label class="ms-auto" for="floatingTextarea1">Calificación total: <?php echo round($prom_rating, 1) ?></label>
                                                <div class="rating-c ms-2">
                                                    <input type="radio" name="rating-c" id="rating-c-"><label for="rating-c">★</label>
                                                </div>
                                                <label class="ms-auto" for="floatingTextarea1">Fecha: <?php echo date('d/m/Y H:i', strtotime($fecha)) ?></label>
                                            </button>
                                        </h2>
                                        <div id="collapse<?php echo $index1 ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $index1 ?>" data-bs-parent="#accordionExample-<?php echo $index1 ?>">
                                            <div class="accordion-body">
                                                <div class="form-floating">
                                                    <textarea class="form-control" name="comment-calidad-hotel" placeholder="Dejar comentario" id="floatingTextarea1" style="height: 100px" readonly><?php echo $comment['opinion_calidad_hoteles'] ?></textarea>
                                                    <label for="floatingTextarea1">Opinión calidad de los hoteles</label>
                                                </div>

                                                <div class="col d-flex mt-2 mb-5 align-items-center">
                                                    <?php
                                                    $cal = $comment['calificacion_calidad_hoteles'];
                                                    $stars = array_map(function ($j) use ($cal) {
                                                        return ($j <= $cal) ? "★" : "☆";
                                                    }, range(1, 5));
                                                    ?>
                                                    <label for="cantidad">Calificacion calidad de los hoteles: </label>
                                                    <div class="rating-c ms-2">
                                                        <?php foreach ($stars as $index2 => $star) : ?>
                                                            <input type="radio" name="rating-c" value="<?php echo 5 - $index2 ?>" id="<?php echo 5 - $index2 ?>"><label for="<?php echo 5 - $index2 ?>"><?php echo $star ?></label>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>

                                                <div class="form-floating">
                                                    <textarea class="form-control" name="comment-transporte" placeholder="Dejar comentario" id="floatingTextarea2" style="height: 100px" readonly><?php echo $comment['opinion_transporte'] ?></textarea>
                                                    <label for="floatingTextarea2">Opinión del transporte</label>
                                                </div>
                                                <div class="col d-flex mt-2 mb-5 align-items-center">
                                                    <?php
                                                    $cal = $comment['calificacion_transporte'];
                                                    $stars = array_map(function ($j) use ($cal) {
                                                        return ($j <= $cal) ? "★" : "☆";
                                                    }, range(1, 5));
                                                    ?>
                                                    <label for="cantidad">Calificacion del transporte: </label>
                                                    <div class="rating-c ms-2">
                                                        <?php foreach ($stars as $index2 => $star) : ?>
                                                            <input type="radio" name="rating-c" value="<?php echo 5 - $index2 ?>" id="<?php echo 5 - $index2 ?>"><label for="<?php echo 5 - $index2 ?>"><?php echo $star ?></label>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>

                                                <div class="form-floating">
                                                    <textarea class="form-control" name="comment-servicio" placeholder="Dejar comentario" id="floatingTextarea3" style="height: 100px" readonly><?php echo $comment['opinion_servicio'] ?></textarea>
                                                    <label for="floatingTextarea3">Opinión servicio</label>
                                                </div>
                                                <div class="col d-flex mt-2 mb-5 align-items-center">
                                                    <?php
                                                    $cal = $comment['calificacion_servicio'];
                                                    $stars = array_map(function ($j) use ($cal) {
                                                        return ($j <= $cal) ? "★" : "☆";
                                                    }, range(1, 5));
                                                    ?>
                                                    <label for="cantidad">Calificacion servicio: </label>
                                                    <div class="rating-c ms-2">
                                                        <?php foreach ($stars as $index2 => $star) : ?>
                                                            <input type="radio" name="rating-c" value="<?php echo 5 - $index2 ?>" id="<?php echo 5 - $index2 ?>"><label for="<?php echo 5 - $index2 ?>"><?php echo $star ?></label>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>

                                                <div class="form-floating">
                                                    <textarea class="form-control" name="comment-precio-calidad" placeholder="Dejar comentario" id="floatingTextarea4" style="height: 100px" readonly><?php echo $comment['opinion_relacion_precio_calidad'] ?></textarea>
                                                    <label for="floatingTextarea4">Opinión precio-calidad</label>
                                                </div>
                                                <div class="col d-flex mt-2 align-items-center">
                                                    <?php
                                                    $cal = $comment['calificacion_relacion_precio_calidad'];
                                                    $stars = array_map(function ($j) use ($cal) {
                                                        return ($j <= $cal) ? "★" : "☆";
                                                    }, range(1, 5));
                                                    ?>
                                                    <label for="cantidad">Calificacion precio-calidad: </label>
                                                    <div class="rating-c ms-2">
                                                        <?php foreach ($stars as $index2 => $star) : ?>
                                                            <input type="radio" name="rating-c" value="<?php echo 5 - $index2 ?>" id="<?php echo 5 - $index2 ?>"><label for="<?php echo 5 - $index2 ?>"><?php echo $star ?></label>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>

                    </ul>
                </div>
            </div>
        </div>


</body>

</html>
