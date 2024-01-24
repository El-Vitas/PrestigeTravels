<?php
include("connection.php");
if(!isset($_SESSION["rut"])){
    header('Location: /PHP/login.php');
    exit();
}
?>

<?php 
if($_SERVER["REQUEST_METHOD"] == "POST"){
        $query_check = "SELECT * FROM WishList WHERE id_usuario = ? AND ".$_POST["tipoId"]." = ?";
        $preRes = $conn->prepare($query_check);
        $preRes->bind_param("ss", $_SESSION["rut"], $_POST["id_producto"]);
        $preRes->execute();
        $result_check = $preRes->get_result()->fetch_all(MYSQLI_ASSOC);

        if (count($result_check) === 0){
            $query = "INSERT INTO WishList (id_usuario, ".$_POST["tipoId"].", tipo) VALUES (?, ?, ?)";
            $preRes = $conn->prepare($query);
            $tipo = $_POST["tipoId"] === "id_hotel" ? "h" : "p";
            $preRes->bind_param("sss", $_SESSION["rut"], $_POST["id_producto"], $tipo);
            $preRes->execute();
        }

        header('Location: '.$_SESSION['previous_pages'][0].'');
        
}
?>
