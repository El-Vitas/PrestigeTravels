<?php
include("connection.php");
if(!isset($_SESSION["rut"])){
    header('Location: http://localhost/Lab2/PHP/login.php');
    exit();
}
?>

<?php 
if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(array_key_exists('Eliminar', $_POST)){
        $sql = "DELETE FROM wishlist WHERE id_wishList = ?";
        $preRes = $conn->prepare($sql);
        $preRes->bind_param("s", $_POST["id"]);
        $preRes->execute();
    }
}
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    include("head.php");
    ?>
    <link rel="stylesheet" href="/Lab2/CSS/wishlist.css?v=<?php echo time(); ?>">
    <title>WishList</title>
</head>

<body>
    <header>
        <?php
        include("nav.php");
        ?>
    </header>

    <div class="container wishlistElement">
    <?php 
        $query = "CALL getWishList(?)";
        $preRes = $conn->prepare($query);
        $preRes->bind_param("s", $_SESSION["rut"]);
        $preRes->execute();
        $result = $preRes->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($result as $key => $value):
            $imageIndex = ($value["id"]) % 3;
            $cal = $value["promedio_calificacion"] ? number_format($value["promedio_calificacion"], 2) : "No posee calificación";

            $link = "";
            if ($value["tipo"] === "h") {
                $link = "http://localhost/Lab2/PHP/infoHotel.php?id=".$value["id"]."";
                $imageUrl = "/Lab2/IMG/hotel" . $imageIndex . "";
            }else{
                $link = "http://localhost/Lab2/PHP/infoPaquete.php?id=".$value["id"]."";
                $imageUrl = "/Lab2/IMG/paquete" . $imageIndex . "";
            }
    ?>
        <div class="card" style="width: 18rem;">
            <a href = "<?php echo $link?>">
                <img class="card-img-top" src="<?php echo $imageUrl ?>.jpg" alt="Card image cap">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $value["nombre"]?></h5>
                    <p class="card-text">Calificación: <?php echo $cal?></p>
                    <form action=<?php echo $_SERVER["PHP_SELF"]?> method="post">
                        <input class="btn btn-primary" type="submit" name="Eliminar" value="Eliminar"/>
                        <input type="hidden" name="id" value=<?php echo $value["id_wishlist"] ?>/>
                    </form>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
    </div>

</body>

</html>
