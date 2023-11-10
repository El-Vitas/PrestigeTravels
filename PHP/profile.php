<?php
include("connection.php");
?>

<?php
$query_comment_paquetes = "SELECT paquetes_usuario.id_paquete, paquetes_usuario.calificacion_calidad_hoteles,
paquetes_usuario.opinion_calidad_hoteles, paquetes_usuario.calificacion_transporte, paquetes_usuario.opinion_transporte,
paquetes_usuario.calificacion_servicio, paquetes_usuario.opinion_servicio,
paquetes_usuario.calificacion_relacion_precio_calidad, paquetes_usuario.opinion_relacion_precio_calidad,
paquetes_usuario.calificacion_total, paquetes_usuario.fecha, paquetes.nombre
FROM paquetes_usuario
JOIN paquetes ON paquetes.id_paquete = paquetes_usuario.id_paquete
WHERE rut = ?
ORDER BY fecha DESC;";
$preRes = $conn->prepare($query_comment_paquetes);
$preRes->bind_param("i", $_SESSION["rut"]);
$preRes->execute();
$preRes = $preRes->get_result();
$result_comment_paquete = $preRes->fetch_all(MYSQLI_ASSOC);

$query_comment_hotel = "SELECT hotel_usuario.id_hotel, hotel_usuario.calificacion_limpieza, 
hotel_usuario.opinion_limpieza, hotel_usuario.calificacion_servicio, hotel_usuario.opinion_servicio, 
hotel_usuario.calificacion_decoracion, hotel_usuario.opinion_decoracion, hotel_usuario.calificacion_calidad_camas,
hotel_usuario.opinion_calidad_camas, hotel_usuario.calificacion_total, hotel_usuario.fecha, hoteles.nombre_hotel
FROM hotel_usuario
JOIN hoteles ON hoteles.id_hotel = hotel_usuario.id_hotel
WHERE rut = ?
ORDER BY fecha DESC;";
$preRes = $conn->prepare($query_comment_hotel);
$preRes->bind_param("i", $_SESSION["rut"]);
$preRes->execute();
$preRes = $preRes->get_result();
$result_comment_hotel = $preRes->fetch_all(MYSQLI_ASSOC);

?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    include("head.php");
    ?>
    <link rel="stylesheet" href="/Lab2/CSS/profile.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/Lab2/CSS/info.css" />
    <title>Perfil</title>
</head>

<body>
    <header>
        <?php
        include("nav.php");
        ?>
    </header>
    <div class="container-fluid">
        <div class="row justify-content-center align-items-center" style="height: 100vh;">
            <div class="col-md-4">
                <div class="box">
                    <div class="perfil text-center">
                    <?php if (isset($_SESSION["rut"])):
                        $rut = $_SESSION["rut"];
                        $ultimoDigito = substr($rut, -1);
                        $rutSeparado = substr($rut, 0, -1) . '-' . $ultimoDigito;
                    ?>
                        <h3>Perfil</h3>
                        <div class="perfil-container">
                            <p class="info"><span class="label">Nombre:</span> </br><?php echo $_SESSION["nombre"]?></p>
                            <p class="info"><span class="label">Rut:</span><br/><?php echo $rutSeparado?></p>
                            <p class="info"><span class="label">Correo:</span> </br><?php echo $_SESSION["correo"] ?></p>
                            <p class="info"><span class="label">Fecha de nacimiento:</span> </br><?php echo date("d/m/Y", strtotime($_SESSION["fecha_nacimiento"])) ?></p> 
                        </div>

                        <a href="EditarPerfil.php" class="btn btn-warning">Editar Perfil</a>
                        <a href="wishList.php" class="btn btn-info">Wish List</a>

                    <?php else: ?>
                        <a href="SignUp.php" class="btn btn-warning">Crear Cuenta</a>
                        <a href="login.php" class="btn btn-warning">Iniciar Sesión</a>'
                    <?php endif ?>  
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="box container-fluid">
                    <div class="perfil text-center">
                        <h3>Tus reseñas</h3>
                        <div class="col-12 scrollable-container2 mb-5 p-3">
                            <h4>Hoteles</h4>
                            <ul class="list-unstyled comentarios">
                                <?php foreach ($result_comment_hotel as $index1 => $comment) : ?>
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

                                    $url_hotel = "infoHotel.php?id=" . $comment["id_hotel"];
                                    ?>
                                    <li class="comentario">
                                        <div class="col mb-2">
                                            <a href="<?php echo $url_hotel ?>" class="btn btn-primary"><?php echo $comment["nombre_hotel"] ?></a>
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

                            <h4>Paquetes</h4>
                            <ul class="list-unstyled comentarios">
                                <?php foreach ($result_comment_paquete as $index1 => $comment) : ?>
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

                                    $url_paquete = "infoPaquete.php?id=" . $comment["id_paquete"];
                                    ?>
                                    <li class="comentario">
                                        <div class="col mb-2">
                                            <a href="<?php echo $url_paquete ?>" class="btn btn-primary"><?php echo $comment["nombre"] ?></a>
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
            </div>


        </div>
    </div>
</body>



</html>