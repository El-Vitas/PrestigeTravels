<?php
include("connection.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php
    include("head.php");
    ?>
    <link rel="stylesheet" href="/CSS/info.css" />
    <title>Busqueda avanzada</title>
</head>

<body>
    <header>
        <?php
        include("nav.php");
        ?>
    </header>

    <div class="container">
        <form method="get" action="busqueda.php">
            <div class="nombres mt-5">
                <h3>Busqueda avanzada</h3>
            </div>
            <div class="row mt-5">
                <div class="form-group advSearch d-flex">
                    <input type="search" id="form1" name="search" class="form-control search-adv" style="height:40px" placeholder="Buscar" />
                    <button type="submit" class="btn btn-primary search-btn" style="width: 50px">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            <div class="container ps-5 pe-5">
                <div class="row mt-5 d-flex">
                    <div class="col form-group advSearch">
                        <label for="precio_min">Precio mínimo (en pesos $)</label>
                        <input type="number" class="form-control rounded-5" name="precio_min" placeholder="Ingresar precio mínimo">
                    </div>
                    <div class="col form-group advSearch">
                        <label for="precio_max">Precio máximo (en pesos $)</label>
                        <input type="number" class="form-control rounded-5" name="precio_max" placeholder="Ingresar precio máximo">
                    </div>
                    <div class="col form-check advSearch">
                        <input type="checkbox" class="form-check-input rounded-5" name="solo_paquetes" id="solo_paquetes">
                        <label class="form-check-label" for="solo_paquetes">Buscar solo paquetes</label>
                    </div>
                </div>
                <div class="row mt-1">
                    <div class="col form-group advSearch">
                        <label for="ciudad">Ciudad</label>
                        <input type="text" class="form-control rounded-5" name="ciudad" placeholder="Ingresar ciudad">
                    </div>
                    <div class="col form-group advSearch d-flex align-items-center">
                        <label for="calificacion">Calificación mínima</label>
                        <div class="rating ms-2">
                            <?php for ($i = 5; $i >= 1; $i--) : ?>
                                <input type="radio" name="calificacion" value="<?php echo $i ?>" id="<?php echo $i ?>"><label for="<?php echo $i ?>">☆</label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="col form-check advSearch">
                        <input type="checkbox" class="form-check-input rounded-5" name="solo_hoteles" id="solo_hoteles">
                        <label class="form-check-label" for="solo_hoteles">Buscar solo hoteles</label>
                    </div>
                </div>
            </div>

            <div class="row mt-1">
                <div class="col form-group advSearch d-flex align-items-center">
                    <div class="col me-5">
                        <label>Fecha de inicio:</label>
                        <input type="date" name="fecha_inicio" class="form-control">
                    </div>
                    <div class="col ms-5">
                        <label>Fecha de fin:</label>
                        <input type="date" name="fecha_fin" class="form-control">
                    </div>
                </div>
            </div>
            <input type="hidden" name="Avanzada" value="1">
        </form>

    </div>

</body>

</html>