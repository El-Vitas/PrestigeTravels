<?php
include("connection.php");
?>


<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION["rut"])) {
        $id = $_POST["id"];
        $rut = $_SESSION["rut"];
        $ratio = isset($_POST["rating"]) ? $_POST["rating"] : $ratio = null;
        $comment = ($_POST["comentario"] == "") ? null : $comment;

        $queryComment = "INSERT INTO paquetes_usuario (rut,id_paquete,calificacion,opinion) VALUES (?, ?, ?,?))";
        $preRes = $conn->prepare($queryComment);
        $preRes->bind_param("sdds", $rut, $id, $ratio, $comment);
        $preRes->execute();
        header("Location: infoPaquete.php?id=".$id."");
    }   else{
        header("Location: login.php");
    }
} else {
    header("Location: error.php");
}
?>