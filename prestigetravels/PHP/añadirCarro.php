<?php
include("connection.php");
if(!isset($_SESSION["rut"])){
    header('Location: /PHP/login.php');
    exit();
}
?>

<?php
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: error.php');
    exit();
}
if (!isset($_SESSION["rut"])) {
    header('Location: login.php');
    exit();
}

$URL = 'Location: '.$_SESSION['previous_pages'][0].'';

if(preg_match("/&error=(1|2)$/", $URL)){
    $URL = preg_replace("/&error=(1|2)$/", "", $URL);
}

if(isset($_POST["cantidad"]) && $_POST["cantidad"] === "0"){
    header($URL . "&error=2");
    exit();

}else{
    $result = array();

    $query = "CALL CarritoCantidad(?, ?, ?)";
    $preRes = $conn->prepare($query);
    var_dump($_POST["id_producto"]);
    var_dump($_SESSION["rut"]);
    var_dump($_POST["tipo"]);
    
    $preRes->bind_param("sss", $_POST["id_producto"], $_SESSION["rut"], $_POST["tipo"]);
    $preRes->execute();
    $preRes->store_result();
    $preRes->bind_result($result["id"], $result["disponible"], $result["cantidad"]);
    $preRes->fetch();
    $preRes->free_result();
    $preRes->close();
    
    if($_POST["cantidad"] + $result["cantidad"] > $result["disponible"]){
        header($URL . "&error=1");
    }else{
        if($result["cantidad"]){
            $query_update = "UPDATE ".$_POST["tabla"]." SET cantidad = cantidad + ? WHERE rut = ? AND ".$_POST["tipoId"]." = ?";
            $preRes = $conn->prepare($query_update);
            $preRes->bind_param("sss", $_POST["cantidad"] ,$_SESSION['rut'], $_POST["id_producto"]);
            $preRes->execute();
            header($URL);
        }else{
            $query_insert = "INSERT INTO ".$_POST["tabla"]." VALUES (?, ?, ?, 0)";
            $preRes = $conn->prepare($query_insert);
            $preRes->bind_param("sss", $_POST["id_producto"], $_SESSION['rut'], $_POST["cantidad"]);
            $preRes->execute();
            header($URL);
        }
    }
}
?>

