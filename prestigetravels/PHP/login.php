<?php
include("connection.php");
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST["password"];
    $rut = $_POST["rut"];

    if (!preg_match("/^[1-9]{8}(-)([1-9]|k)$/", $rut)) {
        $error = "rut invalido";
    } else {
        $rut = preg_replace("/-/", "", $rut);

        $sql = "SELECT * FROM usuarios WHERE rut = '$rut'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (!password_verify($password, $row["password"])) {
                $error = "contraseña incorrecta";
            } else {
                session_start();
                $_SESSION["rut"] = $row["rut"];
                $_SESSION["nombre"] = $row["nombre"];
                $_SESSION["correo"] = $row["correo"];
                $_SESSION["fecha_nacimiento"] = $row["fecha_nacimiento"];

                $_SESSION["desc"] = false;
                $_SESSION["desc_"] = false;

                $descuento = rand(0, 10);
                if($descuento > 5){
                    $_SESSION["desc"] = true;
                }

                header('Location: /PHP/index.php');
            }
        } else {
            $error = "usuario no encontrado";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    include("head.php");
    ?>
    <title>Login</title>
</head>

<body>
    <header>
        <?php
        include("nav.php");
        ?>
    </header>

    <div class="container mt-5 align-items-center bg-white p-5 rounded-5 text-secondary shadow r me-auto ms-auto" style="width: 25rem">
        <span class="error"><?php echo $error ?></span>
        <form method="post" action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>>
            <div class="text-center fs-1 fw-bold">Login</div>
            <div class="input-group mt-4 ">
                <div class="col-12">
                    <label for="rut">Rut</label>
                </div>
                <input class="form-control bg-light" type="text" name="rut" placeholder="12345678-9" />
            </div>
            <div class="input-group mt-1">
                <div class="col-12">
                    <label for="rut">Contraseña</label>
                </div>
                <input class="form-control bg-light" type="password" name="password" placeholder="Contraseña" />
            </div>
            <button type="submit" class="btn btn-primary text-white w-100 mt-4 fw-semibold shadow-sm">Submit</button>

            <div class="d-flex gap-1 justify-content-center mt-1">
                <div>¿No tienes cuenta?</div>
                <a href="SignUp.php" class="text-decoration-none text-info fw-semibold">Registro</a>
        </form>
    </div>

</body>

</html>
