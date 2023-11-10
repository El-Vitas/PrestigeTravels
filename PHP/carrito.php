<?php
include("connection.php");
$total = 0;
$error = "";
if (!isset($_SESSION["rut"])) {
    header('Location: http://localhost/Lab2/PHP/login.php');
    exit();
}
?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $query = "";
    $sendQuery = true;
    if (array_key_exists('op', $_POST)) {
        $query = "UPDATE " . $_POST['tabla'] . " SET cantidad = cantidad " . $_POST['op'] . " 1 WHERE rut = ? and " . $_POST['tipoId'] . " = ?";

        $r = $_POST['op'] === "+" ? $_POST["cantidad"] + 1 : $_POST["cantidad"] - 1;
        if ($r > $_POST["disponible"] or $r === 0) {
            $sendQuery = false;
        }
    } elseif (array_key_exists('eliminar', $_POST)) {
        $query = "DELETE FROM " . $_POST['tabla'] . " WHERE rut = ? and " . $_POST['tipoId'] . " = ?";
    }
    if ($sendQuery && strlen($query) > 0) {
        $preRes = $conn->prepare($query);
        $preRes->bind_param("ss", $_SESSION['rut'], $_POST['id']);
        $preRes->execute();
    }
}
?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST)) {
        $_SESSION["desc_"] = true;
        $_SESSION["desc"] = false;
    } elseif (array_key_exists('rechazar', $_POST)) {
        $_SESSION["desc_"] = false;
        $_SESSION["desc"] = false;
    }
}
?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (array_key_exists('pagar', $_POST)) {
        $query = "CALL DisponiblePaquete(?)";
        $preRes = $conn->prepare($query);
        $preRes->bind_param("s", $_SESSION["rut"]);
        $preRes->execute();
        $disponible_paquete = $preRes->get_result()->fetch_all(MYSQLI_ASSOC);
        $preRes->free_result();
        $preRes->close();

        $query = "CALL DisponibleHotel(?)";
        $preRes = $conn->prepare($query);
        $preRes->bind_param("s", $_SESSION["rut"]);
        $preRes->execute();
        $disponible_hotel = $preRes->get_result()->fetch_all(MYSQLI_ASSOC);
        $preRes->free_result();
        $preRes->close();

        foreach ($disponible_paquete as $key => $value) {
            if ($value["cantidad"] < 0) {
                $error = "paquete " . $value["nombre"] . " no disponible";
            } elseif ($value["cantidad"] < 0) {
                $error = "hotel " . $disponible_hotel[$key]["nombre"] . " asociado al paquete " . $value["nombre"] . " no esta disponible";
            }
        }

        foreach ($disponible_hotel as $key => $value) {
            if ($value["cantidad"]< 0) {
                $error = "hotel " . $value["nombre_hotel"] . " no disponible";
            }
        }
        if (strlen($error) === 0) {
            $query = "UPDATE carrito_paquete SET compra = 1 WHERE rut = " . $_SESSION["rut"] . "";
            $preRes = $conn->prepare($query);
            $preRes->execute();

            $query = "UPDATE carrito_hoteles SET compra = 1 WHERE rut = " . $_SESSION["rut"] . "";
            $preRes = $conn->prepare($query);
            $preRes->execute();

            $query = "DELETE FROM carrito_paquete WHERE rut = " . $_SESSION["rut"] . "";
            $preRes = $conn->prepare($query);
            $preRes->execute();

            $query = "DELETE FROM carrito_hoteles WHERE rut = " . $_SESSION["rut"] . "";
            $preRes = $conn->prepare($query);
            $preRes->execute();

            $_SESSION["desc_"] = false;
        }
    }
}
?>

<?php
$query = "SELECT * FROM carrito_paquete WHERE rut = ?";
$preRes = $conn->prepare($query);
$preRes->bind_param("s", $_SESSION['rut']);
$preRes->execute();
$resultPaquete = $preRes->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<?php
$query = "SELECT * FROM carrito_hoteles WHERE rut = ?";
$preRes = $conn->prepare($query);
$preRes->bind_param("s", $_SESSION['rut']);
$preRes->execute();
$resultHotel = $preRes->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<html>

<head>
    <?php
    include("head.php");
    ?>
    <link rel="stylesheet" href="/Lab2/CSS/carrito.css?v=<?php echo time(); ?>">
    <title>carrito</title>
</head>

<body>
    <header>
        <?php
        include("nav.php");
        ?>
    </header>

    <div class="container">

        <h1>Carrito de compras</h1>

        <h3>Paquetes</h3>

        <div class="container">
            <?php if (count($resultPaquete) == 0): ?> 
                <p> no posee paquetes agregados en la bolsa </p>
            <?php else: ?>
                <?php foreach ($resultPaquete as $i=> $value):
                    $query = "SELECT * FROM paquetes WHERE id_paquete = ?";
                    $preRes = $conn->prepare($query);
                    $preRes->bind_param("s", $resultPaquete[$i]['id_paquete']);
                    $preRes->execute();
                    $paquete = $preRes->get_result()->fetch_assoc();
                    $esta_disponible = ($resultPaquete[$i]['cantidad'] - 1 < $paquete["num_disponibles"]);
                ?>

                    <?php if ($esta_disponible):
                        $total += $paquete['precio'] * $resultPaquete[$i]['cantidad'];
                    ?>

                        <div class = "cartElement">
                            <div class = "info">
                                <a href="infoPaquete.php?id=<?php echo $resultPaquete[$i]["id_paquete"] ?>" class = "nombre"><?php echo $paquete["nombre"] ?></a>
                                <p>precio: <?php echo $paquete["precio"]?></p>
                            </div>

                            <div class = "edit">
                            <form action=' . $_SERVER["PHP_SELF"] . ' method="post">
                                <label for="cantidad">Cantidad:</label>

                                <input readonly class = "cantidad" type="text" id="cantidad" name="cantidad" value="<?php echo $resultPaquete[$i]["cantidad"] ?>"/>

                                <input class="btn btn-primary editntn" type="submit" name="op" value="-" />
                                <input class="btn btn-primary editbtn" type="submit" name="op" value="+" />

                                <input class="btn btn-primary" type="submit" name="eliminar" value="eliminar" />

                                <input type="hidden" name="id" value="<?php echo $resultPaquete[$i]["id_paquete"] ?>"/>
                                <input type="hidden" name="disponible" value=" <?php echo $paquete["num_disponibles"] ?>"/>
                                <input type="hidden" name="tabla" value="carrito_paquete"/>
                                <input type="hidden" name="tipoId" value="id_paquete"/>
                            </form>
                            </div>
                        </div>

                    <?php else: ?>

                        <div class = "cartElement shadow-button">
                            <div class = "info">
                                <a href="infoPaquete.php?id=<?php echo $resultPaquete[$i]["id_paquete"] ?>" class = "nombre"><?php echo $paquete["nombre"] ?></a>
                                <p>precio: <?php echo $paquete["precio"]  ?></p>
                            </div>
                            <span class="dis"> No disponible</span>
                            <div class = "edit">
                                <form action=<?php echo $_SERVER["PHP_SELF"] ?> method="post">
                                    <label for="cantidad">Cantidad:</label>

                                    <input readonly class = "cantidad" type="text" id="cantidad" name="cantidad" value="<? echo  $resultPaquete[$i]["cantidad"] ?>"/>

                                    <input class="btn btn-primary editntn" type="submit" name="op" value="-" />
                                    <input class="btn btn-primary editbtn" type="submit" name="op" value="+" />

                                    <input class="btn btn-primary del" type="submit" name="eliminar" value="eliminar" />

                                    <input type="hidden" name="id" value="<?php echo $resultPaquete[$i]["id_paquete"] ?>"/>
                                    <input type="hidden" name="disponible" value=" <?php echo $paquete["num_disponibles"] ?>"/>
                                    <input type="hidden" name="tabla" value="carrito_paquete"/>
                                    <input type="hidden" name="tipoId" value="id_paquete"/>
                                </form>
                            </div>
                        </div>

                    <?php endif ?>
                <?php endforeach; ?>
            <?php endif ?>
        </div>

        <h3>Hoteles</h3>

        <div class="container">
            <?php if (count($resultHotel) == 0):?> 
                <p>no posee hoteles agregados en la bolsa</p>
            <?php else: ?>
                <?php foreach($resultHotel as $i => $value): 
                    $query = "SELECT * FROM hoteles WHERE id_hotel = ?";
                    $preRes = $conn->prepare($query);
                    $preRes->bind_param("s", $resultHotel[$i]['id_hotel']);
                    $preRes->execute();
                    $hotel = $preRes->get_result()->fetch_assoc();

                    $total += $hotel['precio_por_noche'] * $resultHotel[$i]['cantidad'];
                ?>
                    <div class = "cartElement">
                        <div class = "info">
                            <a href="infoHotel.php?id=<?php echo $resultHotel[$i]["id_hotel"] ?>" class = "nombre"><?php echo $hotel["nombre_hotel"] ?></a>
                            <p>precio: <?php echo $hotel["precio_por_noche"] ?></p>
                        </div>

                        <div class = "edit">
                            <form action=<?php echo $_SERVER["PHP_SELF"] ?> method="post">
                                <label for="cantidad">Cantidad:</label>

                                <input readonly class = "cantidad" type="text" id="cantidad" name="cantidad" value="<?php echo  $resultHotel[$i]["cantidad"] ?>"/>

                                <input class="btn btn-primary editntn" type="submit" name="op" value="-" />
                                <input class="btn btn-primary editbtn" type="submit" name="op" value="+" />

                                <input class="btn btn-primary" type="submit" name="eliminar" value="eliminar" />

                                <input type="hidden" name="id" value="<?php echo $resultHotel[$i]["id_hotel"] ?>"/>
                                <input type="hidden" name="disponible" value="<?php echo $hotel["hab_disponibles"] ?>"/>
                                <input type="hidden" name="tabla" value="carrito_hoteles"/>
                                <input type="hidden" name="tipoId" value="id_hotel"/>
                            </form>
                        </div>
                    </div>
                <?php endforeach;?>
            <?php endif ?>
        </div>

        <div class="precio">
            <p>Total a Pagar:</p>
            <p><?php echo $total; ?></p>
        </div>

        <?php
        if ($_SESSION["desc_"]) {
            echo "<div class='precio'>
                        <p>Total a Pagar con descuento:</p>
                        <p>" . $total * 0.90 . "</p>
                    </div>";
        }
        ?>

        <div class="pagar">
            <form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?> method="post">
                <input type="hidden" name="pagar" />
                <button class="btn btn-primary" type="submit">Pagar</button>
            </form>
            <span><?php echo $error ?></span>
        </div>
        <?php if ($_SESSION["desc"]): ?>
            <div class='desc'>
                <p>Tienes un descuento disponible del 10% al total de tu compra!</p>
                <form action= "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
                    <input class='btn btn-success' type='submit' name='aplicar' value='Aplicar descuento' />
                    <input class='btn btn-danger' type='submit' name='rechazar' value='Rechazar descuento' />
                </form>
            </div>
        <?php endif ?>
        
    </div>
</body>

</html>
