<?php
include("connection.php");
?>

<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php
    include("head.php");
    ?>
    <title>Inicio</title>
</head>

<body>
    <header>
        <?php
        include("nav.php");
        ?>
    </header>

    <div class="container con">
        <div class="row mt-1 div-carrousel productos">
            <div class="col col-lg-4 mt-1 mb-2">
                <div class="nombres mb-1 mt-1">
                    <h1>Mayor disponibilidad</h1>
                </div>
                <div id="hero-carousel-1" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <?php
                        $sql = "CALL mayorReservados(4)";
                        $resultado = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
                        foreach ($resultado as $index => $item) :
                            $activeClass = ($index == 0) ? "active" : "";
                            $aria_current = ($index == 0) ? "true" : "";
                        ?>
                            <button type="button" data-bs-target="#hero-carousel-1" data-bs-slide-to="<?php echo $index ?>" class="<?php echo $activeClass ?>" aria-current="<?php echo $aria_current ?>" aria-label="Slide <?php echo ($index + 1) ?>"></button>
                        <?php endforeach; ?>
                    </div>
                    <div class="carousel-inner">
                        <?php foreach ($resultado as $index => $item) :
                            $activeClass = ($index == 0) ? "active" : "";
                            $modal = ($index == 0) ? "modal" : "";
                            $url_tipo = ($item["tipo"] == "hotel") ? "infoHotel.php?id=" . $item["id"] : "infoPaquete.php?id=" . $item["id"];
                            $imageIndex = ($item["id"]) % 3;
                            $url_img = ($item["tipo"] == "hotel") ? "/Lab2/IMG/hotel" . $imageIndex : "/Lab2/IMG/paquete"  . $imageIndex;
                        ?>
                            <div class="carousel-item <?php echo $activeClass ?> c-item">
                                <img src="<?php echo $url_img ?>.jpg" class="d-block w-100 c-img" alt="Slide <?php echo ($index + 1) ?>">
                                <div class="carousel-caption top-0 mt-3">
                                    <h1 class="fw-bolder text-capitalize con"><?php echo $item["nombre"] ?></h1>
                                    <p class="mt-4 fs-3 text-uppercase">Precio: <?php echo $item["precio"] ?></p>
                                    <p class="mt-4 fs-3 text-uppercase">Disponibles: <?php echo $item["disponible"] ?></p>
                                    <a href="<?php echo $url_tipo ?>"><button class="btn btn-primary px-4 py-2 fs-5 mt-5" data-bs-toggle="<?php echo $modal ?>">Ver</button></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#hero-carousel-1" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#hero-carousel-1" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Siguiente</span>
                    </button>
                </div>
            </div>

            <div class="col col-lg-8 mt-1 mb-2">
                <div class="nombres mb-1 mt-1">
                    <h1 class="text-center text-lg-start">Mejor calificados</h1>
                </div>
                <div id="hero-carousel-2" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <?php
                        $conn->next_result();
                        $sql_cal = "CALL mejorCalificados(10)";
                        $resultado_cal = $conn->query($sql_cal)->fetch_all(MYSQLI_ASSOC);
                        foreach ($resultado_cal as $index => $item_cal) :
                            $activeClass_cal = ($index == 0) ? "active" : "";
                            $aria_current_cal = ($index == 0) ? "true" : "";
                        ?>
                            <button type="button" data-bs-target="#hero-carousel-2" data-bs-slide-to="<?php echo $index ?>" class="<?php echo $activeClass_cal ?>" aria-current="<?php echo $aria_current_cal ?>" aria-label="Slide <?php echo ($index + 1) ?>"></button>
                        <?php endforeach; ?>
                    </div>
                    <div class="carousel-inner">
                        <?php foreach ($resultado_cal as $index => $item_cal) :
                            $activeClass_cal = ($index == 0) ? "active" : "";
                            $modal_cal = ($index == 0) ? "modal" : "";
                            $url_tipo = ($item_cal["tipo"] == "hotel") ? "infoHotel.php?id=" . $item_cal["id"] : "infoPaquete.php?id=" . $item_cal["id"];
                            $imageIndex = ($item_cal["id"]) % 3;
                            $url_img = ($item_cal["tipo"] == "hotel") ? "/Lab2/IMG/hotel" . $imageIndex : "/Lab2/IMG/paquete"  . $imageIndex;

                            $precio = ($item_cal["tipo"] == "hotel") ? "Precio por noche" : "Precio";
                        ?>
                            <div class="carousel-item <?php echo $activeClass_cal ?> c-item">
                                <img src="<?php echo $url_img ?>.jpg" class="d-block w-100 c-img" alt="Slide <?php echo ($index + 1) ?>">
                                <div class="carousel-caption top-0 mt-3">
                                    <h1 class="fw-bolder text-capitalize"><?php echo $item_cal["nombre"] ?></h1>
                                    <p class="mt-4 fs-3 text-uppercase"><?php echo $precio ?>: <?php echo $item_cal["precio"] ?></p>
                                    <p class="mt-4 fs-3 text-uppercase">Calificacion: <?php echo round($item_cal["promedio_calificacion"], 1) ?></p>
                                    <a href="<?php echo $url_tipo ?>"><button class="btn btn-primary px-4 py-2 fs-5 mt-5" data-bs-toggle="<?php echo $modal_cal ?>">Ver</button></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#hero-carousel-2" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#hero-carousel-2" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Siguiente</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>