<?php
include("connection.php");
?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET["id"];
    $query_hotel = 'SELECT * FROM hoteles WHERE id_hotel = ?';
    $preRes = $conn->prepare($query_hotel);
    $preRes->bind_param("s", $id);
    $preRes->execute();
    $preRes = $preRes->get_result();
    $result_info = $preRes->fetch_all(MYSQLI_ASSOC);
    $result_info = $result_info[0];

    $conn->next_result();

    $queryComment = "SELECT hotel_usuario.id_hotel, usuarios.nombre, hotel_usuario.calificacion_limpieza, hotel_usuario.opinion_limpieza,
    hotel_usuario.calificacion_servicio, hotel_usuario.opinion_servicio,
    hotel_usuario.calificacion_decoracion, hotel_usuario.opinion_decoracion,
    hotel_usuario.calificacion_calidad_camas, hotel_usuario.opinion_calidad_camas, hotel_usuario.fecha
    FROM hotel_usuario
    INNER JOIN usuarios ON hotel_usuario.rut = usuarios.rut
    WHERE hotel_usuario.id_hotel = ?
    ORDER BY hotel_usuario.fecha DESC";



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
    <title>Hotel: <?php echo $result_info["nombre_hotel"] ?></title>
</head>

<body>
    <?php include("nav.php"); ?>
    <div class="container mt-3">
        <div class="row">
            <div class="col col-md-5">
                <img src="/Lab2/IMG/hotel<?php echo $id % 3 ?>.jpg" class="rounded-2" style="width: 500px; height: 320px;" width=100% alt="...">
            </div>

            <div class="col-md-7">
                <div class="col-12">
                    <div class="nombre-hotel">
                        <h1>
                            <?php
                            echo "<span>" . $result_info["nombre_hotel"] . "</span>"
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
                                        <td class="atributo-hotel">
                                            <span>Numero de estrellas</span>
                                        </td>
                                        <td>
                                            <?php
                                            echo "<span>" . $result_info["num_estrellas"] . "</span>"
                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="atributo-hotel">
                                            <span>Ciudad</span>
                                        </td>
                                        <td>
                                            <?php
                                            echo "<span>" . $result_info["ciudad"] . "</span>"
                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="atributo-hotel">
                                            <span>Habitaciones disponibles</span>
                                        </td>
                                        <td>
                                            <?php
                                            echo "<span>" . $result_info["hab_disponibles"] . "</span>"
                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="atributo-hotel">
                                            <span>Estacionamiento</span>
                                        </td>
                                        <td>
                                            <?php
                                            $option = ($result_info["estacionamiento"] == 1) ? "Si" : "No";
                                            echo "<span>" . $option . "</span>"
                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="atributo-hotel">
                                            <span>Piscina</span>
                                        </td>
                                        <td>
                                            <?php
                                            $option = ($result_info["piscina"] == 1) ? "Si" : "No";
                                            echo "<span>" . $option . "</span>"
                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="atributo-hotel">
                                            <span>Servicio de lavanderia</span>
                                        </td>
                                        <td>
                                            <?php
                                            $option = ($result_info["servicio_lavanderia"] == 1) ? "Si" : "No";
                                            echo "<span>" . $option . "</span>"
                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="atributo-hotel">
                                            <span>Pet friendly</span>
                                        </td>
                                        <td>
                                            <?php
                                            $option = ($result_info["pet_friendly"] == 1) ? "Si" : "No";
                                            echo "<span>" . $option . "</span>"
                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="atributo-hotel">
                                            <span>Desayuno</span>
                                        </td>
                                        <td>
                                            <?php
                                            $option = ($result_info["desayuno"] == 1) ? "Si" : "No";
                                            echo "<span>" . $option . "</span>"
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
                                    echo "<span>" . $result_info["precio_por_noche"] . "$</span>"
                                    ?>
                                </div>
                                <div class="container">
                                    <form action="añadirCarro.php" method="post" class="row gy-1 justify-content-center">
                                        <div class="row">
                                            <label for="cantidad">Cantidad:</label>
                                            <input type="number" id="cantidad" name="cantidad" min="1" value="1" max=<?php echo $result_info['hab_disponibles'] ?>>
                                        </div>  

                                        <div class="row">
                                            <input class="btn btn-primary col gy-1" type="submit" name="Añadir" value="Añadir al carrito" />
                                        </div>

                                        <input type="hidden" name="id_producto" value="<?php echo $id; ?>">
                                        <input type="hidden" name="tipoId" value="id_hotel">
                                        <input type="hidden" name="tipo" value="h">
                                        <input type="hidden" name="tabla" value="carrito_hoteles">
                                    </form>

                                    <form action="addWishList.php" method="post" class = "row">
                                        <input class="btn btn-primary col gy-1" type="submit" name="addWishList" value="+ Wish List" />
                                        <input type="hidden" name="id_producto" value="<?php echo $id; ?>">
                                        <input type="hidden" name="tipoId" value="id_hotel">
                                    </form>
                                    <?php
                                    if (isset($_GET["error"]) and $_GET["error"] === "1") {
                                        echo "<p>la cantidad seleccionada en el carrito excede el limite disponible</p>";
                                    }elseif(isset($_GET["error"]) and $_GET["error"] === "2"){
                                        echo "<p>la cantidad no puede ser 0, verifique la disponibilidad del hotel</p>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

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
                        $queryUserComment = "SELECT * FROM hotel_usuario WHERE rut = ? AND id_hotel = ?";
                        $preRes = $conn->prepare($queryUserComment);
                        $preRes->bind_param("ss", $_SESSION["rut"], $id);
                        $preRes->execute();
                        $preRes = $preRes->get_result();
                        $resultUserComment = $preRes->fetch_all(MYSQLI_ASSOC);
                    }
                    ?>

                    <?php if ($flag && count($resultUserComment) > 0) : ?>
                        <form action="editar.php" method="post">
                            <input type="hidden" name="rating-limpieza" value="<?php echo $resultUserComment[0]['calificacion_limpieza']; ?>">
                            <input type="hidden" name="rating-servicio" value="<?php echo $resultUserComment[0]['calificacion_servicio']; ?>">
                            <input type="hidden" name="rating-decoracion" value="<?php echo $resultUserComment[0]['calificacion_decoracion']; ?>">
                            <input type="hidden" name="rating-calidad-camas" value="<?php echo $resultUserComment[0]['calificacion_calidad_camas']; ?>">


                            <div class="accordion" id="accordionExample">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading1">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="false" aria-controls="collapse1">
                                            <?php
                                            $ratings = [];

                                            if ($resultUserComment[0]["calificacion_limpieza"] !== null) {
                                                $ratings[] = $resultUserComment[0]["calificacion_limpieza"];
                                            }

                                            if ($resultUserComment[0]["calificacion_servicio"] !== null) {
                                                $ratings[] = $resultUserComment[0]["calificacion_servicio"];
                                            }

                                            if ($resultUserComment[0]["calificacion_decoracion"] !== null) {
                                                $ratings[] = $resultUserComment[0]["calificacion_decoracion"];
                                            }

                                            if ($resultUserComment[0]["calificacion_calidad_camas"] !== null) {
                                                $ratings[] = $resultUserComment[0]["calificacion_calidad_camas"];
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
                                                <textarea class="form-control" name="comment-limpieza" placeholder="Dejar comentario" id="floatingTextarea1" style="height: 100px" readonly><?php echo $resultUserComment[0]["opinion_limpieza"]; ?></textarea>
                                                <label for="floatingTextarea1">Opinión limpieza</label>
                                            </div>
                                            <div class="col d-flex mt-2 mb-5 align-items-center">
                                                <?php
                                                $cal = $resultUserComment[0]["calificacion_limpieza"];
                                                $stars = array_map(function ($j) use ($cal) {
                                                    return ($j <= $cal) ? "★" : "☆";
                                                }, range(1, 5));
                                                ?>
                                                <label for="cantidad">Tu calificacion: </label>
                                                <div class="rating-c ms-2">
                                                    <?php foreach ($stars as $index => $star) : ?>
                                                        <input type="radio" name="rating-limpieza" value="<?php echo 5 - $index; ?>" id="<?php echo 5 - $index; ?>"><label for="<?php echo 5 - $index; ?>"><?php echo $star; ?></label>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>

                                            <div class="form-floating">
                                                <textarea class="form-control" name="comment-servicio" placeholder="Dejar comentario" id="floatingTextarea3" style="height: 100px" readonly><?php echo $resultUserComment[0]["opinion_servicio"]; ?></textarea>
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
                                                        <input type="radio" name="rating-servicio" value="<?php echo 5 - $index; ?>" id="<?php echo 5 - $index; ?>"><label for="<?php echo 5 - $index; ?>"><?php echo $star; ?></label>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>

                                            <div class="form-floating">
                                                <textarea class="form-control" name="comment-decoracion" placeholder="Dejar comentario" id="floatingTextarea4" style="height: 100px" readonly><?php echo $resultUserComment[0]["opinion_decoracion"]; ?></textarea>
                                                <label for="floatingTextarea4">Opinión decoracion</label>
                                            </div>
                                            <div class="col d-flex mt-2 align-items-center">
                                                <?php
                                                $cal = $resultUserComment[0]["calificacion_decoracion"];
                                                $stars = array_map(function ($j) use ($cal) {
                                                    return ($j <= $cal) ? "★" : "☆";
                                                }, range(1, 5));
                                                ?>
                                                <label for="cantidad">Tu calificacion: </label>
                                                <div class="rating-c ms-2">
                                                    <?php foreach ($stars as $index => $star) : ?>
                                                        <input type="radio" name="rating-decoracion" value="<?php echo 5 - $index; ?>" id="<?php echo 5 - $index; ?>"><label for="<?php echo 5 - $index; ?>"><?php echo $star; ?></label>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>

                                            <div class="form-floating">
                                                <textarea class="form-control" name="comment-calidad-camas" placeholder="Dejar comentario" id="floatingTextarea4" style="height: 100px" readonly><?php echo $resultUserComment[0]["opinion_calidad_camas"]; ?></textarea>
                                                <label for="floatingTextarea4">Opinión calidad camas</label>
                                            </div>
                                            <div class="col d-flex mt-2 align-items-center">
                                                <?php
                                                $cal = $resultUserComment[0]["calificacion_calidad_camas"];
                                                $stars = array_map(function ($j) use ($cal) {
                                                    return ($j <= $cal) ? "★" : "☆";
                                                }, range(1, 5));
                                                ?>
                                                <label for="cantidad">Tu calificacion: </label>
                                                <div class="rating-c ms-2">
                                                    <?php foreach ($stars as $index => $star) : ?>
                                                        <input type="radio" name="rating-decoracion" value="<?php echo 5 - $index; ?>" id="<?php echo 5 - $index; ?>"><label for="<?php echo 5 - $index; ?>"><?php echo $star; ?></label>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col mt-2 d-flex justify-content-end">
                                <input type="hidden" name="id" value="<?php echo $id; ?>">
                                <input type="hidden" name="tipo" value="hotel">
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
                                                <textarea class="form-control" name="comment-limpieza" placeholder="Dejar comentario" id="floatingTextarea1" style="height: 100px"></textarea>
                                                <label for="floatingTextarea1">Opinión limpieza</label>
                                            </div>
                                            <div class="col d-flex mt-2 mb-5 align-items-center">
                                                <label for="cantidad">Calificación limpieza: </label>
                                                <div class="rating-limpieza ms-2">
                                                    <?php for ($i = 5; $i >= 1; $i--) : ?>
                                                        <input type="radio" name="rating-limpieza" value="<?php echo $i ?>" id="rating-limpieza-<?php echo $i ?>"><label for="rating-limpieza-<?php echo $i ?>">☆</label>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>

                                            <div class="mt-2"></div>

                                            <div class="form-floating">
                                                <textarea class="form-control" name="comment-servicio" placeholder="Dejar comentario" id="floatingTextarea2" style="height: 100px"></textarea>
                                                <label for="floatingTextarea2">Opinión servicio</label>
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
                                                <textarea class="form-control" name="comment-decoracion" placeholder="Dejar comentario" id="floatingTextarea3" style="height: 100px"></textarea>
                                                <label for="floatingTextarea3">Opinión decoracion</label>
                                            </div>
                                            <div class="col d-flex mt-2 mb-5 align-items-center">
                                                <label for="cantidad">Calificación decoracion: </label>
                                                <div class="rating-decoracion ms-2">
                                                    <?php for ($i = 5; $i >= 1; $i--) : ?>
                                                        <input type="radio" name="rating-decoracion" value="<?php echo $i ?>" id="rating-decoracion-<?php echo $i ?>"><label for="rating-decoracion-<?php echo $i ?>">☆</label>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>

                                            <div class="mt-2"></div>

                                            <div class="form-floating">
                                                <textarea class="form-control" name="comment-calidad-camas" placeholder="Dejar comentario" id="floatingTextarea4" style="height: 100px"></textarea>
                                                <label for="floatingTextarea4">Opinión calidad-camas</label>
                                            </div>
                                            <div class="col d-flex mt-2 mb-5 align-items-center">
                                                <label for="cantidad">Calificación calidad camas: </label>
                                                <div class="rating-calidad-camas ms-2">
                                                    <?php for ($i = 5; $i >= 1; $i--) : ?>
                                                        <input type="radio" name="rating-calidad-camas" value="<?php echo $i ?>" id="rating-calidad-camas-<?php echo $i ?>"><label for="rating-calidad-camas-<?php echo $i ?>">☆</label>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col mt-2 d-flex justify-content-end">
                                <input type="hidden" name="id" value="<?php echo $id; ?>">
                                <input type="hidden" name="tipo" value="hotel">
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
                            $rating_limpieza = $comment["calificacion_limpieza"];
                            $comment_limpieza = $comment["opinion_limpieza"];

                            $rating_servicio = $comment["calificacion_servicio"];
                            $comment_servicio = $comment["opinion_servicio"];

                            $rating_decoracion = $comment["calificacion_decoracion"];
                            $comment_decoracion = $comment["opinion_decoracion"];

                            $rating_calidad_camas = $comment["calificacion_calidad_camas"];
                            $comment_calidad_camas = $comment["opinion_calidad_camas"];
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

                                                if ($comment["calificacion_limpieza"] !== null) {
                                                    $ratings[] = $comment["calificacion_limpieza"];
                                                }

                                                if ($comment["calificacion_servicio"] !== null) {
                                                    $ratings[] = $comment["calificacion_servicio"];
                                                }

                                                if ($comment["calificacion_decoracion"] !== null) {
                                                    $ratings[] = $comment["calificacion_decoracion"];
                                                }

                                                if ($comment["calificacion_calidad_camas"] !== null) {
                                                    $ratings[] = $comment["calificacion_calidad_camas"];
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
                                                    <textarea class="form-control" name="comment-limpieza" placeholder="Dejar comentario" id="floatingTextarea1" style="height: 100px" readonly><?php echo $comment['opinion_limpieza'] ?></textarea>
                                                    <label for="floatingTextarea1">Opinión limpieza</label>
                                                </div>

                                                <div class="col d-flex mt-2 mb-5 align-items-center">
                                                    <?php
                                                    $cal = $comment['calificacion_limpieza'];
                                                    $stars = array_map(function ($j) use ($cal) {
                                                        return ($j <= $cal) ? "★" : "☆";
                                                    }, range(1, 5));
                                                    ?>
                                                    <label for="cantidad">Calificacion limpieza: </label>
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
                                                    <textarea class="form-control" name="comment-decoracion" placeholder="Dejar comentario" id="floatingTextarea2" style="height: 100px" readonly><?php echo $comment['opinion_decoracion'] ?></textarea>
                                                    <label for="floatingTextarea2">Opinión decoracion</label>
                                                </div>
                                                <div class="col d-flex mt-2 mb-5 align-items-center">
                                                    <?php
                                                    $cal = $comment['calificacion_decoracion'];
                                                    $stars = array_map(function ($j) use ($cal) {
                                                        return ($j <= $cal) ? "★" : "☆";
                                                    }, range(1, 5));
                                                    ?>
                                                    <label for="cantidad">Calificacion decoracion: </label>
                                                    <div class="rating-c ms-2">
                                                        <?php foreach ($stars as $index2 => $star) : ?>
                                                            <input type="radio" name="rating-c" value="<?php echo 5 - $index2 ?>" id="<?php echo 5 - $index2 ?>"><label for="<?php echo 5 - $index2 ?>"><?php echo $star ?></label>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>

                                                <div class="form-floating">
                                                    <textarea class="form-control" name="comment-calidad-camas" placeholder="Dejar comentario" id="floatingTextarea4" style="height: 100px" readonly><?php echo $comment['opinion_calidad_camas'] ?></textarea>
                                                    <label for="floatingTextarea4">Opinión calidad camas</label>
                                                </div>
                                                <div class="col d-flex mt-2 align-items-center">
                                                    <?php
                                                    $cal = $comment['calificacion_calidad_camas'];
                                                    $stars = array_map(function ($j) use ($cal) {
                                                        return ($j <= $cal) ? "★" : "☆";
                                                    }, range(1, 5));
                                                    ?>
                                                    <label for="cantidad">Calificacion calidad camas: </label>
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
