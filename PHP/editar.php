<?php
include("connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["rut"])) {
    $id = $_POST["id"];
    $url = $_POST["tipo"] == "hotel" ? "infoHotel.php?id=" . $id : "infoPaquete.php?id=" . $id;
    date_default_timezone_set('America/Santiago');
    $fecha_actual = date('Y/m/d H:i:s');

    if ($_POST["tipo"] == "paquete") {
        $rating_calidad_hotel = isset($_POST["rating-calidad-hotel"]) ? $_POST["rating-calidad-hotel"] : null;
        $comment_calidad_hotel = isset($_POST["comment-calidad-hotel"]) && $_POST["comment-calidad-hotel"] !== "" ? $_POST["comment-calidad-hotel"] : null;

        $rating_transporte = isset($_POST["rating-transporte"]) ? $_POST["rating-transporte"] : null;
        $comment_transporte = isset($_POST["comment-transporte"]) && $_POST["comment-transporte"] !== "" ? $_POST["comment-transporte"] : null;

        $rating_servicio = isset($_POST["rating-servicio"]) ? $_POST["rating-servicio"] : null;
        $comment_servicio = isset($_POST["comment-servicio"]) && $_POST["comment-servicio"] !== "" ? $_POST["comment-servicio"] : null;

        $rating_precio_calidad = isset($_POST["rating-precio-calidad"]) ? $_POST["rating-precio-calidad"] : null;
        $comment_precio_calidad = isset($_POST["comment-precio-calidad"]) && $_POST["comment-precio-calidad"] !== "" ? $_POST["comment-precio-calidad"] : null;

        $ratings = array();

        if ($rating_calidad_hotel !== null) {
            $ratings[] = $rating_calidad_hotel;
        }

        if ($rating_transporte !== null) {
            $ratings[] = $rating_transporte;
        }

        if ($rating_servicio !== null) {
            $ratings[] = $rating_servicio;
        }

        if ($rating_precio_calidad !== null) {
            $ratings[] = $rating_precio_calidad;
        }

        $prom_rating = count($ratings) > 0 ? array_sum($ratings) / count($ratings) : null;

        $flag = $rating_calidad_hotel !== null || $comment_calidad_hotel !== null ||
            $rating_transporte !== null || $comment_transporte !== null ||
            $rating_servicio !== null || $comment_servicio !== null ||
            $rating_precio_calidad !== null || $comment_precio_calidad !== null;

        if (isset($_POST["publicar"])) {
            if ($flag) {
                $queryComment = "INSERT INTO paquetes_usuario (rut, id_paquete, calificacion_calidad_hoteles, opinion_calidad_hoteles, calificacion_transporte, opinion_transporte, calificacion_servicio, opinion_servicio, calificacion_relacion_precio_calidad, opinion_relacion_precio_calidad, calificacion_total, fecha) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $preRes = $conn->prepare($queryComment);
                $preRes->bind_param("iiisisisisds", $_SESSION["rut"], $id, $rating_calidad_hotel, $comment_calidad_hotel, $rating_transporte, $comment_transporte, $rating_servicio, $comment_servicio, $rating_precio_calidad, $comment_precio_calidad, $prom_rating, $fecha_actual);
                $preRes->execute();
            }
            header("Location: " . $url . "");
        } else if (isset($_POST["borrar"]) || (!$flag && isset($_POST["option"]))) {
            $queryDelete = "DELETE FROM paquetes_usuario WHERE rut = ? AND id_paquete = ?";
            $preRes = $conn->prepare($queryDelete);
            $preRes->bind_param("ii", $_SESSION["rut"], $id);
            $preRes->execute();
            header("Location: " . $url . "");
        } elseif (isset($_POST["editar"])) {
            $queryUserComment = "SELECT * FROM paquetes_usuario WHERE rut = ? AND id_paquete = ?";
            $preRes = $conn->prepare($queryUserComment);
            $preRes->bind_param("ss", $_SESSION["rut"], $id);
            $preRes->execute();
            $resultUserComment = $preRes->get_result()->fetch_all(MYSQLI_ASSOC);
            if (count($resultUserComment) == 0) {
                header("Location: error.php");
            }
        } elseif (isset($_POST["update"])) {
            $queryUpdate = "UPDATE paquetes_usuario SET calificacion_calidad_hoteles = ?, opinion_calidad_hoteles = ?, calificacion_transporte = ?, opinion_transporte = ?, calificacion_servicio = ?, opinion_servicio = ?, calificacion_relacion_precio_calidad = ?, opinion_relacion_precio_calidad = ?, calificacion_total = ?, fecha = ? WHERE rut = ? AND id_paquete = ?";
            $preRes = $conn->prepare($queryUpdate);
            $preRes->bind_param("isisisisdsii", $rating_calidad_hotel, $comment_calidad_hotel, $rating_transporte, $comment_transporte, $rating_servicio, $comment_servicio, $rating_precio_calidad, $comment_precio_calidad, $prom_rating, $fecha_actual, $_SESSION["rut"], $id);
            $preRes->execute();

            header("Location: " . $url . "");
        } elseif (!isset($_POST["option"])) {
            header("Location: error.php");
        }
    } elseif ($_POST["tipo"] == "hotel") {
        $rating_limpieza = isset($_POST["rating-limpieza"]) ? $_POST["rating-limpieza"] : null;
        $comment_limpieza = isset($_POST["comment-limpieza"]) && $_POST["comment-limpieza"] !== "" ? $_POST["comment-limpieza"] : null;

        $rating_servicio = isset($_POST["rating-servicio"]) ? $_POST["rating-servicio"] : null;
        $comment_servicio = isset($_POST["comment-servicio"]) && $_POST["comment-servicio"] !== "" ? $_POST["comment-servicio"] : null;

        $rating_decoracion = isset($_POST["rating-decoracion"]) ? $_POST["rating-decoracion"] : null;
        $comment_decoracion = isset($_POST["comment-decoracion"]) && $_POST["comment-decoracion"] !== "" ? $_POST["comment-decoracion"] : null;

        $rating_calidad_camas = isset($_POST["rating-calidad-camas"]) ? $_POST["rating-calidad-camas"] : null;
        $comment_calidad_camas = isset($_POST["comment-calidad-camas"]) && $_POST["comment-calidad-camas"] !== "" ? $_POST["comment-calidad-camas"] : null;

        $ratings = array();

        if ($rating_limpieza !== null) {
            $ratings[] = $rating_limpieza;
        }

        if ($rating_servicio !== null) {
            $ratings[] = $rating_servicio;
        }

        if ($rating_decoracion !== null) {
            $ratings[] = $rating_decoracion;
        }

        if ($rating_calidad_camas !== null) {
            $ratings[] = $rating_calidad_camas;
        }

        $prom_rating = count($ratings) > 0 ? array_sum($ratings) / count($ratings) : null;


        $flag = $rating_limpieza !== null || $comment_limpieza !== null ||
            $rating_servicio !== null || $comment_servicio !== null ||
            $rating_decoracion !== null || $comment_decoracion !== null ||
            $rating_calidad_camas !== null || $comment_calidad_camas !== null;


        if (isset($_POST["publicar"])) {
            if ($flag) {
                $queryComment = "INSERT INTO hotel_usuario (rut, id_hotel, calificacion_limpieza, opinion_limpieza, calificacion_servicio, opinion_servicio, calificacion_decoracion, opinion_decoracion, calificacion_calidad_camas, opinion_calidad_camas, calificacion_total, fecha) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $preRes = $conn->prepare($queryComment);
                $preRes->bind_param("iiisisisisds", $_SESSION["rut"], $id, $rating_limpieza, $comment_limpieza, $rating_servicio, $comment_servicio, $rating_decoracion, $comment_decoracion, $rating_calidad_camas, $comment_calidad_camas, $prom_rating, $fecha_actual);
                $preRes->execute();
            }
            header("Location: " . $url . "");
        } else if (isset($_POST["borrar"]) || (!$flag && isset($_POST["option"]))) {
            $queryDelete = "DELETE FROM hotel_usuario WHERE rut = ? AND id_hotel = ?";
            $preRes = $conn->prepare($queryDelete);
            $preRes->bind_param("ii", $_SESSION["rut"], $id);
            $preRes->execute();
            header("Location: " . $url . "");
        } elseif (isset($_POST["editar"])) {
            $queryUserComment = "SELECT * FROM hotel_usuario WHERE rut = ? AND id_hotel = ?";
            $preRes = $conn->prepare($queryUserComment);
            $preRes->bind_param("ss", $_SESSION["rut"], $id);
            $preRes->execute();
            $resultUserComment = $preRes->get_result()->fetch_all(MYSQLI_ASSOC);
            if (count($resultUserComment) == 0) {
                header("Location: error.php");
            }
        } elseif (isset($_POST["update"])) {
            $queryUpdate = "UPDATE hotel_usuario SET calificacion_limpieza = ?, opinion_limpieza = ?, calificacion_servicio = ?, opinion_servicio = ?, calificacion_decoracion = ?, opinion_decoracion = ?, calificacion_calidad_camas = ?, opinion_calidad_camas = ?, calificacion_total = ?, fecha = ? WHERE rut = ? AND id_hotel = ?";
            $preRes = $conn->prepare($queryUpdate);
            $preRes->bind_param("isisisisdsii", $rating_limpieza, $comment_limpieza, $rating_servicio, $comment_servicio, $rating_decoracion, $comment_decoracion, $rating_calidad_camas, $comment_calidad_camas, $prom_rating, $fecha_actual, $_SESSION["rut"], $id);
            $preRes->execute();

            header("Location: " . $url . "");
        } elseif (!isset($_POST["option"])) {
            header("Location: error.php");
        }
    } else {
        header("Location: error.php");
    }
} else {
    header("Location: " . (!isset($_SESSION["rut"]) ? "login.php" : "error.php"));
}
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include("head.php"); ?>
    <link rel="stylesheet" href="/Lab2/CSS/info.css" />
    <title>Editar</title>
</head>

<body>
    <?php include("nav.php"); ?>
    <div class="container mt-5 mb-5">
        <div class="card text-center mt-5">
            <div class="card-header">
                Editar
            </div>
            <form action="editar.php" method="post">
                <input type="hidden" name="option" value="1">
                <input type="hidden" name="tipo" value="<?php echo $_POST["tipo"]; ?>">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <div class="card-body">
                    <?php if ($_POST["tipo"] == "paquete") : ?>
                        <?php
                        $rating_calidad_hotel = isset($_POST["rating-calidad-hotel"]) ? $_POST["rating-calidad-hotel"] : null;
                        $comment_calidad_hotel = isset($_POST["comment-calidad-hotel"]) ? $_POST["comment-calidad-hotel"] : "";

                        $rating_transporte = isset($_POST["rating-transporte"]) ? $_POST["rating-transporte"] : null;
                        $comment_transporte = isset($_POST["comment-transporte"]) ? $_POST["comment-transporte"] : "";

                        $rating_servicio = isset($_POST["rating-servicio"]) ? $_POST["rating-servicio"] : null;
                        $comment_servicio = isset($_POST["comment-servicio"]) ? $_POST["comment-servicio"] : "";

                        $rating_precio_calidad = isset($_POST["rating-precio-calidad"]) ? $_POST["rating-precio-calidad"] : null;
                        $comment_precio_calidad = isset($_POST["comment-precio-calidad"]) ? $_POST["comment-precio-calidad"] : "";


                        if (isset($_POST["delete-comment-calidad-hotel"])) {
                            $comment_calidad_hotel = "";
                        }

                        if (isset($_POST["delete-calificacion-calidad-hotel"])) {
                            $rating_calidad_hotel = null;
                        }

                        if (isset($_POST["delete-comment-transporte"])) {
                            $comment_transporte = "";
                        }

                        if (isset($_POST["delete-calificacion-transporte"])) {
                            $rating_transporte = null;
                        }

                        if (isset($_POST["delete-comment-servicio"])) {
                            $comment_servicio = "";
                        }

                        if (isset($_POST["delete-calificacion-servicio"])) {
                            $rating_servicio = null;
                        }

                        if (isset($_POST["delete-comment-precio-calidad"])) {
                            $comment_precio_calidad = "";
                        }

                        if (isset($_POST["delete-calificacion-precio-calidad"])) {
                            $rating_precio_calidad = null;
                        }
                        ?>
                        <div class="form-floating">
                            <textarea class="form-control" name="comment-calidad-hotel" placeholder="Dejar comentario" id="floatingTextarea1" style="height: 100px"><?php echo $comment_calidad_hotel ?></textarea>
                            <label for="floatingTextarea1">Opinión calidad de los hoteles</label>
                        </div>

                        <div class="col d-flex mt-2 mb-5 align-items-center">
                            <label for="cantidad">Calificación calidad de los hoteles: </label>
                            <?php
                            $stars = array_fill(0, 5, "");
                            if ($rating_calidad_hotel != null) {
                                $stars[$rating_calidad_hotel - 1] = "checked";
                            }
                            ?>
                            <div class="rating-calidad-hotel ms-2">
                                <input type="radio" name="rating-calidad-hotel" value="5" id="calificacion-calidad-hotel-5" <?php echo $stars[4] ?>><label for="calificacion-calidad-hotel-5">☆</label>
                                <input type="radio" name="rating-calidad-hotel" value="4" id="calificacion-calidad-hotel-4" <?php echo $stars[3] ?>><label for="calificacion-calidad-hotel-4">☆</label>
                                <input type="radio" name="rating-calidad-hotel" value="3" id="calificacion-calidad-hotel-3" <?php echo $stars[2] ?>><label for="calificacion-calidad-hotel-3">☆</label>
                                <input type="radio" name="rating-calidad-hotel" value="2" id="calificacion-calidad-hotel-2" <?php echo $stars[1] ?>><label for="calificacion-calidad-hotel-2">☆</label>
                                <input type="radio" name="rating-calidad-hotel" value="1" id="calificacion-calidad-hotel-1" <?php echo $stars[0] ?>><label for="calificacion-calidad-hotel-1">☆</label>
                            </div>
                            <div class=" col d-flex justify-content-end">
                                <input class="btn btn-primary me-2" type="submit" name="delete-calificacion-calidad-hotel" value="Borrar calificacion" />
                                <input class="btn btn-primary" type="submit" name="delete-comment-calidad-hotel" value="Borrar comentario" />
                            </div>
                        </div>

                        <div class="form-floating">
                            <textarea class="form-control" name="comment-transporte" placeholder="Dejar comentario" id="floatingTextarea2" style="height: 100px"><?php echo $comment_transporte ?></textarea>
                            <label for="floatingTextarea2">Opinión del transporte</label>
                        </div>
                        <div class="col d-flex mt-2 mb-5 align-items-center">
                            <label for="cantidad">Calificación del transporte: </label>
                            <?php
                            $stars = array_fill(0, 5, "");
                            if ($rating_transporte != null) {
                                $stars[$rating_transporte - 1] = "checked";
                            }
                            ?>
                            <div class="rating-transporte ms-2">
                                <input type="radio" name="rating-transporte" value="5" id="calificacion-transporte-5" <?php echo $stars[4] ?>><label for="calificacion-transporte-5">☆</label>
                                <input type="radio" name="rating-transporte" value="4" id="calificacion-transporte-4" <?php echo $stars[3] ?>><label for="calificacion-transporte-4">☆</label>
                                <input type="radio" name="rating-transporte" value="3" id="calificacion-transporte-3" <?php echo $stars[2] ?>><label for="calificacion-transporte-3">☆</label>
                                <input type="radio" name="rating-transporte" value="2" id="calificacion-transporte-2" <?php echo $stars[1] ?>><label for="calificacion-transporte-2">☆</label>
                                <input type="radio" name="rating-transporte" value="1" id="calificacion-transporte-1" <?php echo $stars[0] ?>><label for="calificacion-transporte-1">☆</label>
                            </div>
                            <div class=" col d-flex justify-content-end">
                                <input class="btn btn-primary me-2" type="submit" name="delete-calificacion-transporte" value="Borrar calificación" />
                                <input class="btn btn-primary" type="submit" name="delete-comment-transporte" value="Borrar comentario" />
                            </div>
                        </div>

                        <div class="form-floating">
                            <textarea class="form-control" name="comment-servicio" placeholder="Dejar comentario" id="floatingTextarea3" style="height: 100px"><?php echo $comment_servicio ?></textarea>
                            <label for="floatingTextarea3">Opinión servicio</label>
                        </div>
                        <div class="col d-flex mt-2 mb-5 align-items-center">
                            <label for="cantidad">Calificación servicio: </label>
                            <?php
                            $stars = array_fill(0, 5, "");
                            if ($rating_servicio != null) {
                                $stars[$rating_servicio - 1] = "checked";
                            }
                            ?>
                            <div class="rating-servicio ms-2">
                                <input type="radio" name="rating-servicio" value="5" id="calificacion-servicio-5" <?php echo $stars[4] ?>><label for="calificacion-servicio-5">☆</label>
                                <input type="radio" name="rating-servicio" value="4" id="calificacion-servicio-4" <?php echo $stars[3] ?>><label for="calificacion-servicio-4">☆</label>
                                <input type="radio" name="rating-servicio" value="3" id="calificacion-servicio-3" <?php echo $stars[2] ?>><label for="calificacion-servicio-3">☆</label>
                                <input type="radio" name="rating-servicio" value="2" id="calificacion-servicio-2" <?php echo $stars[1] ?>><label for="calificacion-servicio-2">☆</label>
                                <input type="radio" name="rating-servicio" value="1" id="calificacion-servicio-1" <?php echo $stars[0] ?>><label for="calificacion-servicio-1">☆</label>
                            </div>
                            <div class=" col d-flex justify-content-end">
                                <input class="btn btn-primary me-2" type="submit" name="delete-calificacion-servicio" value="Borrar calificación" />
                                <input class="btn btn-primary" type="submit" name="delete-comment-servicio" value="Borrar comentario" />
                            </div>
                        </div>


                        <div class="form-floating">
                            <textarea class="form-control" name="comment-precio-calidad" placeholder="Dejar comentario" id="floatingTextarea4" style="height: 100px"><?php echo $comment_precio_calidad ?></textarea>
                            <label for="floatingTextarea4">Opinión precio-calidad</label>
                        </div>
                        <div class="col d-flex mt-2 align-items-center">
                            <label for="cantidad">Calificación precio-calidad: </label>
                            <?php
                            $stars = array_fill(0, 5, "");
                            if ($rating_precio_calidad != null) {
                                $stars[$rating_precio_calidad - 1] = "checked";
                            }
                            ?>
                            <div class="rating-precio-calidad ms-2">
                                <input type="radio" name="rating-precio-calidad" value="5" id="calificacion-precio-calidad-5" <?php echo $stars[4] ?>><label for="calificacion-precio-calidad-5">☆</label>
                                <input type="radio" name="rating-precio-calidad" value="4" id="calificacion-precio-calidad-4" <?php echo $stars[3] ?>><label for="calificacion-precio-calidad-4">☆</label>
                                <input type="radio" name="rating-precio-calidad" value="3" id="calificacion-precio-calidad-3" <?php echo $stars[2] ?>><label for="calificacion-precio-calidad-3">☆</label>
                                <input type="radio" name="rating-precio-calidad" value="2" id="calificacion-precio-calidad-2" <?php echo $stars[1] ?>><label for="calificacion-precio-calidad-2">☆</label>
                                <input type="radio" name="rating-precio-calidad" value="1" id="calificacion-precio-calidad-1" <?php echo $stars[0] ?>><label for="calificacion-precio-calidad-1">☆</label>
                            </div>
                            <div class=" col d-flex justify-content-end">
                                <input class="btn btn-primary me-2" type="submit" name="delete-calificacion-precio-calidad" value="Borrar calificación" />
                                <input class="btn btn-primary" type="submit" name="delete-comment-precio-calidad" value="Borrar comentario" />
                            </div>
                        </div>
                    <?php else : ?>
                        <?php
                        $rating_limpieza = isset($_POST["rating-limpieza"]) ? $_POST["rating-limpieza"] : null;
                        $comment_limpieza = isset($_POST["comment-limpieza"]) ? $_POST["comment-limpieza"] : null;

                        $rating_servicio = isset($_POST["rating-servicio"]) ? $_POST["rating-servicio"] : null;
                        $comment_servicio = isset($_POST["comment-servicio"]) ? $_POST["comment-servicio"] : null;

                        $rating_decoracion = isset($_POST["rating-decoracion"]) ? $_POST["rating-decoracion"] : null;
                        $comment_decoracion = isset($_POST["comment-decoracion"]) ? $_POST["comment-decoracion"] : null;

                        $rating_calidad_camas = isset($_POST["rating-calidad-camas"]) ? $_POST["rating-calidad-camas"] : null;
                        $comment_calidad_camas = isset($_POST["comment-calidad-camas"]) ? $_POST["comment-calidad-camas"] : null;



                        if (isset($_POST["delete-comment-limpieza"])) {
                            $comment_limpieza = "";
                        }

                        if (isset($_POST["delete-rating-limpieza"])) {
                            $rating_limpieza = null;
                        }

                        if (isset($_POST["delete-comment-servicio"])) {
                            $comment_servicio = "";
                        }

                        if (isset($_POST["delete-rating-servicio"])) {
                            $rating_servicio = null;
                        }

                        if (isset($_POST["delete-comment-decoracion"])) {
                            $comment_decoracion = "";
                        }

                        if (isset($_POST["delete-rating-decoracion"])) {
                            $rating_decoracion = null;
                        }

                        if (isset($_POST["delete-comment-calidad-camas"])) {
                            $comment_calidad_camas = "";
                        }

                        if (isset($_POST["delete-rating-calidad-camas"])) {
                            $rating_calidad_camas = null;
                        }
                        ?>
                        <div class="form-floating">
                            <textarea class="form-control" name="comment-limpieza" placeholder="Dejar comentario" id="floatingTextarea1" style="height: 100px"><?php echo $comment_limpieza ?></textarea>
                            <label for="floatingTextarea1">Opinión calidad de la limpieza</label>
                        </div>

                        <div class="col d-flex mt-2 mb-5 align-items-center">
                            <label for="cantidad">Calificación calidad de la limpieza: </label>
                            <?php
                            $stars = array_fill(0, 5, "");
                            if ($rating_limpieza != null) {
                                $stars[$rating_limpieza - 1] = "checked";
                            }
                            ?>
                            <div class="rating-limpieza ms-2">
                                <input type="radio" name="rating-limpieza" value="5" id="calificacion-limpieza-5" <?php echo $stars[4] ?>><label for="calificacion-limpieza-5">☆</label>
                                <input type="radio" name="rating-limpieza" value="4" id="calificacion-limpieza-4" <?php echo $stars[3] ?>><label for="calificacion-limpieza-4">☆</label>
                                <input type="radio" name="rating-limpieza" value="3" id="calificacion-limpieza-3" <?php echo $stars[2] ?>><label for="calificacion-limpieza-3">☆</label>
                                <input type="radio" name="rating-limpieza" value="2" id="calificacion-limpieza-2" <?php echo $stars[1] ?>><label for="calificacion-limpieza-2">☆</label>
                                <input type="radio" name="rating-limpieza" value="1" id="calificacion-limpieza-1" <?php echo $stars[0] ?>><label for="calificacion-limpieza-1">☆</label>
                            </div>
                            <div class=" col d-flex justify-content-end">
                                <input class="btn btn-primary me-2" type="submit" name="delete-rating-limpieza" value="Borrar calificación" />
                                <input class="btn btn-primary" type="submit" name="delete-comment-limpieza" value="Borrar comentario" />
                            </div>
                        </div>


                        <div class="form-floating">
                            <textarea class="form-control" name="comment-servicio" placeholder="Dejar comentario" id="floatingTextarea2" style="height: 100px"><?php echo $comment_servicio ?></textarea>
                            <label for="floatingTextarea2">Opinión del servicio</label>
                        </div>
                        <div class="col d-flex mt-2 mb-5 align-items-center">
                            <label for="cantidad">Calificación del servicio: </label>
                            <?php
                            $stars = array_fill(0, 5, "");
                            if ($rating_servicio != null) {
                                $stars[$rating_servicio - 1] = "checked";
                            }
                            ?>
                            <div class="rating-servicio ms-2">
                                <input type="radio" name="rating-servicio" value="5" id="calificacion-servicio-5" <?php echo $stars[4] ?>><label for="calificacion-servicio-5">☆</label>
                                <input type="radio" name="rating-servicio" value="4" id="calificacion-servicio-4" <?php echo $stars[3] ?>><label for="calificacion-servicio-4">☆</label>
                                <input type="radio" name="rating-servicio" value="3" id="calificacion-servicio-3" <?php echo $stars[2] ?>><label for="calificacion-servicio-3">☆</label>
                                <input type="radio" name="rating-servicio" value="2" id="calificacion-servicio-2" <?php echo $stars[1] ?>><label for="calificacion-servicio-2">☆</label>
                                <input type="radio" name="rating-servicio" value="1" id="calificacion-servicio-1" <?php echo $stars[0] ?>><label for="calificacion-servicio-1">☆</label>
                            </div>
                            <div class=" col d-flex justify-content-end">
                                <input class="btn btn-primary me-2" type="submit" name="delete-rating-servicio" value="Borrar calificación" />
                                <input class="btn btn-primary" type="submit" name="delete-comment-servicio" value="Borrar comentario" />
                            </div>
                        </div>


                        <div class="form-floating">
                            <textarea class="form-control" name="comment-decoracion" placeholder="Dejar comentario" id="floatingTextarea3" style="height: 100px"><?php echo $comment_decoracion ?></textarea>
                            <label for="floatingTextarea3">Opinión de la decoración</label>
                        </div>
                        <div class="col d-flex mt-2 mb-5 align-items-center">
                            <label for="cantidad">Calificación de la decoración: </label>
                            <?php
                            $stars = array_fill(0, 5, "");
                            if ($rating_decoracion != null) {
                                $stars[$rating_decoracion - 1] = "checked";
                            }
                            ?>
                            <div class="rating-decoracion ms-2">
                                <input type="radio" name="rating-decoracion" value="5" id="calificacion-decoracion-5" <?php echo $stars[4] ?>><label for="calificacion-decoracion-5">☆</label>
                                <input type="radio" name="rating-decoracion" value="4" id="calificacion-decoracion-4" <?php echo $stars[3] ?>><label for="calificacion-decoracion-4">☆</label>
                                <input type="radio" name="rating-decoracion" value="3" id="calificacion-decoracion-3" <?php echo $stars[2] ?>><label for="calificacion-decoracion-3">☆</label>
                                <input type="radio" name="rating-decoracion" value="2" id="calificacion-decoracion-2" <?php echo $stars[1] ?>><label for="calificacion-decoracion-2">☆</label>
                                <input type="radio" name="rating-decoracion" value="1" id="calificacion-decoracion-1" <?php echo $stars[0] ?>><label for="calificacion-decoracion-1">☆</label>
                            </div>
                            <div class=" col d-flex justify-content-end">
                                <input class="btn btn-primary me-2" type="submit" name="delete-rating-decoracion" value="Borrar calificación" />
                                <input class="btn btn-primary" type="submit" name="delete-comment-decoracion" value="Borrar comentario" />
                            </div>
                        </div>



                        <div class="form-floating">
                            <textarea class="form-control" name="comment-calidad-camas" placeholder="Dejar comentario" id="floatingTextarea4" style="height: 100px"><?php echo $comment_calidad_camas ?></textarea>
                            <label for="floatingTextarea4">Opinión calidad de las camas</label>
                        </div>
                        <div class="col d-flex mt-2 align-items-center">
                            <label for="cantidad">Calificación calidad de las camas: </label>
                            <?php
                            $stars = array_fill(0, 5, "");
                            if ($rating_calidad_camas != null) {
                                $stars[$rating_calidad_camas - 1] = "checked";
                            }
                            ?>
                            <div class="rating-calidad-camas ms-2">
                                <input type="radio" name="rating-calidad-camas" value="5" id="calificacion-calidad-camas-5" <?php echo $stars[4] ?>><label for="calificacion-calidad-camas-5">☆</label>
                                <input type="radio" name="rating-calidad-camas" value="4" id="calificacion-calidad-camas-4" <?php echo $stars[3] ?>><label for="calificacion-calidad-camas-4">☆</label>
                                <input type="radio" name="rating-calidad-camas" value="3" id="calificacion-calidad-camas-3" <?php echo $stars[2] ?>><label for="calificacion-calidad-camas-3">☆</label>
                                <input type="radio" name="rating-calidad-camas" value="2" id="calificacion-calidad-camas-2" <?php echo $stars[1] ?>><label for="calificacion-calidad-camas-2">☆</label>
                                <input type="radio" name="rating-calidad-camas" value="1" id="calificacion-calidad-camas-1" <?php echo $stars[0] ?>><label for="calificacion-calidad-camas-1">☆</label>
                            </div>
                            <div class=" col d-flex justify-content-end">
                                <input class="btn btn-primary me-2" type="submit" name="delete-rating-calidad-camas" value="Borrar calificación" />
                                <input class="btn btn-primary" type="submit" name="delete-comment-calidad-camas" value="Borrar comentario" />
                            </div>
                        </div>

                    <?php endif ?>
                </div>

                <div class="card-footer text-body-secondary">
                    <input class="btn btn-primary" type="submit" name="update" value="Actualizar" />
                </div>

            </form>
        </div>

    </div>
</body>

</html>